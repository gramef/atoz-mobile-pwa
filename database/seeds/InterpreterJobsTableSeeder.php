<?php

use App\InterpreterJob;
use Illuminate\Database\Seeder;

class InterpreterJobsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(InterpreterJob::class, 10)->create([ 'client_id' => 1 ]);
        factory(InterpreterJob::class, 1)->create([ 'client_id' => 2 ]);
    }
}
