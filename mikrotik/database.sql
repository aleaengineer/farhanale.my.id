-- Database: mikrotik_db

-- Create database
CREATE DATABASE IF NOT EXISTS mikrotik_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE mikrotik_db;

-- Users table (admin)
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    full_name VARCHAR(100),
    role ENUM('admin', 'editor') DEFAULT 'admin',
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Categories table
CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) UNIQUE NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    icon VARCHAR(50),
    color VARCHAR(20),
    article_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tags table
CREATE TABLE tags (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) UNIQUE NOT NULL,
    slug VARCHAR(50) UNIQUE NOT NULL,
    article_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Articles table
CREATE TABLE articles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    slug VARCHAR(200) UNIQUE NOT NULL,
    content LONGTEXT NOT NULL,
    excerpt TEXT,
    featured_image VARCHAR(255),
    category_id INT,
    author_id INT,
    status ENUM('published', 'draft', 'archived') DEFAULT 'draft',
    is_featured BOOLEAN DEFAULT FALSE,
    views INT DEFAULT 0,
    read_time INT DEFAULT 0,
    meta_title VARCHAR(200),
    meta_description VARCHAR(300),
    meta_keywords VARCHAR(300),
    published_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_status (status),
    INDEX idx_category (category_id),
    INDEX idx_published (published_at)
);

-- Article tags junction table
CREATE TABLE article_tags (
    id INT PRIMARY KEY AUTO_INCREMENT,
    article_id INT NOT NULL,
    tag_id INT NOT NULL,
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE,
    UNIQUE KEY unique_article_tag (article_id, tag_id)
);

-- Settings table
CREATE TABLE settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(50) UNIQUE NOT NULL,
    setting_value TEXT
);

-- Analytics table (article views)
CREATE TABLE analytics (
    id INT PRIMARY KEY AUTO_INCREMENT,
    article_id INT NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    viewed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE,
    INDEX idx_article_date (article_id, viewed_at)
);

-- Insert default admin user (password: admin123)
INSERT INTO users (username, password, email, full_name, role) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@farhanale.my.id', 'Farhan Ale', 'admin');

-- Insert 10 default categories
INSERT INTO categories (name, slug, description, icon, color) VALUES
('Hotspot Management', 'hotspot-management', 'User Manager, voucher system, radius authentication', 'fa-wifi', '#2563EB'),
('Routing & BGP', 'routing-bgp', 'Static routing, OSPF, BGP, policy routing', 'fa-route', '#10B981'),
('Firewall & Security', 'firewall-security', 'NAT, filter rules, IPsec, attack prevention', 'fa-shield-halved', '#EF4444'),
('Wireless Networking', 'wireless-networking', 'CAPsMAN, wireless configuration, security', 'fa-broadcast-tower', '#8B5CF6'),
('VPN Configuration', 'vpn-configuration', 'L2TP/IPsec, SSTP, OpenVPN, WireGuard', 'fa-lock', '#F59E0B'),
('Bandwidth Management', 'bandwidth-management', 'Queues, PCQ, traffic shaping, limiters', 'fa-tachometer-alt', '#EC4899'),
('Load Balancing', 'load-balancing', 'PCC, NTH, ECMP, failover', 'fa-balance-scale', '#06B6D4'),
('Scripting & Automation', 'scripting-automation', 'Scripting, scheduler, Netwatch, API', 'fa-code', '#6366F1'),
('User Manager', 'user-manager', 'User profiles, billing, authentication', 'fa-users', '#8B5CF6'),
('Troubleshooting & Tips', 'troubleshooting-tips', 'Common issues, best practices, optimization', 'fa-wrench', '#6B7280');

-- Insert default tags
INSERT INTO tags (name, slug) VALUES
('beginner', 'beginner'),
('advanced', 'advanced'),
('configuration', 'configuration'),
('security', 'security'),
('automation', 'automation'),
('routing', 'routing'),
('wireless', 'wireless'),
('vpn', 'vpn'),
('hotspot', 'hotspot'),
('troubleshooting', 'troubleshooting');

