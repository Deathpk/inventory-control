<?php


namespace App\Exceptions;


use App\Exceptions\Interfaces\CustomException;
use App\Models\User;
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
    const USER_ENTITY_LABEL = 'Usuário';
    const COMPANY_ENTITY_LABEL = 'Companhia';

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
            $reportedEntityMessage = $this->resolveReportedEntityMessage();
            Log::error("{$reportedEntityMessage}. {$this->resolveDebuggingMessages()}");
        }
    }

    private function resolveReportedEntityMessage(): string
    {
        $loggedUser = Auth::user();

        if ($loggedUser instanceof User) {
            $companyId = $loggedUser->getCompany()->getId();
            return "{$this->logMessage}, o erro ocorreu com o usuário de ID : {$loggedUser->getId()} \n da Companhia de ID: {$companyId}.";
        }

        return "{$this->logMessage}, o erro ocorreu com a companhia de ID: {$loggedUser->getCompany()->getId()}.";
    }

    private function resolveDebuggingMessages(): string
    {
        if ($this->thrownException) {
            return "\n - Message: {$this->thrownException->getMessage()} \n - Trace: {$this->thrownException->getTraceAsString()}";
        }

        return " ";
    }

    public function getThrownResponse(): string
    {
        return $this->thrownException->getMessage();
    }
}
