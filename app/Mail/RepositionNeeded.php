<?php

namespace App\Mail;
use Illuminate\Support\Collection;

class RepositionNeeded extends BaseEmail
{
    const SUBJECT = 'Um ou mais produtos chegaram ao seu limite minimo de estoque.';
    const VIEW = 'mail.reposition-needed-email';

    public function __construct(Collection $products, array $company)
    {
        $data = [
            'companyName' => $company['name'],
            'products' => $products->toArray()
        ];

        parent::__construct(
            self::SUBJECT,
            $company['email'],
            $company['name'],
            $data,
            self::VIEW
        );
    }
}
