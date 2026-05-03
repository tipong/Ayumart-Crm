<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\CustomerAddress;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\View\View
     */
    public function showRegistrationForm()
    {
        return view('auth.customer-register');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'telepon' => ['required', 'string', 'max:20'],
            'alamat' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'telepon.required' => 'Nomor telepon wajib diisi',
            'alamat.required' => 'Alamat lengkap wajib diisi',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     * Only creates pelanggan (customer) accounts.
     * Also creates a default address in customer_addresses table.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        DB::beginTransaction();

        try {
            // Create user account with id_role = 5 (pelanggan)
            $user = User::create([
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'id_role' => 5, // 5 = pelanggan (customer)
            ]);

            // Create pelanggan data in tb_pelanggan
            $pelanggan = \App\Models\Pelanggan::create([
                'id_user' => $user->id_user,
                'nama_pelanggan' => $data['name'],
                'no_tlp_pelanggan' => $data['telepon'] ?? '',
                'alamat' => $data['alamat'] ?? '',
                'status_pelanggan' => 'aktif',
            ]);

            // Create default address in customer_addresses table
            CustomerAddress::create([
                'id_pelanggan' => $pelanggan->id_pelanggan,
                'label' => 'Alamat Utama', // Default label for registration address
                'alamat_lengkap' => $data['alamat'],
                'nama_penerima' => $data['name'],
                'no_telp_penerima' => $data['telepon'],
                'is_default' => true, // Set as default address
            ]);

            DB::commit();
            return $user;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
