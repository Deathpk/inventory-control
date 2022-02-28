<?php


namespace App\Exceptions;


use Exception;
use Illuminate\Support\Facades\Log;
use JetBrains\PhpStorm\Pure;

class AbstractException extends Exception
{
    protected string $responseMessage;
    protected string $logMessage;
    protected Exception|null $thrownException;

    #[Pure] public function __construct(string $responseMessage = '', string $logMessage = '', ?\Throwable $thrownException = null)
    {
        $this->responseMessage = $responseMessage;
        $this->logMessage = $logMessage;
        $this->thrownException = $thrownException;
        parent::__construct($this->responseMessage, $this->getCode());
    }

    public function report(): void
    {
        if($this->logMessage !== '') {
            Log::error("{$this->logMessage} - Message: {$this->thrownException->getMessage()} \n - Trace: {$this->thrownException->getTraceAsString()}");
        }
    }

    #[Pure] public function getThrownResponse(): string
    {
        return $this->thrownException->getMessage();
    }
}
