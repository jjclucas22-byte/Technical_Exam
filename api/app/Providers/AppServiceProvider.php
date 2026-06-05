<?php

namespace App\Providers;

use App\Models\Customer;
use App\Observers\CustomerObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Customer::observe(CustomerObserver::class);
    }
}
