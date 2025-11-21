<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator; 

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::query();

        // Search by nama or nomor telepon
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('no_telp', 'like', "%{$search}%");
            });
        }

        // Sort by
        $sortBy = $request->input('sort_by', 'updated_at');
        $sortOrder = $request->input('sort_order', 'desc');
        
        if ($sortBy === 'nama') {
            $query->orderBy('nama', $sortOrder);
        } else {
            $query->orderBy('updated_at', $sortOrder);
        }

        $data_pelanggan = $query->paginate(10)->withQueryString();

        return view('admin.dataPelanggan', [
            'data_pelanggan' => $data_pelanggan,
            'search_term' => $request->input('search', ''),
            'sort_by' => $sortBy,
            'sort_order' => $sortOrder,
        ]);
    }

    public function create()
    {

        // return view('admin.inputDataPelanggan');
    }

    public function store(Request $request)
    {
        // 1. Validasi data yang masuk dari AJAX
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:50',
            'no_telp' => 'required|string|max:20',
            'identitas' => 'nullable|string|max:20',
            'jenis_kelamin' => 'required|in:L,P',
            'alamat' => 'nullable|string|max:100',
            'referensi' => 'nullable|string|max:100',
            'keterangan' => 'nullable|string',
        ]);

        // 2. Jika validasi gagal, kirim error kembali sebagai JSON
        if ($validator->fails()) {
            // Kirim 422 (Unprocessable Entity) agar 'catch' di JS bisa menangkapnya
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // 3. Buat customer baru
        // Ini aman karena Model Customer Anda sudah punya $fillable
        $customer = Customer::create($request->all());

        // 4. Kirim respons JSON yang sukses
        return response()->json([
            'success' => true,
            'message' => 'Customer berhasil ditambahkan!',
            'customer' => $customer // Kirim data customer baru kembali ke JS
        ]);
    }
}
