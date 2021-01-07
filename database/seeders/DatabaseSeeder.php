<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        DB::table('roles')->insert([
            ['name' => 'admin', 'code' => '9GdYRx-F*#', 'description' => 'administrator privileges'],
            ['name' => 'tutor', 'code' => 'K9fWw', 'description' => 'tutor privileges'],
            ['name' => 'student', 'code' => '6VcVV', 'description' => 'student privileges']
        ]);
        DB::table('subjects')->insert([
            ['name' => 'Biology'], ['name' => 'Chemistry'], ['name' => 'Physics Algebra'], ['name' => 'Physics Calculus'], ['name' => 'Algebra 1'], ['name' => 'Geometry'], ['name' => 'Algebra 2'], ['name' => 'Trigonometry'], ['name' => 'Precalculus'], ['name' => 'Calculus AB/BC'], ['name' => 'SAT Math 2'], ['name' => 'Macroeconomics'], ['name' => 'Microeconomics'], ['name' => 'Elementary English'], ['name' => 'SAT English']
        ]);
    }
}
