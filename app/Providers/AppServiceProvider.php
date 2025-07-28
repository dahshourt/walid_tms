<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Pagination\Paginator;
use App\Http\Repository\ChangeRequest\ChangeRequestRepository;
use App\Contracts\ChangeRequest\ChangeRequestRepositoryInterface;
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
<<<<<<< HEAD
        //URL::forceRootUrl('https://10.19.44.26'); // replace with your server IP
        Paginator::useBootstrap();
        Schema::defaultstringLength(191);
=======
        // URL::forceRootUrl('https://10.19.44.26/index.php'); // replace with your server IP
        // Paginator::useBootstrap();
        // Schema::defaultstringLength(191);
>>>>>>> 4f5cf38c3277ea55b9a091740684484b5af15008
    }
}
