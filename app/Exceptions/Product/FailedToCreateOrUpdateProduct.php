<?php


namespace App\Exceptions\Product;


use App\Exceptions\AbstractException;
use Exception;
use JetBrains\PhpStorm\Pure;

class FailedToCreateOrUpdateProduct extends AbstractException
{
    #[Pure] public function __construct(\Throwable $thrownException = null)
    {
        $responseMessage = "Oops! , ocorreu um erro inesperado ao criar ou atualizar um produto , tente novamente. Se o erro persistir , contactar o suporte. ";
        $logMessage = "Ocorreu um erro ao criar ou atualizar um produto.";
        parent::__construct($responseMessage, $logMessage, $thrownException);
    }
}
