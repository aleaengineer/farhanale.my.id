# MikroTik Blog

Blog khusus untuk tutorial dan panduan konfigurasi MikroTik oleh Farhan Ale.

## 📋 Fitur

### Public Blog
- **Homepage** dengan featured article dan artikel terbaru
- **Detail artikel** dengan layout yang menarik
- **Categories** untuk browsing berdasarkan kategori
- **Tags** untuk browsing berdasarkan tags
- **Search** dengan real-time filtering
- **Pagination** untuk list artikel
- **Social sharing** (Facebook, Twitter, LinkedIn, WhatsApp)
- **Responsive design** untuk mobile dan desktop
- **SEO optimized** dengan sitemap dan meta tags

### Admin Panel
- **Dashboard** dengan statistik lengkap
- **CRUD articles** dengan rich text editor
- **Categories management** dengan icon dan color picker
- **Tags management**
- **Settings** untuk konfigurasi site
- **Analytics** tracking untuk views
- **Image upload** dengan drag & drop
- **SEO fields** untuk setiap artikel

## 🗂️ Struktur Folder

```
mikrotik/
├── admin/                      # Admin panel
│   ├── index.php              # Dashboard
│   ├── login.php              # Login page
│   ├── logout.php             # Logout handler
│   ├── articles.php           # CRUD articles
│   ├── categories.php         # Manajemen kategori
│   ├── tags.php               # Manajemen tags
│   ├── settings.php           # Settings
│   └── includes/
│       ├── header.php         # Admin header
│       └── footer.php         # Admin footer
├── includes/                   # Blog includes
│   ├── config.php             # Database config
│   ├── functions.php          # Helper functions
│   ├── header.php             # Blog header
│   └── footer.php             # Blog footer
├── assets/
│   ├── css/
│   │   ├── admin.css          # Admin styling
│   │   └── style.css          # Blog styling
│   ├── js/
│   │   ├── admin.js           # Admin functionality
│   │   └── main.js            # Blog interactions
│   └── img/
│       ├── articles/          # Gambar artikel
│       └── logo/              # Logo blog
├── index.php                  # Homepage
├── article.php                # Detail artikel
├── categories.php             # Halaman kategori
├── tags.php                   # Halaman tags
├── search.php                 # Search page
├── sitemap.php                # Sitemap XML
├── robots.txt                 # Robots configuration
└── database.sql                # Database schema
```

## 💾 Database

Database yang digunakan: `mikrotik_db`

### Tabel Utama
- **users** - Admin users
- **categories** - Kategori artikel
- **tags** - Tags artikel
- **articles** - Artikel blog
- **article_tags** - Relasi artikel-tags
- **settings** - Konfigurasi site
- **analytics** - Tracking views

## 🚀 Instalasi

### 1. Import Database
```bash
mysql -u root -p < database.sql
```

### 2. Konfigurasi
Edit file `includes/config.php`:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'mikrotik_db');
```

### 3. Update URL
Edit file `includes/config.php`:
```php
define('SITE_URL', 'https://mikrotik.farhanale.my.id');
```

### 4. Login Admin
Buka `https://mikrotik.farhanale.my.id/admin/`

**Default Credentials:**
- Username: `admin`
- Password: `admin123`

## 🎨 Kategori Default

1. Hotspot Management
2. Routing & BGP
3. Firewall & Security
4. Wireless Networking
5. VPN Configuration
6. Bandwidth Management
7. Load Balancing
8. Scripting & Automation
9. User Manager
10. Troubleshooting & Tips

## 🔧 Teknologi

**Frontend:**
- Bootstrap 5.3.2
- Font Awesome 6.5.1
- Google Fonts (Poppins)
- AOS Library (Animations)
- jQuery 3.7.1
- SweetAlert2 (Alerts)

**Backend:**
- PHP 8.0+
- PDO MySQL
- bcrypt (Password hashing)
- Session management

**SEO:**
- Schema.org markup
- Open Graph tags
- Twitter Card tags
- Sitemap generation
- Robots.txt
- Meta tags optimization

## 📝 Cara Menambah Artikel

1. Login ke admin panel
2. Masuk ke menu **Artikel**
3. Klik **Tambah Artikel Baru**
4. Isi form:
   - Title (wajib)
   - Excerpt (opsional)
   - Content (wajib) - Gunakan HTML untuk formatting
   - Category (opsional)
   - Tags (opsional, bisa multiple)
   - Status: Draft atau Published
   - Featured (opsional) - Tampilkan di homepage
   - Featured Image (opsional)
   - SEO Settings (opsional)
5. Klik **Simpan Artikel**

## 🔐 Security

- Password hashing dengan bcrypt
- Prepared statements untuk SQL injection prevention
- XSS protection dengan sanitization
- CSRF token untuk form submissions
- Session security

## 📊 Analytics

Views tracking otomatis disimpan di tabel `analytics`. Data ini digunakan untuk:
- Popular articles ranking
- Dashboard statistics
- Performance monitoring

## 🎯 SEO

Setiap artikel memiliki SEO fields:
- Meta Title
- Meta Description
- Meta Keywords

URL yang SEO-friendly:
- Homepage: `/`
- Artikel: `/article.php?slug=article-slug`
- Kategori: `/categories.php?slug=category-slug`
- Tags: `/tags.php?slug=tag-slug`

## 📱 Responsive

Blog ini fully responsive dan optimal untuk:
- Desktop (1920px+)
- Laptop (1366px - 1919px)
- Tablet (768px - 1365px)
- Mobile (< 768px)

## 🌐 Social Sharing

Tombol share tersedia di setiap artikel:
- Facebook
- Twitter
- LinkedIn
- WhatsApp
- Copy Link

## 🔄 Update Sitemap

Sitemap otomatis di-generate dari database. Tidak perlu update manual.

## 📞 Kontak

Untuk support atau pertanyaan, hubungi:
- Email: kontak@farhanale.my.id
- WhatsApp: +6281234567890

## 📄 License

Copyright © <?php echo date('Y'); ?> Farhan Ale. All rights reserved.

---

Made with ❤️ by Farhan Ale