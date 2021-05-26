<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use PayPal;

class PayPalPaymentController extends Controller
{
    protected $paypalProvider;

    public function handlePayment(Request $request)
    {
        $amount = $request->input('amount');
        $currency = $request->input('currency');

        PayPal::setProvider();
        $this->paypalProvider = PayPal::getProvider();
        $this->paypalProvider->setApiCredentials(config('paypal'));
        $this->paypalProvider->setAccessToken($this->paypalProvider->getAccessToken());

        $token_response = $this->paypalProvider->getAccessToken();

        // Get Client Token
        $ch = curl_init();
        $headers = array();
        $headers[] = "Content-Type: application/json";
        $headers[] = "Authorization: Bearer " . $token_response['access_token'];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_URL, "https://api-m.sandbox.paypal.com/v1/identity/generate-token");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        $result = curl_exec($ch);
        $result = json_decode($result, TRUE);
        curl_close($ch);

        $data = [
            'client_id' => config('paypal.sandbox.client_id'),
            'client_token' => $result['client_token'],
            'amount' => $amount,
            'currency' => $currency
        ];

        return view('checkout')->with('data', $data);

    }

    public function createOrder(Request $request) {
        $reqData = json_decode($request->getContent(), true);
        $amount = $reqData['amount'];
        $currency = $reqData['currency'];

        PayPal::setProvider();
        $this->paypalProvider = PayPal::getProvider();
        $this->paypalProvider->setApiCredentials(config('paypal'));
        $this->paypalProvider->setAccessToken($this->paypalProvider->getAccessToken());

        $order = $this->paypalProvider->createOrder([
            "intent"=> "CAPTURE",
            'application_context' => [
                'return_url' => 'http://localhost:8000/payment-success',
                'cancel_url' => 'http://localhost:8000/cancel-payment',
            ],
            "purchase_units"=> [[
                "amount"=> [
                    "currency_code"=> $currency,
                    "value"=> $amount
                ]
            ]
        ]]);

        return response()->json($order);
    }

    public function paymentSuccess() {
        return view('success');
    }
    
    public function paymentCancel() {
        return view('cancel');
    }
}