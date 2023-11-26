<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\AdminUser;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Product::factory(20)->create();
        AdminUser::factory(1)->create([
            "name" => "admin",
            "email" => env('PGADMIN_EMAIL'),
            "password" => bcrypt(env('PGADMIN_PASSWORD'))
        ]);
    }
}
