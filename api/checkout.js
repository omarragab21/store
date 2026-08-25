const { sendJson } = require('./_data.js');

module.exports = (req, res) => {
  if (req.method === 'OPTIONS') {
    res.statusCode = 200;
    return res.end();
  }

  let body = req.body || {};
  if (typeof body === 'string') {
    try { body = JSON.parse(body); } catch(e) { body = {}; }
  }

  const items = body.items || [];
  const subtotal = items.reduce((sum, item) => sum + ((item.price || 0) * (item.quantity || 1)), 0);
  const vat = subtotal * 0.15;
  const shipping = subtotal > 200 ? 0 : 25;
  const total = subtotal + vat + shipping;

  return sendJson(res, 200, {
    status: 'success',
    order_id: Math.floor(100000 + Math.random() * 900000),
    invoice_no: `TOK-INV-${Date.now().toString().slice(-6)}`,
    created_at: new Date().toISOString(),
    customer: body.customer || { name: 'عميل توكي', email: 'customer@tokistore.com', phone: '+966500000000' },
    payment_method: body.payment_method || 'Mada / Apple Pay',
    shipping_method: body.shipping_method || 'SMSA Express',
    summary: {
      subtotal_sar: parseFloat(subtotal.toFixed(2)),
      vat_15_sar: parseFloat(vat.toFixed(2)),
      shipping_sar: shipping,
      total_sar: parseFloat(total.toFixed(2)),
      currency: 'SAR'
    },
    items_count: items.length,
    status_text: 'تم استلام طلبك بنجاح وجاري تجهيز الشحن السريع 🚀'
  });
};
