<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

abstract class AbstractEmail extends Mailable
{
    use Queueable, SerializesModels;

    const DEFAULT_EMAIL = 'bettercallmiguel@gmail.com';
    const DEFAULT_FROM_NAME = 'Stock && Repo';

    public $mailSubject;
    public $toEmail;
    public $toName;
    public $carbonCopy;
    public $fromEmail;
    public $fromName;
    public $data;
    public $view;

    public function __construct(string $subject, string $to, string $toName, array $data, string $view, array $cc = [])
    {
        $this->mailSubject = $subject;
        $this->toEmail = $to;
        $this->toName = $toName ?? '';
        $this->carbonCopy = $cc;
        $this->fromEmail = self::DEFAULT_EMAIL;
        $this->fromName = self::DEFAULT_FROM_NAME;
        $this->data = $data;
        $this->view = $view;
    }

    public function build(): Mailable
    {
        $this->subject($this->mailSubject);
        $this->to($this->toEmail, $this->toName);
        $this->from($this->fromEmail, $this->fromName);

        if ($this->carbonCopy) {
            $this->cc($this->carbonCopy);
        }

        return $this->markdown($this->view, $this->data);
    }

}
