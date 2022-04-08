<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;

class RemoveProductsVide extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'product:vide';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for remove all product vide';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Product::select('id', 'is_vide')->where('is_vide', 1)->forceDelete();
        return 0;
    }
}
