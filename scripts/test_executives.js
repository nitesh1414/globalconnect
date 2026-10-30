import http from 'http';

let cookieJar = {};

function updateCookies(res) {
  const setCookie = res.headers['set-cookie'];
  if (setCookie) {
    const list = Array.isArray(setCookie) ? setCookie : [setCookie];
    for (const c of list) {
      const parts = c.split(';')[0].split('=');
      const name = parts[0].trim();
      const val = parts.slice(1).join('=').trim();
      cookieJar[name] = val;
    }
  }
}

function getCookieHeader() {
  return Object.entries(cookieJar).map(([k, v]) => `${k}=${v}`).join('; ');
}

function makeRequest(url, options = {}, postData = null) {
  return new Promise((resolve, reject) => {
    const parsedUrl = new URL(url);
    const headers = { ...options.headers };
    
    const cookie = getCookieHeader();
    if (cookie && !headers['Cookie'] && !headers['cookie']) {
      headers['Cookie'] = cookie;
    }

    const reqOptions = {
      hostname: parsedUrl.hostname,
      port: parsedUrl.port || 80,
      path: parsedUrl.pathname + parsedUrl.search,
      method: options.method || 'GET',
      headers: headers
    };

    const req = http.request(reqOptions, (res) => {
      updateCookies(res);
      let data = '';
      res.on('data', chunk => data += chunk);
      res.on('end', () => {
        resolve({
          statusCode: res.statusCode,
          headers: res.headers,
          body: data
        });
      });
    });

    req.on('error', reject);
    if (postData) {
      req.write(postData);
    }
    req.end();
  });
}

