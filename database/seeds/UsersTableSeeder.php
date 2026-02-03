<?php

use App\User;
use App\Agent;
use App\Client;
use App\Organisation;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(User::class)->create([
            'email' => 'super-admin@example.com',
            'password' => 'password',
            'enabled' => 1,
        ])->assignRole(['admin', 'super-admin']);

        factory(User::class)->create([
            'email' => 'admin@example.com',
            'password' => 'password',
            'enabled' => 1,
        ])->assignRole('admin');

        $user = factory(User::class)->create([
            'email' => 'client@example.com',
            'password' => 'password',
            'enabled' => 1,
        ])->assignRole('client');

        $client = factory(Client::class)->create(['user_id' => $user->id]);
        $client->contactMethods()->attach([1, 2, 3]);

        factory(Organisation::class)->create(['client_id' => $client->id]);

        $newUser = factory(User::class)->create([
            'email' => 'new-client@example.com',
            'password' => 'password',
            'enabled' => 1,
        ])->assignRole('new-client');

        factory(Client::class)->create(['user_id' => $newUser->id])
            ->contactMethods()
            ->attach(2);

        $archivedUser = factory(User::class)->create([
            'email' => 'archived-client@example.com',
            'password' => 'password',
            'enabled' => 1,
        ])->assignRole('client');

        factory(Client::class)->create(['user_id' => $archivedUser->id, 'archived' => 1])
            ->contactMethods()
            ->attach(1);

        factory(Agent::class)->create()
            ->user()
            ->update([
                'email' => 'agent@example.com',
                'password' => 'password',
            ]);

        factory(Agent::class, 20)->create();
    }
}
