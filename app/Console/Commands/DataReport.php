<?php

namespace App\Console\Commands;

use App\Http\Controllers\AccessController;
use App\Http\Controllers\AmoCrmController;
use Illuminate\Console\Command;

class DataReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $amo = new AmoCrmController(new AccessController());
        $amo->generate_data();
        return "Ok";
    }
}
