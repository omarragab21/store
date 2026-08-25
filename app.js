// Toki Store & Marketplace Client Application
if (window.__TOKI_LOADED__) {
  console.log('Toki app already loaded.');
} else {
  window.__TOKI_LOADED__ = true;

let currentLang = 'ar';
let currentCurrency = 'SAR';
let allProducts = [];
let allCategories = [];
let allBanners = [];
let cart = JSON.parse(localStorage.getItem('toki_cart') || '[]');
let activeCategoryFilter = null;

const CURRENCY_RATES = {
  SAR: { rate: 1.0, symbol: 'ر.س', symbolEn: 'SAR', suffix: true },
  USD: { rate: 0.266, symbol: '$', symbolEn: '$', suffix: false },
  AED: { rate: 0.98, symbol: 'د.إ', symbolEn: 'AED', suffix: true }
};

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', async () => {
  setupEventListeners();
  startCountdownTimer();
  await loadStoreData();
  renderCartBadge();
});

// Load Store Data from API / Dummy JSON
async function loadStoreData() {
  try {
    // 1. Load Products
    const prodRes = await fetch('/api/products').catch(() => fetch('/dummy_data/products.json'));
    const prodData = await prodRes.json();
    allProducts = prodData.data || prodData;

    // 2. Load Categories
    const catRes = await fetch('/api/categories').catch(() => fetch('/dummy_data/categories.json'));
    const catData = await catRes.json();
    allCategories = catData.data || catData;

    // 3. Load Banners
    const banRes = await fetch('/api/banners').catch(() => fetch('/dummy_data/banners.json'));
    const banData = await banRes.json();
    allBanners = banData.data || banData;

    renderCategories();
    renderProducts(allProducts);
    renderHeroSlider();
  } catch (err) {
    console.error('Error loading store data:', err);
  }
}

// Format Price with selected currency
function formatPrice(sarAmount) {
  if (sarAmount === null || sarAmount === undefined) return '';
  const curr = CURRENCY_RATES[currentCurrency] || CURRENCY_RATES.SAR;
  const converted = (sarAmount * curr.rate).toFixed(2);
  const symbol = currentLang === 'ar' ? curr.symbol : curr.symbolEn;
  return curr.suffix ? `${converted} ${symbol}` : `${symbol}${converted}`;
}

// Render Categories Strip
function renderCategories() {
  const container = document.getElementById('categoriesStrip');
  if (!container) return;

  let html = `
    <div class="cat-pill ${activeCategoryFilter === null ? 'active' : ''}" onclick="filterByCategory(null)">
      <div class="cat-icon-wrap" style="background: #feee00; font-size: 20px;">🛍️</div>
      <div class="cat-name">${currentLang === 'ar' ? 'جميع الأقسام' : 'All Categories'}</div>
      <div class="cat-count">${allProducts.length} ${currentLang === 'ar' ? 'منتج' : 'items'}</div>
    </div>
  `;

  allCategories.forEach(cat => {
    const iconSrc = cat.image ? `/image/${cat.image}` : `/image/catalog/demo/product/${cat.icon || 'cat1.png'}`;
    const name = currentLang === 'ar' ? cat.name_ar : cat.name_en;
    const isActive = activeCategoryFilter === cat.id ? 'active' : '';

    html += `
      <div class="cat-pill ${isActive}" onclick="filterByCategory(${cat.id})">
        <div class="cat-icon-wrap">
          <img src="${iconSrc}" alt="${name}" onerror="this.src='/image/placeholder.png'">
        </div>
        <div class="cat-name">${name}</div>
        <div class="cat-count">${cat.product_count} ${currentLang === 'ar' ? 'منتجات' : 'items'}</div>
      </div>
    `;
  });

  container.innerHTML = html;
}

