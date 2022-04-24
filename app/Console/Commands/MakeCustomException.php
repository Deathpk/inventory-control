<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Prophecy\Doubler\Generator\ClassCreator;
use Symfony\Component\VarDumper\Caster\ClassStub;

class MakeCustomException extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:custom-exception {exceptionName}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a new Custom Exception , extending from AbstractException';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {// php artisan make:custom-exception Products/SeilaException
        //TODO REFATORAR SAPORRA E EXTRAIR PARA FUNÇÕES ALGUNS ROLÊS...
        $exceptionNameAndPath = $this->argument('exceptionName');
        $exceptionName = Str::contains('/', $exceptionNameAndPath)
            ? STR::after($exceptionNameAndPath, '/')
            : $exceptionNameAndPath
        ;
        $pathToExceptionFolder = "/Exceptions/$exceptionNameAndPath";
    }

    private function prepareStubFile()
    {

    }
}
