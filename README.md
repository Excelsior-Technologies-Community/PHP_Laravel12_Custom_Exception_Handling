**PHP_Laravel12_Custome_Exception_Handling**

**Step 1: Install Laravel 12 Create Project**

Run command:

     Composer create –project laravel/laravel your folder name “^12.0”

**Step 2: Setup Database for .env file**

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=exception_hendling
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=file

**Step 3: Create exception_hendling Folder** 

app/exception_hendling

**Step 4: Create CustomException.php file**

file path:
C:\xampp\htdocs\PHP_Laravel12_Custom_Exception_Handling\app\exception_hendling\CustomException.php

<?php

namespace App\exception_hendling;

use Exception;

class CustomException extends Exception
{
    public function render()
    {
        return response()->json([
            'status' => false,
            'message' => $this->getMessage(),
        ], 400);
    }
}

**Step 5: Routes**

routes/web.php

<?php

use Illuminate\Support\Facades\Route;
use App\exception_hendling\CustomException;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/exception', function () {
    throw new CustomException("This is the custome exception");
});

**STEP 6: Start Server**

**Run:**
       php artisan serve
       
**Open:**
      http://127.0.0.1:8000/exception

**So you can see this type Output:**

 <img width="742" height="271" alt="image" src="https://github.com/user-attachments/assets/00232546-5abb-40aa-81bd-84777d3fc2f2" />

 **Project Folder Structure:**

 PHP_Laravel12_Custom_Exception_Handling
├── app/
│   ├── Exceptions/
│   │   ├── CustomException.php  
│
├── bootstrap/
│   └── app.php
│
├── routes/
│   └── web.php
│
├── .env

└── artisan



