## News App

# Requirement
1. PHP 8
2. Laravel 9

# Set up
1. Clone project directory
2. Install dependencies with the command: composer install

# Database setup
The steps below is to get you started with SQLite database. You may configure any other type of database of your choice.
1. Create  SQLite database file in the database folder (database.sqlite)
3. Configure the .env file to use SQLite: DB_CONNECTION=sqlite. Sample of this config is found in the .env.example file. You could simply rename the file to .env
4. Migrate the database tables using the command: php artisan migrate
5. Seed the database using the command: php artisan db:seed
6. Generate application key with the command: php artisan key:generate


# Credentials
The following user credential is created when you seed the database
- email: user@mail.com
- password: 1234
