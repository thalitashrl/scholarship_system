<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Beasiswa;
use App\Services\NotificationService;

class BeasiswaController extends Controller
{
    // GET ALL
    public function index()
    {
        $data = Beasiswa::with('creator')->get();
        return $this->successResponse($data, 'Daftar beasiswa berhasil diambil');
    }

    // GET DETAIL
    public function show($id)
    {
        $data = Beasiswa::with('creator')->find($id);

        if (!$data) {
            return $this->errorResponse('Beasiswa tidak ditemukan', 404);
        }

        return $this->successResponse($data, 'Detail beasiswa berhasil diambil');
    }

    // CREATE
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required',
            'jenis' => 'required|in:internal,eksternal',
            'deskripsi' => 'required',
            'kuota' => 'required|integer',
            'deadline' => 'required|date',
            'status' => 'required|in:aktif,nonaktif'
        ]);

        $beasiswa = Beasiswa::create([
            'judul' => $request->judul,
            'jenis' => $request->jenis,
            'deskripsi' => $request->deskripsi,
            'kuota' => $request->kuota,
            'deadline' => $request->deadline,
            'status' => $request->status,
            'created_by' => $request->user()->id
        ]);

        if ($beasiswa->status === 'aktif') {
            NotificationService::notifyAllStudents(
                'Beasiswa Baru Tersedia',
                "Beasiswa baru '" . $beasiswa->judul . "' telah dibuka. Batas pendaftaran hingga " . $beasiswa->deadline->format('d-m-Y') . ".",
                'beasiswa',
                '/beasiswa/' . $beasiswa->id
            );
        }

        return $this->successResponse($beasiswa->load('creator'), 'Beasiswa berhasil dibuat', 201);
    }

    // UPDATE
    public function update(Request $request, $id)
    {
        $beasiswa = Beasiswa::find($id);

        if (!$beasiswa) {
            return $this->errorResponse('Beasiswa tidak ditemukan', 404);
        }

        $beasiswa->update($request->all());

        return $this->successResponse($beasiswa->load('creator'), 'Beasiswa berhasil diupdate');
    }

    // DELETE
    public function destroy($id)
    {
        $beasiswa = Beasiswa::find($id);

        if (!$beasiswa) {
            return $this->errorResponse('Beasiswa tidak ditemukan', 404);
        }

        $beasiswa->delete();

        return $this->successResponse(null, 'Beasiswa berhasil dihapus');
    }
}