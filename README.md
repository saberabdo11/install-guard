# Laravel Installation Server

This is a lightweight PHP server for handling Laravel installation scripts. It securely validates purchase codes and buyer usernames to ensure proper licensing and installation.

## Features

- **Purchase Code Validation**: Verify purchase codes and buyer details.
- **Domain Lock**: Ensure installation is limited to a single domain.
- **Secure API**: Handles installation requests via a secure API structure.
- **Customizable Code**: Allows for non-critical code customization while maintaining system integrity.

## Directory Structure

server/ ├── app/ │ ├── Classes/ │ │ ├── FileResponse.php # Handles file-related responses │ │ └── PurchaseValidator.php # Validates purchase codes and usernames │ ├── Controllers/ │ │ └── InstallationController.php # Main logic for installation process ├── data/ │ └── registred.json # Stores registered installations ├── routes/ │ ├── Router.php # Core routing logic │ ├── Request.php # Handles HTTP requests │ └── api.php # API routes for the server ├── files/ # Directory for additional installation files ├── output/ # Directory for processed output files ├── vendor/ # Composer dependencies ├── .htaccess # Apache configuration for the server ├── composer.json # Composer configuration file ├── composer.lock # Composer lock file └── index.php # Entry point for the server

## Installation

1. Clone the repository:

   git clone https://github.com/saberabdo11/install-guard.git

   cd install-guard

   Install dependencies via Composer:

   composer install
   Configure your server:

   Ensure the server has PHP 8.2 or higher.
   Make sure necessary extensions (e.g., cURL, mbstring) are enabled.
   Set permissions:

   Ensure the data/ directory is writable by the server.
   Start the server:

   Use PHP's built-in server for testing:
   php -S localhost:8080
