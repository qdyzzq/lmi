<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public int $otp) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your OTP – Labor Market Information',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'auth.otpEmail',
        );
    }
}