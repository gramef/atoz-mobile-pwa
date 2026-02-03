<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            RolesTableSeeder::class,
            ContactMethodsTableSeeder::class,
            UsersTableSeeder::class,
            LanguagesTableSeeder::class,
            SkillsTableSeeder::class,
            InterpreterJobsTableSeeder::class,
            TranslatorJobsTableSeeder::class,
        ]);
    }
}
