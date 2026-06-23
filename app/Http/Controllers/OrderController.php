<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use Junges\Kafka\Facades\Kafka;
use Junges\Kafka\Message\Message;

class OrderController extends Controller
{
    // 1. Menampilkan halaman toko utama
    public function index()
    {
        $products = Product::all(); // Mengambil produk sampel sepatu kita
        $orders = Order::orderBy('id', 'desc')->get(); // Mengambil riwayat pesanan

        return view('toko', compact('products', 'orders'));
    }

    // 2. Proses saat tombol "Beli" diklik
    public function checkout(Request $request)
    {
        // 1. Validasi input, pastikan product_id wajib ada dan terdaftar di DB
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1'
        ]);

        $product = Product::find($request->product_id);

        // Validasi jika stok tidak cukup sebelum masuk antrean
        if (!$product || $product->stock < $request->quantity) {
            return redirect()->back()->with('error', 'Stok tidak mencukupi!');
        }

        // 2. Simpan data order awal dengan status 'pending'
        // Catatan: Jika di tabel products Anda ada kolom 'price', ganti 150000 jadi $product->price
        $order = Order::create([
            'user_email'  => 'huyearka.usady@unmuhpnk.ac.id',
            'total_price' => $request->quantity * ($product->price ?? 150000),
            'status'      => 'pending'
        ]);

        // 3. LEMPAR DATA KE ANTREAN KAFKA
        $payload = [
            'user_email' => $order->user_email,
            'order_id'   => $order->id,
            'product_id' => (int)$request->product_id,
            'quantity'   => (int)$request->quantity
        ];

        // Kirim ke topic 'order-placed'
        \Junges\Kafka\Facades\Kafka::publish()
            ->onTopic('order-placed')
            ->withBody($payload)
            ->send();

        return redirect()->back()->with('success', 'Pesanan masuk antrean Kafka! Segera refresh halaman untuk melihat status pemotongan stok.');
    }

    // 3. Fitur Tambah Produk Baru atau Restock Stok
    public function addProduct(Request $request)
    {
        $request->validate([
            'product_name' => 'required|string|max:255',
            'stock_added' => 'required|integer|min:1'
        ]);

        // Membuat produk baru berdasarkan input nama dan stok dari user
        Product::create([
            'name' => $request->product_name,
            'stock' => $request->stock_added
        ]);

        return redirect()->back()->with('success', 'Produk "' . $request->product_name . '" berhasil ditambahkan!');
    }
}
