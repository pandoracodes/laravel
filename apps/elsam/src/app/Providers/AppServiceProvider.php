<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Peserta;
use App\Observers\PesertaObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Peserta::observe(PesertaObserver::class);
        \App\Models\Program::observe(\App\Observers\ProgramObserver::class);
        \App\Models\Artikel::observe(\App\Observers\ArtikelObserver::class);
    }
}
