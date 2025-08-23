<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateRtRwRequest;
use App\Models\Rt;
use App\Models\Rw;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert as Swal;

class RtRwController extends Controller
{
    // ... (method index dan show tidak berubah) ...
    public function index()
    {
        $rws = Rw::withCount('residents')->orderBy('number')->get();
        return view('pages.admin.rtrw.index', compact('rws'));
    }

    public function show(Rw $rtrw)
    {
        $residentCount = $rtrw->rts()->withCount('residents')->get()->sum('residents_count');

        return view('pages.admin.rtrw.show', [
            'rw' => $rtrw,
            'residentCount' => $residentCount,
        ]);
    }

    public function store(Request $request)
    {
        if ($request->has('number')) {
            $request->merge([
                'number' => str_pad($request->number, 2, '0', STR_PAD_LEFT),
            ]);
        }

        $validator = Validator::make($request->all(), [
            'number' => 'required|string|digits:2|unique:rws,number',
            // PERUBAHAN DI SINI: integer -> numeric
            'rt_count' => 'required|numeric|min:1|max:99',
        ], [
            'number.digits' => 'Nomor RW harus terdiri dari 2 digit.',
            'number.unique' => 'Nomor RW ini sudah ada.',
            'rt_count.max' => 'Jumlah RT tidak boleh lebih dari 99.',
            'rt_count.min' => 'Jumlah RT minimal harus 1.',
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.rtrw.index')
                ->withErrors($validator, 'store')
                ->withInput();
        }

        DB::transaction(function () use ($request) {
            $rw = Rw::create(['number' => $request->number]);
            for ($i = 1; $i <= $request->rt_count; $i++) {
                Rt::create([
                    'rw_id' => $rw->id,
                    'number' => str_pad($i, 2, '0', STR_PAD_LEFT),
                ]);
            }
        });

        Swal::success('Berhasil', 'Data RW ' . $request->number . ' berhasil ditambahkan.');
        return redirect()->route('admin.rtrw.index');
    }

    // ... (sisa method tidak berubah) ...
    public function edit(Rw $rtrw)
    {
        $rtCount = $rtrw->rts()->count();
        return view('pages.admin.rtrw.edit', ['rw' => $rtrw, 'rtCount' => $rtCount]);
    }

    public function update(UpdateRtRwRequest $request, Rw $rtrw)
    {
        $validated = $request->validated();
        DB::transaction(function () use ($validated, $rtrw) {
            $newRwNumber = str_pad($validated['number'], 2, '0', STR_PAD_LEFT);
            $newRtCount = (int) $validated['rt_count'];
            $oldRtCount = $rtrw->rts()->count();
            $rtrw->update(['number' => $newRwNumber]);
            if ($newRtCount < $oldRtCount) {
                $rtsToDelete = Rt::where('rw_id', $rtrw->id)
                    ->where('number', '>', str_pad($newRtCount, 2, '0', STR_PAD_LEFT))
                    ->withCount('residents')
                    ->get();
                foreach ($rtsToDelete as $rt) {
                    if ($rt->residents_count > 0) {
                        throw new \Exception('Gagal mengurangi jumlah RT karena RT ' . $rt->number . ' masih memiliki data warga.');
                    }
                }
                $rtsToDelete->each->delete();
            } elseif ($newRtCount > $oldRtCount) {
                for ($i = $oldRtCount + 1; $i <= $newRtCount; $i++) {
                    Rt::create(['rw_id' => $rtrw->id, 'number' => str_pad($i, 2, '0', STR_PAD_LEFT)]);
                }
            }
        });
        Swal::success('Berhasil', 'Data RW ' . str_pad($validated['number'], 2, '0', STR_PAD_LEFT) . ' berhasil diperbarui.');
        return redirect()->route('admin.rtrw.index');
    }

    public function destroy(Rw $rtrw)
    {
        $residentCount = $rtrw->rts()->withCount('residents')->get()->sum('residents_count');
        if ($residentCount > 0) {
            Swal::error('Gagal', 'RW ini tidak dapat dihapus karena masih terikat dengan data Warga.');
            return back();
        }
        $rwNumber = $rtrw->number;
        DB::transaction(function () use ($rtrw) {
            $rtrw->rts()->delete();
            $rtrw->delete();
        });
        Swal::success('Berhasil', 'Data RW ' . $rwNumber . ' dan semua RT di dalamnya berhasil dihapus.');
        return redirect()->route('admin.rtrw.index');
    }

    public function destroyRt(Rt $rt)
    {
        if ($rt->residents()->exists()) {
            Swal::error('Gagal', 'RT ' . $rt->number . ' tidak dapat dihapus karena masih memiliki data warga.');
            return back();
        }
        $rtNumber = $rt->number;
        $rwNumber = $rt->rw->number;
        $rt->delete();
        Swal::success('Berhasil', 'Data RT ' . $rtNumber . ' dari RW ' . $rwNumber . ' berhasil dihapus.');
        return back();
    }
}