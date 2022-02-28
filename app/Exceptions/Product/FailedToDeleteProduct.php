<?php


namespace App\Exceptions\Product;


use App\Exceptions\AbstractException;
use Exception;
use JetBrains\PhpStorm\Pure;

class FailedToDeleteProduct extends AbstractException
{
    #[Pure] public function __construct(\Throwable $thrownException = null)
    {
        $responseMessage = "Oops! , ocorreu um erro inesperado ao deletar produto , tente novamente. Se o erro persistir , contactar o suporte. ";
        $logMessage = "Ocorreu um erro ao deletar um produto.";
        parent::__construct($responseMessage, $logMessage, $thrownException);
    }
}
