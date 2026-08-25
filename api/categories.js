const { categories, sendJson } = require('./_data.js');

module.exports = (req, res) => {
  if (req.method === 'OPTIONS') {
    res.statusCode = 200;
    return res.end();
  }
  return sendJson(res, 200, { status: 'success', total: categories.length, data: categories });
};
