<?php

namespace App\Console\Commands;

use App\Imports\UsersImport;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;

class add_agents extends Command
{

    protected $signature = 'add_agents';

    protected $description = 'Command description';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        Excel::import(new UsersImport, storage_path('agents.csv'));
    }
}