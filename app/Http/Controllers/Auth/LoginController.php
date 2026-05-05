<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $loginMode = $request->input('login_mode', 'email');

        if ($loginMode === 'employee') {
            return $this->loginWithEmployeeId($request);
        }

        return $this->loginWithEmail($request);
    }

    /**
     * Traditional email + password login via users table.
     */
    protected function loginWithEmail(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();

            if (isset($user->is_active) && !$user->is_active) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Akun Anda tidak aktif. Hubungi administrator.',
                ])->onlyInput('email');
            }

            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    /**
     * Employee ID login (no password required).
     * Finds or creates a shadow user linked to the employee.
     */
    protected function loginWithEmployeeId(Request $request)
    {
        $request->validate([
            'employee_id' => ['required', 'string'],
        ]);

        $employee = Employee::where('employee_id', $request->employee_id)->first();

        if (!$employee) {
            return back()->withErrors([
                'employee_id' => 'Employee ID tidak ditemukan.',
            ])->withInput(['employee_id' => $request->employee_id, 'login_mode' => 'employee']);
        }

        if (!$employee->is_active) {
            return back()->withErrors([
                'employee_id' => 'Akun employee tidak aktif. Hubungi administrator.',
            ])->withInput(['employee_id' => $request->employee_id, 'login_mode' => 'employee']);
        }

        // Find or create a shadow user linked to this employee
        $user = User::where('employee_id', $employee->id)->first();

        if (!$user) {
            $user = User::create([
                'name' => $employee->name,
                'email' => $employee->employee_id . '@employee.local',
                'password' => Hash::make(Str::random(32)), // random password, not used
                'role_id' => $employee->role_id,
                'is_active' => true,
                'employee_id' => $employee->id,
            ]);
        } else {
            // Sync role from employee
            $user->update([
                'name' => $employee->name,
                'role_id' => $employee->role_id,
                'is_active' => $employee->is_active,
            ]);
        }

        Auth::login($user, false);
        $request->session()->regenerate();

        return redirect()->intended('/dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
