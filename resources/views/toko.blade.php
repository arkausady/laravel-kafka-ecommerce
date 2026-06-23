<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>E-Commerce Enterprise</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body class="bg-light p-5">
    <div class="container">
        <h2 class="mb-4 text-center">🛒 E-Commerce Multi-Produk</h2>

        @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="row gap-4">
            <div class="col-md-7 d-flex flex-column gap-4">

                <div class="card p-3 shadow-sm">
                    <h4>📦 Tambah Produk Baru</h4>
                    <hr>
                    <form action="{{ route('add.product') }}" method="POST">
                        @csrf
                        <div class="mb-2">
                            <input type="text" name="product_name" class="form-control" placeholder="Nama Produk (Misal: Jaket, Topi)" required>
                        </div>
                        <div class="input-group mb-2">
                            <span class="input-group-text">Stok Awal</span>
                            <input type="number" name="stock_added" class="form-control" value="10" min="1">
                        </div>
                        <button type="submit" class="btn btn-success w-100">Tambah Produk</button>
                    </form>
                </div>

                <h4>👟 Produk Tersedia</h4>
                <div class="row row-cols-1 row-cols-md-2 g-3">
                    @forelse($products as $product)
                    <div class="col">
                        <div class="card p-3 shadow-sm h-100">
                            <h5>{{ $product->name }}</h5>
                            <p class="text-muted mb-1">ID Produk: #{{ $product->id }}</p>
                            <h4 class="text-danger">Stok: {{ $product->stock }}</h4>

                            <form action="{{ route('checkout') }}" method="POST" class="mt-auto">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <div class="input-group input-group-sm mb-2">
                                    <span class="input-group-text">Qty</span>
                                    <input type="number" name="quantity" class="form-control" value="1" min="1">
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm w-100">Beli</button>
                            </form>
                        </div>
                    </div>
                    @empty
                    <div class="col-12 text-muted">Belum ada produk. Silakan tambah lewat form di atas.</div>
                    @endforelse
                </div>
            </div>

            <div class="col-md-4 card p-3 shadow-sm h-100">
                <h4>📋 Riwayat Pesanan</h4>
                <hr>
                <table class="table table-striped table-sm">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $o)
                        <tr>
                            <td>#{{ $o->id }}</td>
                            <td>
                                <span class="badge bg-success">{{ $o->status }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="text-center text-muted">Belum ada pesanan</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>