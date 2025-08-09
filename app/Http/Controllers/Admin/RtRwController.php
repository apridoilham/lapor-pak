<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Rt;
use App\Models\Rw;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert as Swal;
class RtRwController extends Controller
{
    public function index()
    {
        $rts = Rt::orderBy('number')->get();
        $rws = Rw::orderBy('number')->get();
        return view('pages.admin.rtrw.index', compact('rts', 'rws'));
    }

    public function storeRt(Request $request)
    {
        $request->validate(['number' => 'required|string|max:3|unique:rts,number']);
        Rt::create(['number' => $request->number]);
        Swal::success('Berhasil', 'Data RT baru berhasil ditambahkan.');
        return redirect()->route('admin.rtrw.index');
    }

    public function destroyRt(Rt $rt)
    {
        $rt->delete();
        Swal::success('Berhasil', 'Data RT berhasil dihapus.');
        return redirect()->route('admin.rtrw.index');
    }

    public function storeRw(Request $request)
    {
        $request->validate(['number' => 'required|string|max:3|unique:rws,number']);
        Rw::create(['number' => $request->number]);
        Swal::success('Berhasil', 'Data RW baru berhasil ditambahkan.');
        return redirect()->route('admin.rtrw.index');
    }

    public function destroyRw(Rw $rw)
    {
        $rw->delete();
        Swal::success('Berhasil', 'Data RW berhasil dihapus.');
        return redirect()->route('admin.rtrw.index');
    }
}