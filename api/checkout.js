const indexHandler = require('./index.js');

module.exports = (req, res) => {
  req.url = '/api/checkout';
  return indexHandler(req, res);
};
