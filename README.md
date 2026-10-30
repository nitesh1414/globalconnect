# Global Connect - Corporate Multi-Page CMS

A complete, modern, and mobile-responsive Multi-Page CMS developed in **Core PHP** and **MySQL**, accurately recreating and enhancing the reference website [https://site.globalconnectsg.com/mysite/](https://site.globalconnectsg.com/mysite/).

---

## 🌟 Key Features

### 1. Public Multi-Page Website
- **Corporate Branding & Design**: Modern navy, gold, and sapphire corporate palette with sticky navigation, top contact bar, and mobile drawer menu.
- **Home Page (`index.php`)**:
  - Hero banner with Singapore & India headquarters branding and trade network graphics.
  - Live Statistics counters: **3,600+ Associate Partners**, **19+ Countries**, **19+ Partner Banks**.
  - Company Experience overview & Growth Strategies teaser.
  - Core service cards for **Trading**, **Finance**, and **Consulting**.
  - Detailed **Singapore Financial Hub Advantages** (5 key pillars: Top Financial Hub, Rule of Law, Educated Workforce, Stable Currency MAS structure, Central Aviation & Timezone alignment).
  - **FDI Singapore to India Analysis** ($15B FDI in 2024-25, 14% growth to $81.04B, expert analysis from Deloitte, IndusLaw, and Shardul Amarchand Mangaldas).
- **About Us (`about.php`)**:
  - Detailed company philosophy, growth ideas, and 6 core expertise pillars (*Financial Structuring, International Trade Finance, Merge & Acquisition, Offshore Fund Raising, Unlocking Values, Debt Syndication*).
- **Trading Portfolio (`trading.php` & `page.php?slug=...`)**:
  - Dynamic grid & dropdown navigation of all trading commodities.
  - Built-in pages for **Gold Bullion** (assay standards, returnable rates table, delivery guidelines), **Sugar** (ICUMSA 45 refined and raw cane sugar specifications), **Agro Products** (rice, grains, pulses, spices, edible oils), **Minerals, Metals & Ores** (mining, smelting, steel, mica), and **Petroleum & Lubricants** (Mazut M-100, Gas Oil D-2).
  - Any new trading subpage added from the admin panel immediately displays on the overview page and in the header dropdown menu.
- **Finance Solutions (`finance.php` & `page.php?slug=...`)**:
  - Dynamic overview & dropdown navigation for trade finance solutions.
  - Built-in pages for **Trade Finance** (Standby LC, Transferable LC, Documentary LC, Back to Back LC), **Banking Support** (corporate banking, prepaid mastercards, SWIFT wire transfers), and **Advisory** (international trade consulting, sourcing, contract manufacturing, logistical support, shipping assistance, negotiations).
  - Any new finance subpage added from the admin panel immediately displays on the overview page and in the header dropdown menu.
- **Consulting Services (`consulting.php`)**:
  - Comprehensive corporate advisory capabilities, capital market solutions, BSE/NSE/SGX listing readiness, and debt syndication.
- **Contact Us (`contact.php`)**:
  - Interactive inquiry submission form with CSRF protection and instant confirmation.
  - Office locations for Singapore Global HQ, Bangalore India HQ, Delhi/Mumbai Hyatt branches, Jakarta Indonesia (Grand Hyatt Plaza), and Hong Kong (Hyatt Regency).
  - Executive contacts directory with direct email links.

---

### 2. Powerful Admin Panel CMS (`/admin/`)
- **Executive Leadership & Directors Manager (`/admin/executives.php`, `/admin/executive-add.php`, `/admin/executive-edit.php`)**:
  - Full Add, Edit, Status Toggle, and Delete capability for Key Executive Contacts & Board of Directors.
  - Fields supported: Full Name with Salutation, Corporate Role / Designation, Corporate Email, Direct / Roaming Phone, Office Desk / Location, Biography / Summary, Profile Avatar Photo Upload (with automatic monogram initial fallback), Sort Order sequence, and Display Status (Active / Inactive).
  - Pre-seeded with all 7 authentic leadership records (Mr Shammy Songra, Mr Cliff Poh, Mr Lima Hutama Halim, Mr Kapil Gulati, Mr Sandeep Pratap Sanap, Mr Siddharth Maheshwari, Mr Amit Gupta).
  - Fully dynamic rendering on public Contact Us page (`contact.php`) with responsive leadership grid, monogram avatars, direct click-to-call phone links, and mailto links.
- **Hero Banners & Sliders Manager (`/admin/sliders.php`, `/admin/slider-add.php`, `/admin/slider-edit.php`)**:
  - Add, edit, reorder, and activate/deactivate homepage hero slides.
  - **Dual Display Modes**:
    - **Clean Image Mode (Without text on image)**: Designed specifically for infographic world map banners (such as the Global Connect Group banner). Renders the full image with 100% complete visibility and zero cropping across desktop, tablet, and mobile devices (preserving all country lines, registration UEN/GSTN/BR numbers, and GCV emblem).
    - **Text Overlay Mode (With text on image)**: Adds customizable badge tag, headline, subtitle description, and dual CTA action buttons floating over high-resolution corporate backgrounds with high-contrast gradient backdrop.
  - **Interactive Multi-Slide Carousel**: Automatically activates when multiple active slides exist, featuring touch-swipe gestures, navigation arrows, pagination bullets, and autoplay rotation.
- **Dashboard (`/admin/index.php`)**:
  - Live KPI metrics: Total Pages, Trading Pages count, Finance Pages count, Inquiries count, and Unread alerts.
  - Quick action shortcuts for instant page creation and inbox access.
  - Recent inquiries feed and recent pages manager.
- **Page Management (`/admin/pages.php`, `/admin/page-add.php`, `/admin/page-edit.php`)**:
  - Filter pages by category (**Trading**, **Finance**, **Consulting**, **General**).
  - Add and edit pages with:
    - Title & automatic URL slug generation
    - Category selection
    - Subtitle / Tagline
    - Featured Image upload or Media library picker
    - **Built-in WYSIWYG Rich Text Editor** with toolbars for headings (H2–H5), bold, italic, underline, lists, tables, links, images, clear formatting, and raw HTML source view.
    - SEO Meta Title, Meta Description, and Meta Keywords.
    - Publication Status toggle (Published / Draft) and Menu Order sorting.
- **Inquiries Inbox (`/admin/contacts.php`)**:
  - View all leads and contact messages submitted through the website.
  - Filter by Unread, Read, or Replied status.
  - Open inquiry details, reply via direct mailto link, mark as replied, or delete.
- **Site Settings & Logo Manager (`/admin/settings.php`)**:
  - **100% Dynamic Branding**: Direct file upload for website Logo, Favicon, and Experience images with instant live previews.
  - **Editable Brand Typography**: Customize Logo Title (Part 1 & 2), Logo Subtitle Tagline, Browser Titles, and Meta Taglines.
  - **Global Contact Directory**: Manage Singapore phone numbers, 24/7 Global Roaming lines, primary/secondary/tertiary emails, and 5 global office addresses (Singapore HQ, India Bangalore, Hyatt Hotel Branches, Jakarta Indonesia, Hong Kong).
  - **Live Counters & Section Copy**: Edit all homepage statistics (Partners, Countries, Partner Banks, Years of Experience) and section copy (Philosophy, Capabilities, Singapore Hub, CTA banners) with instant frontend updates.
- **Media Library (`/admin/media.php`)**:
  - Upload images (JPEG, PNG, SVG, WebP).
  - Browse existing asset library with one-click copy URL path.
- **Administrator Profile (`/admin/profile.php`)**:
  - Update administrator name, email, and password.

---

## 🔐 Admin Credentials

- **Admin URL**: `http://localhost:3000/admin/login.php` (or `/admin/`)
- **Username**: `admin`
- **Password**: `admin123`

---

## 📁 Project Directory Structure

```
globalconnect/
├── assets/
│   ├── css/
│   │   ├── style.css             # Main corporate responsive stylesheet
│   │   ├── admin.css             # Modern Admin panel dashboard stylesheet
│   │   └── editor.css            # WYSIWYG rich text editor styling
│   ├── js/
│   │   ├── main.js               # Navigation, mobile menu drawer, interactions
│   │   └── rich-editor.js        # Built-in WYSIWYG rich text editor
│   └── images/                   # High-res SVG and PNG corporate assets
├── config/
│   └── database.php              # Database config supporting MySQL & SQLite PDO
├── data/
│   └── globalconnect.db          # Auto-initialized SQLite database
├── database.sql                  # Complete MySQL schema & initial data dump
├── includes/
│   ├── db.php                    # PDO Database singleton & automatic schema seeder
│   ├── functions.php             # Core helper functions, sanitization, CSRF, auth
│   ├── header.php                # Top bar, logo & dynamic responsive navigation
│   └── footer.php                # 4-column corporate footer & bottom copyright bar
├── admin/
│   ├── includes/                 # Admin partials (auth, sidebar, header, footer)
│   ├── index.php                 # Admin dashboard with KPIs and recent feeds
│   ├── login.php                 # Secure administrator login
│   ├── logout.php                # Logout and session termination
│   ├── pages.php                 # All pages management with category filters
│   ├── page-add.php              # Add new page (Trading, Finance, Consulting, General)
│   ├── page-edit.php             # Edit page with rich text editor & image uploader
│   ├── page-delete.php           # Delete custom pages
│   ├── sliders.php               # Hero banner list & status toggle
│   ├── slider-add.php            # Add hero banner form
│   ├── slider-edit.php           # Edit hero banner form
│   ├── slider-delete.php         # Delete hero banner handler
│   ├── executives.php            # Executive contacts & directors management
│   ├── executive-add.php         # Add new executive / director form
│   ├── executive-edit.php        # Edit executive / director form
│   ├── executive-delete.php      # Delete executive handler
│   ├── categories.php            # Category management
│   ├── contacts.php              # Customer inquiries inbox & reader
│   ├── settings.php              # Global website settings
│   ├── media.php                 # Media library & file uploader
│   └── profile.php               # Admin profile & password change
├── uploads/                      # Upload directory for CMS images
├── index.php                     # Public Home page
├── about.php                     # Public About Us page
├── trading.php                   # Public Trading overview
├── finance.php                   # Public Finance overview
├── consulting.php                # Public Consulting overview
├── contact.php                   # Public Contact Us page with interactive form
├── page.php                      # Dynamic page renderer for all subpages
├── server.js                     # Local dev server with WebAssembly PHP 8.3 runtime
└── package.json
```

---

## 🚀 Running the Project

### Using Built-in Server (Node.js & WebAssembly PHP 8.3)
```bash
npm start
```
The server will start listening at `http://0.0.0.0:3000`.

### Deploying to standard LAMP / Apache / Nginx / cPanel:
1. Create a MySQL database (e.g. `globalconnect_db`).
2. Import `database.sql` into your MySQL database:
   ```bash
   mysql -u root -p globalconnect_db < database.sql
   ```
3. Set your environment variables in `.env` or in `config/database.php`:
   - `DB_DRIVER=mysql`
   - `DB_HOST=localhost`
   - `DB_NAME=globalconnect_db`
   - `DB_USER=your_db_user`
   - `DB_PASS=your_db_password`
4. Point your web server document root to the project directory.
