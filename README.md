# Backup Management System

This is a simple File Backup Management system built with PHP and Tailwind CSS. It allows users to upload, view, and manage files in categories. The system also supports automatic timestamping of file uploads and provides a secure login mechanism.

## Features

- **User Authentication**: Secure login/logout system.
- **File Upload**: Upload files with automatic timestamping.
- **Category Management**: Create, edit, and delete categories to organize your files.
- **File Management**: View and manage all uploaded files.
- **Responsive UI**: Built using Tailwind CSS for a clean, responsive interface.

## Installation

1. Clone this repository to your local machine or server.

   ```bash
   git clone https://github.com/CodeByMoriarty/Backup-Management-System.git

2. Set up a MySQL or MariaDB database and configure the database connection in db.php.

3. Create a table in your database for storing file information. 

## Usage

## Dashboard 
After logging in, users will be presented with the dashboard that includes the following options:

## Manage Categories 
Create and manage file categories.

## Upload Files
Upload files to the system.

## View Files
Browse and view uploaded files.

## File Upload
When uploading files, users can select the category to which the file belongs, and the system will automatically assign a timestamp for the file.

## File Management
Uploaded files are listed on the "View Files" page, where users can browse and manage them.

## Security
CSRF Protection: The system uses CSRF tokens to protect against Cross-Site Request Forgery (CSRF) attacks.
Session Management: A secure session management system is implemented to track users and their activities.

Dependencies
PHP >= 7.4
MySQL or MariaDB
Tailwind CSS (via CDN)
FontAwesome (for icons)

## Contributing

We welcome contributions to this project! If you'd like to help, please follow these steps:

1. Fork the repository.
2. Create a new branch for your changes.
3. Make your changes and commit them with clear messages.
4. Push your changes to your forked repository.
5. Open a pull request to merge your changes.

Please ensure that your code follows the existing style and that tests are included if necessary.