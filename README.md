# MyCopyPaste

A modern, secure, and user-friendly web application for sharing text with unique, temporary links. Visit [MyCopyPaste](https://www.mycopypaste.io) to try it out!

> MyCopyPaste is a lightweight, secure text sharing solution that allows users to share text content through unique, one-time access links. Built with PHP 8.0+ and MySQL, this application provides a clean, modern interface for quick text sharing with enhanced security features. Perfect for sharing sensitive information, temporary access codes, or any text content that should only be viewed once. Features include one-click copy functionality, text download options.

## 🌟 Features

- **Text Sharing**: Share any text content with a unique, temporary link
- **One-Time Access**: Links become invalid after first use for enhanced security
- **Clean Interface**: Modern, responsive design with a user-friendly experience
- **Copy to Clipboard**: One-click copy functionality for shared content
- **Download Option**: Download shared text as a .txt file
- **Mobile Responsive**: Works seamlessly on all devices

## 🚀 Quick Start

1. Clone the repository:
```bash
git clone https://github.com/ahk780/MyCopyPaste.git
cd MyCopyPaste
```

2. Import the database:
```bash
mysql -u your_username -p your_database < database.sql
```

3. Configure your web server (Apache/Nginx) to point to the project directory

4. Update the database connection settings in `config.php`:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
define('DB_NAME', 'your_database');
```

## 📋 Requirements

- PHP 8.0 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)
- mod_rewrite enabled (for Apache)

## 🗄️ Database Structure

The application uses a single table `copies` with the following structure:

```sql
CREATE TABLE `copies` (
  `id` int(11) NOT NULL,
  `unique_key` varchar(8) NOT NULL,
  `text` text NOT NULL,
  `user_ip` varchar(45) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL,
  `used_at` datetime DEFAULT NULL,
  `used_by_ip` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
```

### Field Descriptions:
- `id`: Auto-incrementing primary key
- `unique_key`: 8-character unique identifier for the shared content
- `text`: The shared text content
- `user_ip`: IP address of the content creator
- `status`: Content status (0 = unused, 1 = used)
- `created_at`: Timestamp of content creation
- `used_at`: Timestamp of first access
- `used_by_ip`: IP address of the first viewer

## 🔒 Security Features

- One-time access links
- XSS protection
- SQL injection prevention
- Input sanitization

## 🎨 Customization

The application uses a clean, modern design with a green color scheme. You can customize the appearance by modifying the CSS variables in `css/style.css`:

```css
:root {
    --primary-color: #10B981;
    --primary-hover: #059669;
    /* ... other variables ... */
}
```

## 📱 Mobile Support

The application is fully responsive and works on:
- Desktop browsers
- Mobile devices
- Tablets
- All modern web browsers

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## 📞 Support

For support, feature requests, or bug reports:
- Visit [MyCopyPaste](https://www.mycopypaste.io)
- Contact the developer on Telegram: [@ahk780](https://t.me/ahk780)

## 📄 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 🙏 Acknowledgments

- Thanks to all contributors and users
- Special thanks to the open-source community

---

Made with ❤️ by [@ahk780](https://t.me/ahk780) 
