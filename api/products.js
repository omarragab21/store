const { products, sendJson } = require('./_data.js');

module.exports = (req, res) => {
  if (req.method === 'OPTIONS') {
    res.statusCode = 200;
    return res.end();
  }

  try {
    const rawUrl = req.url || '';
    const query = {};
    if (rawUrl.includes('?')) {
      const qs = rawUrl.split('?')[1];
      const params = new URLSearchParams(qs);
      for (const [k, v] of params.entries()) {
        query[k] = v;
      }
    }

    // Check if product ID requested (e.g. ?id=1)
    if (query.id) {
      const pid = parseInt(query.id, 10);
      const prod = products.find(p => p.id === pid);
      if (!prod) {
        return sendJson(res, 404, { status: 'error', message: `Product #${pid} not found` });
      }
      return sendJson(res, 200, { status: 'success', data: prod });
    }

    let filtered = [...products];

    // Filter by category
    if (query.category_id) {
      const catId = parseInt(query.category_id, 10);
      filtered = filtered.filter(p => p.categories && p.categories.includes(catId));
    }

    // Filter by search
    if (query.q || query.search) {
      const q = (query.q || query.search).toLowerCase().trim();
      filtered = filtered.filter(p => 
        (p.name_ar && p.name_ar.toLowerCase().includes(q)) ||
        (p.name_en && p.name_en.toLowerCase().includes(q)) ||
        (p.summary_ar && p.summary_ar.toLowerCase().includes(q)) ||
        (p.category_name_ar && p.category_name_ar.toLowerCase().includes(q))
      );
    }

    // Sort
    if (query.sort === 'price_asc') {
      filtered.sort((a, b) => (a.special_price_sar || a.price_sar) - (b.special_price_sar || b.price_sar));
    } else if (query.sort === 'price_desc') {
      filtered.sort((a, b) => (b.special_price_sar || b.price_sar) - (a.special_price_sar || a.price_sar));
    } else if (query.sort === 'rating') {
      filtered.sort((a, b) => b.rating - a.rating);
    }

    return sendJson(res, 200, {
      status: 'success',
      total: filtered.length,
      data: filtered
    });
  } catch (err) {
    return sendJson(res, 500, { status: 'error', message: err.message });
  }
};
