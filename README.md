This project was done in order to familiarize myself with the underlying workings of frameworks like Laravel, but while using only vanilla PHP to create and understand that functionality from scratch.

If you would like to run this locally, there are a few important steps to perform:

- In config/ there is a file called _db.php, which needs to be renamed to 'db.php' and filled with the credentials needed to access your MySQL database

- If you want to import a starter database with a few sample listings, that is available in data/database.sql

- There is a composer.json file in the main folder which specifies a PSR-4 autoloader, and you would need to have Composer installed, then run 'Composer install' in the folder where composer.json is located to set up the vendor/ folder with the autoloader.php needed to run this

- If you are running this on the PHP built-in server, it will be important to check that your php.ini has the following: remove the semicolon to uncomment the line ';extension=pdo_mysql', and the line ';extension_dir = "ext"' or else it might not be able to locate the MySQL driver

- The document root needs to be set to the 'public' folder, so if using the PHP built-in server, you could navigate to the main folder of the project, and then run 'php -S localhost:8000 -t public' to properly configure it

This application has the functionality to view job listings, and to register or log in as a user and post new job listings.  A job listing is associated with the user who created it and it can only be edited or deleted by that user.  There is also the ability to search listings that include a specified keyword and/or location.  The main features of this application are not about the UI or presentation but rather the routing, authorization middleware, and other backend functionality.
