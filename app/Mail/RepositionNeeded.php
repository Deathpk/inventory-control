<?php

namespace App\Mail;

use App\Models\Company;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
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

//    public function build(): Mailable
//    {
//
//
//        $this->subject('');
//        $this->to($this->company['email'], $this->company['name']); //TODO adicionar o e-mail do admin e usuários.
//        $this->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
//
//        return $this->markdown('mail.reposition-needed-email', [
//            'companyName' => $this->company['name'],
//            'products' => $this->products->toArray()
//        ]);
//    }

}
