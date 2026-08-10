# DynamicWebsite
Simple CRUD (Create, Read, Update, Delete) website application runs completely in JS, CSS, PHP, SQL, and ~~HTML~~.

## Members
 - John Victor Acero
 - Andrei Mulato
 - Richsander Orduna
 - Jeffrey Reyes
 - Mark Denniel Urqueza

<details>
 <summary>File Directories </summary>
```text
Directory structure:
└── jvacero-dynamicwebsite/
    ├── README.md
    ├── index.php
    ├── admin/
    │   ├── about_devs.php
    │   ├── admin_dashboard.php
    │   ├── admin_delete.php
    │   ├── admin_footer.php
    │   ├── admin_header.php
    │   ├── admin_login.php
    │   ├── admin_registration.php
    │   ├── admin_up_item.php
    │   └── admin_update.php
    ├── assets/
    │   ├── addtocart.css
    │   ├── admin_dashboard.css
    │   ├── admin_delete.css
    │   ├── admin_footer.css
    │   ├── admin_header.css
    │   ├── admin_login.css
    │   ├── admin_registration.css
    │   ├── admin_update.css
    │   ├── dashboard.css
    │   ├── footer.css
    │   ├── header.css
    │   ├── style.css
    │   └── user_style.css
    ├── config/
    │   ├── auth.php
    │   ├── cookies.php
    │   ├── dbquery_localdb.php
    │   ├── login_process.php
    │   ├── logout.php
    │   ├── mysqli_connect.php
    │   └── session.php
    ├── database/
    │   └── user.sql
    ├── includes/
    │   ├── addtocart.php
    │   ├── dashboard.php
    │   ├── delivered.php
    │   ├── footer.php
    │   ├── header.php
    │   └── index.php
    └── uploads/ ```
    </details>

# DynamicWebsite System (Pixel Market / Buraot System)

A PHP and MySQL-based e-commerce platform built with an integrated administration portal and an 8-bit retro gaming aesthetic.

---

## 1. Project Overview

### Technical Architecture
The system employs a PHP backend connected to a MySQL relational database. The schema utilizes cascade constraints (`ON DELETE CASCADE`) across user, session, cart, and order tables to maintain relational integrity.

```
┌─────────────────────────────────────────────────────────┐
│                    User Browser                         │
└───────────┬─────────────────────────────────┬───────────┘
            │                                 │
            ▼                                 ▼
┌───────────────────────┐         ┌───────────────────────┐
│    Customer Front     │         │   Admin Portal        │
│  (index, cart, etc.)  │         │  (admin_dashboard)    │
└───────────┬───────────┘         └───────────┬───────────┘
            │                                 │
            ▼                                 ▼
┌─────────────────────────────────────────────────────────┐
│                   PHP Backend API                       │
│      - Auth Enforcement (auth.php)                      │
│      - MySQLi Interface (mysqli_connect.php)            │
└───────────┬─────────────────────────────────────────────┘
            │
            ▼
┌─────────────────────────────────────────────────────────┐
│                   MySQL Database                        │
│   (user, product, cart, order_history, order_items)     │
└─────────────────────────────────────────────────────────┘
```

---

### Plain-Language Summary
Think of this project as a **virtual retro online store** (like a pixel-art storefront) with a backend control panel. 
* **For Customers:** It works like any shopping site where people can view items, add them to a shopping cart, and place orders.
* **For Store Managers:** It provides an administrative panel to add new products, update prices, view sales history, manage existing users, or remove user accounts.

---

## 2. Core Functional Modules

### A. Core Configuration & Database Setup (`config/`)

#### Technical Operations
* **Database Connection (`mysqli_connect.php`):** Instantiates a `mysqli` object targeting a database named `user`.
* **Automated Migration (`dbquery_localdb.php`):** Verifies the existence of the MySQL database and auto-generates five core tables (`user`, `product`, `cart`, `order_history`, and `order_items`) along with foreign key constraints if they do not exist locally.
* **Authentication & Synchronization (`auth.php`):** Exposes `enforce_admin_access()`. It queries the database using prepared statements to verify active session details against the `user` table. If the user’s `admin` column isn't `1`, or if their account was deleted, the session is cleared and the browser redirects to `index.php`.
* **Authentication Handler (`login_process.php` & `logout.php`):** Implements password verification using `password_verify()` against standard MySQL hashes, executes session identifier regeneration (`session_regenerate_id(true)`), and purges session state/cookies upon logout.

