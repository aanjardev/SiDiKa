<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BranchController extends Controller
{
    public function index(Request $request)
    {
        $query = Branch::query();

        // Search by nama or alamat
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%");
            });
        }

        // Sort by
        $sortBy = $request->input('sort_by', 'updated_at');
        $sortOrder = $request->input('sort_order', 'desc');

        if ($sortBy === 'nama') {
            $query->orderBy('nama', 'asc');
        } elseif ($sortBy === 'nama_desc') {
            $query->orderBy('nama', 'desc');
        } else {
            $query->orderBy('updated_at', $sortOrder);
        }

        $data_cabang = $query->paginate(10)->withQueryString();

        return view('admin.dataCabang', [
            'data_cabang' => $data_cabang,
            'search_term' => $request->input('search', ''),
            'sort_by' => $sortBy,
            'sort_order' => $sortOrder,
        ]);
    }

    public function create()
    {
        return view('admin.inputDataCabang');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => [
                'required',
                'string',
                'max:50',
                'regex:/^[A-Za-zÀ-ž\s\.,\-]+$/'
            ],
            'alamat' => [
                'required',
                'string',
                'max:150',
                'regex:/^[A-Za-z0-9À-ž\s\.,\-\/]+$/'
            ],
            'nomor_telepon' => [
                'required',
                'regex:/^(?:\+62|62|0)[0-9]{8,15}$/'
            ],
            'link_maps' => [
                'required',
                'url',
                'max:255',
                Rule::unique('perusahaan_cabang', 'link_maps'),
            ],
        ], [
            'nama.regex' => 'Nama cabang hanya boleh mengandung huruf, spasi, titik, koma, dan tanda hubung.',
            'alamat.regex' => 'Alamat hanya boleh huruf, angka, spasi, titik, koma, garis miring, dan tanda hubung.',
            'nomor_telepon.regex' => 'Nomor telepon harus berupa angka dan diawali dengan 0, 62, atau +62.',
            'link_maps.url' => 'Link Maps harus berupa URL yang valid.',
            'link_maps.unique' => 'Link Google Maps sudah terdaftar.',
        ]);

        Branch::create($validated);

        return redirect()->route('admin.branches.index')
            ->with('success', 'Cabang berhasil ditambahkan.');
    }
    public function edit(Branch $branch)
    {
        // Reuse view 'admin.inputDataCabang' tapi kirim data branch
        return view('admin.inputDataCabang', compact('branch'));
    }

    public function destroy($id)
    {
        $branch = Branch::findOrFail($id);
        $branch->delete();

        return redirect()->route('admin.branches.index')
            ->with('success', 'Cabang berhasil dihapus.');
    }

    public function update(Request $request, Branch $branch) // 🟢 Route Model Binding
    {
        $validated = $request->validate([
            'nama' => [
                'required',
                'string',
                'max:50',
                'regex:/^[A-Za-zÀ-ž\s\.,\-]+$/'
            ],
            'alamat' => [
                'required',
                'string',
                'max:150',
                'regex:/^[A-Za-z0-9À-ž\s\.,\-\/]+$/'
            ],
            'nomor_telepon' => [
                'required',
                'regex:/^(?:\+62|62|0)[0-9]{8,15}$/'
            ],
            'link_maps' => [
                'required',
                'url',
                'max:255',
                Rule::unique('perusahaan_cabang', 'link_maps')->ignore($branch->id),
            ],
        ], [
            'nama.regex' => 'Nama cabang hanya boleh mengandung huruf, spasi, titik, koma, dan tanda hubung.',
            'alamat.regex' => 'Alamat hanya boleh huruf, angka, spasi, titik, koma, garis miring, dan tanda hubung.',
            'nomor_telepon.regex' => 'Nomor telepon harus berupa angka dan diawali dengan 0, 62, atau +62.',
            'link_maps.url' => 'Link Maps harus berupa URL yang valid.',
            'link_maps.unique' => 'Link Google Maps sudah terdaftar.',
        ]);

        $branch->update($validated);

        return redirect()->route('admin.branches.index')
            ->with('success', 'Cabang berhasil diperbarui.');
    }
}
