<?php

use Illuminate\Database\Seeder;
use App\ContactMethod;

class ContactMethodsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $contactMethods = [
            ['id' => 1, 'contact_method' => 'email'],
            ['id' => 2, 'contact_method' => 'sms'],
            ['id' => 3, 'contact_method' => 'phone'],
        ];

        foreach ($contactMethods as $contactMethod) {
            ContactMethod::updateOrCreate([ 'id' => $contactMethod['id'] ], $contactMethod);
        }
    }
}
