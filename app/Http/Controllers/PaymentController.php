<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class PaymentController extends Controller
{




    public function verify(Request $request)
    {
        // $booking = Booking::find($id);

        $args = http_build_query(array(
            'token' => $request->token,
            'amount'  => 1000
        ));

        $url = "https://khalti.com/api/v2/payment/verify/";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $args);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $headers = ['Authorization: key test_secret_key_cd57d7d4d8f742c2999818ea920689d7'];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $data = json_decode($response, true);

        $paymentId = $data['idx'];
        $amount = $data['amount'];
        $paidAt = Carbon::parse($data['created_on'])->format('Y-m-d H:i:s');
        $payerName = $data['user']['name'];

        $payment = new Payment;
        $payment->payment_id = $paymentId;
        $payment->amount = $amount;
        // $payment->booking_id = $booking->id;
        $payment->paid_at = $paidAt;
        $payment->payer_name = $payerName;
        $payment->save();

        return response($response, $status_code);
    }

    public function Show()
    {
        $payment = Payment::all();
        return response($payment);
    }
}
