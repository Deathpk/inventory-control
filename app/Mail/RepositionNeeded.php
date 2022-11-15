<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class RepositionNeeded extends AbstractEmail
{
    use Queueable, SerializesModels;

    const SUBJECT = 'Um ou mais produtos chegaram ao seu limite minimo de estoque.';
    const VIEW = 'mail.reposition-needed-email';

    private Collection $products;
    private array $company;

    public function __construct(Collection $products, array $company)
    {
        $data = [
            'companyName' => $company['name'],
            'products' => $products->toArray()
        ];

        parent::__construct(
            self::SUBJECT,
            'bettercallmiguel@gmail.com',//['email'],
            $company['name'],
            $data,
            self::VIEW
        );
    }
}
