<x-mail::message>
    # Halo, Terima Kasih Atas Pesanan Anda!

    Pesanan Anda dengan nomor **#{{ $order->id }}** telah berhasil masuk ke dalam sistem kami dan saat ini berstatus **{{ $order->status }}**.

    <x-mail::panel>
        **Detail Tagihan:**
        * Total Pembayaran: Rp {{ number_format($order->total_price, 0, ',', '.') }}
        * Status: Waiting Payment
    </x-mail::panel>

    Silakan lakukan pembayaran sesuai dengan total tagihan di atas untuk menyelesaikan transaksi Anda.

    Terima kasih,<br>
    {{ config('app.name') }}
</x-mail::message>