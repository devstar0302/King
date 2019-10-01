<?php
namespace App\Providers;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\ServiceProvider;
class EloquentSortableServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Builder::macro('sort', function () {
            return $this->orderBy('created_at', 'desc');
        });
    }
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}