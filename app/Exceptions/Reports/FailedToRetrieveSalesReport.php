<?php

namespace App\Exceptions\Reports;

use App\Exceptions\AbstractException;
use JetBrains\PhpStorm\Pure;

class FailedToRetrieveSalesReport extends AbstractException
{
    #[Pure] public function __construct(\Throwable $thrownException = null)
    {
        $responseMessage = "Oops! , ocorreu um erro inesperado ao processar o relatório de vendas, tente novamente. Se o erro persistir , contactar o suporte. ";
        $logMessage = "Ocorreu um erro ao retornar o relatório de vendas.";
        parent::__construct(responseMessage: $responseMessage, logMessage: $logMessage, thrownException: $thrownException);
    }
}
