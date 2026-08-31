<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::updateOrCreate(['id' => 8], [
            'name' => 'Adel Salah',
            'email' => 'adel@portal365.org',
            'password' => bcrypt('Admin@123'),
            'is_tutor' => true,
        ]);

        $this->call(NewCoursesSeeder::class);
    }
}
