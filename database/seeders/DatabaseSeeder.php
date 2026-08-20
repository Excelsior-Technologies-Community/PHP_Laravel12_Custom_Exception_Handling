<?php

namespace Database\Seeders;

use App\Models\ExceptionLog;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        ExceptionLog::factory(50)->create();
    }
}
