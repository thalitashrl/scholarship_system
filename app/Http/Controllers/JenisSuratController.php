<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JenisSurat;

class JenisSuratController extends Controller
{
    /**
     * Tampilkan daftar jenis surat.
     */
    public function index()
    {
        $jenisSurat = JenisSurat::orderBy('nama', 'asc')->paginate(10);
        return view('admin.jenis_surat.index', compact('jenisSurat'));
    }

    /**
     * Simpan jenis surat baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:master_jenis_surat,nama',
            'deskripsi' => 'nullable|string',
        ]);

        JenisSurat::create($request->all());

        return redirect()->route('admin.jenis-surat.index')->with('success', 'Jenis surat berhasil ditambahkan.');
    }

    /**
     * Perbarui jenis surat yang ada.
     */
    public function update(Request $request, $id)
    {
        $jenisSurat = JenisSurat::findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:255|unique:master_jenis_surat,nama,' . $jenisSurat->id,
            'deskripsi' => 'nullable|string',
        ]);

        $jenisSurat->update($request->all());

        return redirect()->route('admin.jenis-surat.index')->with('success', 'Jenis surat berhasil diperbarui.');
    }

    /**
     * Hapus jenis surat.
     */
    public function destroy($id)
    {
        $jenisSurat = JenisSurat::findOrFail($id);
        $jenisSurat->delete();

        return redirect()->route('admin.jenis-surat.index')->with('success', 'Jenis surat berhasil dihapus.');
    }
}
