<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JenisBeasiswa;

class JenisBeasiswaController extends Controller
{
    /**
     * Tampilkan daftar jenis beasiswa.
     */
    public function index()
    {
        $jenisBeasiswa = JenisBeasiswa::orderBy('nama', 'asc')->paginate(10);
        return view('admin.jenis_beasiswa.index', compact('jenisBeasiswa'));
    }

    /**
     * Simpan jenis beasiswa baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:master_jenis_beasiswa,nama',
            'deskripsi' => 'nullable|string',
        ]);

        JenisBeasiswa::create($request->all());

        return redirect()->route('admin.jenis-beasiswa.index')->with('success', 'Jenis beasiswa berhasil ditambahkan.');
    }

    /**
     * Perbarui jenis beasiswa yang ada.
     */
    public function update(Request $request, $id)
    {
        $jenisBeasiswa = JenisBeasiswa::findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:255|unique:master_jenis_beasiswa,nama,' . $jenisBeasiswa->id,
            'deskripsi' => 'nullable|string',
        ]);

        $jenisBeasiswa->update($request->all());

        return redirect()->route('admin.jenis-beasiswa.index')->with('success', 'Jenis beasiswa berhasil diperbarui.');
    }

    /**
     * Hapus jenis beasiswa.
     */
    public function destroy($id)
    {
        $jenisBeasiswa = JenisBeasiswa::findOrFail($id);
        $jenisBeasiswa->delete();

        return redirect()->route('admin.jenis-beasiswa.index')->with('success', 'Jenis beasiswa berhasil dihapus.');
    }
}
