<?php

namespace App\Http\Requests\Order;

use App\Enums\OrderStatus;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class OrderStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => 'required',
            'payment_type' => 'required',
            'pay' => 'required|numeric',
            'gcash_reference' => 'required|string|max:255',
            'proof_of_payment' => 'required|image|mimes:jpeg,png,jpg|max:2048',

        ];
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            'order_date' => Carbon::now()->timezone('Asia/Manila')->format('Y-m-d'),
            'order_status' => OrderStatus::PENDING->value,
            'total_products' => Cart::instance('order')->count(),
            'sub_total' => Cart::instance('order')->subtotal(),
            'vat' => Cart::instance('order')->tax(),
            'total' => Cart::instance('order')->total(),
            'invoice_no' => $this->generateInvoiceNumber(),
            'due' => (Cart::instance('order')->total() - $this->pay),
        ]);
    }

    private function generateInvoiceNumber(): string
    {
        $date = now()->timezone('Asia/Manila')->format('Ymd');
        $prefix = "INV-ORD-{$date}-";
        
        // Get the latest invoice number for today
        $latestOrder = DB::table('orders')
            ->where('invoice_no', 'like', "{$prefix}%")
            ->orderBy('created_at', 'desc')
            ->first();
        
        if ($latestOrder) {
            // Extract the sequence number and increment it
            $lastSequence = intval(substr($latestOrder->invoice_no, -4));
            $nextSequence = str_pad($lastSequence + 1, 4, '0', STR_PAD_LEFT);
        } else {
            // First order of the day
            $nextSequence = '0001';
        }
        
        return $prefix . $nextSequence;
    }
}