<?php

namespace Webkul\Admin\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * New Admin Mail class
 *
 * @author    Arhamsoft <info@arhamsoft.com>
 * @copyright 2020 Arhamsoft (pvt) Ltd (https://arhamsoft.com)
 */
class NewAdminNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The order instance.
     *
     * @var Order
     */
    public $order;


    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($order)
    {
        $this->order = $order;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
         return $this->to(config('mail.admin.address'))
                ->subject(trans('shop::app.mail.order.subject'))
                ->view('shop::emails.sales.new-admin-order');
    }
}