// Render Products Grid
function renderProducts(products) {
  const grid = document.getElementById('productsGrid');
  const countElem = document.getElementById('productsCount');
  if (!grid) return;

  if (countElem) {
    countElem.textContent = `${products.length} ${currentLang === 'ar' ? 'منتج متاح' : 'products found'}`;
  }

  if (!products || products.length === 0) {
    grid.innerHTML = `
      <div style="grid-column: 1/-1; text-align: center; padding: 60px 20px; background: #fff; border-radius: 12px;">
        <div style="font-size: 48px; margin-bottom: 12px;">🔍</div>
        <h3>${currentLang === 'ar' ? 'لم يتم العثور على منتجات مطابقة' : 'No products found'}</h3>
        <p style="color: #64748b; margin-top: 6px;">${currentLang === 'ar' ? 'جرب البحث بكلمات أخرى أو اختر قسماً مختلفاً' : 'Try searching for other keywords or select another category'}</p>
      </div>
    `;
    return;
  }

  let html = '';
  products.forEach(p => {
    const name = currentLang === 'ar' ? p.name_ar : p.name_en;
    const catName = p.category_name_ar || (currentLang === 'ar' ? 'إلكترونيات' : 'Electronics');
    const imageSrc = `/image/${p.main_image}`;
    const hasDiscount = p.special_price_sar && p.special_price_sar < p.price_sar;
    const displayPrice = hasDiscount ? p.special_price_sar : p.price_sar;

    html += `
      <div class="product-card">
        ${p.badge ? `<div class="product-badge-tag">${p.badge}</div>` : ''}
        <div class="product-image-container">
          <img class="product-image" src="${imageSrc}" alt="${name}" onerror="this.src='/image/placeholder.png'">
          <div class="quick-view-overlay" onclick="openQuickView(${p.id})">
            👁️ ${currentLang === 'ar' ? 'معاينة سريعة' : 'Quick View'}
          </div>
        </div>
        <div class="product-body">
          <div class="product-category-label">${catName}</div>
          <h3 class="product-title" title="${name}">${name}</h3>
          
          <div class="product-rating">
            <span>⭐⭐⭐⭐⭐</span>
            <span style="font-weight: bold; color: #1e293b;">${p.rating || 4.8}</span>
            <span class="product-reviews-count">(${p.reviews_count || 15})</span>
          </div>

          <div class="product-price-row">
            <div class="current-price">${formatPrice(displayPrice)}</div>
            ${hasDiscount ? `
              <div class="old-price">${formatPrice(p.price_sar)}</div>
              <div class="discount-tag">-${p.discount_percent}%</div>
            ` : ''}
          </div>

          <button class="add-to-cart-btn" onclick="addToCart(${p.id})">
            🛒 ${currentLang === 'ar' ? 'أضف إلى السلة' : 'Add to Cart'}
          </button>
        </div>
      </div>
    `;
  });

  grid.innerHTML = html;
}

// Render Hero Slider
function renderHeroSlider() {
  const sliderImg = document.getElementById('sliderImg');
  const sliderTitle = document.getElementById('sliderTitle');
  if (sliderImg) {
    sliderImg.src = '/image/catalog/demo/banners/slider.png';
  }
}

// Filter by Category
function filterByCategory(catId) {
  activeCategoryFilter = catId;
  renderCategories();

  if (catId === null) {
    renderProducts(allProducts);
  } else {
    const filtered = allProducts.filter(p => p.categories && p.categories.includes(catId));
    renderProducts(filtered);
  }
}

