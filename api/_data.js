// Standalone data module for Vercel Serverless Functions
const products = require('./data/products.json');
const categories = require('./data/categories.json');
const banners = require('./data/banners.json');
const settings = require('./data/settings.json');
const orders = require('./data/orders.json');
const vendors = require('./data/vendors.json');
const coupons = require('./data/coupons.json');
const shippingCouriers = require('./data/shipping_couriers.json');
const saudiZones = require('./data/zones_saudi.json');

function sendJson(res, statusCode, data) {
  res.statusCode = statusCode;
  res.setHeader('Content-Type', 'application/json; charset=utf-8');
  res.setHeader('Access-Control-Allow-Origin', '*');
  res.setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
  res.setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
  
  if (res.status && typeof res.status === 'function') {
    res.status(statusCode);
  }
  if (res.json && typeof res.json === 'function') {
    return res.json(data);
  }
  res.end(JSON.stringify(data));
}

module.exports = {
  products,
  categories,
  banners,
  settings,
  orders,
  vendors,
  coupons,
  shippingCouriers,
  saudiZones,
  sendJson
};
