<?php

namespace Webkul\Customer\Http\Controllers;

use Hash;
use Webkul\Customer\Repositories\CustomerRepository;
use Webkul\Product\Repositories\ProductReviewRepository;
use Illuminate\Support\Facades\Mail;
use Webkul\Customer\Mail\ContactUs;
use Webkul\Customer\Mail\AdminContact;
/**
 * Customer controlller for the customer basically for the tasks of customers which will be
 * done after customer authentication.
 *
 * @author  Prashant Singh <prashant.singh852@webkul.com>
 * @copyright 2020 Arhamsoft (pvt) Ltd (https://arhamsoft.com)
 */
class CustomerMail extends Controller
{
    /**
     * Contains route related configuration
     *
     * @var array
     */
    protected $_config;

    /**
     * CustomerRepository object
     *
     * @var Object
    */
    protected $customerRepository;

    /**
     * ProductReviewRepository object
     *
     * @var array
    */
    protected $productReviewRepository;

    /**
     * Create a new controller instance.
     *
     * @param  \Webkul\Customer\Repositories\CustomerRepository $customer
     * @param  \Webkul\Product\Repositories\ProductReviewRepository $productReview
     * @return void
    */
    public function __construct(CustomerRepository $customerRepository, ProductReviewRepository $productReviewRepository)
    {
        // $this->middleware('customer');

        $this->_config = request('_config');

        $this->customerRepository = $customerRepository;

        $this->productReviewRepository = $productReviewRepository;
    }

    public function sendMail()
    {
        $data = collect(request()->input())->except('_token')->toArray();

        if ($data['full_name'] != "" && $data['email_address'] != "" && $data['email_subject'] != "" && $data['message'] != "") {
            $mail_data = [
                'name' => $data['full_name'],
                'email' => $data['email_address'],
                'subject' => $data['email_subject'],
                'message' => $data['message']
            ];
            $mail_status = Mail::queue(new ContactUs($mail_data));
            
            // echo $mail_status;
            $mail_status_admin = Mail::queue(new AdminContact($mail_data));
            // echo $mail_status_admin;die;

            if ($mail_status == 0) {
                session()->flash('message', 'We have received your message. We will contact you as soon as possible');
                 return redirect()->back();
                
            }
            
             if ($mail_status_admin == 0) {
                session()->flash('message', 'We have received your message. We will contact you as soon as possible');
                 return redirect()->back();
                
            }
           
            else {
                session()->flash('error', 'Something went wrong');
                return redirect()->back();
                
            }
            
        } else {
            session()->flash('error','ALL fields are required');
            return redirect()->back();
        }
        
    }
}
