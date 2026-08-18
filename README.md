# Wedding Website

A **customizable**, containerized PHP and MySQL web application designed for displaying wedding information, managing guest confirmations (RSVP), gift list with contribution functionality, and an accommodations list.

![PHP](https://img.shields.io/badge/PHP-8.4-blue?style=flat-square&logo=php)
![MySQL](https://img.shields.io/badge/MySQL-9.7-orange?style=flat-square&logo=mysql)
![Docker](https://img.shields.io/badge/Docker-Compose-2496ED?style=flat-square&logo=docker)
![License](https://img.shields.io/badge/License-MIT-green?style=flat-square)


<p align="center">
  <img src="docs/gifs/01.gif" width="850px">
</p>

---

## 📑 Table of Contents

- [🧩 Features](#-features)
- [🛠️ Tech Stack](#%EF%B8%8F-tech-stack)
- [🚀 Quick Start](#-quick-start)
- [📁 Project Structure](#-project-structure)
- [⚙️ Configuration Guide](#%EF%B8%8F-configuration-guide)
- [🗄️ Database & Backups](#-database--backups)
- [🔐 Environment Variables](#-environment-variables)
- [📊 Admin Panel Features](#-admin-panel-features)
- [📦 Deployment](#-deployment)
- [</> Development](#-development)
- [📝 Common Tasks](#-common-tasks)
- [🔎 Troubleshooting](#-troubleshooting)
- [📄 License](#-license)
- [💬 Support](#-support)

---

## 🧩 Features

🎨 **Customizable UI**
- Toggle features on/off without code changes
- Personalize UI content
- Customizations via a JSON file (`config.json`)

✅ **Guest Management**
- RSVP confirmation system using a MySQL database
- Guest attendance tracking
- Confirmations have an optional message for the couple

🎁 **Gift Registry System**
- Browse available gifts and contribution options
- Track gift contributions
- Customizable gift list by importing a JSON/CSV file

🏨 **Accommodations List**
- Help guests find nearby places to stay
- Provide links to booking platforms
- Customizable accommodations list via a JSON file (`accommodations.json`)

🛡️ **Admin Page**
- Secure admin panel with authentication
- Import/Update gift list from a JSON/CSV file
- Export data to CSV for external processing
- Manage data (confirmations, gifts)

📱 **Optimized for Mobile Devices**
- Responsive design

💾 **Automated Backups**
- Linux Cron-based scheduled backups
- Generate timestamped CSV exports
- Automatic cleanup of expired backups

🐳 **Docker Setup**
- Containerized application
- PHP 8.4 with Apache Web Server
- MySQL 9.7 database container


---

## 🛠️ Tech Stack

| Component | Technology |
|-----------|-----------|
| **Backend** | PHP 8.4 |
| **Web Server** | Apache 2.4 |
| **Database** | MySQL 9.7 |
| **Container Runtime** | Docker & Docker Compose |
| **Frontend** | HTML5, CSS3, JavaScript |
| **Automation** | Linux Cron |

---

## 🚀 Quick Start

### Prerequisites
- Docker installed

### Installation

1. **Clone the repository:**
   ```bash
   git clone https://github.com/yourusername/wedding-website.git
   cd wedding-website
   ```

2. **Configure environment variables:**
   ```bash
   cp .env.example .env
   # Edit .env with your database credentials
   ```

3. **Start the application:**
   ```bash
   docker compose up -d --build
   ```

4. **Access the application:**
   - Open http://localhost:8080/ in your browser
   - Admin panel: http://localhost:8080/pages/admin.php

---

## 📁 Project Structure

```
wedding-website/
├── backups/                          # Auto-generated CSV backups
├── config/
│   ├── config.json                   # Main customization file
│   ├── admin_config.json             # Admin credentials
│   ├── accommodations.json           # Accommodation listings
│   ├── gifts_example.json            # Sample gift data (JSON format)
│   ├── gifts_example.csv             # Sample gift data (CSV format)
│   └── crontab.txt                   # Cron job schedule
├── public/                           # Web root
│   ├── index.php                     # Website entry point
│   ├── pages/                        # Website pages
│   ├── actions/                      # API endpoints for form processing
│   ├── css/                          # Stylesheets
│   ├── js/                           # Client-side JavaScript
│   ├── images/                       # Images
│   └── fonts/                        # Custom fonts
├── src/
│   ├── database/
│   │   ├── connection.php            # PDO database connection
│   │   └── init.sql                  # Database schema
│   ├── utils/                        # PHP utility classes
│   └── scripts/
│       └── backup.php                # Cron backup execution script
├── docker-compose.yml                # Service orchestration
├── Dockerfile                        # PHP + Apache + Cron container image
├── .env.example                      # Environment template
└── README.md
```

---

## ⚙️ Configuration Guide

### Main Configuration: `config.json`

This file controls all site content and feature toggles.

#### Feature Flags
```json
{
  "enable_rsvp": true,                 // Enable guest confirmations functionality
  "enable_gift_list": true,            // Enable gift registry functionality
  "enable_accommodations": true,       // Enable accommodations page
  "enable_admin_link": true,           // Show admin link in footer
  "enable_backup": true                // Enable automated backups
}
```

#### Backup Configuration
```json
{
  "backup_frequency": "daily",         // Options: hourly, daily, weekly, monthly
  "backup_expiration_days": 15         // Keep backups for N days before deletion
}
```

#### Wedding Details
```json
{
  "bride_name": "Mary",
  "groom_name": "James",
  "wedding_date": "05/15/2026",
  "wedding_location": "London, England",
  "venue_location": "London House",
  "venue_location_link": "https://maps.app.goo.gl/...",
  "ceremony_location": "London Church",
  "ceremony_location_link": "https://maps.app.goo.gl/...",
  "rsvp_deadline": "04/15/2026",
  "dress_code": "Formal Attire"
}
```

#### Contact Information
```json
{
  "bride_phone": "123-456-7890",
  "groom_phone": "098-765-4321"
}
```

#### Page Content
```json
{
  "header_message": ["We Are Getting", "Married!"],
  "homepage_initial_message": ["Dear Family and Friends,", "..."],
  "about_message": ["The wedding ceremony will take place..."],
  "where_to_stay_message": ["We know that many of you..."],
  "gift_list_message": ["The best gift you can give us..."],
  "gift_list_instructions": []
}
```

#### Other Options
```json
{
  "homepage_image_format": "svg",      // Options: SVG, JPG, PNG, WEBP, GIF
  "gift_list_image_format": "svg",     // Options: SVG, JPG, PNG, WEBP, GIF
  "CSV_delimiter": ";"                 // Delimiter character used in the CSV files
}
```

### Admin Credentials: `admin_config.json`

Stores admin login credentials.

Definitely don't use the default credentials when deploying the application. You can use an online hash generator (e.g. bcrypt algorithm hash generator) to get the hash of your desired password.

The default hash is for the password 'admin123'.

```json
{
  "admin_username": "admin",
  "admin_password_hash": "..."		// Change this to the hash of your desired password
}
```

### Accommodations: `accommodations.json`
In this file you can add, edit or remove accommodations. These are displayed in the "Where To Stay" page.

```json
{
  "accommodations": [
	{
	  "name": "Hotel Royal",
	  "location": "123 Main St, London",
	  "link": "https://booking.com/...",
	  "image_name": "01.jpg",
	  "distance": "10 minutes"
    }
  ]
}
```

### Gift List: `gifts_example.json` or `gifts_example.csv`
This JSON/CSV files are a template you should use to add, remove, and edit gifts. To apply the changes, you need to go to the administrator page and manually import one of this files. This is because the gifts are fetched from the MySQL database, and not directly from the JSON/CSV file.

**Caution:** when editing the gift list, take into account that the gift ID is the link between the config files and the MySQL database, past gift contributions will be linked to this ID. You should not change IDs when editing the data. You can remove gifts from the JSON/CSV files and it will take effect in the database, but only if the item has no guest contributions in the database, if this happens you will get an error and the database is not updated.

#### JSON File
```json
{
  "gifts": [
    {
      "id": 1,
      "name": "Sofa",
      "value": 1300
    },
    {
      "id": 2,
      "name": "Armchair",
      "value": 700
    }
  ]
}
```

#### CSV File
```csv
ID;Name;Value(€)
1;Sofa;1300
2;Armchair;700
...
21;Honeymoon;999999
```
You can use different delimeters (**``,``**, **``;``**, etc) in your CSV file.

When exporting CSV files, you can select the desired delimiter by changing the value of "CSV_delimiter" in the ``config.json`` file. For gift list imports, the application automatically detects the correct delimiter when importing.

### Image Configuration
**Note:** when using SVGs, if you want the application to dynamically change their color to the website color pallet, you might need to edit their code and/or CSS properties to get the SVGs to change color correctly.

#### Homepage Images
Homepage image format can be edited in ``config.json``. Images (ceremony and venue images) should be placed inside the ``/public/images`` folder. They need to be named ``ceremony.your_format`` and ``venue.your_format``, where ``your_format`` represents the image file format you selected in the ``config.json`` configuration file.

#### Gift List Images
Their format can also be edited in ``config.json``. Images should be placed inside the ``/public/images/gifts`` folder. They need to be named ``gift_id.your_format``, where ``gift_id`` represents the gift ID, and ``your_format`` represents the image file format you selected in the ``config.json`` configuration file.

---

## 🗄️ Database & Backups

### Automated Backup System

The application includes an automated backup system powered by Linux Cron:

- **Frequency**: Configure in `config.json` (hourly/daily/weekly/monthly)
- **Format**: Timestamped CSV files
- **Location**: `/backups/` directory
- **Retention**: Automatic deletion based on `backup_expiration_days`
- **Triggers**: Cron job runs backup scripts in the container

**Backup Files Generated:**
- `gifts_backup_YYYY-MM-DD_HH-MM-SS.csv`
- `guest_confirmations_backup_YYYY-MM-DD_HH-MM-SS.csv`
- `guest_gifts_backup_YYYY-MM-DD_HH-MM-SS.csv`

### Import/Export Features

- **Export**: Download data as CSV from admin panel
- **Import**: Load/Update gifts list from CSV or JSON files

---

## 🔐 Environment Variables

Create a `.env` file from `.env.example`:

```env
# Database Configuration
MYSQL_ROOT_PASSWORD=change_this_root_password
MYSQL_DATABASE=wedding_db
MYSQL_USER=wedding_db_user
MYSQL_PASSWORD=change_this_user_password
```

---

## 📊 Admin Panel Features

Access the admin dashboard at `/pages/admin.php` with your configured credentials:

✅ View all guest confirmations  
✅ View guest contributions  
✅ Manage gift registry  
✅ Export data to CSV  
✅ Update gifts list  

---

## 📦 Deployment

### Docker Deployment

```bash
# Build and start services
docker compose up -d --build

# View logs
docker compose logs -f web

# Stop services
docker compose down
```

---

## </> Development

### Hot Reload

The Docker setup mounts the entire project directory, allowing changes to:
- `config.json` - Reload page to see changes
- PHP files - Refresh browser for updated content
- CSS/JS - Clear browser cache and refresh

**Note**: No container rebuild needed for config or code changes!

### Database Schema

Database is initialized automatically on first run using `src/database/init.sql`.

To reset the database:
```bash
docker compose down -v  # Remove volumes
docker compose up       # Recreate with fresh schema
```

---

## 📝 Common Tasks

### Change Wedding Details
Edit `config.json`:
```json
{
  "bride_name": "Your Name",
  "wedding_date": "MM/DD/YYYY"
}
```

### Add Accommodations
Edit `config/accommodations.json` to update the list with your accommodations.

### Import Gifts
1. Prepare CSV or JSON file
2. Go to Admin → Import Gifts
3. Select file and upload

### Adjust Backup Settings
```json
{
  "backup_frequency": "weekly",
  "backup_expiration_days": 30
}
```

### Download Guest Data
1. Admin Dashboard → Export
2. Choose export type (confirmations/contributions/gifts list with contributions)
3. Download CSV file

---

## 🔎 Troubleshooting

| Issue | Possible Solution |
|-------|----------|
| **Port 8080 already in use** | Change port in `docker-compose.yml`: `"9000:80"` |
| **Changes not showing** | Clear cache and refresh (Ctrl+F5) |
| **Backups not running** | Check `enable_backup: true` in `config.json` and verify Cron is active |
| **Admin login fails** | Verify `admin_config.json` credentials are set correctly. Verify password hash is correct. |

---

## 📄 License

This project is licensed under the MIT License - see LICENSE file for details.


## 💬 Support

For issues, questions, or suggestions:
- Open an [Issue](https://github.com/acamp3lo/wedding-website/issues)
- Check existing discussions
- Review documentation above

---
