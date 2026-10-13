import express from 'express';
import cookieParser from 'cookie-parser';
import mime from 'mime-types';
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';
import { PHP, PHPRequestHandler } from '@php-wasm/universal';
import { loadNodeRuntime, createNodeFsMountHandler } from '@php-wasm/node';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const app = express();
const PORT = process.env.PORT || 3000;
const DOC_ROOT = '/var/www/html';
const WORKSPACE_DIR = __dirname;

// Create uploads and data directories if they don't exist
if (!fs.existsSync(path.join(WORKSPACE_DIR, 'uploads'))) {
  fs.mkdirSync(path.join(WORKSPACE_DIR, 'uploads'), { recursive: true });
}
if (!fs.existsSync(path.join(WORKSPACE_DIR, 'data'))) {
  fs.mkdirSync(path.join(WORKSPACE_DIR, 'data'), { recursive: true });
}

let php;
let requestHandler;

async function initPHP() {
  const origCwd = process.cwd();
  const wasmDir = path.join(WORKSPACE_DIR, 'node_modules/@php-wasm/node-8-3/asyncify');
  process.chdir(wasmDir);
  const runtime = await loadNodeRuntime('8.3', {
    emscriptenOptions: { processId: 1 }
  });
  process.chdir(origCwd);

  php = new PHP(runtime);
  php.mkdirTree(DOC_ROOT);
  php.mount(DOC_ROOT, createNodeFsMountHandler(WORKSPACE_DIR));

  requestHandler = new PHPRequestHandler({
    php,
    documentRoot: DOC_ROOT,
    absoluteUrl: `http://0.0.0.0:${PORT}`
  });

  console.log('PHP 8.3 WebAssembly Runtime initialized successfully with workspace mount.');
}

app.use(cookieParser());

// Static file serving for assets and uploads
app.use('/assets', express.static(path.join(WORKSPACE_DIR, 'assets')));
app.use('/uploads', express.static(path.join(WORKSPACE_DIR, 'uploads')));

// Middleware to capture request body and handle direct static files
app.use((req, res, next) => {
  const ext = path.extname(req.path).toLowerCase();
  const staticExts = ['.css', '.js', '.png', '.jpg', '.jpeg', '.gif', '.svg', '.ico', '.woff', '.woff2', '.ttf', '.webp'];
  
  if (staticExts.includes(ext)) {
    const filePath = path.join(WORKSPACE_DIR, req.path);
    if (fs.existsSync(filePath) && fs.statSync(filePath).isFile()) {
      const mimeType = mime.lookup(filePath) || 'application/octet-stream';
      res.setHeader('Content-Type', mimeType);
      return fs.createReadStream(filePath).pipe(res);
    }
  }

  const chunks = [];
  req.on('data', chunk => chunks.push(chunk));
  req.on('end', () => {
    req.rawBody = Buffer.concat(chunks);
    next();
  });
});

// Forward all other requests to PHP
app.use(async (req, res) => {
  try {
    if (!requestHandler) {
      return res.status(503).send('PHP Runtime is initializing. Please refresh in a moment.');
    }

    const host = req.headers.host || `0.0.0.0:${PORT}`;
    const protocol = req.headers['x-forwarded-proto'] || req.protocol || 'http';
    const fullUrl = `${protocol}://${host}${req.originalUrl}`;

    const headers = {};
    for (const [key, value] of Object.entries(req.headers)) {
      if (typeof value === 'string') {
        headers[key] = value;
      } else if (Array.isArray(value)) {
        headers[key] = value.join(', ');
      }
    }

    const requestOptions = {
      url: fullUrl,
      method: req.method,
      headers: headers,
    };

    if (req.rawBody && req.rawBody.length > 0) {
      requestOptions.body = req.rawBody;
    }

    const phpResponse = await requestHandler.request(requestOptions);

    // Set response headers
    if (phpResponse.headers) {
      for (const [headerName, headerValues] of Object.entries(phpResponse.headers)) {
        if (headerName.toLowerCase() === 'transfer-encoding') continue;
        if (Array.isArray(headerValues)) {
          if (headerName.toLowerCase() === 'set-cookie') {
            res.setHeader('set-cookie', headerValues);
          } else {
            res.setHeader(headerName, headerValues.join(', '));
          }
        } else if (headerValues) {
          res.setHeader(headerName, headerValues);
        }
      }
    }

    res.status(phpResponse.httpStatusCode || 200);

    if (phpResponse.bytes) {
      res.end(Buffer.from(phpResponse.bytes));
    } else {
      res.end(phpResponse.text || '');
    }
  } catch (err) {
    console.error('PHP Request Error:', err);
    res.status(500).send(`<h1>Server Error</h1><pre>${err.message}\n${err.stack}</pre>`);
  }
});

async function start() {
  await initPHP();
  app.listen(PORT, '0.0.0.0', () => {
    console.log(`Global Connect Web Server running at http://0.0.0.0:${PORT}`);
  });
}

start().catch(err => {
  console.error('Startup failed:', err);
  process.exit(1);
});
