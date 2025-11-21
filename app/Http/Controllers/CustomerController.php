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

    public function show($id)
    {
        $pelanggan = Customer::findOrFail($id);
        return view('admin.inputDataPelanggan', compact('pelanggan'))->with('readOnly', true);
    }

    public function edit($id)
    {
        $pelanggan = Customer::findOrFail($id);
        return view('admin.inputDataPelanggan', compact('pelanggan'));
    }

    public function update(Request $request, $id)
    {
        $pelanggan = Customer::findOrFail($id);

        $validated = $request->validate([
            'nama' => 'required|string|max:50',
            'no_telp' => 'required|string|max:20',
            'identitas' => 'nullable|string|max:20',
            'jenis_kelamin' => 'required|in:L,P',
            'alamat' => 'nullable|string|max:100',
            'referensi' => 'nullable|string|max:100',
            'keterangan' => 'nullable|string',
        ], [
            'nama.required' => 'Nama pelanggan wajib diisi.',
            'no_telp.required' => 'Nomor telepon wajib diisi.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'jenis_kelamin.in' => 'Jenis kelamin harus L atau P.',
        ]);

        $pelanggan->update($validated);

        return redirect()->route('admin.customers.index')
                         ->with('success', 'Data pelanggan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $pelanggan = Customer::findOrFail($id);
        $pelanggan->delete();

        return redirect()->route('admin.customers.index')
                         ->with('success', 'Data pelanggan berhasil dihapus.');
    }
}
