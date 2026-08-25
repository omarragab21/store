# 🛍️ متجر وسوق توكي الإلكتروني | Toki Store & Marketplace

> **منصة وسوق إلكتروني متكامل متعدد التجار وتخصيص وطباعة المنتجات (شبيه نون وأمازون) مجهز بالكامل للنشر الفوري على Vercel و GitHub مع استخراج كامل لقاعدة البيانات كبيانات تجريبية JSON وواجهات REST API سحابية (Serverless).**

[![Vercel Deployment Ready](https://img.shields.io/badge/Vercel-Deployment%20Ready-black?logo=vercel)](https://vercel.com)
[![Node.js Serverless API](https://img.shields.io/badge/API-REST%20Serverless-green?logo=node.js)](/api/health)
[![OpenCart Engine](https://img.shields.io/badge/Engine-OpenCart%203.0.2-blue)](/toki2%20(1)/toki)
[![Database](https://img.shields.io/badge/Database-109%20SQL%20Tables%20Extracted-orange)](/dummy_data)
[![RTL & LTR](https://img.shields.io/badge/Languages-Arabic%20RTL%20%7C%20English-yellow)](/index.html)

---

## 🌟 نظرة عامة على المشروع (Project Overview)

مشروع **توكي (Toki)** هو منصة وسوق إلكتروني متطور مستوحى من تجربة منصات التجارة الكبرى مثل **نون (Noon)**، موجه للسوق السعودي والخليجي مع دعم:
- 🏬 **سوق متعدد التجار (Multi-Vendor Marketplace):** إدارة حسابات البائعين والمتاجر الشريكة.
- 🎨 **استوديو تصميم وطباعة المنتجات (Custom Product Designer):** تخصيص التيشيرتات والأكواب والهدايا قبل الشراء.
- 💳 **بوابات الدفع الخليجية:** دعم مدى (Mada)، أبل باي (Apple Pay)، تابي (Tabby)، والدفع عند الاستلام.
- 🚚 **الشحن والخدمات اللوجستية:** ربط جاهز مع سمسا (SMSA Express)، أرامكس (Aramex)، ومرسول/توصيل توكي الفوري.
- 🌐 **دعم كامل للغتين:** العربية (RTL) والإنجليزية (LTR) مع تحويل العملات (ريال سعودي SAR، دولار USD، درهم إماراتي AED).

---

## 🚀 جاهزية النشر على Vercel (Vercel Deployment Ready)

تم تجهيز المشروع بالكامل ليعمل بسلاسة على منصة **Vercel** بدون أي مشاكل متعلقة بعدم وجود خادم MySQL محلي:

1. **إعدادات Vercel (`vercel.json`):**
   - توجيه مسارات الـ API إلى دوال Serverless (`api/index.js`).
   - استضافة واستعراض الصور والأصول الثابتة من مجلد `image/`.
   - استعراض بيانات الـ JSON مباشرة من `dummy_data/`.
   - دعم التوجيه للواجهة الأمامية `index.html`.
2. **استخراج قاعدة البيانات (SQL to JSON Extractor):**
   - تم تفريغ وتحويل **109 جداول** من ملف الـ SQL الأصلي (`tokistore (3).sql`) إلى ملفات JSON مهيكلة ومنظمة بالكامل داخل مجلد `dummy_data/`.
3. **واجهة متجر تفاعلية ومستكشف API مدمج:**
   - واجهة متجر عصرية بتصميم نون/توكي (ألوان أصفر وأسود، عربة تسوق حية، شراء بخطوة واحدة One-Page Checkout، معاينة سريعة Quick View، وبحث ذكي حي).
   - مستكشف REST API مدمج لتجربة جميع المسارات مباشرة.
   - مستكشف قاعدة بيانات لاستعراض الجداول والبيانات المستخرجة بنقرة زر.

---

## 📁 هيكل المجلدات والملفات (Directory Structure)

```text
store/
├── vercel.json                  # إعدادات التوجيه والـ Serverless لنظام Vercel
├── package.json                 # حزم وتشغيل الـ Scripts
├── server.js                    # خادم Node.js محلي سريع للتطوير والتجربة
├── index.html                   # واجهة المتجر التفاعلية (Storefront & API Explorer)
├── style.css                    # تنسيقات واجهة المتجر بهوية نون/توكي (RTL/LTR)
├── app.js                       # منطق التطبيق التفاعلي وعربة التسوق والطلبات
├── extract_sql_to_dummy_data.py # سكربت بايثون لتحويل الـ SQL إلى Dummy Data JSON
├── .gitignore                   # استبعاد الملفات المؤقتة والمهملات
├── api/
│   └── index.js                 # Vercel Serverless Function (REST API لكافة الوظائف)
├── dummy_data/                  # ملفات البيانات المستخرجة من الـ SQL:
│   ├── products.json            # المنتجات وتفاصيلها والأسعار بالريال والمخزون
│   ├── categories.json          # شجرة الأقسام والتصنيفات
│   ├── banners.json             # البانرات والعروض وسلايدر الواجهة
│   ├── orders.json              # سجل الطلبات والفواتير
│   ├── customers.json           # بيانات العملاء
│   ├── coupons.json             # كوبونات الخصم
│   ├── shipping_couriers.json   # شركات الشحن (سمسا، أرامكس، توكي)
│   ├── zones_saudi.json         # المناطق والمدن السعودية
│   ├── settings.json            # إعدادات المتجر والعملات وطرق الدفع
│   ├── vendors.json             # المتاجر والتجار الشركاء
│   └── database_all.json        # تفريغ الـ 109 جدول كاملة كمرجع شامل
├── image/                       # صور المنتجات والبانرات والأيقونات وشعارات الدفع
├── tokistore (3).sql            # النسخة الأصلية لقاعدة البيانات (MySQL/MariaDB)
├── PROJECT_DOCUMENTATION.md     # التوثيق المعماري والوظيفي الشامل للنظام
└── toki2 (1)/                   # النواة الأصلية لـ OpenCart 3.0.2.0 (PHP Backend)
    └── toki/                    # (admin, catalog, system, vqmod, etc.)
```

---

## 🔌 دليل الـ REST API المتاح على Vercel

توفر دالة الـ Serverless (`api/index.js`) كافة المسارات التالية مع دعم CORS والتصفية والبحث:

| المسار (Endpoint) | الطريقة | الوصف |
|-------------------|---------|-------|
| `/api/health` | `GET` | فحص حالة النظام والخدمة والإحصائيات العامة |
| `/api/products` | `GET` | قائمة المنتجات (يدعم التصفية حسب `category_id`، والبحث `q`، والفرز `sort`، والسعر `min_price`/`max_price`) |
| `/api/products/:id` | `GET` | استرجاع بيانات منتج محدد بالمواصفات ومعرض الصور |
| `/api/categories` | `GET` | استرجاع قائمة الأقسام والتصنيفات مع عدد المنتجات |
| `/api/banners` | `GET` | استرجاع البانرات الإعلانية وسلايدر الواجهة |
| `/api/vendors` | `GET` | استرجاع المتاجر والبائعين في السوق متعدد التجار |
| `/api/orders` | `GET` | استرجاع عينات من الطلبات السابقة والفواتير |
| `/api/coupons` | `GET` | التحقق من كوبونات الخصم (`?code=TOKI20`) |
| `/api/shipping` | `GET` | قائمة شركات الشحن والتوصيل المتاحة وأسعارها |
| `/api/zones` | `GET` | المناطق والمدن السعودية المدعومة |
| `/api/settings` | `GET` | إعدادات المتجر، العملات، وطرق الدفع |
| `/api/db/:table` | `GET` | استعلام مباشر لأي جدول من جداول الـ 109 المستخرجة |
| `/api/checkout` | `POST` | محاكاة إتمام الطلب وإنشاء فاتورة رسمية وحساب الضريبة |

---

## 💻 التشغيل المحلي والتطوير (Local Development)

### 1. تشغيل السيرفر المحلي:
```bash
npm start
```
أو
```bash
npm run dev
```
افتح المتصفح على: `http://localhost:3000`

### 2. إعادة استخراج البيانات من الـ SQL:
في حال قمت بتحديث ملف `tokistore (3).sql`، يمكنك إعادة توليد ملفات الـ JSON بالأمر:
```bash
npm run export:data
```

---

## 🚢 خطوات النشر على GitHub و Vercel (Deployment Steps)

### النشر على GitHub:
```bash
git init
git add .
git commit -m "feat: initialize toki store repository with vercel config and dummy json data"
git branch -M main
git remote add origin https://github.com/omarragab21/store.git
git push -u origin main
```

### النشر على Vercel:
1. اذهب إلى [Vercel Dashboard](https://vercel.com/new).
2. اختر المستودع `omarragab21/store`.
3. اضغط **Deploy**.
4. سيتم بناء ونشر الموقع فورياً ويعمل المتجر بالكامل مع دوال الـ Serverless API.

---

## 📜 الترخيص (License)
مشروع متجر وسوق توكي مفتوح المصدر بموجب رخصة MIT.
جميع الحقوق محفوظة للمطور.
