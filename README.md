# Hamro Koseli 🎁

A web application built with **Laravel** framework.

---

## 📋 Requirements

Before you begin, make sure your system has the following installed:

- PHP >= 8.1
- Composer
- MySQL or MariaDB
- Node.js & NPM
- Git

---

## 🚀 Installation and Setup

### 1. Clone the Repository

```bash
git clone https://github.com/anil-sudo/hamrokoseli.git
cd hamrokoseli
```

### 2. Install PHP Dependencies

```bash
composer install
```

### 3. Install Node Dependencies

```bash
npm install
```

### 4. Configure Environment

Copy the `.env.example` file and rename it to `.env`:

```bash
cp .env.example .env
```

Then open the `.env` file and update the database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=hamrokoseli
DB_USERNAME=your_db_username
DB_PASSWORD=your_db_password
```

### 5. Generate Application Key

```bash
php artisan key:generate
```

### 6. Run Database Migrations

```bash
php artisan migrate
```

### 7. (Optional) Seed the Database

```bash
php artisan db:seed
```

### 8. Build Frontend Assets

```bash
npm run dev
```

> For production build:
> ```bash
> npm run build
> ```

### 9. Start the Development Server

```bash
php artisan serve
```

Now open your browser and visit: **http://127.0.0.1:8000**

---

## 🗂️ Project Structure

```
hamrokoseli/
├── app/            # Application logic (Models, Controllers, etc.)
├── bootstrap/      # App bootstrap files
├── config/         # Configuration files
├── database/       # Migrations, seeders, factories
├── public/         # Publicly accessible files
├── resources/      # Views, CSS, JS
├── routes/         # Web and API routes
├── storage/        # Logs, cache, uploaded files
└── tests/          # Automated tests
```

---

## ⚙️ Common Artisan Commands

```bash
# Clear all cache
php artisan optimize:clear

# Run tests
php artisan test

# Create a new controller
php artisan make:controller NameController

# Create a new model with migration
php artisan make:model ModelName -m
```

---

## 🤝 Contributing

1. Fork the repository
2. Create your feature branch: `git checkout -b feature/your-feature-name`
3. Commit your changes: `git commit -m 'Add some feature'`
4. Push to the branch: `git push origin feature/your-feature-name`
5. Open a Pull Request

---

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
