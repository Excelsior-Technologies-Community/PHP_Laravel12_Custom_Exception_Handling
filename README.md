
**PHP_Laravel_12_Custom_Exception_Handling**

**Step 1: Install Fresh Laravel 12 Application**
⦁	We create a new Laravel 12 project for implementing custom exception handling.
⦁	Open Terminal / Command Prompt and run:
              composer create-project laravel/laravel:^12.0 exception_hendling
⦁	Move into the project folder:
              cd exception_hendling
⦁	Generate application key:
             php artisan key:generate

**Step 2: Configure Database (.env)**
       Open .env file and update database settings:
<img width="550" height="147" alt="image" src="https://github.com/user-attachments/assets/69eb3017-285a-4506-96da-8180aba78b14" />

 
Save the file.

Step 3: Understand Laravel 12 Exception Handling Structure
⦁	Laravel handles all exceptions using the Exception Handler class.
Default  File Path:
             C:\xampp\htdocs\exception_hendling\app\exception_hendling\CustomException.php

This file is responsible for:
⦁	Handling all application exceptions
⦁	Custom error responses
⦁	Custom error pages (404, 500, etc.)

Step 4: Customize Exception Handling in CustomException.php
⦁	We customize how Laravel responds to specific exceptions.
Open File:
             C:\xampp\htdocs\exception_hendling\app\exception_hendling\CustomException.php
 

Step 5: Create a Test Route for Exception
⦁	We create a route to manually throw an exception for testing.
Open File:
C:\xampp\htdocs\exception_hendling\routes\web.php

Add Route:
 

Step 6: Run Laravel 12 Project
Open project terminal and run:
           php artisan serve

Open browser:
          http://127.0.0.1:8000

Step 7: Test Custom Exception Pages
Open browser:
           http://127.0.0.1:8000/exception
 





