# Linux Installation Guide for Academic Content Display System (ACDS)

This guide provides step-by-step instructions for installing and configuring the ACDS system on a Linux server.

---

## 1. System Requirements

✅ **Operating System**: Ubuntu 20.04 LTS / Debian 11 / CentOS 8 or newer
✅ **Web Server**: Apache 2.4+
✅ **PHP**: 7.4 or higher (8.x recommended)
✅ **Required PHP Modules**:
  - `php-json`
  - `php-fileinfo`
  - `php-gd` (optional for image processing)
✅ **File Permissions**: Write access for web server user

---

## 2. Installation Steps

### 2.1 Install Required Packages

**Ubuntu/Debian:**
```bash
sudo apt update
sudo apt install apache2 php libapache2-mod-php php-json php-fileinfo
```

**CentOS/RHEL:**
```bash
sudo dnf install httpd php php-json php-fileinfo
sudo systemctl enable httpd
sudo systemctl start httpd
```

---

### 2.2 Download and Install ACDS

1.  **Navigate to web root:**
    ```bash
    cd /var/www/html/
    ```

2.  **Download the latest version:**
    ```bash
    wget https://github.com/adityanarayan98/Display/archive/refs/heads/main.zip
    unzip main.zip
    mv Display-main Display
    ```

3.  **Set correct ownership:**
    ```bash
    sudo chown -R www-data:www-data /var/www/html/Display/
    ```

4.  **Set proper file permissions:**
    ```bash
    sudo chmod -R 755 /var/www/html/Display/
    sudo chmod -R 775 /var/www/html/Display/temp/
    sudo chmod 664 /var/www/html/Display/settings.json
    ```

---

### 2.3 Configure Apache

1.  **Create Apache configuration file:**
    ```bash
    sudo nano /etc/apache2/sites-available/display.conf
    ```

2.  **Add this configuration:**
    ```apache
    <VirtualHost *:80>
        ServerAdmin admin@example.com
        DocumentRoot /var/www/html/Display
        ServerName display.example.com
        
        <Directory /var/www/html/Display>
            Options Indexes FollowSymLinks MultiViews
            AllowOverride All
            Require all granted
        </Directory>
        
        ErrorLog ${APACHE_LOG_DIR}/display_error.log
        CustomLog ${APACHE_LOG_DIR}/display_access.log combined
    </VirtualHost>
    ```

3.  **Enable the site:**
    ```bash
    sudo a2ensite display.conf
    sudo systemctl reload apache2
    ```

---

## 3. Important Linux Specific Notes

### 🔴 **CRITICAL: Case Sensitivity**
Linux file systems are **case-sensitive**:
- `Image.jpg` is NOT the same as `image.jpg`
- `Front_display` folder is NOT the same as `front_display`
- All filenames and URLs must match EXACTLY

**Always use the exact same case when:**
- Uploading files
- Referencing media
- Creating project folders

---

### 🔐 File Permissions

**Correct permissions for Linux:**
```bash
# Directories
sudo find /var/www/html/Display -type d -exec chmod 755 {} \;

# Files
sudo find /var/www/html/Display -type f -exec chmod 644 {} \;

# Writable folders
sudo chmod 775 /var/www/html/Display/temp/
sudo chmod 775 /var/www/html/Display/iq3/
sudo chmod 775 /var/www/html/Display/Front_display/
sudo chmod 664 /var/www/html/Display/settings.json
sudo chmod 600 /var/www/html/Display/logs.json
```

**Web server user:**
- Ubuntu/Debian: `www-data`
- CentOS/RHEL: `apache`

---

### 📋 SELinux Configuration (CentOS/RHEL)

If you have SELinux enabled (default on CentOS):
```bash
sudo setsebool -P httpd_unified 1
sudo setsebool -P httpd_can_network_connect 1
sudo semanage fcontext -a -t httpd_sys_rw_content_t "/var/www/html/Display/temp(/.*)?"
sudo semanage fcontext -a -t httpd_sys_rw_content_t "/var/www/html/Display/iq3(/.*)?"
sudo restorecon -Rv /var/www/html/Display/
```

---

## 4. First Time Setup

1.  **Access the system:**
    ```
    http://your-server-ip/Display/
    ```

2.  **Login to admin panel:**
    ```
    http://your-server-ip/Display/admin.php
    ```

    - Default Username: `admin`
    - Default Password: See `config.php`

3.  **Change default password immediately!**

✅ **All folders and configuration files are created automatically on first load.** No manual setup required.

---

## 5. Automatic Startup on Boot

✅ **Apache starts automatically on boot by default**

To verify:
```bash
sudo systemctl is-enabled apache2
```

---

## 6. Performance Optimization

For best performance on Linux:
```bash
# Enable Apache modules
sudo a2enmod rewrite deflate expires headers
sudo systemctl restart apache2
```

---

## 7. Troubleshooting Linux Specific Issues

| Issue | Solution |
|---|---|
| **File not found error** | Double check case sensitivity. Linux is case-sensitive! |
| **Permission denied** | Run `chown` and `chmod` commands as shown above |
| **Upload failed** | Check folder permissions and PHP `upload_max_filesize` |
| **Images not loading** | Verify filename case matches exactly |
| **500 Internal Server Error** | Check Apache error log: `tail -f /var/log/apache2/display_error.log` |
| **Logs not working** | Ensure `logs.json` file has 0600 permissions and owned by www-data |

---

## 8. Logging

**Apache access logs:**
```bash
tail -f /var/log/apache2/display_access.log
```

**Apache error logs:**
```bash
tail -f /var/log/apache2/display_error.log
```

**System Application Logs:**
All admin actions and events are logged automatically in `logs.json` and accessible only via admin panel. Logs are automatically retained for 30 days.

---

## ✅ Installation Complete

Your ACDS system is now installed and running on Linux.

For further configuration, login to the admin panel and create your projects.

---

© 2026 Aditya Narayan Sahoo