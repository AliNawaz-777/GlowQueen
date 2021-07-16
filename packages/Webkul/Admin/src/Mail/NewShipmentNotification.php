<?php

namespace Webkul\Admin\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * New Shipment Mail class
 *
 * @author    Arhamsoft <info@arhamsoft.com>
 * @copyright 2020 Arhamsoft (pvt) Ltd (https://arhamsoft.com)
 */
class NewShipmentNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The shipment instance.
     *
     * @var Shipment
     */
    public $shipment;

    /**
     * Create a new message instance.
     *
     * @param mixed $shipment
     * @return void
     */
    public function __construct($shipment)
    {
        $this->shipment = $shipment;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $order = $this->shipment->order;
        
       

        return $this->to($order->customer_email, $order->customer_full_name)
                 ->from($address = 'glowqueen@glowqueen.pk', $name = 'GlowQueen.pk')
                //  ->from(config('mail.from.address'))
                // ->from(env('SHOP_MAIL_FROM'))
                ->subject(trans('shop::app.mail.shipment.subject', ['order_id' => $order->increment_id]))
                ->view('shop::emails.sales.new-shipment');
    }
}
