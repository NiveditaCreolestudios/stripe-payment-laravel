# stripe-payment-php

Assuming your are done with complete setup of laravel.

To install stripe to your project using composer

`composer require stripe/stripe-php`

This will install stripe on your project with all the dependencies required, and you are ready to go.

There are two types of key
	Publishable-key
	Secret-key

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
