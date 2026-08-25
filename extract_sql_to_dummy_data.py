import re
import json
import os
import html

def parse_complete_sql(sql_file_path):
    print(f"Loading {sql_file_path}...")
    with open(sql_file_path, 'r', encoding='utf-8', errors='ignore') as f:
        content = f.read()

    # 1. Extract table column definitions
    table_columns = {}
    create_table_regex = re.compile(
        r'CREATE TABLE [`"]?([a-zA-Z0-9_]+)[`"]?\s*\((.*?)\)\s*ENGINE=',
        re.DOTALL | re.IGNORECASE
    )
    
    for match in create_table_regex.finditer(content):
        table_name = match.group(1)
        body = match.group(2)
        cols = []
        for line in body.split('\n'):
            line = line.strip()
            if not line or any(line.upper().startswith(k) for k in ['PRIMARY KEY', 'KEY', 'UNIQUE', 'CONSTRAINT', 'FULLTEXT']):
                continue
            col_match = re.match(r'[`"]?([a-zA-Z0-9_]+)[`"]?\s+', line)
            if col_match:
                cols.append(col_match.group(1))
        table_columns[table_name] = cols

    # 2. Extract INSERT statements
    table_data = {}
    insert_blocks = re.split(r'INSERT INTO [`"]?([a-zA-Z0-9_]+)[`"]?', content)
    
    for i in range(1, len(insert_blocks), 2):
        table_name = insert_blocks[i]
        insert_body = insert_blocks[i+1]
        
        stmt_end = insert_body.find(';\n')
        if stmt_end == -1:
            stmt_end = insert_body.find(';')
        if stmt_end != -1:
            insert_body = insert_body[:stmt_end]
            
        explicit_cols = None
        values_pos = re.search(r'VALUES\s*', insert_body, re.IGNORECASE)
        if not values_pos:
            continue
            
        prefix = insert_body[:values_pos.start()].strip()
        if prefix.startswith('(') and prefix.endswith(')'):
            explicit_cols = [c.strip(' `"') for c in prefix[1:-1].split(',')]
            
        columns = explicit_cols or table_columns.get(table_name, [])
        values_str = insert_body[values_pos.end():].strip()
        
        rows = parse_sql_values(values_str)
        
        if table_name not in table_data:
            table_data[table_name] = []
            
        for row in rows:
            if columns and len(columns) == len(row):
                row_dict = dict(zip(columns, row))
            else:
                row_dict = {f"col_{idx}": val for idx, val in enumerate(row)}
            table_data[table_name].append(row_dict)
            
    print(f"Extracted {len(table_data)} tables from SQL.")
    return table_data

def parse_sql_values(values_str):
    rows = []
    in_row = False
    in_str = False
    str_char = ''
    escaped = False
    current_val = []
    current_row = []
    
    i = 0
    n = len(values_str)
    
    while i < n:
        c = values_str[i]
        
        if not in_row:
            if c == '(':
                in_row = True
                current_row = []
                current_val = []
            i += 1
            continue
            
        if in_str:
            if escaped:
                current_val.append(c)
                escaped = False
            elif c == '\\':
                escaped = True
            elif c == str_char:
                if i + 1 < n and values_str[i+1] == str_char:
                    current_val.append(str_char)
                    i += 1
                else:
                    in_str = False
            else:
                current_val.append(c)
        else:
            if c in ("'", '"'):
                in_str = True
                str_char = c
            elif c == ',':
                val = ''.join(current_val).strip()
                current_row.append(format_val(val))
                current_val = []
            elif c == ')':
                val = ''.join(current_val).strip()
                current_row.append(format_val(val))
                rows.append(current_row)
                in_row = False
                current_row = []
                current_val = []
            else:
                current_val.append(c)
        i += 1
        
    return rows

def format_val(val):
    if val.upper() == 'NULL' or val == '':
        return None if val.upper() == 'NULL' else ''
    if val.lstrip('-+').isdigit():
        return int(val)
    if re.match(r'^-?\d+\.\d+$', val):
        try:
            return float(val)
        except:
            pass
    return val

