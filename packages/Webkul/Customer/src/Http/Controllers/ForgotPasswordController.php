<?php

namespace Webkul\Customer\Http\Controllers;

use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Support\Facades\Password;
use Webkul\Customer\Repositories\CustomerRepository;
use Webkul\Customer\Repositories\CustomerGroupRepository;
use Illuminate\Support\Facades\Mail;
use Webkul\Shop\Mail\ForgotPasswordEmail;
use Illuminate\Support\Str;
use Cookie;

/**
 * Forgot Password controlller for the customer.
 *
 * @author    Prashant Singh <prashant.singh852@webkul.com>
 * @copyright 2020 Arhamsoft (pvt) Ltd (https://arhamsoft.com)
 */
class ForgotPasswordController extends Controller
{

    use SendsPasswordResetEmails;

    /**
     * Contains route related configuration
     *
     * @var array
     */
    protected $_config;

    /**
     * Create a new controller instance.
     *
     * @return void
     */

    /**
     * CustomerRepository object
     *
     * @var Object
    */
    protected $customerRepository;

    /**
     * CustomerGroupRepository object
     *
     * @var Object
    */
    protected $customerGroupRepository;

    /**
     * Create a new Repository instance.
     *
     * @param  \Webkul\Customer\Repositories\CustomerRepository      $customer
     * @param  \Webkul\Customer\Repositories\CustomerGroupRepository $customerGroupRepository
     * @return void
    */
    // public function __construct()
    // {
    //     $this->_config = request('_config');
    // }
    public function __construct(
        CustomerRepository $customerRepository,
        CustomerGroupRepository $customerGroupRepository
    )
    {
        $this->_config = request('_config');

        $this->customerRepository = $customerRepository;

        $this->customerGroupRepository = $customerGroupRepository;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view($this->_config['view']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    // public function store()
    // {
    //     try {
    //         $this->validate(request(), [
    //             'email' => 'required|email'
    //         ]);

    //         $response = $this->broker()->sendResetLink(
    //             request(['email'])
    //         );
    //         dd($response);
    //         if ($response == Password::RESET_LINK_SENT) {
    //             session()->flash('success', trans($response));

    //             return back();
    //         }

    //         return back()
    //             ->withInput(request(['email']))
    //             ->withErrors(
    //                 ['email' => trans($response)]
    //             );
    //     } catch (\Exception $e) {
    //         session()->flash('error', trans($e->getMessage()));

    //         return redirect()->back();
    //     }
    // }

    public function store()
    {
        try {
            $this->validate(request(), [
                'email' => 'required|email'
            ]);

            $email = request()->input('email');

            $check_email = $this->customerRepository->findOneWhere(['email' => $email]);

            if ($check_email) {
                $id = $check_email['id'];
                $new_password = Str::random();
                $crytped_password = bcrypt($new_password);
                $status = $this->customerRepository->update(['password' => $crytped_password], $id);
                $mail_data = [
                    'email' => $email,
                    'password' => $new_password,
                    'full_name' => $check_email['first_name'].' '.$check_email['last_name']
                ];

                try {
                    Mail::queue(new ForgotPasswordEmail($mail_data));

                    session()->flash('success', trans("Password sent to ".$email.". Check email for password"));

                    return back();
                } catch (\Exception $e) {
                    session()->flash('error', trans("Something went wrong"));

                    return back();
                }
                

                if ($status) {
                    
                } else {
                    session()->flash('error', trans("Something went wrong"));

                    return back();
                }
            } else {

                session()->flash('error', trans("Email Address is not vaild"));

                return back();
            }
            
        
        } catch (\Exception $e) {
            session()->flash('error', trans($e->getMessage()));

            return redirect()->back();
        }
    }

    /**
     * Get the broker to be used during password reset.
     *
     * @return \Illuminate\Contracts\Auth\PasswordBroker
     */
    public function broker()
    {
        return Password::broker('customers');
    }
}