// Live Search
function handleSearch(query) {
  const q = (query || '').toLowerCase().trim();
  const suggestionsBox = document.getElementById('searchSuggestions');

  if (!q) {
    if (suggestionsBox) suggestionsBox.classList.remove('active');
    renderProducts(activeCategoryFilter ? allProducts.filter(p => p.categories.includes(activeCategoryFilter)) : allProducts);
    return;
  }

  const results = allProducts.filter(p => 
    p.name_ar.toLowerCase().includes(q) ||
    p.name_en.toLowerCase().includes(q) ||
    (p.category_name_ar && p.category_name_ar.toLowerCase().includes(q)) ||
    (p.model && p.model.toLowerCase().includes(q))
  );

  // Render suggestions dropdown
  if (suggestionsBox) {
    if (results.length > 0) {
      let sugHtml = '';
      results.slice(0, 5).forEach(p => {
        sugHtml += `
          <div class="suggestion-item" onclick="openQuickView(${p.id})">
            <img class="suggestion-thumb" src="/image/${p.main_image}" onerror="this.src='/image/placeholder.png'">
            <div>
              <div style="font-weight: 700; font-size: 13px;">${currentLang === 'ar' ? p.name_ar : p.name_en}</div>
              <div style="font-size: 11px; color: #e11900; font-weight: 800;">${formatPrice(p.special_price_sar || p.price_sar)}</div>
            </div>
          </div>
        `;
      });
      suggestionsBox.innerHTML = sugHtml;
      suggestionsBox.classList.add('active');
    } else {
      suggestionsBox.classList.remove('active');
    }
  }

  renderProducts(results);
}

// Quick View Modal
function openQuickView(productId) {
  const prod = allProducts.find(p => p.id === productId);
  if (!prod) return;

  const modalBody = document.getElementById('quickViewContent');
  const name = currentLang === 'ar' ? prod.name_ar : prod.name_en;
  const desc = currentLang === 'ar' ? prod.description_ar : prod.description_en;
  const hasDiscount = prod.special_price_sar && prod.special_price_sar < prod.price_sar;
  const price = hasDiscount ? prod.special_price_sar : prod.price_sar;

  let specsHtml = '';
  if (prod.specs) {
    specsHtml = '<div style="margin: 16px 0; background: #f8fafc; padding: 12px; border-radius: 8px;">';
    for (const [k, v] of Object.entries(prod.specs)) {
      specsHtml += `<div style="display:flex; justify-content:space-between; font-size:12px; margin-bottom:4px;">
        <span style="color:#64748b;">${k}:</span>
        <span style="font-weight:bold;">${v}</span>
      </div>`;
    }
    specsHtml += '</div>';
  }

  let galleryHtml = '';
  if (prod.gallery_images && prod.gallery_images.length > 0) {
    galleryHtml = '<div style="display:flex; gap:8px; margin-top:12px;">';
    prod.gallery_images.forEach(img => {
      galleryHtml += `
        <img src="/image/${img}" style="width:50px; height:50px; object-fit:contain; border:1px solid #e2e8f0; border-radius:6px; cursor:pointer;" onclick="document.getElementById('qvMainImg').src='/image/${img}'">
      `;
    });
    galleryHtml += '</div>';
  }

  modalBody.innerHTML = `
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
      <div>
        <div style="height: 280px; display: flex; align-items: center; justify-content: center; background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px;">
          <img id="qvMainImg" src="/image/${prod.main_image}" style="max-height: 100%; max-width: 100%; object-fit: contain;">
        </div>
        ${galleryHtml}
      </div>
      <div>
        <span style="font-size: 11px; background: #feee00; padding: 2px 8px; border-radius: 4px; font-weight: bold;">${prod.badge || 'أصلي'}</span>
        <h2 style="font-size: 18px; margin: 8px 0; font-weight: 800;">${name}</h2>
        <div style="font-size: 12px; color: #64748b; margin-bottom: 12px;">الموديل: ${prod.model} | SKU: ${prod.sku}</div>
        
        <div style="display: flex; align-items: baseline; gap: 10px; margin-bottom: 12px;">
          <span style="font-size: 24px; font-weight: 900; color: #1e293b;">${formatPrice(price)}</span>
          ${hasDiscount ? `<span style="text-decoration: line-through; color: #94a3b8;">${formatPrice(prod.price_sar)}</span>` : ''}
        </div>

        <p style="font-size: 13px; color: #475569; line-height: 1.5; margin-bottom: 14px;">${prod.summary_ar || desc}</p>
        
        ${specsHtml}

        <div style="display: flex; gap: 12px; margin-top: 20px;">
          <button class="add-to-cart-btn" style="flex: 1; padding: 12px;" onclick="addToCart(${prod.id}); closeModal('quickViewModal');">
            🛒 ${currentLang === 'ar' ? 'إضافة إلى السلة' : 'Add to Cart'}
          </button>
        </div>
      </div>
    </div>
  `;

  document.getElementById('quickViewModal').classList.add('active');
}

