const indexHandler = require('./index.js');

module.exports = (req, res) => {
  req.url = '/api/health';
  return indexHandler(req, res);
};
