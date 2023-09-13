<?php

namespace Database\Seeders;

use App\Models\Profile;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class User extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = \App\Models\User::create([
            'name' => "Akbar",
            'username' => "employee",
            'password' => "secret",
            'role' => "employee"
        ]);

        $profile = new Profile();
        $profile->gender = "male";
        $profile->phone = "6281271762774";
        $profile->user()->associate($user);

        $profile->save();
    }
}