// Shopping Cart Functions
function addToCart(productId) {
  const prod = allProducts.find(p => p.id === productId);
  if (!prod) return;

  const existing = cart.find(item => item.id === productId);
  if (existing) {
    existing.quantity += 1;
  } else {
    cart.push({
      id: prod.id,
      name_ar: prod.name_ar,
      name_en: prod.name_en,
      price: prod.special_price_sar || prod.price_sar,
      image: prod.main_image,
      quantity: 1
    });
  }

  saveCart();
  renderCartBadge();
  showToast(currentLang === 'ar' ? 'تمت إضافة المنتج إلى السلة 🛍️' : 'Added to cart successfully!');
}

function updateCartQuantity(productId, delta) {
  const item = cart.find(i => i.id === productId);
  if (!item) return;

  item.quantity += delta;
  if (item.quantity <= 0) {
    cart = cart.filter(i => i.id !== productId);
  }

  saveCart();
  renderCartDrawer();
  renderCartBadge();
}

function removeFromCart(productId) {
  cart = cart.filter(i => i.id !== productId);
  saveCart();
  renderCartDrawer();
  renderCartBadge();
}

function saveCart() {
  localStorage.setItem('toki_cart', JSON.stringify(cart));
}

function renderCartBadge() {
  const badge = document.getElementById('cartBadge');
  const totalItems = cart.reduce((sum, i) => sum + i.quantity, 0);
  if (badge) {
    badge.textContent = totalItems;
  }
}

function openCartDrawer() {
  renderCartDrawer();
  document.getElementById('cartDrawer').classList.add('active');
}

function closeCartDrawer() {
  document.getElementById('cartDrawer').classList.remove('active');
}

function renderCartDrawer() {
  const container = document.getElementById('cartItemsList');
  const subtotalElem = document.getElementById('cartSubtotal');
  const vatElem = document.getElementById('cartVat');
  const shippingElem = document.getElementById('cartShipping');
  const totalElem = document.getElementById('cartTotal');

  if (!container) return;

  if (cart.length === 0) {
    container.innerHTML = `
      <div style="text-align: center; padding: 40px 10px;">
        <div style="font-size: 40px; margin-bottom: 8px;">🛒</div>
        <h4>${currentLang === 'ar' ? 'سلتك فارغة حالياً' : 'Your cart is empty'}</h4>
        <p style="font-size: 12px; color: #64748b; margin-top: 6px;">${currentLang === 'ar' ? 'تصفح أحدث العروض والمنتجات وأضفها للسلة' : 'Browse products and add them to your cart'}</p>
      </div>
    `;
    if (subtotalElem) subtotalElem.textContent = formatPrice(0);
    if (vatElem) vatElem.textContent = formatPrice(0);
    if (shippingElem) shippingElem.textContent = formatPrice(0);
    if (totalElem) totalElem.textContent = formatPrice(0);
    return;
  }

  let html = '';
  let subtotal = 0;

  cart.forEach(item => {
    const itemTotal = item.price * item.quantity;
    subtotal += itemTotal;
    const name = currentLang === 'ar' ? item.name_ar : item.name_en;

    html += `
      <div class="cart-item">
        <img class="cart-item-img" src="/image/${item.image}" onerror="this.src='/image/placeholder.png'">
        <div class="cart-item-details">
          <h4 style="font-size: 13px; font-weight: 700; margin-bottom: 4px;">${name}</h4>
          <div style="font-size: 13px; font-weight: 800; color: #e11900;">${formatPrice(item.price)}</div>
          
          <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 8px;">
            <div style="display: flex; align-items: center; gap: 8px; border: 1px solid #e2e8f0; border-radius: 6px; padding: 2px 6px;">
              <button style="border:none; background:none; cursor:pointer; font-weight:bold;" onclick="updateCartQuantity(${item.id}, -1)">-</button>
              <span style="font-size: 13px; font-weight: bold;">${item.quantity}</span>
              <button style="border:none; background:none; cursor:pointer; font-weight:bold;" onclick="updateCartQuantity(${item.id}, 1)">+</button>
            </div>
            <button style="border:none; background:none; color:#ef4444; font-size:12px; cursor:pointer;" onclick="removeFromCart(${item.id})">🗑️ حذف</button>
          </div>
        </div>
      </div>
    `;
  });

  container.innerHTML = html;

  const vat = subtotal * 0.15;
  const shipping = subtotal > 200 ? 0 : 25;
  const total = subtotal + vat + shipping;

  if (subtotalElem) subtotalElem.textContent = formatPrice(subtotal);
  if (vatElem) vatElem.textContent = formatPrice(vat);
  if (shippingElem) shippingElem.textContent = shipping === 0 ? (currentLang === 'ar' ? 'مجاني 🚚' : 'Free') : formatPrice(shipping);
  if (totalElem) totalElem.textContent = formatPrice(total);
}

