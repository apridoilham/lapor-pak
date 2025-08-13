<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateRtRwRequest; // Import request baru
use App\Models\Rt;
use App\Models\Rw;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert as Swal;

class RtRwController extends Controller
{
    public function index()
    {
        $rws = Rw::with('rts')->orderBy('number')->get();
        return view('pages.admin.rtrw.index', compact('rws'));
    }

    public function store(Request $request)
    {
        if ($request->has('number')) {
            $request->merge([
                'number' => str_pad($request->number, 3, '0', STR_PAD_LEFT),
            ]);
        }

        $request->validate([
            'number' => 'required|string|digits:3|unique:rws,number',
            'rt_count' => 'required|integer|min:1|max:99',
        ], [
            'rt_count.max' => 'Jumlah RT tidak boleh lebih dari 99.',
            'rt_count.min' => 'Jumlah RT minimal harus 1.',
        ]);

        DB::transaction(function () use ($request) {
            $rw = Rw::create(['number' => $request->number]);

            for ($i = 1; $i <= $request->rt_count; $i++) {
                Rt::create([
                    'rw_id' => $rw->id,
                    'number' => str_pad($i, 3, '0', STR_PAD_LEFT),
                ]);
            }
        });

        Swal::success('Berhasil', 'Data RW ' . $request->number . ' dengan ' . $request->rt_count . ' RT berhasil ditambahkan.');
        return redirect()->route('admin.rtrw.index');
    }

    // TAMBAHKAN METODE edit() BARU DI SINI
    public function edit(Rw $rw)
    {
        $rtCount = $rw->rts()->count();
        return view('pages.admin.rtrw.edit', compact('rw', 'rtCount'));
    }

    // TAMBAHKAN METODE update() BARU DI SINI
    public function update(UpdateRtRwRequest $request, Rw $rw)
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated, $rw) {
            $newRwNumber = str_pad($validated['number'], 3, '0', STR_PAD_LEFT);
            $newRtCount = (int) $validated['rt_count'];
            $oldRtCount = $rw->rts()->count();

            // 1. Update nomor RW
            $rw->update(['number' => $newRwNumber]);

            // 2. Sesuaikan jumlah RT
            if ($newRtCount > $oldRtCount) {
                // Jika jumlah RT bertambah, buat RT baru
                for ($i = $oldRtCount + 1; $i <= $newRtCount; $i++) {
                    Rt::create([
                        'rw_id' => $rw->id,
                        'number' => str_pad($i, 3, '0', STR_PAD_LEFT),
                    ]);
                }
            } elseif ($newRtCount < $oldRtCount) {
                // Jika jumlah RT berkurang, hapus RT yang berlebih
                Rt::where('rw_id', $rw->id)
                    ->where('number', '>', str_pad($newRtCount, 3, '0', STR_PAD_LEFT))
                    ->delete();
            }
        });

        Swal::success('Berhasil', 'Data RW ' . $validated['number'] . ' berhasil diperbarui.');
        return redirect()->route('admin.rtrw.index');
    }

    public function destroy(Rw $rw)
    {
        // Pastikan tidak ada relasi sebelum menghapus
        if ($rw->admins()->exists() || $rw->residents()->exists()) {
            Swal::error('Gagal', 'RW ini tidak dapat dihapus karena masih terikat dengan data Admin atau Pelapor.');
            return back();
        }

        $rwNumber = $rw->number;
        $rw->delete(); // On-delete cascade akan menghapus RT terkait
        Swal::success('Berhasil', 'Data RW ' . $rwNumber . ' dan semua RT di dalamnya berhasil dihapus.');
        return redirect()->route('admin.rtrw.index');
    }
}