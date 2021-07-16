<?php

namespace Webkul\Customer\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Registration Mail class
 *
 * @author    Arhamsoft (Pvt) Ltd.
 * @copyright 2020 Arhamsoft (Pvt) Ltd (https://arhamsoft.com)
 */
class ContactUs extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    public function __construct($data) {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
       // return $this->to(env('ADMIN_MAIL_TO'))
        return $this->from(config('mail.from.address'))
                ->to($this->data['email'])
                ->subject($this->data['subject'])
                ->view('shop::emails.customer.contactus')->with('data', $this->data);
    }
}