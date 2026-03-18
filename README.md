# Farhan Ale - Personal Branding Website

Website personal branding untuk portofolio profesional Farhan Ale, seorang Network & Automation Engineer yang bergerak di bidang jaringan, MikroTik, dan automation.

## 🚀 Fitur

### Frontend
- **Homepage**: Hero section dengan animasi, statistik, portfolio showcase, dan skills preview
- **About Page**: Bio pribadi, experience timeline, dan skills lengkap
- **Portfolio**: Galeri proyek dengan filter berdasarkan kategori
- **Blog**: Artikel dengan pagination, kategori, dan fitur pencarian
- **Contact Form**: Form kontak dengan validasi dan notifikasi
- **Responsive Design**: Mobile-friendly dengan breakpoint sm, md, lg, xl
- **Modern UI**: Desain elegan dengan tema Ungu & Pink, glassmorphism effects, dan smooth animations

### Admin Panel
- **Dashboard**: Statistik overview dan recent activities
- **Portfolio Management**: CRUD operations untuk portfolio projects
- **Blog Management**: CRUD operations untuk blog posts
- **Certificate Management**: CRUD operations untuk sertifikat
- **Skill Management**: CRUD operations untuk skills dengan progress bar
- **Settings**: Konfigurasi situs (nama, deskripsi, kontak, social media)
- **Authentication**: Secure login system dengan password hashing

### Teknologi
- **Backend**: PHP 8+ dengan PDO (MySQL)
- **Frontend**: Bootstrap 5.3, HTML5, CSS3, JavaScript
- **Libraries**:
  - Font Awesome 6 (Icons)
  - AOS (Animate On Scroll)
  - SweetAlert2 (Alert Notifications)
  - jQuery
- **Database**: MySQL dengan 7 tabel

## 📦 Struktur Direktori

```
farhanale.my.id/
├── admin/                  # Admin Panel
│   ├── index.php          # Dashboard
│   ├── login.php          # Login page
│   ├── logout.php         # Logout
│   ├── portfolio.php      # Manage portfolio
│   ├── blogs.php          # Manage blogs
│   ├── certificates.php   # Manage certificates
│   ├── skills.php         # Manage skills
│   └── settings.php       # Site settings
├── assets/                # Static assets
│   ├── css/
│   │   ├── style.css      # Frontend styles
│   │   └── admin.css      # Admin panel styles
│   ├── js/
│   │   ├── main.js        # Frontend JavaScript
│   │   └── admin.js       # Admin panel JavaScript
│   └── img/
│       ├── blog/          # Blog images
│       ├── certificates/   # Certificate images
│       └── projects/      # Portfolio images
├── includes/              # Core PHP files
│   ├── config.php         # Database configuration
│   ├── functions.php      # Helper functions
│   ├── header.php         # Header component
│   └── footer.php         # Footer component
├── index.php              # Homepage
├── about.php              # About page
├── portfolio.php          # Portfolio gallery
├── blog.php               # Blog listing
├── blog-detail.php        # Single blog post
├── contact.php            # Contact form
├── database.sql           # Database schema & dummy data
└── README.md              # This file
```

## 🛠️ Installation

### Prerequisites
- PHP 7.4 atau lebih tinggi
- MySQL 5.7 atau lebih tinggi
- XAMPP, WAMP, atau server environment lainnya

### Langkah-langkah

1. **Clone atau download project**:
   ```bash
   cd C:\xampp\htdocs\farhanale.my.id
   ```

2. **Import database**:
   - Buka phpMyAdmin: http://localhost/phpmyadmin
   - Buat database
   - Import file `database.sql`

   Atau via command line:
   ```bash
   mysql -u root -p database_name < database.sql
   ```

3. **Konfigurasi database** (opsional):
   Edit file `includes/config.php` jika credentials MySQL Anda berbeda:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   define('DB_NAME', 'database_name');
   ```

4. **Start server**:
   - Start Apache dan MySQL di XAMPP Control Panel
   - Buka browser dan akses: `http://localhost/farhanale.my.id`

