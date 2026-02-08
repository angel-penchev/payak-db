# Payak DB Project

## 📂 Project Structure
This project is self-contained. The entry point is `index.php` in the root directory.
* **assets/** - CSS, JS, Images
* **config/** - Database connection (PDO)
* **includes/** - Header, Footer, helper functions
* **sql/** - Contains `setup.sql` for database initialization
* **views/** - Page templates and logic

## 🛠️ Technologies
* **Backend:** PHP (Vanilla)
* **Database:** MySQL / MariaDB
* **Frontend:** Tailwind CSS (via CDN)
    * *Note: An active internet connection is required to load styles.*

---

## 🚀 Setup for Windows (Lecturer / Evaluator)

1.  **Download & Place:**
    * Download or clone this repository.
    * Move the folder `payak-db` into your XAMPP `htdocs` directory (usually `C:\xampp\htdocs\`).

2.  **Database Setup:**
    * Open **phpMyAdmin** (`http://localhost/phpmyadmin`).
    * Create a new database named **`payak_db`**.
    * Select the new database and go to the **Import** tab.
    * Upload the file located in: `sql/setup.sql`.
    * Click **Go** (Import).

3.  **Run:**
    * Open your browser and visit: [http://localhost/payak-db/](http://localhost/payak-db/)

> **Note:** The `config/db.php` is set to use the default XAMPP credentials (`root` user, empty password). No configuration changes should be needed.

---

## 🐧 Setup for Linux (Arch/Ubuntu/Debian)

There are two ways to set this up. **Method 1 is recommended** for development as it avoids permission issues.

### Method 1: Symbolic Link (Recommended)
Keep the project in your home directory (where you have full permissions) and "link" it to XAMPP.

1.  **Clone the repo to your workspace:**
    ```bash
    cd ~/
    git clone [https://github.com/angel-penchev/payak-db.git](https://github.com/angel-penchev/payak-db.git)
    ```

2.  **Create a symbolic link to XAMPP:**
    ```bash
    sudo ln -s ~/payak-db /opt/lampp/htdocs/payak-db
    ```
    *Now you can edit files in your home folder without needing `sudo`, and XAMPP will serve them automatically.*

3.  **Database Setup:**
    ```bash
    # Create the DB
    /opt/lampp/bin/mysql -u root -e "CREATE DATABASE payak_db;"

    # Import the structure
    /opt/lampp/bin/mysql -u root payak_db < ~/payak-db/sql/setup.sql
    ```

### Method 2: Direct Install (Fixing Permissions)
If you cloned directly into `/opt/lampp/htdocs`, you likely cannot edit files. Fix ownership to your user:

1.  **Clone into htdocs:**
    ```bash
    cd /opt/lampp/htdocs
    sudo git clone [https://github.com/angel-penchev/payak-db.git](https://github.com/angel-penchev/payak-db.git)
    ```

2.  **Fix Permissions (Give ownership to your user):**
    ```bash
    sudo chown -R $USER:$USER /opt/lampp/htdocs/payak-db
    sudo chmod -R 755 /opt/lampp/htdocs/payak-db
    ```
    *(Then run the database setup commands from Method 1)*

---

## ⚙️ Configuration
The database connection settings are located in `config/db.php`.

```php
$host = 'localhost';
$dbname = 'payak_db';
$user = 'root';
$pass = ''; // Default for XAMPP. Update if your setup has a password.
