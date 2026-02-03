<?php

use App\Document;
use App\TranslatorJob;
use Illuminate\Database\Seeder;

class TranslatorJobsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(TranslatorJob::class, 10)
            ->create([ 'client_id' => 1 ])
            ->each(function ($job) {
                $job->documents()->save(factory(Document::class)->make());
            });
    }
}
