<div align="center">
  <h1>MockDasher</h1>
  <p><strong>A beautifully designed, feature-rich IELTS Exam Simulation Platform</strong></p>
</div>

<hr/>

## 🎯 About the Project

**MockDasher** is a comprehensive exam simulation platform that allows students to experience a real IELTS computer-based testing environment. By providing high-quality practice tests across all four modules (Reading, Listening, Writing, and Speaking), MockDasher helps learners prepare efficiently and confidently for their actual exams.

Built with a modern tech stack, MockDasher incorporates a premium user interface using the **Dwimik Design System** to offer an intuitive, dynamic, and responsive experience for both students and administrators.

---

## ✨ Key Features

- **Real Exam Interface**: A simulation of the official computer-based IELTS testing environment.
- **Comprehensive Modules**: Support for Reading, Listening, Writing, and Speaking.
- **Smart Test Features**:
  - Live Word Counter for Writing tasks.
  - Autosave functionality.
  - "Flag for Review" question management.
  - Interactive Answer Sheet navigation.
- **Admin Dashboard**: Powerful content management system to create, edit, and organize tests and question modules.
- **User Dashboard**: Personalized student portal to track progress, view recent activities, and analyze test results.
- **Dynamic UI/UX**: Premium aesthetic featuring Dwimik colors, modern typography (Inter font), fluid layouts, and micro-animations.

---

## 🛠 Tech Stack

MockDasher leverages industry-leading technologies:

- **Framework**: Laravel 11.x
- **Language**: PHP 8.3
- **Database**: MySQL
- **Frontend**: 
  - Tailwind CSS v4 (Powered by Dwimik Design System)
  - Blade Templating Engine
  - Alpine.js for lightweight reactivity
- **Assets/Bundling**: Vite

---

## 🚀 Getting Started

Follow these steps to set up the project locally on your machine.

### Prerequisites

- PHP >= 8.2
- Composer
- Node.js & npm
- MySQL / SQLite / PostgreSQL

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/prayangshuuu/MockDasher.git
   cd MockDasher
   ```

2. **Install PHP and Node dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment Setup**
   Copy the example environment file and generate a new application key:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   > ⚠️ **Note:** Ensure you configure your database connection settings (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`) in the `.env` file before proceeding. By default Laravel 11 uses SQLite.

4. **Run Migrations & Seed Database**
   Set up your database tables and populate them with essential default data (including demo accounts):
   ```bash
   php artisan migrate --seed
   ```

5. **Start the Development Servers**
   Run the Laravel backend server:
   ```bash
   php artisan serve
   ```
   In a separate terminal window, compile frontend assets:
   ```bash
   npm run dev
   ```

   You can now access the application at `http://127.0.0.1:8000`.

---

## 🔑 Demo Accounts

The database seeder automatically provides two pre-configured accounts for testing purposes:

### **Admin Account**
| Role | Email | Password |
|---|---|---|
| Administrator | `prayangshu073@gmail.com` | `MockDasher@TST` |

### **User Account**
| Role | Email | Password |
|---|---|---|
| Student | `prayangshuuu@gmail.com` | `MockDasher@TST` |

---

## 📂 Project Structure

- `app/` - Core PHP application logic (Controllers, Models, Middleware).
- `database/` - Migrations, seeders, and model factories.
- `resources/` - Blade views, CSS styles, JavaScript, and Dwimik design tokens.
- `routes/` - Web and API endpoint definitions.
- `public/` - Publicly accessible files and compiled assets.

---

## 🗺 Roadmap

- [x] Integrate Dwimik Design System
- [x] Build Real IELTS Computer-Based Interface
- [x] Live Word Counter & Answer Highlighting
- [ ] Automated AI answers scoring system (Writing/Speaking)
- [ ] Dark Mode Support
- [ ] Audio recording integration with Speech-to-Text for Speaking tasks

---

## 📄 License

This project is open-source.
