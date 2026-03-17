# Upload ke GitHub - Instruksi

## Langkah 1: Buat Repository Baru di GitHub

1. Buka browser dan kunjungi: https://github.com/new
2. Login ke akun GitHub Anda
3. Isi form dengan:
   - **Repository name**: `farhanale-website` (atau nama lain yang Anda inginkan)
   - **Description**: Personal Branding Website untuk Farhan Ale - Network & Automation Engineer
   - **Public/Private**: Pilih Public (gratis) atau Private
4. Klik tombol **"Create repository"**
5. **JANGAN** centang "Initialize this repository with a README"
6. Copy URL repository yang muncul (format: `https://github.com/username/farhanale-website.git`)

## Langkah 2: Hubungkan Git dengan GitHub

Setelah repository dibuat, jalankan perintah berikut di terminal (di C:\xampp\htdocs\farhanale.my.id):

```bash
git remote add origin [URL_REPOSITORY_YANG_ANDA_COPY]
```

Contoh:
```bash
git remote add origin https://github.com/farhanale/farhanale-website.git
```

## Langkah 3: Push ke GitHub

Jalankan perintah berikut untuk upload semua file:

```bash
git push -u origin master
```

Jika menggunakan GitHub baru yang default branch-nya `main`, gunakan:
```bash
git push -u origin main
```

## Langkah 4: Masukkan Username dan Password (atau Personal Access Token)

GitHub akan meminta:
- **Username**: Masukkan username GitHub Anda
- **Password**: 
  - Jika menggunakan 2FA, Anda perlu **Personal Access Token (PAT)**
  - Buat PAT di: https://github.com/settings/tokens
  - Pilih "Generate new token (classic)"
  - Beri nama token
  - Centang permissions: `repo` (full control)
  - Generate dan copy token
  - Gunakan token sebagai password

## Langkah 5: Verifikasi

Setelah berhasil, cek:
1. Buka URL repository GitHub Anda
2. Pastikan semua file sudah muncul di sana
3. File yang ada:
   - index.php
   - about.php
   - portfolio.php
   - blog.php
   - contact.php
   - blog-detail.php
   - includes/
   - admin/
   - assets/
   - database.sql
   - README.md
   - .htaccess

## Catatan Penting

1. **Personal Access Token (PAT)**:
   - GitHub sudah tidak mendukung password login untuk git
   - Harus menggunakan Personal Access Token
   - Token hanya muncul sekali saat generate, simpan baik-baik

2. **Branch Name**:
   - GitHub default sekarang: `main`
   - Git default: `master`
   - Jika error, coba ubah branch master ke main:
   ```bash
   git branch -M main
   git push -u origin main
   ```

3. **Konfigurasi Credential Helper** (Opsional):
   Agar tidak perlu login ulang setiap kali:
   ```bash
   git config --global credential.helper store
   ```
   Peringatan: Password/token akan tersimpan dalam plain text di `.git-credentials`

## Troubleshooting

### Error: "Support for password authentication was removed"
- Solusi: Gunakan Personal Access Token (PAT) sebagai pengganti password

### Error: "fatal: remote origin already exists"
- Solusi: Update remote origin:
  ```bash
  git remote set-url origin [URL_BARU]
  ```

### Error: "fatal: 'master' does not appear to be a git repository"
- Solusi: Pastikan berada di folder project yang benar

### Error: "Connection refused" atau "Could not resolve host"
- Solusi: Cek koneksi internet dan GitHub sedang down

## Selesai!

Setelah berhasil push, website Anda sudah tersimpan di GitHub dan:
- Bisa diakses kapan saja
- Bisa di-share ke orang lain
- Ada backup di cloud
- Buka kolaborasi dengan developer lain
- Deploy ke hosting lain dengan mudah

## Next Steps (Opsional)

Setelah di GitHub, Anda bisa:
1. **Deploy ke GitHub Pages** (gratis untuk static site)
2. **Deploy ke Netlify/Vercel** (gratis)
3. **Deploy ke hosting lain** (shared hosting, VPS)
4. **Setup GitHub Actions** untuk CI/CD
5. **Invite collaborators** untuk kerja tim
6. **Set up GitHub Issues** untuk bug tracking
7. **GitHub Wiki** untuk dokumentasi

---
**Created with ❤️ by opencode**
