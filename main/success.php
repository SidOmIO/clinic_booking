<?php

require ('../vendor/autoload.php');
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$stripe_secret_key = $_ENV['STRIPE_SECRET_KEY'];

\Stripe\Stripe::setApiKey($stripe_secret_key);

// Fetch the checkout session to confirm payment
$session_id = $_GET['session_id'];

try {
    $session = \Stripe\Checkout\Session::retrieve($session_id);

    if ($session->payment_status == 'paid') {
        // Payment was successful
        echo '<h1>Payment Successful!</h1>';
        echo '<p>Thank you for your purchase.</p>';
        // Perform any additional processing like updating the database
    } else {
        echo '<h1>Payment not completed</h1>';
        echo '<p>Your payment could not be completed. Please try again.</p>';
    }
} catch (\Stripe\Exception\ApiErrorException $e) {
    echo '<h1>Error</h1>';
    echo '<p>' . $e->getMessage() . '</p>';
}

?>