async function runTests() {
  console.log('=== STARTING TESTS FOR EXECUTIVES FEATURE ===\n');

  // Test 1: Contact page
  console.log('Test 1: Fetching Contact page (http://127.0.0.1:3000/contact.php)...');
  const contactRes = await makeRequest('http://127.0.0.1:3000/contact.php');
  console.log('Status:', contactRes.statusCode);
  const hasShammy = contactRes.body.includes('Mr Shammy Songra');
  const hasCliff = contactRes.body.includes('Mr Cliff Poh');
  const hasLima = contactRes.body.includes('Mr. Lima Hutama Halim');
  const hasKapil = contactRes.body.includes('Mr. Kapil Gulati');
  const hasSandeep = contactRes.body.includes('Mr Sandeep Pratap Sanap');
  const hasSiddharth = contactRes.body.includes('Mr. Siddharth Maheshwari');
  const hasAmit = contactRes.body.includes('Mr. Amit Gupta');

  console.log('Found Mr Shammy Songra:', hasShammy);
  console.log('Found Mr Cliff Poh:', hasCliff);
  console.log('Found Mr Lima Hutama Halim:', hasLima);
  console.log('Found Mr Kapil Gulati:', hasKapil);
  console.log('Found Mr Sandeep Pratap Sanap:', hasSandeep);
  console.log('Found Mr Siddharth Maheshwari:', hasSiddharth);
  console.log('Found Mr Amit Gupta:', hasAmit);

  if (!hasShammy || !hasCliff || !hasLima || !hasKapil || !hasSandeep || !hasSiddharth || !hasAmit) {
    throw new Error('Contact page missing one or more authentic executive records!');
  }
  console.log('✅ Test 1 Passed: All 7 executives dynamically loaded on contact page.\n');

  // Test 2: Admin Login
  console.log('Test 2: Logging in as Admin...');
  const loginGet = await makeRequest('http://127.0.0.1:3000/admin/login.php');
  const csrfMatch = loginGet.body.match(/name="csrf_token"\s+value="([^"]+)"/);
  const csrfToken = csrfMatch ? csrfMatch[1] : '';
  console.log('CSRF Token:', csrfToken);

  const postParams = new URLSearchParams({
    csrf_token: csrfToken,
    username: 'admin',
    password: 'admin123'
  }).toString();

  const loginPost = await makeRequest('http://127.0.0.1:3000/admin/login.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
      'Content-Length': Buffer.byteLength(postParams)
    }
  }, postParams);

  console.log('Login POST Status:', loginPost.statusCode);

  // Test 3: Admin Executives List
  console.log('\nTest 3: Fetching Admin Executives list (/admin/executives.php)...');
  const execListRes = await makeRequest('http://127.0.0.1:3000/admin/executives.php');
  console.log('Status:', execListRes.statusCode);
  console.log('Contains "Leadership Directory":', execListRes.body.includes('Leadership Directory'));
  console.log('Contains "Mr Shammy Songra":', execListRes.body.includes('Mr Shammy Songra'));
  
  if (!execListRes.body.includes('Leadership Directory') || !execListRes.body.includes('Mr Shammy Songra')) {
    throw new Error('Admin executives page failed to load leadership directory!');
  }
  console.log('✅ Test 3 Passed: Admin executives list loaded successfully.\n');

  // Test 4: Add New Executive
  console.log('Test 4: Adding New Executive via POST /admin/executive-add.php...');
  const addFormRes = await makeRequest('http://127.0.0.1:3000/admin/executive-add.php');
  const addCsrfMatch = addFormRes.body.match(/name="csrf_token"\s+value="([^"]+)"/);
  const addCsrf = addCsrfMatch ? addCsrfMatch[1] : '';

  const newExecParams = new URLSearchParams({
    csrf_token: addCsrf,
    name: 'Mr. David Tan',
    role: 'Senior Trade Finance Advisor – Asia Pacific',
    email: 'david.tan@globalconnectsg.com',
    phone: '(65) 8123 4567',
    location: 'Singapore Regional Hub',
    bio: 'Specializing in cross-border trade structuring, structured commodities finance, and Southeast Asia banking syndication.',
    status: 'active',
    sort_order: '8'
  }).toString();

  const addExecPost = await makeRequest('http://127.0.0.1:3000/admin/executive-add.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
      'Content-Length': Buffer.byteLength(newExecParams)
    }
  }, newExecParams);

  console.log('Add Executive POST Status:', addExecPost.statusCode);

  // Verify on Contact page
  const contactRes2 = await makeRequest('http://127.0.0.1:3000/contact.php');
  const hasDavid = contactRes2.body.includes('Mr. David Tan');
  console.log('Public contact.php contains "Mr. David Tan":', hasDavid);
  if (!hasDavid) {
    throw new Error('New executive "Mr. David Tan" not rendered on contact page!');
  }
  console.log('✅ Test 4 Passed: Added new executive and verified on public contact page.\n');

  // Test 5: Edit Executive
  console.log('Test 5: Editing Executive...');
  const execListRes2 = await makeRequest('http://127.0.0.1:3000/admin/executives.php');
  
  // Find David Tan table row (inside <tbody>) and extract ID
  const tbody = execListRes2.body.split('<tbody>')[1]?.split('</tbody>')[0] || execListRes2.body;
  const rows = tbody.split('<tr>');
  let davidId = null;
  for (const row of rows) {
    if (row.includes('Mr. David Tan') && row.includes('executive-edit.php?id=')) {
      const m = row.match(/executive-edit\.php\?id=(\d+)/);
      if (m) {
        davidId = m[1];
        break;
      }
    }
  }
  if (!davidId) {
    throw new Error('Could not find David Tan edit link in table row!');
  }
  console.log('Extracted David Tan ID:', davidId);

  const editFormRes = await makeRequest(`http://127.0.0.1:3000/admin/executive-edit.php?id=${davidId}`);
  const editCsrfMatch = editFormRes.body.match(/name="csrf_token"\s+value="([^"]+)"/);
  const editCsrf = editCsrfMatch ? editCsrfMatch[1] : '';

  const editParams = new URLSearchParams({
    csrf_token: editCsrf,
    name: 'Mr. David Tan, CFA',
    role: 'Managing Director – Asia Pacific Trade Finance',
    email: 'david.tan@globalconnectsg.com',
    phone: '(65) 8123 9999',
    location: 'Singapore HQ & Tokyo Desk',
    bio: 'Leading structured trade finance programs and capital syndication.',
    status: 'active',
    sort_order: '8'
  }).toString();

  const editExecPost = await makeRequest(`http://127.0.0.1:3000/admin/executive-edit.php?id=${davidId}`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
      'Content-Length': Buffer.byteLength(editParams)
    }
  }, editParams);

  console.log('Edit Executive POST Status:', editExecPost.statusCode);

  // Verify update on contact page
  const contactRes3 = await makeRequest('http://127.0.0.1:3000/contact.php');
  const hasDavidUpdated = contactRes3.body.includes('Mr. David Tan, CFA');
  const hasOriginalShammy = contactRes3.body.includes('Mr Shammy Songra');
  console.log('Public contact.php contains "Mr. David Tan, CFA":', hasDavidUpdated);
  console.log('Public contact.php still contains "Mr Shammy Songra":', hasOriginalShammy);
  if (!hasDavidUpdated || !hasOriginalShammy) {
    throw new Error('Updated executive verification failed!');
  }
  console.log('✅ Test 5 Passed: Edited executive and verified updates on public contact page.\n');

  // Test 6: Toggle Status to Inactive
  console.log('Test 6: Toggling status to inactive...');
  await makeRequest(`http://127.0.0.1:3000/admin/executives.php?toggle_id=${davidId}`);

  const contactRes4 = await makeRequest('http://127.0.0.1:3000/contact.php');
  const hasDavidWhenInactive = contactRes4.body.includes('Mr. David Tan, CFA');
  console.log('Public contact.php hides inactive executive:', !hasDavidWhenInactive);
  if (hasDavidWhenInactive) {
    throw new Error('Inactive executive was unexpectedly shown on public contact page!');
  }
  console.log('✅ Test 6 Passed: Inactive executive hidden from public contact page.\n');

  // Test 7: Delete Executive
  console.log('Test 7: Deleting Executive...');
  const listRes3 = await makeRequest('http://127.0.0.1:3000/admin/executives.php');
  
  // Extract delete link CSRF token
  const delLinkMatch = listRes3.body.match(new RegExp(`executive-delete\\.php\\?id=${davidId}&amp;csrf_token=([a-f0-9]+)`)) || listRes3.body.match(new RegExp(`executive-delete\\.php\\?id=${davidId}&csrf_token=([a-f0-9]+)`));
  const delCsrf = delLinkMatch ? delLinkMatch[1] : csrfToken;
  console.log('Delete CSRF Token:', delCsrf);

  const delRes = await makeRequest(`http://127.0.0.1:3000/admin/executive-delete.php?id=${davidId}&csrf_token=${delCsrf}`);
  console.log('Delete GET Status:', delRes.statusCode);

  const listRes4 = await makeRequest('http://127.0.0.1:3000/admin/executives.php');
  const tbody4 = listRes4.body.split('<tbody>')[1]?.split('</tbody>')[0] || '';
  const stillInTable = tbody4.includes('Mr. David Tan');
  console.log('Executive removed from admin directory table:', !stillInTable);
  console.log('Contains deletion success alert:', listRes4.body.includes('has been removed successfully'));
  if (stillInTable) {
    throw new Error('Executive still present in admin table after deletion!');
  }
  console.log('✅ Test 7 Passed: Executive deleted successfully.\n');

  console.log('🎉 ALL EXECUTIVES CRUD AND DYNAMIC DISPLAY TESTS PASSED 100% SUCCESSFULLY!');
}

runTests().catch(err => {
  console.error('Test Failed:', err);
  process.exit(1);
});
