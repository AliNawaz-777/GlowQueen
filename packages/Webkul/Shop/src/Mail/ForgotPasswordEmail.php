<?php

namespace Webkul\Shop\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
/**
 * Subscriber Mail class
 *
 * @author  Arhamsoft (info@arhamsoft.com)
 *
 * @copyright 2020 Arhamsoft (pvt) Ltd (https://arhamsoft.com)
 */
class ForgotPasswordEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $forgotData;

    public function __construct($forgotData) {
        $this->forgotData = $forgotData;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->to($this->forgotData['email'])
                ->from(config('mail.from.address'))
                ->subject(trans('Password Reset'))
                ->view('shop::emails.customer.forget-password')->with('data',$this->forgotData);
    }
}