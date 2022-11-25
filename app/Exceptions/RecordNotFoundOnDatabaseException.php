<?php

namespace App\Exceptions;

use JetBrains\PhpStorm\Pure;

class RecordNotFoundOnDatabaseException extends AbstractException
{
    #[Pure] public function __construct(string $reportedEntity, int|string $entityId = null)
    {
        $entityId = $entityId ? "de ID: {$entityId}" : '';
        $responseMessage = "Não conseguimos achar um registro do(a) {$reportedEntity} {$entityId} na nossa base de dados. Por favor , tente novamente.";
        parent::__construct(responseMessage: $responseMessage);
    }
}
