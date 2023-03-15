<?php

namespace App\Exceptions\Auth;

use App\Exceptions\AbstractException;

class OldPasswordInvalid extends AbstractException
{
    public function __construct()
    {
        $responseMessage = "A senha antiga não condiz com a atual. Por favor, corrija e tente novamente.";
        parent::__construct(responseMessage: $responseMessage, statusCode: 401);
    }
}
