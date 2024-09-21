<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(){
        User::create([
           'name' => "admin",
           'email' => "admin@gmail.com",
           'password' => Hash::make("admin"),
           'role' => "admin"
        ]);
    }
    /**
     * Run the database seeds.
     */
    public function down(): void
    {
        //
    }
}