-- Insert sample articles
INSERT INTO articles (title, slug, content, excerpt, category_id, author_id, status, is_featured, published_at) VALUES
('Konfigurasi Dasar MikroTik untuk Pemula', 'konfigurasi-dasar-mikrotik-untuk-pemula', 
'<h2>Pendahuluan</h2>
<p>MikroTik adalah salah satu router yang paling populer digunakan di dunia jaringan. Artikel ini akan membahas konfigurasi dasar MikroTik mulai dari setup IP address, NAT, sampai firewall basic.</p>

<h2>1. Login ke MikroTik</h2>
<p>Anda bisa login ke MikroTik menggunakan:</p>
<ul>
<li><strong>Winbox</strong> - Aplikasi GUI yang bisa didownload dari situs resmi MikroTik</li>
<li><strong>WebFig</strong> - Akses melalui browser pada alamat IP router</li>
<li><strong>SSH/CLI</strong> - Akses menggunakan terminal</li>
</ul>

<h2>2. Mengatur IP Address</h2>
<p>IP address adalah pengenal unik untuk setiap interface pada router. Berikut cara mengaturnya:</p>

<pre><code>/ip address add address=192.168.1.1/24 interface=ether1</code></pre>

<h2>3. Konfigurasi Default Gateway</h2>
<p>Default gateway adalah pintu keluar menuju internet:</p>

<pre><code>/ip route add gateway=192.168.1.254</code></pre>

<h2>4. Setting NAT</h2>
<p>NAT (Network Address Translation) diperlukan untuk memungkinkan jaringan lokal mengakses internet:</p>

<pre><code>/ip firewall nat add chain=srcnat src-address=192.168.1.0/24 action=masquerade</code></pre>

<h2>5. Basic Firewall</h2>
<p>Firewall dasar untuk keamanan jaringan:</p>

<pre><code>/ip firewall filter add chain=input connection-state=established,related action=accept
/ip firewall filter add chain=input protocol=icmp action=accept
/ip firewall filter add chain=input action=drop</code></pre>

<h2>6. Testing Koneksi</h2>
<p>Setelah semua konfigurasi selesai, lakukan testing:</p>
<ul>
<li>Ping ke gateway</li>
<li>Ping ke Google DNS (8.8.8.8)</li>
<li>Buka browser dan coba akses website</li>
</ul>

<h2>Kesimpulan</h2>
<p>Dengan langkah-langkah di atas, router MikroTik Anda sudah siap digunakan untuk jaringan dasar. Selamat mencoba!</p>', 
'Konfigurasi dasar MikroTik mulai dari setup IP address, NAT, dan firewall basic dengan Winbox dan CLI.', 
4, 1, 'published', TRUE, NOW()),

('Load Balancing PCC: Tutorial Lengkap', 'load-balancing-pcc-tutorial-lengkap',
'<h2>Apa itu Load Balancing?</h2>
<p>Load balancing adalah teknik mendistribusikan traffic jaringan ke beberapa jalur koneksi internet yang berbeda. PCC (Per Connection Classifier) adalah method paling populer untuk load balancing di MikroTik.</p>

<h2>Kelebihan Load Balancing PCC</h2>
<ul>
<li>Mendistribusikan traffic secara merata</li>
<li>Failover otomatis jika satu koneksi mati</li>
<li>Tidak memerlukan konfigurasi yang rumit di client</li>
<li>Stabil dan teruji</li>
</ul>

<h2>Prasyarat</h2>
<p>Sebelum memulai, pastikan Anda memiliki:</p>
<ul>
<li>2 atau lebih koneksi internet</li>
<li>Router MikroTik dengan cukup port</li>
<li>IP address dari masing-masing ISP</li>
</ul>

<h2>Langkah-Langkah Konfigurasi</h2>

<h3>1. Setup IP Address untuk Masing-Masing ISP</h3>
<pre><code># ISP 1
/ip address add address=192.168.10.2/24 interface=ether1

# ISP 2
/ip address add address=192.168.20.2/24 interface=ether2

# Gateway
/ip route add gateway=192.168.10.1 routing-mark=to_isp1
/ip route add gateway=192.168.20.1 routing-mark=to_isp2</code></pre>

<h3>2. Marking Connections</h3>
<pre><code>/ip firewall mangle add chain=prerouting src-address=192.168.1.0/24 \
    connection-state=new nth=2,1,0 action=mark-connection \
    new-connection-mark=conn_isp1 passthrough=yes

/ip firewall mangle add chain=prerouting src-address=192.168.1.0/24 \
    connection-state=new nth=2,1,1 action=mark-connection \
    new-connection-mark=conn_isp2 passthrough=yes</code></pre>

<h3>3. Marking Routing</h3>
<pre><code>/ip firewall mangle add chain=prerouting connection-mark=conn_isp1 \
    action=mark-routing new-routing-mark=to_isp1 passthrough=no

/ip firewall mangle add chain=prerouting connection-mark=conn_isp2 \
    action=mark-routing new-routing-mark=to_isp2 passthrough=no</code></pre>

<h3>4. NAT Configuration</h3>
<pre><code>/ip firewall nat add chain=srcnat out-interface=ether1 \
    action=masquerade

/ip firewall nat add chain=srcnat out-interface=ether2 \
    action=masquerade</code></pre>

<h2>Testing Failover</h2>
<p>Untuk menguji failover, cabut salah satu kabel ISP dan lihat apakah traffic otomatis berpindah ke ISP lain.</p>

<h2>Kesimpulan</h2>
<p>Load balancing dengan method PCC adalah solusi efektif untuk mengoptimalkan penggunaan multiple ISP. Pastikan selalu monitor performa jaringan Anda.</p>', 
'Load balancing di MikroTik dengan method PCC (Per Connection Classifier) untuk multiple ISP dengan failover otomatis.', 
7, 1, 'published', FALSE, NOW()),

