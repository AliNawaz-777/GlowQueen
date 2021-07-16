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
class AdminContact extends Mailable
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
         return $this->to(config('mail.admin.address'))
                ->subject($this->data['subject'])
                ->view('shop::emails.customer.admin-contactus')->with('data', $this->data);
    }
}