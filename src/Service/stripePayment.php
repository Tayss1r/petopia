<?php

namespace App\Service;

use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use Stripe\Stripe;
use function Symfony\Component\DependencyInjection\Loader\Configurator\env;

class stripePayment
{
    public $redirectUrl;
    public function __construct() {
        Stripe::setApiKey($_ENV['STRIPE_SECRET']);
        Stripe::setApiVersion('2024-06-20');
    }

    /**
     * @throws ApiErrorException
     */
    public function startPayment($cart, $shippingCost): void
    {

        $cartProduct = $cart['cart'];
        $product = [
            [
                'quantity' => 1,
                'price' => $shippingCost,
                'name'=> 'shipping cost'
            ]
        ];

        foreach ($cartProduct as $value) {
            $productItem = [];
            $productItem['name'] = $value['product']->getName();
            $productItem['quantity'] = $value['quantity'];
            $productItem['price'] = $value['product']->getPrice();
            $product[] = $productItem;
        }



        $session = session::create([
            'line_items' => [
                array_map(fn (array $product) => [
                    'quantity' => $product['quantity'],
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => $product['name'],
                        ],
                        'unit_amount' => $product['price']*100,
                    ],
                ], $product )
            ],
            'mode' => 'payment',
            'cancel_url' => 'http://127.0.0.1:8000/payment/cancel',
            'success_url' => 'http://127.0.0.1:8000/payment/success',
            'billing_address_collection' => 'required',
            'shipping_address_collection' => [
                'allowed_countries' => ['FR','TN']
            ],
            'metadata' => [

            ]
        ]);
        $this->redirectUrl = $session->url;
    }

    public function getStripeRedirectUrl() {
        return $this->redirectUrl;
    }
}