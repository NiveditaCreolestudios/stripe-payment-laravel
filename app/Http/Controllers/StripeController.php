<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
##strip
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Charge;
use Stripe\Token;
use Stripe\Transfer;

class StripeController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | createToken
    |--------------------------------------------------------------------------
    |
    | Author    : Senil Shah <senil@creolestudios.com>
    | Purpose   : Create a stripe token through which we can make payments successfull
    | In Params : Card Number, expiry month, expiry year, CVV
    | Date      : 18th April 2019
    |
    */

    public function createToken($cardNumber, $expMonth, $expYear, $cvv)
    {
    	try {
	    	Stripe::setApiKey(Config('services.stripe.key'));
	    	$token = Token::create([
	            'card' => [
		            'number'    => $cardNumber,
		            'exp_month' => $expMonth,
		            'exp_year'  => $expYear,
		            'cvc'       => $cvv,
	            ],
	        ]);
	        return $token;
    	} catch (\Exception $e) {
    		/* You can handle you exceptions here. For now, lets just print the error to see what cause the problem */
    		print_r($e->getMessage().'-:-'.$e->getLine()); die;
    	}
    }

    /*
    |--------------------------------------------------------------------------
    | createCustomer
    |--------------------------------------------------------------------------
    |
    | Author    : Senil Shah <senil@creolestudios.com>
    | Purpose   : Create a stripe customer based on the token created and email address
    | In Params : Card Number, expiry month, expiry year, CVV
    | Date      : 18th April 2019
    |
    */

    public function createCustomer($tokenId, $emailAddress)
    {
    	try {
	    	Stripe::setApiKey(Config('services.stripe.key'));
	    	$customer = Customer::create(array(
                'email'  => $emailAddress,
                'source' => $tokenId
            ));
	        return $customer;
    	} catch (\Exception $e) {
    		/* You can handle you exceptions here. For now, lets just print the error to see what cause the problem */
    		print_r($e->getMessage().'-:-'.$e->getLine()); die;
    	}
    }

    /*
    |--------------------------------------------------------------------------
    | makePayment
    |--------------------------------------------------------------------------
    |
    | Author    : Senil Shah <senil@creolestudios.com>
    | Purpose   : Based on token created from card details and customer created, 
    | initiate a charge that can make payment
    | In Params : Card details, amount to be paid
    | Date      : 18th April 2019
    |
    */

    public function makePayment(Request $request)
    {
    	try {
    		$Input = Input::all();
	    	Stripe::setApiKey(Config('services.stripe.key'));
	    	$token    = self::createToken($Input['card_number'], $Input['exp_month'], $Input['exp_year'], $Input['cvv']);
	    	$customer = self::createCustomer($token->id, $Input['email_address']);
	    	$charge = Charge::create(array(
                    'customer' => $customer->id,
                    /* Here we are multiplying the amount with 100 as stripe makes payment in cents. So to make it the actual payment amount, just multiply it with 100 */
                    'amount'   => $Input['amount']*100,
                    /* The currency in which you want the payment to be initiated */
                    'currency' => '{currency}'
                ));
	    	if($charge){
	    		/* 
	    		| Your code after successfull payment goes here. 
	    		| Successfull payment can also be verified by checking if paid key exists in $charge and is set to 1 i.e ($charge->paid==1) 
	    		| Save the charge id which can be accessed by $charge->id. This will be helpful in case of refunds.
	    		*/
	    	} else {
	    		/* Your Code for unsuccessfull payment goes here */
	    	}
	        return $token;
    	} catch (\Exception $e) {
    		/* You can handle you exceptions here. For now, lets just print the error to see what cause the problem */
    		print_r($e->getMessage().'-:-'.$e->getLine()); die;
    	}
    }

    /*
    |--------------------------------------------------------------------------
    | makeRefund
    |--------------------------------------------------------------------------
    |
    | Author    : Senil Shah <senil@creolestudios.com>
    | Purpose   : If for some reason, user cancels the order, or there is a need to refund the user, then using the charge_id that we get in response | on successfull payment, we can refund the customer either ful or partial
    | In Params : amount for partial refund, charge id. But this can be better fetched at back-end
    | Date      : 18th April 2019
    |
    */

    public function makeRefund(Request $request)
    {
    	try {
    		$Input = Input::all();
	    	Stripe::setApiKey(Config('services.stripe.key'));
	    	/* Full refund */
			$refund = \Stripe\Refund::create([
			    'charge' => '{charge-id}',/* the one which we stored in the charge method */
			]);
			/* Partial refund */
			$refund = \Stripe\Refund::create([
			    'charge' => '{charge-id}',/* the one which we stored in the charge method */
    			'amount' => '{amount-to-refund}',
			]);
	    	if($refund){
	    		/* Your code after successfull refund goes here. */
	    	} else {
	    		/* Your Code for unsuccessfull refund goes here */
	    	}
	        return $token;
    	} catch (\Exception $e) {
    		/* You can handle you exceptions here. For now, lets just print the error to see what cause the problem */
    		print_r($e->getMessage().'-:-'.$e->getLine()); die;
    	}
    }

    /*
    |--------------------------------------------------------------------------
    | makeTransfer
    |--------------------------------------------------------------------------
    |
    | Author    : Senil Shah <senil@creolestudios.com>
    | Purpose   : Transfer fund from merchants account to individual account that are associated with stripe.
    | In Params : amount to transfer, account_id of the individual who is to be transferred
    | Date      : 18th April 2019
    |
    */

    public function makeTransfer(Request $request)
    {
    	try {
    		$Input = Input::all();
	    	Stripe::setApiKey(Config('services.stripe.key'));
	    	$transfer = Transfer::create([
	    		/* Here we are multiplying the amount with 100 as stripe makes payment in cents. So to make it the actual payment amount, just multiply it with 100 */
	          	"amount"      => '{transfer-amount}'*100,
	          	/* 
	          	| The currency in which you want the payment to be initiated.
	          	| Ensure your currency, maybe due to currency difference, there might be a transfer failure.
	          	*/
	          	"currency"    => '{currency}',
	          	/*
				| Account of customer will only be created in countires supported by stripe. 
				| Ensure users country before creating users account or it may give you failure.
	          	*/
	          	"destination" => '{connected-stripe-account-is}',
	        ]);
	    	if($refund){
	    		/* Your code after successfull refund goes here. */
	    	} else {
	    		/* Your Code for unsuccessfull refund goes here */
	    	}
	        return $token;
    	} catch (\Exception $e) {
    		/* You can handle you exceptions here. For now, lets just print the error to see what cause the problem */
    		print_r($e->getMessage().'-:-'.$e->getLine()); die;
    	}
    }
}
