const { products, categories, banners, orders, vendors, sendJson } = require('./_data.js');

module.exports = (req, res) => {
  if (req.method === 'OPTIONS') {
    res.statusCode = 200;
    return res.end();
  }

  return sendJson(res, 200, {
    status: 'success',
    app: 'Toki Store & Marketplace API (Vercel Serverless)',
    version: '3.0.2',
    metrics: {
      products_count: products.length,
      categories_count: categories.length,
      banners_count: banners.length,
      orders_count: orders.length,
      vendors_count: vendors.length
    }
  });
};
