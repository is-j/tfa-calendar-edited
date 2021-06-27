<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\Subject;
use App\Models\Language;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Role::insert([
            ['name' => 'admin', 'code' => '9GdYRx-F*#'],
            ['name' => 'tutor', 'code' => 'K9fWw'],
            ['name' => 'student', 'code' => '6VcVV']
        ]);
        Subject::insert([
            ['name' => 'Biology'], ['name' => 'Chemistry'], ['name' => 'Physics Algebra'], ['name' => 'Physics Calculus'], ['name' => 'Algebra 1'], ['name' => 'Geometry'], ['name' => 'Algebra 2'], ['name' => 'Trigonometry'], ['name' => 'Precalculus'], ['name' => 'Calculus AB/BC'], ['name' => 'Macroeconomics'], ['name' => 'Microeconomics'], ['name' => 'Elementary English'], ['name' => 'SAT English'], ['name' => 'Computer Science']
        ]);
        User::insert([
            ['name' => 'Administrator', 'email' => 'scheduler@tutoringforall.org', 'role_id' => 1, 'timezone' => 'America/Chicago', 'password' => Hash::make(config('app.adminpw'))]
        ]);
        Language::insert([
            ['name' => 'English'], ['name' => 'Spanish'], ['name' => 'Mandarin Chinese'], ['name' => 'Portuguese'], ['name' => 'Hindi'], ['name' => 'French']
        ]);
    }
}
