# Project Overview
This project is built using the Laravel framework and utilizes Laravel Sail for local development. Laravel Sail provides a simple command-line interface for managing Docker containers, making it easy to set up a local development environment.

## Prerequisites
Before you begin, ensure you have the following installed on your machine:

- Docker Desktop (Download)
- Docker Compose (comes pre-installed with Docker Desktop)
- Git (to clone the repository)

# Getting Started
Follow these steps to get your Laravel project up and running on your local machine:

## Clone the Repository
Open your terminal and navigate to your desired directory. Clone the project repository with the following command:

```
git clone https://github.com/Hazel-Labs/hazel-web-app
```


## Navigate to Project Directory

```
cd hazel-web-app
```

## Install Dependencies
Run the following command to install PHP dependencies via Composer and NPM:

```
composer install
npm install
```

## Env Vars and Applicatoin Key

If this is your first time running the application you will need to run the following commands to copy the local env configs and to generate an enryption key for the application.

```
cp .env.example .env
./vendor/bin/sail artisan key:generate 
```

## Build and Start the Docker Containers
Once the dependencies are installed, start the Laravel Sail environment with the following command:

```
npm run dev
./vendor/bin/sail up
```

This command will start the necessary Docker containers and services defined in the docker-compose.yml file.

## Access the Application
After Sail has successfully started, you can access the application by navigating to the following URL in your web browser:

```
http://localhost
```


## Running Migrations (Optional)
If you need to set up the database schema, you can run the migrations using:

```
./vendor/bin/sail artisan migrate
```

## Stopping the Application
To stop the running containers, use the following command in a separate terminal:

```
./vendor/bin/sail down
```

## Seeding the Application Database
There is a set of test data for this application. You can load it by running the following commans.

```
./vendor/bin/sail artisan db:seed
```


## Additional Commands
You can execute other Artisan commands by prefixing them with Sail. For example, to run tests, use:

```
./vendor/bin/sail artisan test
```

### Seed Test Users Account Information

You can use the following email and password to log into the application.

```
email: admin@example.com
password: password
```

