<?php


namespace App\Exceptions;


use App\Exceptions\Interfaces\CustomException;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use JetBrains\PhpStorm\Pure;

class AbstractException extends Exception implements CustomException
{
    protected string $responseMessage;
    protected string $logMessage;
    protected \Throwable|null $thrownException;

    const PRODUCT_ENTITY_LABEL = 'Produto';
    const CATEGORY_ENTITY_LABEL = 'Categoria';
    const BRAND_ENTITY_LABEL = 'Marca';

    #[Pure] public function __construct(string $responseMessage = '', string $logMessage = '', ?\Throwable $thrownException = null)
    {
        $this->responseMessage = $responseMessage;
        $this->logMessage = $logMessage;
        $this->thrownException = $thrownException;
        parent::__construct($this->responseMessage, $this->getCode());
    }

    public function report(): void
    {
        $loggedUser = 1;// Auth::user()->id; TODO DEPOIS DE IMPLEMENTAR O MODULO DE AUTH TIRAR ISSO.

        if($this->logMessage !== '') {
            Log::error("{$this->logMessage} o erro aconteceu com o usuário: {$loggedUser}.\n - Message: {$this->thrownException->getMessage()} \n - Trace: {$this->thrownException->getTraceAsString()}");
        }
    }

    public function getThrownResponse(): string
    {
        return $this->thrownException->getMessage();
    }
}
