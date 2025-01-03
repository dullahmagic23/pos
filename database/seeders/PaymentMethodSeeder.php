<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $paymentMethods = [
            ['name' => 'Cash'],
            ['name' => 'Tigo Pesa'],
            ['name' => 'Airtel Money'],
            ['name' => 'M-Pesa'],
            ['name' => 'Halopesa'],
            ['name' => 'NMB Mobile'],
            ['name' => 'CRDB Mobile'],
            ['name' => 'Bank Transfer'],
            ['name' => 'Visa'],
            ['name' => 'MasterCard'],
            ['name' => 'American Express'],
            ['name' => 'Discover'],
            ['name' => 'Paypal'],
            ['name' => 'Stripe'],
            ['name' => 'Google Pay'],
            ['name' => 'Apple Pay'],
            ['name' => 'Samsung Pay'],
            ['name' => 'Alipay'],
            ['name' => 'WeChat Pay'],
            ['name' => 'UnionPay'],
            ['name' => 'JCB'],
            ['name' => 'Diners Club'],
            ['name' => 'RuPay'],
            ['name' => 'BHIM UPI'],
            ['name' => 'Paytm'],
            ['name' => 'PhonePe'],
            ['name' => 'Mobikwik'],
            ['name' => 'Freecharge'],
            ['name' => 'Ola Money'],
            ['name' => 'Amazon Pay'],
            ['name' => 'Flipkart Pay'],
            ['name' => 'BHIM'],
            ['name' => 'Skrill'],
            ['name' => 'Neteller'],
            ['name' => 'WebMoney'],
            ['name' => 'Perfect Money'],
            ['name' => 'Payoneer'],
            ['name' => 'Payza'],
            ['name' => 'Payeer'],
            ['name' => 'AdvCash'],
            ['name' => 'Skrill'],
            ['name' => 'Neteller'],
            ['name' => 'WebMoney'],
            ['name' => 'Perfect Money'],
            ['name' => 'Payoneer'],
            ['name' => 'Payza'],
            ['name' => 'Payeer'],
            ['name' => 'AdvCash'],
            ['name' => 'Skrill'],
            ['name' => 'Neteller'],
            ['name' => 'WebMoney'],
            ['name' => 'Perfect Money'],
            ['name' => 'Payoneer'],
            ['name' => 'Payza'],
            ['name' => 'Payeer'],
            ['name' => 'AdvCash'],
            ['name' => 'Skrill'],
            ['name' => 'Neteller'],
            ['name' => 'WebMoney'],
        ];

        foreach ($paymentMethods as $paymentMethod) {
            \App\Models\PaymentMethod::create($paymentMethod);
        }
    }
}