// Checkout Modal
function openCheckoutModal() {
  if (cart.length === 0) {
    showToast(currentLang === 'ar' ? 'السلة فارغة! الرجاء إضافة منتجات أولاً' : 'Cart is empty!');
    return;
  }
  closeCartDrawer();
  document.getElementById('checkoutModal').classList.add('active');
}

async function handlePlaceOrder(event) {
  event.preventDefault();
  const form = document.getElementById('checkoutForm');
  const formData = new FormData(form);

  const payload = {
    customer: {
      name: formData.get('fullName'),
      email: formData.get('email'),
      phone: formData.get('phone'),
      city: formData.get('city'),
      address: formData.get('address')
    },
    payment_method: formData.get('paymentMethod'),
    shipping_method: formData.get('shippingCourier'),
    items: cart
  };

  try {
    const res = await fetch('/api/checkout', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    });
    const data = await res.json();

    // Show Invoice / Confirmation
    closeModal('checkoutModal');
    showOrderSuccess(data);
    cart = [];
    saveCart();
    renderCartBadge();
  } catch (err) {
    console.error('Checkout error:', err);
    showToast('حدث خطأ أثناء معالجة الطلب');
  }
}

function showOrderSuccess(orderData) {
  const modal = document.getElementById('orderSuccessModal');
  const content = document.getElementById('orderSuccessContent');

  content.innerHTML = `
    <div style="text-align: center; padding: 20px;">
      <div style="font-size: 56px; margin-bottom: 12px;">🎉</div>
      <h2 style="font-size: 20px; font-weight: 800; color: #16a34a; margin-bottom: 8px;">${orderData.status_text || 'تم تأكيد طلبك بنجاح!'}</h2>
      <p style="font-size: 13px; color: #64748b;">رقم الفاتورة: <strong style="color: #0f172a;">${orderData.invoice_no}</strong></p>
      
      <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px; margin: 20px 0; text-align: right;">
        <div style="display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 6px;">
          <span>العميل:</span>
          <strong>${orderData.customer.name}</strong>
        </div>
        <div style="display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 6px;">
          <span>طريقة الدفع:</span>
          <strong>${orderData.payment_method}</strong>
        </div>
        <div style="display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 6px;">
          <span>شركة الشحن:</span>
          <strong>${orderData.shipping_method}</strong>
        </div>
        <div style="display: flex; justify-content: space-between; font-size: 15px; font-weight: 900; margin-top: 10px; padding-top: 8px; border-top: 1px dashed #cbd5e1;">
          <span>الإجمالي النهائي:</span>
          <span style="color: #e11900;">${formatPrice(orderData.summary.total_sar)}</span>
        </div>
      </div>

      <button class="add-to-cart-btn" style="width: 100%; padding: 12px;" onclick="closeModal('orderSuccessModal')">
        متابعة التسوق 🛍️
      </button>
    </div>
  `;

  modal.classList.add('active');
}

