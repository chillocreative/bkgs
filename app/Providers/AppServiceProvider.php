<?php

namespace App\Providers;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\User;
use App\Observers\AuditObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Builder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Schema::defaultStringLength(191);

        Model::shouldBeStrict(! app()->isProduction());

        if (class_exists(AuditObserver::class)) {
            User::observe(AuditObserver::class);
            Invoice::observe(AuditObserver::class);
            Payment::observe(AuditObserver::class);
            Setting::observe(AuditObserver::class);
        }
    }
}
