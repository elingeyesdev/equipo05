<?php

namespace Modules\Incendios\Http\Controllers\Auth;

use Modules\Incendios\Http\Controllers\Controller;
use Modules\Incendios\Models\User;
use Modules\Incendios\Models\Voluntario;
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
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'cedula_identidad' => ['nullable', 'string', 'max:20', 'unique:users'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'ciudad' => ['nullable', 'string', 'max:100'],
            'zona' => ['nullable', 'string', 'max:100'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     * Creates user as VOLUNTARIO by default.
     *
     * @param  array  $data
     * @return \Modules\Incendios\Models\User
     */
    protected function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            // Create the base user
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'telefono' => $data['telefono'] ?? null,
                'cedula_identidad' => $data['cedula_identidad'] ?? null,
            ]);

            // Create voluntario profile (default role for registration)
            Voluntario::create([
                'user_id' => $user->id,
                'direccion' => $data['direccion'] ?? null,
                'ciudad' => $data['ciudad'] ?? null,
                'zona' => $data['zona'] ?? null,
                'notas' => 'Registrado desde el formulario web',
            ]);

            // Assign voluntario role using Spatie
            $user->assignRole('voluntario');

            return $user;
        });
    }
}
