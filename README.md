# stripe-payment-laravel

Assuming your are done with complete setup of laravel.

To install stripe to your project using composer

`composer require stripe/stripe-php`

This will install stripe on your project with all the dependencies required, and you are ready to go.

There are two types of key
	-Publishable-key
	-Secret-key

We can set this in services.php

e.g
	
    `stripe' => [
        'model' => App\User::class,
        'key' => env('STRIPE_KEY','{publishable-key-here}'),
        'secret' => env('STRIPE_SECRET','{secret-key-here}'),
        'webhook' => [
            'secret' => env('STRIPE_WEBHOOK_SECRET'),
            'tolerance' => env('STRIPE_WEBHOOK_TOLERANCE', 300),
        ],
    ]`

And you can use this keys in your controller using

`Stripe::setApiKey(Config('services.stripe.key'));`

`Stripe::setApiKey(Config('services.stripe.secret'));`

You can find following things in `App\Http\Controllers`
    
    ## **createToken**
        * Create a stripe token through which we can make payments successfull.
    ## **createCustomer**
        * Create a stripe customer based on the token created and email address.
    ## **makePayment**
        * Based on token created from card details and customer created, initiate a charge that can make payment.
    ## **makeRefund**
        * If for some reason, user cancels the order, or there is a need to refund the user, then using the charge_id that we get in response on successfull payment, we can refund the customer either ful or partial.
    ## **makeTransfer**
        * Transfer fund from merchants account to individual account that are associated with stripe.
        
        
