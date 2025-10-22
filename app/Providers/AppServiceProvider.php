<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Pagination\Paginator;
use App\Http\Repository\ChangeRequest\ChangeRequestRepository;
use App\Contracts\ChangeRequest\ChangeRequestRepositoryInterface;
use App\Http\Repository\Director\DirectorRepository;
use App\Contracts\Director\DirectorRepositoryInterface;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(ChangeRequestRepositoryInterface::class, ChangeRequestRepository::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        //URL::forceRootUrl('https://10.19.44.26/index.php'); // replace with your server IP
        Paginator::useBootstrap();
        Schema::defaultstringLength(191);
    }
}
