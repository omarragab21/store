const indexHandler = require('./index.js');

module.exports = (req, res) => {
  const url = new URL(req.url, `http://${req.headers.host || 'localhost'}`);
  req.url = '/api/banners' + url.search;
  return indexHandler(req, res);
};
