<?php

namespace App\Providers;

use App\Events\EmployeeInvited;
use App\Events\Sales\SaleCreated;
use App\Listeners\Sales\CheckIfSoldProductsNeedsReposition;
use App\Listeners\Sales\CreateProductSaleReport;
use App\Listeners\Sales\CreateSaleReport;
use App\Listeners\SendEmployeeInvitation;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        SaleCreated::class => [
            CreateProductSaleReport::class,
            CreateSaleReport::class,
            CheckIfSoldProductsNeedsReposition::class
        ],
        EmployeeInvited::class => [
            SendEmployeeInvitation::class
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
