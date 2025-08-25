<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateAdminProfileRequest;
use App\Models\User;
use App\Traits\FileUploadTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert as Swal;

class ProfileController extends Controller
{
    use FileUploadTrait; // Gunakan Trait

    public function index()
    {
        return view('pages.admin.profile.index', [
            'user' => Auth::user(),
        ]);
    }

    public function edit()
    {
        return view('pages.admin.profile.edit', [
            'user' => Auth::user(),
        ]);
    }

    public function update(UpdateAdminProfileRequest $request)
    {
        $validatedData = $request->validated();
        $user = User::findOrFail(Auth::id());

        if ($path = $this->handleFileUpload($request, 'avatar', 'assets/avatar', $user->avatar)) {
            // Jika avatar lama bukan dari Google (URL), hapus dari storage
            if ($user->avatar && !filter_var($user->avatar, FILTER_VALIDATE_URL)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $validatedData['avatar'] = $path;
        }

        $user->update($validatedData);

        Swal::success('Berhasil', 'Profil Anda berhasil diperbarui.');

        return redirect()->route('admin.profile.index');
    }
}