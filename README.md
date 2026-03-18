# MockDasher

MockDasher is a modern, premium SaaS platform tailored for IELTS preparation. It provides students with full-length mock tests spanning all four modules (Listening, Reading, Writing, and Speaking), detailed test history tracking, and analytics to visualize band score progression.

## Features Let Architecture Do the Work 🚀
- **Comprehensive Mock Tests:** Simulates real IELTS exams with timed, module-based progression.
- **Dynamic Dashboards:** Provides personalized test recommendations, score analysis, and progress charts.
- **Robust Architecture:** Employs Service layers to decouple high-complexity logic from controllers, prioritizing clean, scalable MVC execution.
- **Secure by Default:** Utilizes strict Laravel FormRequests for structural validation and authorization logic.

---

## Architecture Overview

This project is built using the **Laravel 11** ecosystem, emphasizing maintainability, scalability, and adherence to SOLID principles:
- **Presentation Layer (Blade & Tailwind CSS):** A clean separation of UI styling. No hardcoded colors or design tokens are pushed from the backend; the UI adapts dynamically via configuration and data mapping.
- **Controllers Layer (HTTP):** Lean controllers that manage exclusively HTTP lifecycle events and leverage Dependency Injection for services.
- **Service Layer (Business Logic):** Extracting business domains into dedicated classes (e.g., `TestHistoryService`, `DashboardStatsService`) ensuring the "Fat Models, Skinny Controllers" paradigm is respected while avoiding model bloat.
- **Aggregated Queries:** Avoiding `$model->get()->avg()` N+1 equivalents by strictly using Database-level `->avg()` Eloquent aggregations.

## Prerequisites
Ensure your local development environment meets the following requirements:
- **PHP** 8.2 or higher
- **Composer** v2+
- **Node.js** & **NPM** (for compiling frontend assets)
- **MySQL** 8.0+ or another preferred equivalent relational database

## Installation Steps

1. **Clone the repository:**
   ```bash
   git clone <repository-url>
   cd MockDasher
   ```

2. **Install PHP Dependencies:**
   ```bash
   composer install
   ```

3. **Install & Build Frontend Assets:**
   ```bash
   npm install
   npm run build
   ```

4. **Environment Setup:**
   Duplicate the `.env.example` file and configure your local environment variables.
   ```bash
   cp .env.example .env
   ```
   **Crucial `.env` values to set:**
   - `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`

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

## Deployment

To deploy MockDasher to a production server (like Forge, Vapor, or a VPS):

1. **Ensure environment optimization:**
   In your `.env` file, change `APP_ENV=production` and `APP_DEBUG=false`.
2. **Optimize Laravel:**
   ```bash
   php artisan optimize
   php artisan view:cache
   php artisan event:cache
   ```
3. **Queue Workers:**
   If you rely on asynchronous queues (e.g., for automated grading or mailing), ensure your supervisor process manages:
   ```bash
   php artisan queue:work
   ```
4. **Permissions:**
   Ensure the `storage` and `bootstrap/cache` directories are writable by the web server user (e.g., `www-data`).