('Hotspot Management dengan User Manager', 'hotspot-management-dengan-user-manager',
'<h2>Pengenalan User Manager</h2>
<p>User Manager adalah aplikasi bawaan MikroTik yang digunakan untuk manajemen user hotspot, termasuk profil, billing, dan authentication.</p>

<h2>Fitur User Manager</h2>
<ul>
<li>User profiles dengan berbagai paket</li>
<li>Billing dan payment management</li>
<li>Voucher system</li>
<li>Radius authentication</li>
<li>Reports dan analytics</li>
</ul>

<h2>Instalasi User Manager</h2>
<pre><code>/tool user-manager package install</code></pre>

<h2>Setting Radius Server</h2>
<pre><code>/radius add secret=123456 service=hotspot src-address=127.0.0.1</code></pre>

<h2>Configuring Hotspot</h2>
<pre><code>/ip hotspot setup</code></pre>

<p>Ikuti wizard untuk konfigurasi dasar hotspot.</p>

<h2>Membuat User Profile</h2>
<pre><code>/tool user-manager profile add name=Paket-1Jam \
    limit-uptime=1h price=5000 currency=IDR</code></pre>

<h2>Membuat Voucher</h2>
<pre><code>/tool user-manager user add username=voucher001 \
    password=1234 profile=Paket-1Jam</code></pre>

<h2>Monitoring User</h2>
<p>Gunakan IP hotspot/user untuk memantau user yang sedang aktif.</p>

<h2>Kesimpulan</h2>
<p>User Manager adalah solusi lengkap untuk manajemen hotspot MikroTik dengan fitur billing dan user management yang powerful.</p>', 
'Manajemen hotspot MikroTik menggunakan User Manager untuk profil user, billing, dan voucher system.', 
1, 1, 'published', FALSE, NOW()),

('VPN L2TP/IPsec Server Setup Guide', 'vpn-l2tp-ipsec-server-setup-guide',
'<h2>Apa itu L2TP/IPsec?</h2>
<p>L2TP/IPsec adalah kombinasi protocol yang menyediakan VPN tunnel yang aman. L2TP digunakan untuk tunneling, sedangkan IPsec memberikan enkripsi.</p>

<h2>Kelebihan L2TP/IPsec</h2>
<ul>
<li>Native support di Windows, Mac, dan Android</li>
<li>Enkripsi yang kuat dengan IPsec</li>
<li>Stabil dan reliable</li>
<li>Mudah diimplementasikan</li>
</ul>

<h2>Langkah-Langkah Konfigurasi</h2>

<h3>1. Enable IPsec</h3>
<pre><code>/ip ipsec peer add name=farhanale secret=MySecretKey</code></pre>

<h3>2. Setup L2TP Server</h3>
<pre><code>/interface l2tp-server server set enabled=yes \
    default-profile=default-encryption authentication=mschap2</code></pre>

<h3>3. Create PPP Profile</h3>
<pre><code>/ppp profile add name=vpn-profile local-address=10.0.0.1 \
    remote-address=vpn-pool dns-server=8.8.8.8</code></pre>

<h3>4. Create PPP User</h3>
<pre><code>/ppp secret add name=farhan password=MyPassword profile=vpn-profile</code></pre>

<h3>5. Firewall Rules</h3>
<pre><code>/ip firewall filter add chain=input protocol=udp \
    dst-port=1701,500,4500 action=accept</code></pre>

<h2>Testing VPN Connection</h2>
<p>Testing koneksi dari client Windows:</p>
<ol>
<li>Buka Network & Internet Settings</li>
<li>Click VPN > Add VPN Connection</li>
<li>Fill server IP, username, dan password</li>
<li>Click Connect</li>
</ol>

<h2>Troubleshooting</h2>
<ul>
<li>Pastikan firewall mengizinkan port 1701, 500, dan 4500</li>
<li>Check IPsec status dengan /ip ipsec active-peers print</li>
<li>Verify L2TP connections dengan /interface l2tp-server print</li>
</ul>

<h2>Kesimpulan</h2>
<p>L2TP/IPsec adalah solusi VPN yang aman dan mudah diimplementasikan untuk remote access ke jaringan MikroTik Anda.</p>', 
'Implementasi VPN server dengan protocol L2TP/IPsec untuk remote access yang aman di MikroTik.', 
5, 1, 'published', FALSE, NOW()),

