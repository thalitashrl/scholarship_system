<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Mahasiswa;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminUserController extends Controller
{
    /**
     * Tampilkan daftar seluruh pengguna beserta role dan profil akademiknya.
     */
    public function index()
    {
        $users = User::with(['roles', 'mahasiswa'])->orderBy('created_at', 'desc')->paginate(10);
        $roles = Role::orderBy('name', 'asc')->get();
        return view('admin.users.index', compact('users', 'roles'));
    }

    /**
     * Simpan pengguna baru (dan profil akademik jika mahasiswa).
     */
    public function store(Request $request)
    {
        if ($request->has('ipk') && $request->filled('ipk')) {
            $request->merge([
                'ipk' => str_replace(',', '.', $request->ipk),
            ]);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'role_id' => 'required|exists:roles,id',
        ]);

        $role = Role::findOrFail($request->role_id);

        if ($role->name === 'mahasiswa') {
            $request->validate([
                'nim' => 'required|string|unique:mahasiswa,nim',
                'prodi' => 'required|string|max:255',
                'fakultas' => 'required|string|max:255',
                'angkatan' => 'required|integer|min:2000|max:' . (date('Y') + 1),
                'ipk' => 'nullable|numeric|min:0|max:4.00',
            ]);
        }

        try {
            DB::beginTransaction();

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $user->roles()->attach($role->id);

            if ($role->name === 'mahasiswa') {
                Mahasiswa::create([
                    'user_id' => $user->id,
                    'nim' => $request->nim,
                    'prodi' => $request->prodi,
                    'fakultas' => $request->fakultas,
                    'angkatan' => $request->angkatan,
                    'ipk' => $request->ipk,
                ]);
            }

            DB::commit();

            return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menambahkan pengguna: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Perbarui pengguna (dan profil akademik jika mahasiswa).
     */
    public function update(Request $request, $id)
    {
        if ($request->has('ipk') && $request->filled('ipk')) {
            $request->merge([
                'ipk' => str_replace(',', '.', $request->ipk),
            ]);
        }

        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
            'role_id' => 'required|exists:roles,id',
        ]);

        $role = Role::findOrFail($request->role_id);

        if ($role->name === 'mahasiswa') {
            $mahasiswaId = $user->mahasiswa ? $user->mahasiswa->id : null;
            $request->validate([
                'nim' => 'required|string|unique:mahasiswa,nim,' . $mahasiswaId,
                'prodi' => 'required|string|max:255',
                'fakultas' => 'required|string|max:255',
                'angkatan' => 'required|integer|min:2000|max:' . (date('Y') + 1),
                'ipk' => 'nullable|numeric|min:0|max:4.00',
            ]);
        }

        try {
            DB::beginTransaction();

            $user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);

            if ($request->filled('password')) {
                $user->update([
                    'password' => Hash::make($request->password),
                ]);
            }

            $user->roles()->sync([$role->id]);

            if ($role->name === 'mahasiswa') {
                Mahasiswa::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'nim' => $request->nim,
                        'prodi' => $request->prodi,
                        'fakultas' => $request->fakultas,
                        'angkatan' => $request->angkatan,
                        'ipk' => $request->ipk,
                    ]
                );
            } else {
                // Hapus profil mahasiswa jika perubahannya berpindah role dari mahasiswa ke lainnya
                if ($user->mahasiswa) {
                    $user->mahasiswa->delete();
                }
            }

            DB::commit();

            return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memperbarui pengguna: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Hapus pengguna.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete(); // Cascading delete otomatis akan menghapus profil mahasiswa terkait di database

        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil dihapus.');
    }
}
