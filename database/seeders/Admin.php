<?php

namespace Database\Seeders;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class Admin extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::create([
            'name' => "Admin",
            'username' => "admin",
            'password' => "secret",
            'role' => "admin"
        ]);

        $profile = new Profile();
        $profile->gender = "male";
        $profile->phone = "6281271762774";
        $profile->user()->associate($user);

        $profile->save();
    }
}
