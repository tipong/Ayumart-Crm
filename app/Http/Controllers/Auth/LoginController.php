<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * Show the customer login form
     */
    public function showLoginForm()
    {
        return view('auth.customer-login');
    }

    /**
     * Show the staff login form
     */
    public function showStaffLoginForm()
    {
        return view('auth.staff-login');
    }

    /**
     * Handle staff login
     */
    public function staffLogin(Request $request)
    {
        try {
            $this->validate($request, [
                'email' => 'required|email',
                'password' => 'required',
            ]);

            // Attempt to login with email and password
            if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $request->filled('remember'))) {
                $user = Auth::user();

                // Check if user is staff (id_role 1-4)
                if ($user->id_role >= 1 && $user->id_role <= 4) {
                    $request->session()->regenerate();

                    // Get role name for success message
                    $roleNames = [
                        1 => 'Owner',
                        2 => 'Admin',
                        3 => 'Customer Service',
                        4 => 'Kurir',
                    ];

                    $roleName = $roleNames[$user->id_role] ?? 'Staff';

                    // Redirect to appropriate dashboard
                    $roleDashboards = [
                        1 => 'owner.dashboard',
                        2 => 'admin.dashboard',
                        3 => 'cs.dashboard',
                        4 => 'kurir.dashboard',
                    ];

                    $dashboardRoute = $roleDashboards[$user->id_role];

                    return redirect()->route($dashboardRoute)->with('success', "Selamat datang, {$roleName}!");
                } else {
                    // Not a staff account
                    Auth::logout();
                    return back()->with('error', 'Akun Anda bukan akun staff. Silakan gunakan halaman login pelanggan untuk masuk sebagai pelanggan.')
                               ->withInput($request->except('password'));
                }
            }

            return back()->with('error', 'Email atau password yang Anda masukkan salah. Silakan coba lagi.')
                       ->withInput($request->except('password'));

        } catch (\Illuminate\Database\QueryException $e) {
            // Database error - user friendly message
            Log::error('Database Error on Staff Login: ' . $e->getMessage());

            return back()->with('error', 'Maaf, terjadi kesalahan pada sistem. Silakan hubungi administrator atau coba beberapa saat lagi.')
                       ->withInput($request->except('password'));

        } catch (\Exception $e) {
            // General error - user friendly message
            Log::error('Error on Staff Login: ' . $e->getMessage());

            return back()->with('error', 'Terjadi kesalahan yang tidak terduga. Silakan coba lagi atau hubungi administrator jika masalah berlanjut.')
                       ->withInput($request->except('password'));
        }
    }

    /**
     * Redirect user based on their role after login.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        try {
            // Check if customer tries to use customer login
            if ($user->id_role == 5) {
                // Customer login - redirect to home page (not dashboard)
                $userName = $user->getName();
                return redirect()->route('home')->with('success', 'Selamat datang kembali, ' . $userName . '!');
            }

            // If staff uses customer login, logout and redirect
            if ($user->id_role >= 1 && $user->id_role <= 4) {
                Auth::logout();
                return redirect()->route('staff.login')->with('error', 'Akun staff harus login melalui halaman login staff. Silakan gunakan halaman login staff.');
            }

            // Default redirect
            return redirect()->route('home');

        } catch (\Exception $e) {
            Log::error('Error on authenticated redirect: ' . $e->getMessage());
            Auth::logout();
            return redirect()->route('login')->with('error', 'Terjadi kesalahan saat login. Silakan coba lagi.');
        }
    }
}

