<?php

namespace App\Providers;

use App\Events\Auth\RecoverPasswordRequested;
use App\Events\EmployeeInvited;
use App\Events\Products\UnitRemovedFromInventory;
use App\Events\Sales\SaleCreated;
use App\Http\Requests\Auth\RecoverPasswordRequest;
use App\Listeners\Auth\SendPasswordRecoveryRequestedEmail;
use App\Listeners\Sales\CheckIfSoldProductsNeedsReposition;
use App\Listeners\CreateInventoryReport;
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
            CreateInventoryReport::class,
            CreateSaleReport::class,
            CheckIfSoldProductsNeedsReposition::class
        ],
        UnitRemovedFromInventory::class => [
            CreateInventoryReport::class
        ],
        EmployeeInvited::class => [
            SendEmployeeInvitation::class
        ],
        RecoverPasswordRequested::class => [
            SendPasswordRecoveryRequestedEmail::class
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
