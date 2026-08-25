const { banners, sendJson } = require('./_data.js');

module.exports = (req, res) => {
  if (req.method === 'OPTIONS') {
    res.statusCode = 200;
    return res.end();
  }
  return sendJson(res, 200, { status: 'success', total: banners.length, data: banners });
};
