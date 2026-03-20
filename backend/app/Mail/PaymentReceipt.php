<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentReceipt extends Mailable
{
    use Queueable, SerializesModels;

    public $payment;
    public $rental;
    public $driver;
    public $vehicle;

    public function __construct($payment, $rental, $driver, $vehicle)
    {
        $this->payment = $payment;
        $this->rental = $rental;
        $this->driver = $driver;
        $this->vehicle = $vehicle;
    }

    public function build()
    {
        return $this->subject('Comprobante de Pago')
            ->view('emails.payment_receipt');
    }
}
