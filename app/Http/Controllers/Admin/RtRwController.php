<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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

    public function destroy(Rw $rw)
    {
        $rw->delete();
        Swal::success('Berhasil', 'Data RW ' . $rw->number . ' dan semua RT di dalamnya berhasil dihapus.');
        return redirect()->route('admin.rtrw.index');
    }
}