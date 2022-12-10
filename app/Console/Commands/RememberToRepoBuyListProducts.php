<?php

namespace App\Console\Commands;

use App\Exceptions\FailedToCheckProductsInNeedOfReposition;
use App\Services\CheckBuyListProductsInNeedOfRepositionService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;

class RememberToRepoBuyListProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:buylist';

    /**
     * This Command Will Check if there Is still any product
     * in need of reposition at the buylist , if so ,
     * It will notify the User via e-mail and push notifications.
     * It Runs Everyday at 8 AM.
     * @var string
     */
    protected $description = 'Check if there Is still any product in need of reposition at the buylist. It Runs Everyday at 8 AM';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(CheckBuyListProductsInNeedOfRepositionService $service): void
    {
        try {
            $service->checkBuyListOfEachCompany();
            echo"Checagem feita com sucesso!";
        } catch(Throwable $e) {
            Log::error(
                "Ocorreu um erro ao checar por produtos que necessitam de reposição via command.
                \n Message: \n {$e->getMessage()}
                \n StackTrace: \n {$e->getTraceAsString()}"
            );
        }
    }
}
