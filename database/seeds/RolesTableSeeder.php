<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            [ 'id' => 1, 'name' => 'super-admin' ],
            [ 'id' => 2, 'name' => 'admin' ],
            [ 'id' => 3, 'name' => 'client' ],
            [ 'id' => 4, 'name' => 'new-client' ],
            [ 'id' => 5, 'name' => 'agent' ],
            [ 'id' => 6, 'name' => 'translator' ],
            [ 'id' => 7, 'name' => 'qualified-interpreter' ],
            [ 'id' => 8, 'name' => 'community-interpreter' ],
            [ 'id' => 9, 'name' => 'new-agent' ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate([ 'id' => $role['id'] ], $role);
        }
    }
}