('Scripting Otomatisasi Backup Config', 'scripting-otomatisasi-backup-config',
'<h2>Pentingnya Backup</h2>
<p>Backup konfigurasi router MikroTik sangat penting untuk menghindari kehilangan data dan konfigurasi. Scripting automasi memudahkan proses backup secara terjadwal.</p>

<h2>Metode Backup</h2>
<ol>
<li>Local backup ke router</li>
<li>Export ke FTP/SFTP server</li>
<li>Email notification</li>
<li>Upload ke cloud storage</li>
</ol>

<h2>1. Local Backup Script</h2>
<pre><code>/system script add name="backup-local" source={
    /system backup save name=backup-[/system clock get date]
    /export file=backup-config
}</code></pre>

<h2>2. Upload ke FTP Server</h2>
<pre><code>/system script add name="backup-ftp" source={
    :local ftpServer "192.168.1.100"
    :local ftpUser "backup"
    :local ftpPass "password"
    :local fileName "backup-[/system clock get date]"
    
    /system backup save name=$fileName
    /tool fetch address=$ftpServer mode=ftp src-path=$fileName.backup \
        user=$ftpUser password=$ftpPass upload=yes
}</code></pre>

<h2>3. Email Notification</h2>
<pre><code>/system script add name="backup-email" source={
    /tool e-mail send from=mikrotik@farhanale.my.id \
        to=admin@farhanale.my.id subject="MikroTik Backup" \
        file=backup-config.rsc
}</code></pre>

<h2>Setup Scheduler</h2>
<pre><code>/system scheduler add name="backup-daily" on-event=backup-local \
    interval=1d start-time=02:00:00</code></pre>

<h2>Auto-Restore Config</h2>
<pre><code>/system script add name="restore-config" source={
    :local fileName "backup-config"
    /import file-name=$fileName
}</code></pre>

<h2>Best Practices</h2>
<ul>
<li>Backup secara terjadwal (daily/weekly)</li>
<li>Simpan backup di multiple lokasi</li>
<li>Test restore secara berkala</li>
<li>Include email notification</li>
<li>Dokumentasikan prosedur restore</li>
</ul>

<h2>Kesimpulan</h2>
<p>Automasi backup dengan scripting MikroTik memastikan keamanan konfigurasi jaringan Anda dan meminimalkan risiko kehilangan data.</p>', 
'Scripting automasi untuk backup konfigurasi MikroTik dengan scheduler, FTP upload, dan email notification.', 
8, 1, 'published', FALSE, NOW());

-- Update article counts for categories
UPDATE categories c SET article_count = (
    SELECT COUNT(*) FROM articles a WHERE a.category_id = c.id AND a.status = 'published'
);

-- Insert default settings
INSERT INTO settings (setting_key, setting_value) VALUES
('site_name', 'MikroTik Blog by Farhan Ale'),
('site_description', 'Tutorial dan panduan lengkap tentang konfigurasi MikroTik, networking, dan automation'),
('site_url', 'https://mikrotik.farhanale.my.id'),
('meta_title', '{title} | MikroTik Blog'),
('meta_description', 'Pelajari konfigurasi MikroTik dengan tutorial lengkap dan praktis'),
('contact_email', 'kontak@farhanale.my.id'),
('whatsapp', '+6281234567890'),
('linkedin', 'https://linkedin.com/in/farhanale'),
('github', 'https://github.com/farhanale'),
('youtube', 'https://youtube.com/@farhanale'),
('instagram', 'https://instagram.com/farhanale'),
('facebook', 'https://facebook.com/farhanale'),
('twitter', 'https://twitter.com/farhanale'),
('per_page_articles', '9'),
('per_page_search', '12'),
('enable_analytics', 'true'),
('google_analytics', ''),
('enable_comments', 'false'),
('disqus_shortname', '');