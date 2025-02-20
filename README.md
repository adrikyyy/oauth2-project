# Sistem Login Aman dengan OAuth2

## Ikhtisar Proyek
Proyek ini merupakan implementasi sistem login berbasis OAuth2 yang mengutamakan keamanan, performa, dan kemudahan penggunaan. Sistem ini mengintegrasikan alur autentikasi standar OAuth2 (dengan authorization code flow) dengan manajemen pengguna menggunakan MySQL, enkripsi RSA untuk keamanan token, serta tampilan form login berbasis popup yang responsif dan intuitif. Dokumentasi ini menjelaskan secara mendetail arsitektur sistem, langkah instalasi, konfigurasi, dan mekanisme kerja dari setiap komponen.

## Fitur Utama
### Server OAuth2 & Alur Autentikasi Standar
- Server dapat dijalankan dengan baik.
- Implementasi flow autentikasi OAuth2 sesuai standar, mulai dari generate authorization code hingga pertukaran token.
- Refresh token dan access token berfungsi dengan baik.
- Penggunaan RSA untuk enkripsi token berjalan dengan benar.
- Keamanan data pengguna dijaga melalui enkripsi, penggunaan hashed password, dan pengaturan session yang aman.

### Implementasi Popup Form untuk Login
- Popup login muncul sesuai desain yang diharapkan.
- Komunikasi antara client dan server menggunakan Fetch API berjalan lancar.
- Tampilan UI/UX popup form intuitif dan responsif.
- Validasi input pada form login dilakukan dengan benar (misalnya, penggunaan CSRF token).

### Penggunaan MySQL untuk Manajemen Pengguna
- Struktur tabel disesuaikan dengan kebutuhan OAuth2.
- Operasi CRUD (Create, Read, Update, Delete) pada pengguna dan token dilakukan dengan benar dan menggunakan prepared statements untuk mencegah SQL injection.
- Keamanan database diperhatikan dengan penggunaan hashed password dan query yang aman.
- Penggunaan indexing dan optimasi query untuk meningkatkan performa.

### Integrasi dan Fungsionalitas Sistem
- Flow login dan logout berjalan tanpa error.
- Aplikasi dapat mengelola session pengguna dengan baik.
- Token-based authentication diterapkan di seluruh sistem.
- API endpoint terstruktur dan terdokumentasi dengan baik.

### Dokumentasi dan Presentasi
- Dokumentasi kode dan arsitektur sistem jelas dan mudah dipahami.
- Presentasi menjelaskan konsep, implementasi, dan tantangan proyek secara menyeluruh.
- Tim mampu menjawab pertanyaan terkait proyek dengan baik.

## Struktur Folder Proyek
```
oauth2-project/
├── public/
│   ├── assets/
│   │   ├── css/
│   │   │   └── styles.css         # Styling UI/UX
│   │   └── js/
│   │       └── popup.js           # Skrip untuk popup login
│   ├── index.php                  # Halaman utama (menampilkan tombol login/logout)
│   ├── login.php                  # Form login (popup)
│   ├── callback.php               # Endpoint callback OAuth2 (menangani authorization code)
│   └── logout.php                 # Handler logout
├── server/
│   ├── config.php                 # Konfigurasi (database, environment, session, RSA)
│   ├── auth.php                   # Handler autentikasi OAuth2 (generate dan refresh token)
│   ├── rsa.php                    # Pengelolaan enkripsi RSA
│   └── session.php                # Manajemen session pengguna
├── database/
│   └── init.sql                   # Skrip SQL untuk membuat dan menginisialisasi tabel
├── .env                           # File konfigurasi environment (tidak di-commit ke Git)
├── .gitignore                     # Mengabaikan folder vendor, .env, server/keys, dll.
└── composer.json                  # Konfigurasi dependensi PHP dan autoload
```

## Instalasi dan Konfigurasi
### Persyaratan
- PHP versi 7.4 atau lebih tinggi
- MySQL/MariaDB
- Composer
- XAMPP (atau LAMP/WAMP stack) atau PHP built-in server

### Langkah-Langkah Instalasi
1. **Clone Proyek**
   ```sh
   git clone https://github.com/adrikyyy/oauth2-project.git
   cd oauth2-project
   ```

2. **Instal Dependensi**
   ```sh
   composer install
   ```

3. **Konfigurasi File Environment**
   Buat file `.env` di root proyek dan isikan konfigurasi seperti berikut:
   ```ini
   APP_ENV=development
   APP_DEBUG=true
   APP_URL=http://localhost:8000

   DB_HOST=localhost
   DB_NAME=oauth2_system
   DB_USER=root
   DB_PASSWORD=

   OAUTH_CLIENT_ID=your_client_id_here
   OAUTH_CLIENT_SECRET=your_secret_here
   OAUTH_REDIRECT_URI=http://localhost:8000/callback.php

   SECURE_COOKIE=true
   SESSION_LIFETIME=7200
   
   JWT_SECRET=your_jwt_secret_key_here
   ENCRYPTION_KEY=your_encryption_key_here
   ```

4. **Generate RSA Keys**
   ```sh
   mkdir -p server/keys
   openssl genrsa -out server/keys/private.pem 2048
   openssl rsa -in server/keys/private.pem -pubout -out server/keys/public.pem
   ```

5. **Setup Database**
   ```sh
   mysql -u root -p < database/init.sql
   ```

6. **Insert Data Awal**
   ```sql
   INSERT INTO users (username, email, password_hash)
   VALUES ('admin', 'admin@example.com', 'HASH_BARU_DARI_GENERATE_HASH');
   ```

## Alur OAuth2 dan Proses Login
1. Pengguna menekan tombol “Login” di `index.php`.
2. Popup login muncul (`login.php`).
3. Form dikirim ke server untuk validasi.
4. Jika sukses, authorization code dihasilkan dan dikirim ke `callback.php`.
5. `callback.php` menukar authorization code dengan access token.
6. Token digunakan untuk autentikasi API.
7. Logout dapat dilakukan melalui `logout.php`.

## Keamanan dan Praktik Terbaik
- **CSRF Protection** dengan token.
- **Password Hashing** menggunakan bcrypt.
- **Prepared Statements** untuk database query.
- **RSA Encryption** untuk token.
- **Session Management** dengan konfigurasi aman.

## Menjalankan Aplikasi
- **PHP Built-in Server:**
  ```sh
  php -S localhost:8000 -t public
  ```
- **XAMPP:** Konfigurasi Virtual Host.

## Troubleshooting
- **Invalid Credentials:** Periksa username/password.
- **Database Errors:** Periksa query dan struktur tabel.
- **Asset Loading Issues:** Pastikan document root benar.

## Kesimpulan
Sistem ini memenuhi standar keamanan dan performa OAuth2 dengan enkripsi RSA, validasi pengguna, serta responsif.