```
Database Schemas:
┌─────────────────┐       ┌─────────────────┐       ┌─────────────────┐
│      user       │       │      cart       │       │     product     │
├─────────────────┤       ├─────────────────┤       ├─────────────────┤
│ id (PK)         │◄─────┐│ id (PK)         │       │ id (PK)         │
│ name            │      ││ user_id (FK)    │   ┌───│ productimage    │
│ email           │      └┼─user_id         │   │   │ productname     │
│ username        │       │ product_id (FK)─┼───┘   │ price           │
│ password        │       │ quantity        │       │ stock           │
│ admin (BOOL)    │       │ added_at        │       └─────────────────┘
└─────────────────┘       └─────────────────┘
```

#### Plain-Language Summary
* **The Connector (`mysqli_connect.php`):** The digital pipeline that lets the website communicate with its database.
* **The Auto-Builder (`dbquery_localdb.php`):** A helper script that checks whether the website's database exists; if missing, it automatically creates all required tables.
* **The Bouncer (`auth.php`):** Verifies user credentials upon entry. If standard users attempt to access administrator pages, the script redirects them away.
* **Login & Logout (`login_process.php`, `logout.php`):** Handles logging into accounts, securely checks passwords, and wipes user session details upon logging out.

---

### B. Administration Control Panel (`admin/`)

#### Technical Operations
* **Dashboard & Metrics (`admin_dashboard.php`):** Integrates jQuery DataTables to render interactive, searchable records of user entries and activity logs. Executes `UNION ALL` SQL queries joining `cart`, `user`, `product`, `order_items`, and `order_history` tables to produce a unified operational audit log.
* **User Management (`admin_registration.php`, `admin_update.php`, `admin_delete.php`):** Enables administration users to register new accounts via `password_hash()`, alter existing details (e.g., name, username, password) dynamically, and purge target users using prepared statements.
* **Inventory Uploads (`admin_up_item.php`):** Handles multi-part POST requests (`multipart/form-data`). Validates file uploads, generates unique timestamped filenames, moves the binary files into the `uploads/` directory, and persists product records in the `product` database table.
* **Developer Documentation (`about_devs.php`):** Renders dynamic credits cards using a custom retro CSS theme and an internal array dataset.

#### Plain-Language Summary
* **Control Center (`admin_dashboard.php`):** An administrative overview panel containing searchable lists of all registered users and real-time activity logs showing what users have placed in their carts or purchased.
* **Account Tools (`admin_registration.php`, `admin_update.php`, `admin_delete.php`):** Forms allowing administrators to create new system managers, update user details, or permanently remove accounts.
* **Product Uploader (`admin_up_item.php`):** A form for adding new store items, specifying prices, stock amounts, and uploading product images.
* **Dev Credits (`about_devs.php`):** A page displaying information about the software developers who built the application.

---

### C. Customer Interface & Operations (`includes/`, `index.php`)

#### Technical Operations
* **Master Layout (`index.php`):** Serves as the primary entry point, integrating global stylesheets and dynamic layout blocks (`header.php`, `dashboard.php`, `footer.php`).
* **Shopping Cart Processing (`includes/addtocart.php`):** Synchronizes PHP session storage (`$_SESSION['cart']`) with persistent MySQL `cart` records. Supports adding items, incrementing quantities, and removing records via POST actions.

#### Plain-Language Summary
* **Homepage (`index.php`):** The primary website landing page that pulls together the top navigation bar, main product display, and bottom page footer.
* **Shopping Cart (`includes/addtocart.php`):** Handles adding items to a user's shopping cart, adjusting item quantities, or removing items prior to checking out.

---

### D. User Interface Styling (`assets/`)

#### Technical Operations
* CSS resources utilize a pixel-art design system.
* Styling features pixel typography (`Press Start 2P`), retro color palettes, hard box-shadows (`4px 4px 0 #000000`), zero border-radii, and hard pixel rendering rules (`image-rendering: pixelated`).

#### Plain-Language Summary
* **Visual Theme:** The styling files give the entire site a retro 8-bit arcade look, complete with blocky fonts, high-contrast borders, pixelated images, and arcade-style buttons.

---

## 3. Database Schema Overview

| Table Name | Primary Purpose | Key Fields |
| :--- | :--- | :--- |
| **`user`** | Stores customer and admin user credentials. | `id`, `name`, `email`, `username`, `password`, `admin` |
| **`product`** | Stores store items, stock levels, and prices. | `id`, `productname`, `price`, `stock`, `productimage` |
| **`cart`** | Tracks items currently held in user shopping carts. | `id`, `user_id`, `product_id`, `quantity`, `added_at` |
| **`order_history`** | Stores finalized customer orders and delivery states. | `id`, `user_id`, `order_reference`, `total`, `status` |
| **`order_items`** | Holds individual item line breakdowns per completed order. | `id`, `order_id`, `product_id`, `quantity`, `price`, `subtotal` |