5. **Login ke Admin Panel**:
   - URL: `http://localhost/farhanale.my.id/admin/`

   ⚠️ **PENTING**: Ganti password default setelah login pertama!

## 📝 Usage

### Mengelola Konten via Admin Panel

1. **Portfolio**:
   - Navigasi ke: Admin > Portfolio
   - Klik "Add New Portfolio"
   - Upload gambar dan isi form
   - Simpan untuk publish

2. **Blog**:
   - Navigasi ke: Admin > Blogs
   - Klik "Add New Blog"
   - Tulis konten, pilih kategori, upload gambar
   - Slug akan otomatis digenerate dari title

3. **Certificates**:
   - Navigasi ke: Admin > Certificates
   - Upload sertifikat dan isi detail

4. **Skills**:
   - Navigasi ke: Admin > Skills
   - Tambah skill dengan nama, kategori, percentage, dan icon

5. **Settings**:
   - Navigasi ke: Admin > Settings
   - Update site name, description, contact info, dan social media links

## 🎨 Customization

### Mengubah Warna Tema

Edit file `assets/css/style.css` dan ubah CSS variables:
```css
:root {
    --primary: #8B5CF6;    /* Primary color - Ungu */
    --secondary: #EC4899;  /* Secondary color - Pink */
    --accent: #F59E0B;     /* Accent color - Amber */
}
```

### Mengubah Konten Hero Section

Edit file `index.php` di bagian `hero-content`:
```php
<h1 class="hero-title">Hi, I'm <span class="gradient-text">Farhan Ale</span></h1>
<p class="hero-subtitle">Network & Automation Engineer</p>
<p class="hero-description">...</p>
```

### Menambah Skills Baru

Bisa via Admin Panel atau langsung di database:
```sql
INSERT INTO skills (name, category, percentage, icon) 
VALUES ('New Skill', 'Category', 80, 'fa-icon');
```

## 🔒 Security Features

- **SQL Injection Protection**: Prepared statements untuk semua database queries
- **XSS Protection**: Output escaping dan input sanitization
- **CSRF Protection**: Token validation untuk form submissions
- **Password Security**: Bcrypt hashing untuk password admin
- **Session Management**: Secure session configuration
- **File Upload Validation**: Validasi tipe file dan ukuran (max 2MB)

## 🌐 Deployment untuk Production

Sebelum deploy ke production:

1. **Ganti Password Admin**:
   - Login ke admin panel
   - Akses database via phpMyAdmin
   - Update password dengan hash baru:
   ```php
   $password = password_hash('new_password', PASSWORD_DEFAULT);
   ```

2. **Konfigurasi Environment**:
   - Update `DB_HOST`, `DB_USER`, `DB_PASS` di `includes/config.php`
   - Set `display_errors = Off` di `php.ini`

3. **HTTPS Setup**:
   - Install SSL certificate
   - Update semua HTTP links ke HTTPS

4. **Backup Database**:
   ```bash
   mysqldump -u username -p database_name > backup.sql
   ```

## 📱 Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Mobile browsers (iOS Safari, Chrome Mobile)

## 🤝 Support & Contributions

Jika Anda menemukan bug atau ingin mengirimkan pull request, silakan hubungi:
- Email: kontak@farhanale.my.id
- Website: https://farhanale.my.id

## 📄 License

Website ini dibuat untuk penggunaan personal Farhan Ale. Tidak untuk distribusi komersial tanpa izin.

## 🙏 Credits

- **Bootstrap Framework**: https://getbootstrap.com
- **Font Awesome**: https://fontawesome.com
- **AOS Animation**: https://michalsnik.github.io/aos/
- **SweetAlert2**: https://sweetalert2.github.io/
- **Google Fonts**: Poppins font family

---

**Developed with ❤️ by Farhan Ale**

*Network & Automation Engineer | MikroTik Specialist | Automation Expert*
