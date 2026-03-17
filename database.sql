-- Database: farhanale_db

-- Create database
CREATE DATABASE IF NOT EXISTS farhanale_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE farhanale_db;

-- Users table (admin)
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Portfolio table
CREATE TABLE portfolio (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    image VARCHAR(255),
    category VARCHAR(50),
    project_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Blogs table
CREATE TABLE blogs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    content TEXT,
    image VARCHAR(255),
    category VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Certificates table
CREATE TABLE certificates (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(100) NOT NULL,
    issuer VARCHAR(100),
    issue_date DATE,
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Skills table
CREATE TABLE skills (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    category VARCHAR(50),
    percentage INT,
    icon VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Messages table (contact form)
CREATE TABLE messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(100),
    message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Site settings table
CREATE TABLE settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(50) UNIQUE NOT NULL,
    setting_value TEXT
);

-- Insert default admin user (password: admin123)
INSERT INTO users (username, password, email) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@farhanale.my.id');

-- Insert dummy portfolio data
INSERT INTO portfolio (title, description, category, project_url, image) VALUES
('MikroTik Hotspot Setup', 'Konfigurasi sistem hotspot dengan voucher system, radius authentication, dan bandwidth management yang optimal.', 'MikroTik', '#', 'mikrotik-hotspot.jpg'),
('Network Infrastructure Design', 'Perancangan dan implementasi infrastruktur jaringan enterprise dengan topologi star dan redundancy.', 'Networking', '#', 'network-infra.jpg'),
('Automated Backup System', 'Sistem backup otomatis menggunakan Python dan cron job untuk semua konfigurasi network devices.', 'Automation', '#', 'backup-system.jpg'),
('VPN Configuration', 'Implementasi VPN server dengan protocol L2TP/IPsec untuk remote access yang aman.', 'Networking', '#', 'vpn-config.jpg'),
('Load Balancing Setup', 'Konfigurasi load balancing dengan method PCC untuk multiple ISP connection.', 'MikroTik', '#', 'load-balance.jpg'),
('WiFi Monitoring System', 'Sistem monitoring jaringan WiFi real-time dengan grafik dan alerting otomatis.', 'Automation', '#', 'wifi-monitor.jpg');

-- Insert dummy blog data
INSERT INTO blogs (title, slug, content, category, image) VALUES
('Konfigurasi Dasar MikroTik untuk Pemula', 'konfigurasi-dasar-mikrotik-untuk-pemula', 'MikroTik adalah salah satu router yang paling populer digunakan di dunia jaringan. Artikel ini akan membahas konfigurasi dasar MikroTik mulai dari setup IP address, NAT, sampai firewall basic. Kita akan belajar step by step cara mengkonfigurasi router MikroTik dengan Winbox dan CLI.', 'MikroTik', 'blog1.jpg'),
('Network Automation dengan Python', 'network-automation-dengan-python', 'Python menjadi bahasa pemrograman paling populer untuk network automation. Artikel ini akan membahas library Python yang sering digunakan seperti Netmiko, Paramiko, dan Napalm. Kita akan belajar membuat script otomatis untuk backup konfigurasi, monitoring, dan provisioning network devices.', 'Automation', 'blog2.jpg'),
('Best Practices Security Jaringan', 'best-practices-security-jaringan', 'Keamanan jaringan adalah aspek yang sangat penting dalam infrastruktur IT. Artikel ini akan membahas best practices untuk mengamankan jaringan termasuk konfigurasi firewall, segmentasi jaringan, monitoring, dan incident response.', 'Networking', 'blog3.jpg'),
('Load Balancing MikroTik: L7 vs PCC', 'load-balancing-mikrotik-l7-vs-pcc', 'Load balancing di MikroTik dapat dilakukan dengan berbagai method. Artikel ini akan membandingkan dua method populer: L7 (Layer 7) dan PCC (Per Connection Classifier). Kita akan membahas kelebihan dan kekurangan masing-masing method serta kapan harus menggunakannya.', 'MikroTik', 'blog4.jpg');

-- Insert dummy certificate data
INSERT INTO certificates (title, issuer, issue_date, image) VALUES
('MikroTik Certified Network Associate (MTCNA)', 'MikroTik', '2023-08-15', 'mtcna.jpg'),
('Python for Network Automation', 'Coursera', '2024-01-10', 'python-auto.jpg'),
('AWS Cloud Practitioner', 'Amazon Web Services', '2024-02-28', 'aws.jpg');

-- Insert dummy skills data
INSERT INTO skills (name, category, percentage, icon) VALUES
('MikroTik', 'Networking', 95, 'fa-server'),
('Python', 'Automation', 80, 'fa-code'),
('Networking', 'Networking', 90, 'fa-globe'),
('Automation', 'Automation', 75, 'fa-cogs'),
('Linux', 'Tools', 85, 'fa-linux'),
('Security', 'Networking', 70, 'fa-shield-halved'),
('DevOps', 'Tools', 65, 'fa-infinity'),
('Cloud', 'Tools', 60, 'fa-cloud'),
('Docker', 'Tools', 55, 'fa-docker');

-- Insert site settings
INSERT INTO settings (setting_key, setting_value) VALUES
('site_name', 'Farhan Ale - Network & Automation Engineer'),
('site_description', 'Portfolio dan blog tentang networking, MikroTik, dan automation oleh Farhan Ale'),
('contact_email', 'kontak@farhanale.my.id'),
('whatsapp', '+6281234567890'),
('linkedin', 'https://linkedin.com/in/farhanale'),
('instagram', 'https://instagram.com/farhanale'),
('github', 'https://github.com/farhanale'),
('address', 'Jakarta, Indonesia');
