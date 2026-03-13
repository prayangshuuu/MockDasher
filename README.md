# MockDasher

MockDasher is a platform that simulates the IELTS exam environment and provides free practice tests.

## About the Project
MockDasher allows students to experience a real IELTS exam environment. It provides a comprehensive set of practice tests covering all four modules to help users prepare effectively for their actual exam.

## Features
* IELTS exam simulation
* Writing practice with word counter
* Speaking recording with speech-to-text
* Admin panel for managing test content
* Real exam style interface

## Tech Stack
* Laravel
* MySQL
* Tailwind CSS
* Alpine.js

## Installation

To run this project locally, follow these steps:

1. Clone the repository:
   ```bash
   git clone https://github.com/prayangshuuu/MockDasher.git
   ```

2. Navigate to the project directory:
   ```bash
   cd MockDasher
   ```

3. Install dependencies:
   ```bash
   composer install
   npm install
   ```

4. Configure the environment variables:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   *Make sure to update the database credentials in the `.env` file.*

5. Run migrations and seed the database:
   ```bash
   php artisan migrate --seed
   ```

6. Start the development servers:
   ```bash
   php artisan serve
   ```
   In a separate terminal, run:
   ```bash
   npm run dev
   ```

## Project Structure
* `app/`: Contains the core logic, Models, Controllers, and middleware of the application.
* `routes/`: Defines all the web and API routes.
* `resources/`: Stores raw assets, views (Blade templates), CSS, and JavaScript files.
* `database/`: Contains database migrations, factories, and seeders.

## Future Roadmap
* Writing module
* Speaking module
* Listening module
* Reading module
* AI scoring system

## License
This project is open-source.
