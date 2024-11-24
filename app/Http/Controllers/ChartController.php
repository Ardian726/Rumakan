<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Products;

class ChartController extends Controller
{
    public function addToCart(Request $request)
    {
        // Pastikan customer sudah login
        if (!Auth::guard('customers')->check()) {
            return redirect()->route('customers-login')->withErrors([
                'message' => 'Silakan login untuk menambahkan produk ke keranjang.',
            ]);
        }

        $customers = Auth::guard('customers')->user();

        // Validasi input
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product_id = $request->input('product_id');
        $quantity = $request->input('quantity', 1);

        // Cari atau buat pesanan (order) untuk customer
        $order = Order::firstOrCreate(
            ['customer_id' => $customers->id, 'status' => 'pending'],
            ['order_date' => now(), 'total_amount' => 0]
        );

        // Pastikan produk ada
        $products = Products::findOrFail($product_id);

        // Tambahkan atau update detail pesanan (order_details)
        $orderDetails = OrderDetails::where('order_id', $order->id)
            ->where('product_id', $product_id)
            ->first();

        if ($orderDetails) {
            // Jika produk sudah ada di keranjang, tambahkan jumlahnya
            $orderDetails->quantity += $quantity;
        } else {
            // Jika produk belum ada di keranjang, buat entri baru
            $orderDetails = new OrderDetails();
            $orderDetails->order_id = $order->id;
            $orderDetails->product_id = $product_id;
            $orderDetails->quantity = $quantity;
        }

        // Hitung subtotal dan simpan
        $orderDetails->subtotal = $orderDetails->quantity * $products->price;
        $orderDetails->save();

        // Update total amount di tabel orders
        $order->total_amount = $order->orderDetails()->sum('subtotal');
        $order->save();

        return redirect()->back()->with('success', 'Produk berhasil ditambahkan ke keranjang.');
    }

    public function showChart()
    {
        // Pastikan customer sudah login
        if (!Auth::guard('customers')->check()) {
            return redirect()->route('customers-login')->withErrors([
                'message' => 'Silakan login untuk melihat keranjang.',
            ]);
        }

        $customer = Auth::guard('customers')->user();

        // Cari pesanan (order) dengan status pending
        $order = Order::where('customer_id', $customer->id)
            ->where('status', 'pending')
            ->with('orderDetails.product') // Ambil relasi orderDetails dan product
            ->first();

        // Jika tidak ada pesanan, tampilkan pesan kosong
        if (!$order) {
            return view('cart', ['orderDetails' => [], 'totalAmount' => 0]);
        }

        // Ambil detail pesanan
        $orderDetails = $order->orderDetails;

        // Total jumlah harga
        $totalAmount = $order->total_amount;

        return view('chart', compact('orderDetails', 'totalAmount'));
    }
}
