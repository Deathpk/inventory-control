<?php

namespace App\Exceptions;

class EntityDontExistsOnContext extends AbstractException
{
    public function __construct(string $reportedEntity)
    {
        $responseMessage = "Oops! , um(a) $reportedEntity não existe no contexto atual , portanto , não poderá ser atualizada.";
        parent::__construct(responseMessage: $responseMessage);
    }
}