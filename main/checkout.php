<?php
session_start();
if(!isset($_SESSION['login']) && !isset($_SESSION['type'])) {
header("location: ../index.php");
}
require ('../vendor/autoload.php');
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$stripe_secret_key = $_ENV['STRIPE_SECRET_KEY'];

\Stripe\Stripe::setApiKey($stripe_secret_key);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $items = $_POST['items'];

    $line_items = [];
    foreach ($items as $item) {
        // Convert price to integer (in cents)
        $unit_amount = (int)($item['price'] * 100);

        $line_items[] = [
            'quantity' => $item['quantity'],
            'price_data' => [
                'currency' => 'myr',  // Adjust as per your currency
                'unit_amount' => $unit_amount,
                'product_data' => [
                    'name' => $item['name']
                ]
            ]
        ];
    }

    $checkout_session = \Stripe\Checkout\Session::create([
        'mode' => 'payment',
        'success_url' => "{$_ENV['APP_BASE_PATH']}/main/consultation/details.php?id={$_POST['id']}&session_id={CHECKOUT_SESSION_ID}",
        'cancel_url' => "{$_ENV['APP_BASE_PATH']}/main/consultation/details.php?id={$_POST['id']}",
        'locale' => 'auto',
        'line_items' => $line_items
    ]);

    header("Location: " . $checkout_session->url);
    exit();
}
?>