// API Explorer Tool
async function executeApiExplorer(endpoint) {
  const viewer = document.getElementById('apiResponseViewer');
  const urlDisplay = document.getElementById('apiUrlDisplay');
  if (urlDisplay) urlDisplay.textContent = endpoint;
  if (viewer) viewer.textContent = 'Loading response from ' + endpoint + '...';

  try {
    const res = await fetch(endpoint);
    const data = await res.json();
    if (viewer) {
      viewer.textContent = JSON.stringify(data, null, 2);
    }
  } catch (err) {
    if (viewer) viewer.textContent = 'Error: ' + err.message;
  }
}

// Database Tables Explorer Tool
async function loadDbTable(tableName) {
  const viewer = document.getElementById('dbResponseViewer');
  if (viewer) viewer.textContent = 'Loading table data for `' + tableName + '`...';

  try {
    const res = await fetch(`/api/db/${tableName}`);
    const data = await res.json();
    if (viewer) {
      viewer.textContent = JSON.stringify(data, null, 2);
    }
  } catch (err) {
    if (viewer) viewer.textContent = 'Error: ' + err.message;
  }
}

// Language Toggle
function setLanguage(lang) {
  currentLang = lang;
  document.body.classList.toggle('ltr', lang === 'en');
  document.documentElement.dir = lang === 'ar' ? 'rtl' : 'ltr';
  document.documentElement.lang = lang;
  renderCategories();
  renderProducts(allProducts);
  renderCartBadge();
}

// Currency Toggle
function setCurrency(curr) {
  currentCurrency = curr;
  renderProducts(allProducts);
  renderCartDrawer();
}

// Countdown Timer simulation
function startCountdownTimer() {
  let seconds = 3600 * 5 + 42 * 60 + 19;
  setInterval(() => {
    seconds--;
    if (seconds <= 0) seconds = 86400;
    const h = Math.floor(seconds / 3600);
    const m = Math.floor((seconds % 3600) / 60);
    const s = seconds % 60;
    const hElem = document.getElementById('cdHours');
    const mElem = document.getElementById('cdMinutes');
    const sElem = document.getElementById('cdSeconds');
    if (hElem) hElem.textContent = String(h).padStart(2, '0');
    if (mElem) mElem.textContent = String(m).padStart(2, '0');
    if (sElem) sElem.textContent = String(s).padStart(2, '0');
  }, 1000);
}

// Generic Modal Helpers
function closeModal(modalId) {
  const modal = document.getElementById(modalId);
  if (modal) modal.classList.remove('active');
}

function openModal(modalId) {
  const modal = document.getElementById(modalId);
  if (modal) modal.classList.add('active');
}

// Toast helper
function showToast(msg) {
  const toast = document.createElement('div');
  toast.style.position = 'fixed';
  toast.style.bottom = '20px';
  toast.style.left = '50%';
  toast.style.transform = 'translateX(-50%)';
  toast.style.background = '#1e293b';
  toast.style.color = '#feee00';
  toast.style.padding = '10px 20px';
  toast.style.borderRadius = '30px';
  toast.style.fontWeight = 'bold';
  toast.style.fontSize = '13px';
  toast.style.zIndex = '99999';
  toast.style.boxShadow = '0 10px 25px rgba(0,0,0,0.2)';
  toast.textContent = msg;
  document.body.appendChild(toast);
  setTimeout(() => toast.remove(), 2500);
}

// Setup Event Listeners
function setupEventListeners() {
  const searchInput = document.getElementById('searchInput');
  if (searchInput) {
    searchInput.addEventListener('input', (e) => handleSearch(e.target.value));
  }

  // Click outside search suggestions
  document.addEventListener('click', (e) => {
    const sug = document.getElementById('searchSuggestions');
    if (sug && !e.target.closest('.search-wrapper')) {
      sug.classList.remove('active');
    }
  });
}
}

