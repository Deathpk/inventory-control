<?php

namespace App\Exceptions;

use JetBrains\PhpStorm\Pure;

class RecordNotFoundOnDatabaseException extends AbstractException
{
    #[Pure] public function __construct(string $reportedEntity, int $entityId = null)
    {
        $entityId = $entityId ? "de ID: {$entityId}" : '';
        $responseMessage = "Não conseguimos achar um registro do(a) {$reportedEntity} {$entityId} no banco. Por favor , tente novamente.";
        parent::__construct(responseMessage: $responseMessage);
    }
}
