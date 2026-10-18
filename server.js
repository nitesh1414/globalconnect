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

// Create uploads directory if it does not exist
if (!fs.existsSync(path.join(WORKSPACE_DIR, 'uploads'))) {
  fs.mkdirSync(path.join(WORKSPACE_DIR, 'uploads'), { recursive: true });
}

let php;
let requestHandler;

// Static file search helper
function findStaticFile(requestedPath) {
  const cleanPath = requestedPath.split('?')[0];
  const filename = path.basename(cleanPath);

  // Candidate paths to check
  const candidates = [
    path.join(WORKSPACE_DIR, cleanPath),
    path.join(WORKSPACE_DIR, cleanPath.replace(/^\/(?:mysite|globalconnect)\//, '/')),
    path.join(WORKSPACE_DIR, 'assets', cleanPath.replace(/^(\/)?assets\//, '')),
    path.join(WORKSPACE_DIR, 'assets/css', filename),
    path.join(WORKSPACE_DIR, 'assets/js', filename),
    path.join(WORKSPACE_DIR, 'assets/images', filename),
    path.join(WORKSPACE_DIR, 'uploads', filename),
    path.join(WORKSPACE_DIR, filename)
  ];

  for (const cand of candidates) {
    if (fs.existsSync(cand) && fs.statSync(cand).isFile()) {
      return cand;
    }
  }
  return null;
}

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

  // Mount virtual subdirectories for simulated subfolders
  try {
    php.mkdirTree(`${DOC_ROOT}/mysite`);
    php.mount(`${DOC_ROOT}/mysite`, createNodeFsMountHandler(WORKSPACE_DIR));
    php.mkdirTree(`${DOC_ROOT}/globalconnect`);
    php.mount(`${DOC_ROOT}/globalconnect`, createNodeFsMountHandler(WORKSPACE_DIR));
  } catch (e) {}

  requestHandler = new PHPRequestHandler({
    php,
    documentRoot: DOC_ROOT,
    absoluteUrl: `http://0.0.0.0:${PORT}`
  });

  console.log('PHP 8.3 WebAssembly Runtime initialized successfully with workspace mount.');
}

app.use(cookieParser());

// Robust static asset middleware with universal fallback
app.use((req, res, next) => {
  const cleanUrl = req.path.split('?')[0];
  const ext = path.extname(cleanUrl).toLowerCase();
  const staticExts = ['.css', '.js', '.png', '.jpg', '.jpeg', '.gif', '.svg', '.ico', '.woff', '.woff2', '.ttf', '.webp', '.map'];

  if (staticExts.includes(ext) || cleanUrl.includes('/assets/') || cleanUrl.includes('/uploads/')) {
    const foundPath = findStaticFile(cleanUrl);
    if (foundPath) {
      const mimeType = mime.lookup(foundPath) || 'application/octet-stream';
      res.setHeader('Content-Type', mimeType);
      res.setHeader('Cache-Control', 'public, max-age=86400');
      res.setHeader('Access-Control-Allow-Origin', '*');
      return fs.createReadStream(foundPath).pipe(res);
    }
  }

  // Capture body for POST/PUT requests
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
      return res.send(Buffer.from(phpResponse.bytes));
    } else if (phpResponse.text) {
      return res.send(phpResponse.text);
    } else {
      return res.end();
    }
  } catch (err) {
    console.error('PHP execution error:', err);
    res.status(500).send(`
      <div style="font-family: sans-serif; padding: 30px; background: #fff1f2; border: 1px solid #fecdd3; border-radius: 8px; color: #9f1239;">
        <h2>PHP Execution Error</h2>
        <pre style="white-space: pre-wrap; font-size: 13px;">${err.stack || err.message}</pre>
      </div>
    `);
  }
});

// Start Server
initPHP().then(() => {
  app.listen(PORT, '0.0.0.0', () => {
    console.log(`Global Connect Web Server running at http://0.0.0.0:${PORT}`);
  });
}).catch(err => {
  console.error('Failed to initialize PHP runtime:', err);
  process.exit(1);
});
