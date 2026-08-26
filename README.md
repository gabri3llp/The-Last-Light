# The Last Light: Survival Bulletin & Transceiver Board System
### Final Capstone Technical Deliverable | Repository Specification Manual

---

## 1. System Design & Architectural Concept Overview

"The Last Light" is an immersive web-application dashboard engineered to function as a distributed information network for decentralized survivors in a post-apocalyptic dark fantasy reality. 

Instead of traditional user panels, the platform operates as a terminal transceiver board. The application's goal is to allow isolated trading outposts to map out local danger anomalies, log resource-trading bulletins (Medicine, Weapons, Food), rate peer user reliability profiles through cryptographic Trust Rating variables, and execute site logs.

---

## 2. Relational Database Schema Model Specification
The platform utilizes an interconnected MySQL relational architecture mapping multi-table operations through explicit integrity vectors:

### 2.1 `users` Table Schema
Tracks profile authentication levels, security role masks, and localized demographic markers:
- `user_id` (INT, Primary Key, Auto-Increment)
- `username` (VARCHAR, Unique, Sanatized Account Target)
- `email` (VARCHAR, Unique Authentication Vector)
- `password_hash` (VARCHAR, Monitored via PASSWORD_DEFAULT Blowfish encryption rules)
- `settlement` (VARCHAR, Used to match local proximity tabs)
- `role` (ENUM('survivor', 'merchant', 'admin'), Handles secure panel access masks)
- `trust_rating` (DECIMAL(2,1), Scaled from 1.0 to 5.0 to render profile diamonds dynamically)

### 2.2 `notices` Table Schema
Houses the active core trading records and sighting posts across the board:
- `notice_id` (INT, Primary Key, Auto-Increment)
- `user_id` (INT, Foreign Key referencing `users.user_id` with cascading deletion gates)
- `category_id` (INT, Foreign Key mapping relational lookups to category descriptions)
- `title` (VARCHAR, Notice subject context line)
- `description` (TEXT, Main body block data)
- `trade_value` (VARCHAR, Optional valuation fields for merchant bartering)
- `rarity` (ENUM('common','uncommon','rare','epic','legendary','mythic'))
- `settlement` (VARCHAR, Origin outpost location)
- `status` (ENUM('active', 'archived', 'resolved'))
- `image_path` (VARCHAR, Relative path string linking local directory image strings)
- `created_at` (TIMESTAMP, Default tracking system clock variables)

---

## 3. Component & Directory Architecture Map

```text
The Last Light/
├── Components/
│   └── sidebar.php           # Unified vertical navigation matrix tracking dynamic role labels
├── Database/
│   ├── config.php            # Active global session initializer and mysqli connectivity hook
│   ├── login_process.php     # Secure authentication matching controller parsing URL params
│   ├── signup_process.php    # Registration pipeline validation mechanism tracking duplicates
│   ├── delete_notice.php     # Secure administrator/owner cascading record cleanup engine
│   └── update_notice.php     # Bug-free backend data overwrite controller
├── Imgs/
│   └── notices/              # Target repository folder storing uploaded user attachments
├── Pages/
│   ├── login.php             # Unified brand entry view containing error box wrappers
│   ├── signIn.php            # Secure account setup container with inline warning blocks
│   ├── index.php             # Filter-aware master feed processing dynamic navigation tabs
│   ├── notice.php            # Sighting feed displaying interactive trade response overrides
│   ├── profile.php           # Session metrics console displaying responsive star systems
│   └── admin.php             # Secure admin environment rendering dynamic system logs
└── Style.css                 # Master viewport layout sheet regulating view boundary anchoring
```

---

## 4. Local Deployment Manual & Initialization Steps

### 4.1 Environment Configuration
1. Clone or extract this project repository directory structure folder directly into your XAMPP server's root deployment path: `C:\xampp\htdocs\The Last Light\`
2. Initialize your local server stack by launching the **XAMPP Control Panel application**.
3. Toggle the active processing switches for both **Apache Web Server** and **MySQL Database** modules to `ON`.

### 4.2 Database Initialization Vector
1. Open your internet web browser dashboard and navigate to the local panel utility: `http://localhost/phpmyadmin/`
2. Select "New" in the side navigation panel and establish a new relational container named precisely: `The_Last_Light`
3. Click into your new database, select the **Import** tab across the top workspace strip menu, choose the backup file located inside your project repository folder (`Database/schema.sql`), and execute the import sequence.

### 4.3 Production Run Verification Link
Access the application portal by inputting the direct path link into your browser address query line bar:
`http://localhost/The Last Light/Pages/login.php`

---

## 5. Verified Grading Assessment Evaluation Credentials

### 5.1 Regular Survivor Profile Test Account
- **Access Link Target:** `login.php`
- **Email Asset:** `survivor@light.net` (or any new user created via `signIn.php`)
- **Password Token:** `password123`
- **Functional Check:** Accesses `index.php` tabs, views notice boards, updates personal records inside `profile.php`, and runs into block security barriers if attempting to access `admin.php`.

### 5.2 System Administrator Super-User Account
- **Access Link Target:** `admin.php`
- **Email Asset:** `admin@light.net`
- **Password Token:** `adminpassword`
- **Functional Check:** Unlocks full system diagnostic matrices, views live count registers, evaluates global activity logs, and utilizes the localized inline confirmation dialog overlays to delete records from the dataset.
