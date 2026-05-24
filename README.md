# MockDasher

MockDasher is a modern, premium SaaS platform tailored for IELTS preparation. It provides students with full-length mock tests spanning all four modules (Listening, Reading, Writing, and Speaking), detailed test history tracking, and analytics to visualize band score progression.

## Project Features 🚀
- **Comprehensive Mock Tests:** Simulates real IELTS exams with timed, module-based progression.
- **Dynamic Dashboards:** Provides personalized test recommendations, score analysis, and progress charts using 100% dynamic data binding.
- **Robust Architecture:** Employs Service layers to decouple high-complexity logic from controllers, prioritizing clean, scalable MVC execution.
- **Secure by Default:** Utilizes strict Laravel FormRequests for structural validation and authorization logic.
- **Premium UI:** Stripe-level SaaS aesthetics featuring glassmorphism, responsive navigation, and intuitive empty states.

---

## UI Architecture Rules

This project strictly adheres to the following frontend principles:
1. **Strict CSS Centralization:** NO inline `<style>` tags or native `style="..."` attributes are allowed. All custom utility overrides and global variables must be exclusively located in `resources/css/app.css` or achieved via Tailwind classes and Alpine.js `:style` bindings.
2. **100% Dynamic Data Binding:** No hardcoded or "mock" text (e.g., "John Doe", "Lorem Ipsum") is permitted. All data representations, loops, tables, and media flow directly from the database or configurations.
3. **Empty States Required:** Any looped data (`@forelse`) or dynamic condition must include an engaging empty state ("No records found") for a flawless user experience.
4. **Configuration & Language Driven:** Static UI labels (like "Dashboard", "Submit") are abstracted into Laravel's language files (`__('messages.key')`) for seamless localization and scalability.
5. **Component Reliability:** All buttons, forms, and links must be fully functional. Forms require `@csrf`, `@error`, and `old()` inputs. Links must map to valid named `route()` instances natively.

---

## Prerequisites
Ensure your local development environment meets the following requirements:
- **PHP** 8.2 or higher
- **Composer** v2+
- **Node.js** & **NPM** (for compiling frontend assets)
- **MySQL** 8.0+ or another preferred equivalent relational database

## Setup Instructions

1. **Clone the repository:**
   ```bash
   git clone https://github.com/prayangshuuu/MockDasher
   cd MockDasher
   ```

2. **Install PHP Dependencies:**
   ```bash
   composer install
   ```

3. **Install & Build Frontend Assets (Vite):**
   ```bash
   npm install
   npm run build
   ```

4. **Environment Setup:**
   Duplicate the `.env.example` file and configure your local environment variables.
   ```bash
   cp .env.example .env
   ```
   *Crucial `.env` values to set: `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`*

5. **Generate Application Key:**
   ```bash
   php artisan key:generate
   ```

6. **Run Migrations & Seeders:**
   Prepare your database schema and insert default test data.
   ```bash
   php artisan migrate --seed
   ```

7. **Link Storage:**
   Ensure public disk assets (like profile photos or test audio files) are accessible.
   ```bash
   php artisan storage:link
   ```

8. **Serve the Application:**
   ```bash
   php artisan serve
   ```
   *Your application is now accessible at `http://localhost:8000`.*

---

## Default Login Credentials 🔐

After running the seeders (`php artisan db:seed`), you can access the platform using these default accounts:

| Role      | Email                  | Password   |
| :-------- | :--------------------- | :--------- |
| **Admin** | `admin@prayangshu.com` | `password` |
| **User**  | `user@prayangshu.com`  | `password` |

---

## Deployment

To deploy MockDasher to a production server:
1. **Environment Config:** Set `APP_ENV=production` and `APP_DEBUG=false` in `.env`.
2. **Build Assets:** Ensure `npm run build` is executed for production assets.
3. **Optimize Laravel:**
   ```bash
   php artisan optimize
   php artisan view:cache
   php artisan event:cache
   ```
4. **Permissions:** Ensure `storage` and `bootstrap/cache` are writable.
