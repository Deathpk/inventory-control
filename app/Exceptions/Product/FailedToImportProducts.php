<?php

namespace App\Exceptions\Product;

use App\Exceptions\AbstractException;
use JetBrains\PhpStorm\Pure;

class FailedToImportProducts extends AbstractException
{
    #[Pure] public function __construct(\Throwable $thrownException = null)
    {
        $responseMessage = "Ocorreu um erro inesperado ao importar os produtos vinculados a planilha. Por favor , tente novamente , caso o erro persista , contacte  o suporte.";
        $logMessage = "Erro ao importar produtos via anexo.";
        parent::__construct(responseMessage: $responseMessage, logMessage: $logMessage, thrownException: $thrownException);
    }
}
