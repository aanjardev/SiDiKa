<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Employee;
use App\Models\User;
use App\Services\EmailService;
use App\Models\Karyawan;

class PermissionsController extends Controller
{
    protected $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }
    public function index()
    {
        // Join users dan karyawan dengan pagination
        $user_data = DB::table('users')
            ->join('karyawan', 'users.id', '=', 'karyawan.id')
            ->select(
                'users.id',
                'users.name',
                'users.email',
                'users.role',
                'users.status',
                'karyawan.status as karyawan_status'
            )
            ->paginate(10); // 10 items per page

        return view('admin.permissions', compact('user_data'));
    }

    public function create()
    {
        $karyawan_data = DB::table('karyawan')
            ->whereNotIn('id', DB::table('users')->pluck('id'))
            ->where('status', 'aktif')
            ->get();
        return view('admin.inputPermissions', compact('karyawan_data'));
    }


    public function store(Request $request){
    $request->validate([
        'email' => 'required|email|unique:users,email',
        'role' => 'required'
    ]);

    $karyawan = Employee::findOrFail($request->karyawan_name);

    // Generate activation token
    $activationToken = bin2hex(random_bytes(32));

    $user = User::create([
        'id' => $karyawan->id,
        'name' => $karyawan->nama_lengkap,
        'email' => $request->email,
        'password' => Hash::make(Str::random(32)), // Random password yang tidak akan digunakan
        'role' => $request->role,
        'status' => 'pending',
        'activation_token' => $activationToken,
        'token_expiry' => now()->addHours(72), // Token berlaku 3 hari
    ]);

    // Kirim email activation
    $emailSent = $this->emailService->sendActivationEmail($user, $activationToken);

    if ($emailSent) {
        return redirect()->route('admin.permissions')
            ->with('success', 'User berhasil dibuat! Email aktivasi telah dikirim ke ' . $user->email);
    } else {
        // Jika email gagal dikirim, masih lanjutkan tapi beri warning
        return redirect()->route('admin.permissions')
            ->with('success', 'User berhasil dibuat! Namun email aktivasi gagal dikirim. Silakan generate ulang token.')
            ->with('warning', 'Email activation failed. Please check email configuration.');
    }
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $karyawan_data = Employee::whereNotIn('id', User::pluck('id'))
                                ->orWhere('id', $id) // agar user saat ini tetap muncul
                                ->where('status', 'aktif')
                                ->get();
        return view('admin.inputPermissions', compact('user', 'karyawan_data'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'password' => 'nullable|min:6',
            'email' => 'required|email',
        ]);

        $user = User::findOrFail($id);

        $user->update([
            'email' => $request->email,
            'password' => $request->password ? Hash::make($request->password) : $user->password,
        ]);

        return redirect()->route('admin.permissions')->with('success', 'User berhasil diperbarui!');
    }

    public function destroy($id)
    {
        // Prevent users from deactivating themselves
        if (Auth::id() && (int) $id === (int) Auth::id()) {
            return redirect()->route('admin.permissions')
                ->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
        }

        $user = User::findOrFail($id);
        $user->update(['status' => 'inactive']);
        // Sinkronkan status karyawan
        Karyawan::where('id', $id)->update(['status' => 'non-aktif']);

        return redirect()->route('admin.permissions')->with('success', 'User dinonaktifkan. Anda dapat mengaktifkannya kembali jika diperlukan.');
    }

    public function regenerateToken($id)
    {
        $user = User::findOrFail($id);
        
        if ($user->status !== 'pending') {
            return redirect()->route('admin.permissions')
                ->with('error', 'Token hanya bisa di-generate untuk user dengan status pending.');
        }

        // Generate new token dan extend expiry
        $activationToken = bin2hex(random_bytes(32));
        $user->update([
            'activation_token' => $activationToken,
            'token_expiry' => now()->addHours(72), // Extend 3 hari lagi
        ]);

        // Kirim email activation baru
        $emailSent = $this->emailService->sendActivationEmail($user, $activationToken);

        if ($emailSent) {
            return redirect()->route('admin.permissions')
                ->with('success', 'Token aktivasi berhasil diperbarui! Email baru telah dikirim ke ' . $user->email);
        } else {
            return redirect()->route('admin.permissions')
                ->with('success', 'Token aktivasi berhasil diperbarui! Namun email gagal dikirim.')
                ->with('warning', 'Email activation failed. Please check email configuration.');
        }
    }

    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:active,inactive',
        ]);

        // Prevent users from deactivating themselves
        if ($validated['status'] === 'inactive' && Auth::id() && (int) $id === (int) Auth::id()) {
            return redirect()->route('admin.permissions')
                ->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
        }

        $user = User::findOrFail($id);
        $user->update(['status' => $validated['status']]);

        // Sinkronkan status karyawan
        $karyawanStatus = $validated['status'] === 'active' ? 'aktif' : 'non-aktif';
        Karyawan::where('id', $id)->update(['status' => $karyawanStatus]);

        $message = $validated['status'] === 'active'
            ? 'User berhasil diaktifkan.'
            : 'User berhasil dinonaktifkan. User non-aktif tidak dapat login.';

        return redirect()->route('admin.permissions')->with('success', $message);
    }

}
