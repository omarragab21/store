const fs = require('fs');
const path = require('path');

// Helper to load JSON file safely
function loadJson(filename) {
  try {
    const fullPath = path.join(process.cwd(), 'dummy_data', filename);
    if (fs.existsSync(fullPath)) {
      return JSON.parse(fs.readFileSync(fullPath, 'utf8'));
    }
    // Fallback relative to __dirname
    const relPath = path.join(__dirname, '..', 'dummy_data', filename);
    if (fs.existsSync(relPath)) {
      return JSON.parse(fs.readFileSync(relPath, 'utf8'));
    }
  } catch (err) {
    console.error(`Error loading ${filename}:`, err);
  }
  return null;
}

// In-memory cache for dummy data
const products = loadJson('products.json') || [];
const categories = loadJson('categories.json') || [];
const banners = loadJson('banners.json') || [];
const settings = loadJson('settings.json') || {};
const orders = loadJson('orders.json') || [];
const vendors = loadJson('vendors.json') || [];
const coupons = loadJson('coupons.json') || [];
const shippingCouriers = loadJson('shipping_couriers.json') || [];
const saudiZones = loadJson('zones_saudi.json') || [];
let databaseAll = null; // lazy load for performance

module.exports = (req, res) => {
  // Polyfill status and json for non-Vercel local node environments
  if (!res.status) {
    res.status = function(code) {
      this.statusCode = code;
      return this;
    };
  }
  if (!res.json) {
    res.json = function(data) {
      this.setHeader('Content-Type', 'application/json; charset=utf-8');
      this.end(JSON.stringify(data));
      return this;
    };
  }

  // CORS Headers
  res.setHeader('Access-Control-Allow-Origin', '*');
  res.setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
  res.setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization');
  res.setHeader('Content-Type', 'application/json; charset=utf-8');

  if (req.method === 'OPTIONS') {
    return res.status(200).end();
  }

  // Parse URL & Query
  const urlObj = new URL(req.url, `http://${req.headers.host || 'localhost'}`);
  const pathname = urlObj.pathname.replace(/^\/api/, '') || '/';
  const query = Object.fromEntries(urlObj.searchParams.entries());

  try {
    // 1. Root / Health Check
    if (pathname === '/' || pathname === '/health' || pathname === '/status') {
      return res.status(200).json({
        status: 'success',
        app: 'Toki Store & Marketplace API (Vercel Serverless)',
        version: '3.0.2-vercel-ready',
        database: 'Extracted OpenCart SQL Mock Database',
        metrics: {
          products_count: products.length,
          categories_count: categories.length,
          banners_sets: banners.length,
          orders_count: orders.length,
          vendors_count: vendors.length,
          total_sql_tables: 109
        },
        endpoints: [
          '/api/products',
          '/api/products/:id',
          '/api/categories',
          '/api/banners',
          '/api/vendors',
          '/api/orders',
          '/api/settings',
          '/api/coupons',
          '/api/shipping',
          '/api/zones',
          '/api/db/:table',
          '/api/checkout'
        ]
      });
    }

    // 2. Products List & Filtering
    if (pathname === '/products') {
      let filtered = [...products];

      // Filter by category
      if (query.category_id) {
        const catId = parseInt(query.category_id, 10);
        filtered = filtered.filter(p => p.categories && p.categories.includes(catId));
      }

      // Filter by search query
      if (query.q || query.search) {
        const q = (query.q || query.search).toLowerCase().trim();
        filtered = filtered.filter(p => 
          (p.name_ar && p.name_ar.toLowerCase().includes(q)) ||
          (p.name_en && p.name_en.toLowerCase().includes(q)) ||
          (p.summary_ar && p.summary_ar.toLowerCase().includes(q)) ||
          (p.model && p.model.toLowerCase().includes(q)) ||
          (p.category_name_ar && p.category_name_ar.toLowerCase().includes(q))
        );
      }

      // Filter by min / max price
      if (query.min_price) {
        const minP = parseFloat(query.min_price);
        filtered = filtered.filter(p => (p.special_price_sar || p.price_sar) >= minP);
      }
      if (query.max_price) {
        const maxP = parseFloat(query.max_price);
        filtered = filtered.filter(p => (p.special_price_sar || p.price_sar) <= maxP);
      }

      // Sort
      if (query.sort === 'price_asc') {
        filtered.sort((a, b) => (a.special_price_sar || a.price_sar) - (b.special_price_sar || b.price_sar));
      } else if (query.sort === 'price_desc') {
        filtered.sort((a, b) => (b.special_price_sar || b.price_sar) - (a.special_price_sar || a.price_sar));
      } else if (query.sort === 'rating') {
        filtered.sort((a, b) => b.rating - a.rating);
      }

      // Pagination
      const page = Math.max(1, parseInt(query.page || '1', 10));
      const limit = Math.max(1, Math.min(50, parseInt(query.limit || '20', 10)));
      const startIndex = (page - 1) * limit;
      const paginated = filtered.slice(startIndex, startIndex + limit);

      return res.status(200).json({
        status: 'success',
        total: filtered.length,
        page,
        limit,
        data: paginated
      });
    }

    // 3. Single Product by ID
    const productMatch = pathname.match(/^\/products\/(\d+)$/);
    if (productMatch) {
      const id = parseInt(productMatch[1], 10);
      const prod = products.find(p => p.id === id);
      if (!prod) {
        return res.status(404).json({ status: 'error', message: `Product #${id} not found` });
      }
      return res.status(200).json({ status: 'success', data: prod });
    }

    // 4. Categories
    if (pathname === '/categories') {
      return res.status(200).json({
        status: 'success',
        total: categories.length,
        data: categories
      });
    }

    // 5. Banners & Sliders
    if (pathname === '/banners') {
      return res.status(200).json({
        status: 'success',
        total: banners.length,
        data: banners
      });
    }

    // 6. Vendors / Marketplace
    if (pathname === '/vendors') {
      return res.status(200).json({
        status: 'success',
        total: vendors.length,
        data: vendors
      });
    }

    // 7. Settings & Config
    if (pathname === '/settings') {
      return res.status(200).json({
        status: 'success',
        data: settings
      });
    }

    // 8. Coupons
    if (pathname === '/coupons') {
      if (query.code) {
        const match = coupons.find(c => c.code.toLowerCase() === query.code.toLowerCase());
        if (match) {
          return res.status(200).json({ status: 'success', valid: true, coupon: match });
        }
        return res.status(200).json({ status: 'success', valid: false, message: 'Invalid or expired coupon' });
      }
      return res.status(200).json({ status: 'success', total: coupons.length, data: coupons });
    }

    // 9. Shipping Couriers
    if (pathname === '/shipping') {
      return res.status(200).json({
        status: 'success',
        total: shippingCouriers.length,
        data: shippingCouriers
      });
    }

    // 10. Zones
    if (pathname === '/zones') {
      return res.status(200).json({
        status: 'success',
        total: saudiZones.length,
        data: saudiZones
      });
    }

    // 11. Orders
    if (pathname === '/orders') {
      return res.status(200).json({
        status: 'success',
        total: orders.length,
        data: orders
      });
    }

    // 12. Database Explorer: /api/db/:table
    const dbMatch = pathname.match(/^\/db\/([a-zA-Z0-9_]+)$/);
    if (dbMatch) {
      const tableName = dbMatch[1];
      if (!databaseAll) {
        databaseAll = loadJson('database_all.json') || {};
      }
      if (tableName === 'tables') {
        const tableStats = Object.keys(databaseAll).map(t => ({
          table: t,
          rows: databaseAll[t].length
        }));
        return res.status(200).json({ status: 'success', total: tableStats.length, tables: tableStats });
      }
      if (databaseAll[tableName]) {
        return res.status(200).json({
          status: 'success',
          table: tableName,
          count: databaseAll[tableName].length,
          data: databaseAll[tableName].slice(0, 100)
        });
      }
      return res.status(404).json({ status: 'error', message: `Table '${tableName}' not found in SQL database dump` });
    }

    // 13. Checkout Simulation (POST)
    if (pathname === '/checkout' && req.method === 'POST') {
      let body = {};
      try {
        body = typeof req.body === 'string' ? JSON.parse(req.body) : (req.body || {});
      } catch (e) {
        body = {};
      }

      const items = body.items || [];
      const subtotal = items.reduce((sum, item) => sum + (item.price * (item.quantity || 1)), 0);
      const vat = subtotal * 0.15;
      const shipping = subtotal > 200 ? 0 : 25;
      const total = subtotal + vat + shipping;

      return res.status(200).json({
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
          discount_sar: 0,
          total_sar: parseFloat(total.toFixed(2)),
          currency: 'SAR'
        },
        items_count: items.length,
        status_text: 'تم استلام طلبك بنجاح وجاري تجهيز الشحن السريع 🚀'
      });
    }

    // Fallback 404
    return res.status(404).json({
      status: 'error',
      message: `API endpoint '${pathname}' not found. Try /api/products, /api/categories, /api/banners, etc.`
    });

  } catch (err) {
    console.error('Serverless Handler Error:', err);
    return res.status(500).json({
      status: 'error',
      message: 'Internal serverless execution error',
      error: err.message
    });
  }
};
