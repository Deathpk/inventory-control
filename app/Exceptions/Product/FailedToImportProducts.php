<?php

namespace App\Exceptions\Product;

use App\Exceptions\AbstractException;
use JetBrains\PhpStorm\Pure;

class FailedToImportProducts extends AbstractException
{
    #[Pure] public function __construct()
    {
        $responseMessage = "O Arquivo importado está corrompido ou não é válido. O tamanho do arquivo deve ser de no máximo 3MB.";
        parent::__construct($responseMessage);
    }
}
