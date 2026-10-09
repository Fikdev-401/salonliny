<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\RiwayatGajiKaryawan;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class KaryawanController extends Controller
{
    public function index(Request $request)
    {
        $query = Karyawan::with('user');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('nama', 'like', "%{$search}%");
        }

        if ($request->filled('status')) {
            $query->where('status_kerja', $request->status);
        }

        $karyawans = $query->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('karyawan.index', compact('karyawans'));
    }

    public function create()
    {
        return view('karyawan.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'tanggal_bergabung' => 'required|date',
            'no_hp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'jenis_kelamin' => 'nullable|in:L,P',
            'gaji_pokok' => 'required|numeric|min:0',
            'catatan' => 'nullable|string',
            'buat_user' => 'nullable|boolean',
            'username' => 'required_if:buat_user,1|nullable|string|unique:users,username',
            'email' => 'required_if:buat_user,1|nullable|email|unique:users,email',
        ]);

        $temporaryPassword = null;

        DB::transaction(function () use ($request, $validated, &$temporaryPassword) {
            $karyawan = Karyawan::create([
                'nama' => $validated['nama'],
                'jabatan' => $validated['jabatan'],
                'tanggal_lahir' => $validated['tanggal_lahir'] ?? null,
                'tanggal_bergabung' => $validated['tanggal_bergabung'],
                'no_hp' => $validated['no_hp'] ?? null,
                'alamat' => $validated['alamat'] ?? null,
                'jenis_kelamin' => $validated['jenis_kelamin'] ?? null,
                'gaji_pokok' => $validated['gaji_pokok'],
                'catatan' => $validated['catatan'] ?? null,
            ]);

            RiwayatGajiKaryawan::updateOrCreate(
                [
                    'karyawan_id' => $karyawan->id,
                    'berlaku_mulai' => $validated['tanggal_bergabung'],
                ],
                [
                    'gaji_pokok' => $validated['gaji_pokok'],
                    'catatan' => $validated['catatan'] ?? 'Gaji pokok awal saat data karyawan dibuat.',
                    'created_by' => Auth::id(),
                ]
            );

            if ($request->boolean('buat_user')) {
                $roleKaryawan = Role::where('slug', 'karyawan')->firstOrFail();
                $temporaryPassword = Str::password(12);
                
                $user = User::create([
                    'nama' => $validated['nama'],
                    'username' => $validated['username'],
                    'email' => $validated['email'],
                    'password' => Hash::make($temporaryPassword),
                    'role_id' => $roleKaryawan->id,
                    'no_hp' => $validated['no_hp'] ?? null,
                    'alamat' => $validated['alamat'] ?? null,
                    'must_change_password' => true,
                ]);

                $karyawan->update(['user_id' => $user->id]);
            }
        });

        $message = 'Karyawan berhasil ditambahkan.';
        if ($temporaryPassword) {
            $message .= " Password sementara: {$temporaryPassword}.";
        }

        return redirect()->route('karyawan.index')->with('success', $message);
    }

    public function show(Karyawan $karyawan)
    {
        $karyawan->load(['absensis' => function ($q) {
            $q->orderBy('tanggal', 'desc')->limit(30);
        }, 'riwayatGajis']);
        
        // Hitung statistik layanan bulan ini
        $bulanIni = now()->startOfMonth();
        $layananBulanIni = \App\Models\TransaksiDetail::where('karyawan_utama_id', $karyawan->id)
            ->whereHas('transaksi', function ($query) use ($bulanIni) {
                $query->whereBetween('tanggal_transaksi', [
                    $bulanIni->copy()->startOfMonth(),
                    $bulanIni->copy()->endOfMonth(),
                ]);
            })
            ->count();

        return view('karyawan.show', compact('karyawan', 'layananBulanIni'));
    }

    public function edit(Karyawan $karyawan)
    {
        return view('karyawan.edit', compact('karyawan'));
    }

    public function update(Request $request, Karyawan $karyawan)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'tanggal_bergabung' => 'required|date',
            'no_hp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'jenis_kelamin' => 'nullable|in:L,P',
            'status_kerja' => 'required|in:aktif,cuti,nonaktif',
            'gaji_pokok' => 'required|numeric|min:0',
            'catatan' => 'nullable|string',
        ]);

        $gajiPokokLama = (float) $karyawan->gaji_pokok;
        $karyawan->update($validated);

        if ($gajiPokokLama !== (float) $validated['gaji_pokok']) {
            RiwayatGajiKaryawan::updateOrCreate(
                [
                    'karyawan_id' => $karyawan->id,
                    'berlaku_mulai' => now()->toDateString(),
                ],
                [
                    'gaji_pokok' => $validated['gaji_pokok'],
                    'catatan' => 'Perubahan gaji pokok melalui form edit karyawan.',
                    'created_by' => Auth::id(),
                ]
            );
        }

        return redirect()->route('karyawan.index')
            ->with('success', 'Karyawan berhasil diperbarui.');
    }

    public function destroy(Karyawan $karyawan)
    {
        $karyawan->update(['status_kerja' => 'nonaktif']);
        return redirect()->route('karyawan.index')
            ->with('success', 'Karyawan berhasil dinonaktifkan.');
    }

    public function search(Request $request)
    {
        $search = $request->get('q');
        $karyawans = Karyawan::where('status_kerja', 'aktif')
            ->where('nama', 'like', "%{$search}%")
            ->limit(10)
            ->get();

        return response()->json($karyawans);
    }
}
