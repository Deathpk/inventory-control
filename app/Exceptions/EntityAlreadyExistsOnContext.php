<?php

namespace App\Exceptions;

class EntityAlreadyExistsOnContext extends AbstractException
{
    public function __construct(string $reportedEntity)
    {
        $responseMessage = "Oops! , já existe um(a) $reportedEntity com os mesmos valores.";
        parent::__construct(responseMessage: $responseMessage);
    }
}