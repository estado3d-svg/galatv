const http = require('http');
const path = require('path');

const server = http.createServer((req, res) => {
  const url = new URL(req.url, `http://localhost:8000`);
  
  if (url.pathname === '/' || url.pathname === '/index.html') {
    res.writeHead(200, { 'Content-Type': 'text/html' });
    res.end(require('fs').readFileSync(path.join(__dirname, 'index.html'), 'utf8'));
  } else if (url.pathname.startsWith('/assets/')) {
    res.writeHead(200, { 'Content-Type': 'image/jpeg' });
    res.end(require('fs').readFileSync(path.join(__dirname, 'assets', url.pathname.slice(1)), 'utf8'));
  } else {
    res.writeHead(200, { 'Content-Type': 'text/css' });
    res.end(require('fs').readFileSync(path.join(__dirname, url.pathname), 'utf8'));
  }
});

server.listen(8000, '127.0.0.1', () => {
  console.log('Server running at http://127.0.0.1:8000');
});
