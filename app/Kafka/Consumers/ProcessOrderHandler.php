<?php

namespace App\Kafka\Consumers;

use Junges\Kafka\Contracts\ConsumerMessage;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Support\Facades\Mail;
use App\Mail\InvoiceMail;
use Illuminate\Support\Facades\Log;

class ProcessOrderHandler
{
    public function __invoke(ConsumerMessage $message)
    {
        $payload = $message->getBody();

        Log::info("=== KAFKA CONSUMER BERHASIL MENANGKAP DATA ===");

        // PENGAMAN: Cek apakah payload berbentuk array atau objek
        if (is_array($payload)) {
            $userMail  = $payload['user_email'] ?? null;
            $orderId   = $payload['order_id'] ?? null;
            $productId = $payload['product_id'] ?? null;
            $quantity  = $payload['quantity'] ?? 0;
        } else if (is_object($payload)) {
            $userMail  = $payload->user_email ?? null;
            $orderId   = $payload->order_id ?? null;
            $productId = $payload->product_id ?? null;
            $quantity  = $payload->quantity ?? 0;
        } else {
            Log::error("Format payload tidak dikenali.");
            return;
        }

        Log::info("Memproses Order ID: " . ($orderId ?? 'Tidak ada ID'));

        if (!$orderId || !$productId) {
            Log::error("Data penting (Order ID/Product ID) hilang dari payload Kafka.");
            return;
        }

        // Cari data di database MySQL Docker
        $product = Product::lockForUpdate()->find($productId);
        $order   = Order::find($orderId);

        if ($product && $product->stock >= $quantity) {
            // Potong stok
            $product->decrement('stock', $quantity);

            // Ubah status order
            if ($order) {
                // 1. Update status di database dulu (supaya aman)
                $order->update(['status' => 'Waiting Payment']);

                // 2. Baru kirim emailnya
                try {
                    Mail::to($userMail)->send(new InvoiceMail($order));
                    Log::info("Email Invoice sukses dikirim ke: " . $userMail);
                } catch (\Exception $e) {
                    Log::error("Gagal mengirim email: " . $e->getMessage());
                }
            }
            Log::info("Stok sukses dipotong untuk Order ID {$orderId}. Email: " . $userMail);
        } else {
            if ($order) {
                $order->update(['status' => 'Failed out of Stock']);
            }
            Log::warning("Order ID {$orderId} Gagal! Stok Habis atau Produk Tidak Ditemukan.");
        }
    }
}