def export_organized_dummy_data(table_data, output_dir):
    os.makedirs(output_dir, exist_ok=True)
    
    # 1. Export Raw Complete DB JSON
    with open(os.path.join(output_dir, 'database_all.json'), 'w', encoding='utf-8') as f:
        json.dump(table_data, f, ensure_ascii=False, indent=2)
    print("Exported database_all.json")
    
    # Product realistic names mapping
    realistic_products = {
        1: {
            "name_ar": "هاتف توكي سمارت إكستريم 5G (ذاكرة 256 جيجابايت)",
            "name_en": "Toki Smart Extreme 5G Phone (256GB)",
            "category_name_ar": "موبايلات",
            "rating": 4.9,
            "reviews": 128,
            "badge": "الأكثر مبيعاً 🔥",
            "specs": {"الشاشة": "6.7 بوصة AMOLED 120Hz", "المعالج": "Snapdragon 8 Gen 2", "البطارية": "5000 mAh"}
        },
        2: {
            "name_ar": "لابتوب توكي إير فائق النحافة Core i7 (شاشة 15.6 بوصة)",
            "name_en": "Toki Air Ultra Slim Laptop Core i7 (15.6 Inch)",
            "category_name_ar": "لابتوبات وإلكترونيات",
            "rating": 4.8,
            "reviews": 94,
            "badge": "عرض خاص ⚡",
            "specs": {"المعالج": "Intel Core i7 13th Gen", "الرام": "16GB DDR5", "التخزين": "1TB NVMe SSD"}
        },
        3: {
            "name_ar": "ساعة توكي الذكية المقاومة للماء مع مراقب اللياقة",
            "name_en": "Toki Waterproof Smart Watch with Fitness Tracker",
            "category_name_ar": "إلكترونيات وساعات",
            "rating": 4.7,
            "reviews": 210,
            "badge": "خصم مميز 30%",
            "specs": {"البطارية": "تدوم 10 أيام", "المستشعرات": "نبضات القلب، الأكسجين، النوم", "المقاومة": "IP68"}
        },
        4: {
            "name_ar": "سماعات رأس توكي اللاسلكية بنظام العزل النشط للضوضاء",
            "name_en": "Toki Wireless ANC Over-Ear Headphones",
            "category_name_ar": "صوتيات وإلكترونيات",
            "rating": 4.9,
            "reviews": 340,
            "badge": "وصل حديثاً ✨",
            "specs": {"البطارية": "40 ساعة تشغيل", "عزل الضوضاء": "Active Hybrid ANC", "الاتصال": "Bluetooth 5.3"}
        },
        5: {
            "name_ar": "ماكينة تحضير الاسبريسو والقهوة الاحترافية من توكي",
            "name_en": "Toki Pro Espresso & Coffee Machine",
            "category_name_ar": "أجهزة منزلية وسوبرماركت",
            "rating": 4.6,
            "reviews": 85,
            "badge": "شحن مجاني 🚚",
            "specs": {"الضغط": "20 Bar", "سعة الخزان": "1.5 لتر", "المادة": "Stainless Steel"}
        },
        6: {
            "name_ar": "مجموعة العناية الفاخرة بالبشرة والجمال العضوي",
            "name_en": "Toki Luxury Organic Skin Care & Beauty Set",
            "category_name_ar": "الجمال والعناية",
            "rating": 4.8,
            "reviews": 156,
            "badge": "طبيعي 100%",
            "specs": {"المكونات": "مستخلصات عضوية طبيعية", "المحتوى": "سيروم + كريم ليلي + غسول"}
        },
        7: {
            "name_ar": "كاميرا تصوير رقمية احترافية 4K UHD مع عدسة زووم",
            "name_en": "Toki 4K UHD Pro Digital Camera with Zoom Lens",
            "category_name_ar": "كاميرات وتصوير",
            "rating": 4.9,
            "reviews": 62,
            "badge": "احترافي 📷",
            "specs": {"الدقة": "48 Megapixels", "الفيديو": "4K 60fps", "الزووم": "24x Optical"}
        },
        8: {
            "name_ar": "عطر توكي الملكي المركز للجنسين (100 مل)",
            "name_en": "Toki Royal Eau De Parfum (100ml)",
            "category_name_ar": "عطور وأزياء",
            "rating": 5.0,
            "reviews": 412,
            "badge": "الأعلى تقييماً 🌟",
            "specs": {"النوع": "Eau de Parfum", "الحجم": "100ml", "النوتات": "العود والمسك والعنبر والورد"}
        }
    }

    # 2. Build High-Level Products JSON
    langs = {l.get('language_id'): l.get('code') for l in table_data.get('oc_language', [])}
    
    prod_desc = {}
    for d in table_data.get('oc_product_description', []):
        pid = d.get('product_id')
        if pid not in prod_desc:
            prod_desc[pid] = {}
        lid = d.get('language_id')
        lang_code = langs.get(lid, f'lang_{lid}')
        
        name_clean = html.unescape(d.get('name', ''))
        desc_clean = html.unescape(d.get('description', ''))
        
        prod_desc[pid][lang_code] = {
            'name': name_clean,
            'description': desc_clean,
            'meta_title': html.unescape(d.get('meta_title', '')),
            'meta_description': html.unescape(d.get('meta_description', '')),
            'tag': html.unescape(d.get('tag', ''))
        }
        
    prod_cats = {}
    for pc in table_data.get('oc_product_to_category', []):
        pid = pc.get('product_id')
        if pid not in prod_cats:
            prod_cats[pid] = []
        prod_cats[pid].append(pc.get('category_id'))
        
    prod_images = {}
    for pi in table_data.get('oc_product_image', []):
        pid = pi.get('product_id')
        if pid not in prod_images:
            prod_images[pid] = []
        prod_images[pid].append({
            'image': pi.get('image'),
            'sort_order': pi.get('sort_order', 0)
        })
        
    prod_specials = {}
    for ps in table_data.get('oc_product_special', []):
        pid = ps.get('product_id')
        if pid not in prod_specials:
            prod_specials[pid] = []
        prod_specials[pid].append({
            'price': ps.get('price'),
            'date_start': ps.get('date_start'),
            'date_end': ps.get('date_end')
        })

    manufacturers = {m.get('manufacturer_id'): m for m in table_data.get('oc_manufacturer', [])}

    products = []
    for p in table_data.get('oc_product', []):
        pid = p.get('product_id')
        p_desc = prod_desc.get(pid, {})
        
        real_info = realistic_products.get(pid, {})
        
        name_ar = real_info.get('name_ar') or p_desc.get('ar', {}).get('name') or f"منتج {pid}"
        name_en = real_info.get('name_en') or p_desc.get('en-gb', {}).get('name') or f"Product {pid}"
        
        desc_ar = p_desc.get('ar', {}).get('description') or (
            f"منتج أصلي 100% عالي الجودة مع ضمان شامل لمدة عامين. مناسب لجميع الاستخدامات ويوفر أعلى أداء وموثوقية مع شحن سريع لجميع مدن المملكة العربية السعودية ودول الخليج."
        )
        desc_en = p_desc.get('en-gb', {}).get('description') or (
            f"100% Genuine product with comprehensive 2-year warranty. Fast delivery across Saudi Arabia and Gulf regions."
        )

        clean_desc_ar = re.sub(r'<[^>]+>', ' ', desc_ar).strip()
        clean_desc_en = re.sub(r'<[^>]+>', ' ', desc_en).strip()

        price = float(p.get('price', 0)) if p.get('price') is not None else 0.0
        special = prod_specials.get(pid, [{}])[0].get('price')
        
        # If no special in DB, create realistic promo price
        if not special and price > 160:
            special = round(price * 0.82, 2)
            
        special_price = float(special) if special is not None else None
        
        discount_percent = 0
        if special_price and price > 0 and special_price < price:
            discount_percent = round(((price - special_price) / price) * 100)
            
        m_id = p.get('manufacturer_id', 0)
        m_info = manufacturers.get(m_id, {})
        
        # Build gallery images
        gallery = [p.get('image', '')]
        if f"catalog/demo/product/p{pid}.png" not in gallery:
            gallery.append(f"catalog/demo/product/p{pid}.png")
        for i in range(1, 4):
            alt_img = f"catalog/demo/product/p{(pid + i) % 8 + 1}.png"
            if alt_img not in gallery and len(gallery) < 4:
                gallery.append(alt_img)

        product_obj = {
            'id': pid,
            'model': p.get('model', f'TOK-2024-{pid}'),
            'sku': p.get('sku', f'SKU-TK-{1000+pid}'),
            'name_ar': name_ar,
            'name_en': name_en,
            'description_ar': desc_ar,
            'description_en': desc_en,
            'summary_ar': clean_desc_ar[:180] + '...',
            'summary_en': clean_desc_en[:180] + '...',
            'price_sar': price,
            'special_price_sar': special_price,
            'discount_percent': discount_percent,
            'currency': 'SAR',
            'quantity': p.get('quantity', 100),
            'in_stock': (p.get('quantity', 0) > 0),
            'main_image': p.get('image', f'catalog/demo/product/p{pid}.png'),
            'gallery_images': gallery,
            'categories': prod_cats.get(pid, [57, 61, 62]),
            'category_name_ar': real_info.get('category_name_ar', 'إلكترونيات'),
            'manufacturer': {
                'id': m_id,
                'name': m_info.get('name', 'Toki Original'),
                'image': m_info.get('image', 'catalog/demo/manufacturer/logo1.png')
            },
            'rating': real_info.get('rating', 4.8),
            'reviews_count': real_info.get('reviews', 25),
            'specs': real_info.get('specs', {"الضمان": "سنتين", "المنشأ": "أصلي"}),
            'shipping_free': price > 160,
            'badge': real_info.get('badge', 'الأكثر مبيعاً 🔥'),
            'vendor': {
                'id': (pid % 3) + 1,
                'name': ['متجر النخبة للإلكترونيات', 'عالم الموضة الخليجية', 'استوديو توكي الرسمي'][pid % 3],
                'verified': True
            },
            'date_added': str(p.get('date_added', '2024-01-15'))
        }
        products.append(product_obj)
        
    with open(os.path.join(output_dir, 'products.json'), 'w', encoding='utf-8') as f:
        json.dump(products, f, ensure_ascii=False, indent=2)
    print(f"Exported products.json ({len(products)} products)")

    # 3. Build High-Level Categories JSON
    cat_desc = {}
    for cd in table_data.get('oc_category_description', []):
        cid = cd.get('category_id')
        if cid not in cat_desc:
            cat_desc[cid] = {}
        lid = cd.get('language_id')
        cat_desc[cid][lid] = {
            'name': html.unescape(cd.get('name', '')),
            'description': html.unescape(cd.get('description', '')),
            'meta_title': html.unescape(cd.get('meta_title', ''))
        }
        
    categories = []
    for c in table_data.get('oc_category', []):
        cid = c.get('category_id')
        cd = cat_desc.get(cid, {})
        name_ar = cd.get(2, {}).get('name') or cd.get(1, {}).get('name') or f"قسم {cid}"
        name_en = cd.get(1, {}).get('name') or name_ar
        
        categories.append({
            'id': cid,
            'parent_id': c.get('parent_id', 0),
            'name_ar': name_ar,
            'name_en': name_en,
            'image': c.get('image', ''),
            'icon': f'cat{cid % 7 + 1}.png',
            'top': bool(c.get('top', 0)),
            'sort_order': c.get('sort_order', 0),
            'status': bool(c.get('status', 1)),
            'product_count': max(1, sum(1 for p in products if cid in p['categories']))
        })
    categories.sort(key=lambda x: (not x['top'], x['sort_order']))
    
    with open(os.path.join(output_dir, 'categories.json'), 'w', encoding='utf-8') as f:
        json.dump(categories, f, ensure_ascii=False, indent=2)
    print(f"Exported categories.json ({len(categories)} categories)")

    # 4. Build Banners JSON
    banners_group = {}
    for b in table_data.get('oc_banner', []):
        banners_group[b.get('banner_id')] = {
            'id': b.get('banner_id'),
            'name': html.unescape(b.get('name', '')),
            'status': b.get('status', 1),
            'slides': []
        }
        
    for bi in table_data.get('oc_banner_image', []):
        bid = bi.get('banner_id')
        if bid in banners_group:
            banners_group[bid]['slides'].append({
                'id': bi.get('banner_image_id'),
                'title': html.unescape(bi.get('title', '')),
                'link': bi.get('link', ''),
                'image': bi.get('image', ''),
                'sort_order': bi.get('sort_order', 0)
            })
            
    banners = list(banners_group.values())
    with open(os.path.join(output_dir, 'banners.json'), 'w', encoding='utf-8') as f:
        json.dump(banners, f, ensure_ascii=False, indent=2)
    print(f"Exported banners.json ({len(banners)} banner sets)")

    # 5. Build Coupons JSON
    coupons = []
    for c in table_data.get('oc_coupon', []):
        coupons.append({
            'id': c.get('coupon_id'),
            'name': html.unescape(c.get('name', '')),
            'code': c.get('code', 'TOKI20'),
            'type': 'percentage' if c.get('type') == 'P' else 'fixed',
            'discount': float(c.get('discount', 10)),
            'total_min': float(c.get('total', 100)),
            'status': bool(c.get('status', 1))
        })
    with open(os.path.join(output_dir, 'coupons.json'), 'w', encoding='utf-8') as f:
        json.dump(coupons, f, ensure_ascii=False, indent=2)
    print(f"Exported coupons.json ({len(coupons)} coupons)")

    # 6. Build Shipping Couriers JSON
    couriers = [
        {"id": 1, "code": "smsa", "name_ar": "سمسا إكسبريس (SMSA Express)", "name_en": "SMSA Express", "cost_sar": 25, "delivery_time": "1-2 أيام عمل", "logo": "smsa.png"},
        {"id": 2, "code": "aramex", "name_ar": "أرامكس للشحن السريع (Aramex)", "name_en": "Aramex Logistics", "cost_sar": 28, "delivery_time": "2-3 أيام عمل", "logo": "aramex.png"},
        {"id": 3, "code": "toki_express", "name_ar": "مرسول وتوصيل توكي السريع (Toki Express)", "name_en": "Toki Express Instant Delivery", "cost_sar": 15, "delivery_time": "خلال 3 ساعات (داخل الرياض وجدة)", "logo": "toki_express.png"}
    ]
    with open(os.path.join(output_dir, 'shipping_couriers.json'), 'w', encoding='utf-8') as f:
        json.dump(couriers, f, ensure_ascii=False, indent=2)
    print("Exported shipping_couriers.json")

    # 7. Build Saudi Zones JSON
    saudi_zones = [
        {"zone_id": 2879, "name_ar": "منطقة الرياض", "name_en": "Riyadh Region", "cities": ["الرياض", "الخرج", "الدرعية", "المجمعة"]},
        {"zone_id": 2880, "name_ar": "منطقة مكة المكرمة", "name_en": "Makkah Region", "cities": ["مكة المكرمة", "جدة", "الطائف", "رابغ"]},
        {"zone_id": 2881, "name_ar": "المنطقة الشرقية", "name_en": "Eastern Province", "cities": ["الدمام", "الخبر", "الظهران", "الأحساء", "الجبيل"]},
        {"zone_id": 2882, "name_ar": "منطقة المدينة المنورة", "name_en": "Madinah Region", "cities": ["المدينة المنورة", "ينبع", "العلا"]},
        {"zone_id": 2883, "name_ar": "منطقة عسير", "name_en": "Asir Region", "cities": ["أبها", "خميس مشيط", "النماص"]},
        {"zone_id": 2884, "name_ar": "منطقة القصيم", "name_en": "Qassim Region", "cities": ["بريدة", "عنيزة", "الرس"]}
    ]
    with open(os.path.join(output_dir, 'zones_saudi.json'), 'w', encoding='utf-8') as f:
        json.dump(saudi_zones, f, ensure_ascii=False, indent=2)
    print("Exported zones_saudi.json")

    # 8. Build Store Config & Settings JSON
    store_config = {
        'store_name': 'متجر وسوق توكي - Toki Store & Marketplace',
        'store_slogan': 'تسوق آلاف المنتجات بأسعار استثنائية وشحن سريع لجميع مدن المملكة',
        'store_owner': 'مؤسسة توكي للتجارة الإلكترونية',
        'email': 'support@tokistore.com',
        'telephone': '+966 50 123 4567',
        'vat_number': '310123456700003',
        'cr_number': '1010789456',
        'maroof_id': '182934',
        'default_currency': 'SAR',
        'currencies': [
            {'code': 'SAR', 'title': 'ريال سعودي', 'symbol_right': ' ر.س', 'value': 1.0},
            {'code': 'USD', 'title': 'US Dollar', 'symbol_left': '$', 'value': 0.266},
            {'code': 'AED', 'title': 'درهم إماراتي', 'symbol_right': ' د.إ', 'value': 0.98}
        ],
        'languages': [
            {'code': 'ar', 'name': 'العربية', 'direction': 'rtl', 'active': True},
            {'code': 'en', 'name': 'English', 'direction': 'ltr', 'active': True}
        ],
        'payment_methods': [
            {'code': 'mada', 'name_ar': 'مدى (Mada)', 'name_en': 'Mada Debit Card', 'icon': 'mada.png', 'active': True},
            {'code': 'apple_pay', 'name_ar': 'أبل باي (Apple Pay)', 'name_en': 'Apple Pay', 'icon': 'applepay.png', 'active': True},
            {'code': 'visa_master', 'name_ar': 'بطاقات فيزا وماستركارد', 'name_en': 'Visa & MasterCard', 'icon': 'visa.png', 'active': True},
            {'code': 'tabby', 'name_ar': 'تابي (قسمها على 4 دفعات بدون فوائد)', 'name_en': 'Tabby (Split in 4)', 'icon': 'tabby.png', 'active': True},
            {'code': 'cod', 'name_ar': 'الدفع عند الاستلام', 'name_en': 'Cash On Delivery', 'icon': 'cod.png', 'active': True}
        ]
    }
    with open(os.path.join(output_dir, 'settings.json'), 'w', encoding='utf-8') as f:
        json.dump(store_config, f, ensure_ascii=False, indent=2)
    print("Exported settings.json")

    # 9. Build Orders JSON
    order_prods = {}
    for op in table_data.get('oc_order_product', []):
        oid = op.get('order_id')
        if oid not in order_prods:
            order_prods[oid] = []
        order_prods[oid].append({
            'product_id': op.get('product_id'),
            'name': html.unescape(op.get('name', '')),
            'model': op.get('model', ''),
            'quantity': op.get('quantity', 1),
            'price_sar': float(op.get('price', 0)),
            'total_sar': float(op.get('total', 0))
        })
        
    orders = []
    for o in table_data.get('oc_order', []):
        oid = o.get('order_id')
        orders.append({
            'id': oid,
            'invoice_no': o.get('invoice_no', f'TOK-INV-2024-00{oid}'),
            'customer_id': o.get('customer_id', 0),
            'customer_name': f"{o.get('firstname', '')} {o.get('lastname', '')}".strip() or f'العميل رقم {oid}',
            'email': o.get('email', f'customer{oid}@tokistore.com'),
            'telephone': o.get('telephone', '+966500000000'),
            'shipping_city': o.get('shipping_city', 'الرياض'),
            'shipping_country': o.get('shipping_country', 'المملكة العربية السعودية'),
            'payment_method': o.get('payment_method', 'مدى / Apple Pay'),
            'shipping_method': o.get('shipping_method', 'سمسا اكسبريس'),
            'total_sar': float(o.get('total', 0)) or (150.0 * oid),
            'currency': o.get('currency_code', 'SAR'),
            'order_status_id': o.get('order_status_id', 1),
            'order_status': 'تم التوصيل بنجاح' if o.get('order_status_id') in [5, 3] else 'جاري التجهيز والشحن',
            'items': order_prods.get(oid, [{"product_id": 1, "name": "هاتف توكي سمارت 5G", "quantity": 1, "price_sar": 150.0, "total_sar": 150.0}]),
            'date_added': str(o.get('date_added', '2024-02-10 14:22:10'))
        })
    with open(os.path.join(output_dir, 'orders.json'), 'w', encoding='utf-8') as f:
        json.dump(orders, f, ensure_ascii=False, indent=2)
    print(f"Exported orders.json ({len(orders)} orders)")

    # 10. Build Vendors JSON
    vendors = [
        {
            'id': 1,
            'name': 'متجر النخبة للإلكترونيات الذكية',
            'name_en': 'Elite Smart Electronics Store',
            'rating': 4.9,
            'sales_count': 1420,
            'city': 'الرياض',
            'verified': True,
            'badge': 'تاجر موثق ذهبي 🥇',
            'banner': 'catalog/demo/banners/bann1.png',
            'products_count': 4
        },
        {
            'id': 2,
            'name': 'عالم الموضة والأزياء الخليجية',
            'name_en': 'Gulf Fashion & Fragrances World',
            'rating': 4.8,
            'sales_count': 850,
            'city': 'جدة',
            'verified': True,
            'badge': 'تاجر بلاتيني 💎',
            'banner': 'catalog/demo/banners/bann2.png',
            'products_count': 2
        },
        {
            'id': 3,
            'name': 'استوديو توكي للطباعة والتخصيص الفوري',
            'name_en': 'Toki Print & Custom Design Studio',
            'rating': 5.0,
            'sales_count': 3200,
            'city': 'الدمام',
            'verified': True,
            'badge': 'المتجر الرسمي لتوكي 👑',
            'banner': 'catalog/demo/banners/slider.png',
            'products_count': 2
        }
    ]
    with open(os.path.join(output_dir, 'vendors.json'), 'w', encoding='utf-8') as f:
        json.dump(vendors, f, ensure_ascii=False, indent=2)
    print("Exported vendors.json")

    print("\n All SQL tables and structured dummy JSON datasets exported successfully!")

if __name__ == '__main__':
    sql_path = 'tokistore (3).sql'
    tables = parse_complete_sql(sql_path)
    export_organized_dummy_data(tables, 'dummy_data')
