<?php

namespace App\Exceptions;

use JetBrains\PhpStorm\Pure;

class RecordNotFoundOnDatabaseException extends AbstractException
{
    #[Pure] public function __construct(string $reportedEntity)
    {
        $responseMessage = "Não conseguimos achar um registro do(a) {$reportedEntity} no banco. Por favor , tente novamente.";
        parent::__construct($responseMessage);
    }
}
