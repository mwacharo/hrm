<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    use RegistersUsers;

    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct()
    {
        $this->middleware('guest');
    }

    protected function validator(array $data)
    {

        return Validator::make($data, [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'phone' => 'nullable|string|max:15',
            'unit_id' => 'required',
            'office_id' => 'required',

            'department_id' => 'required',
            'designation_id' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);
    }

    protected function create(array $data)
    {

        return User::create([
            'firstname' => $data['first_name'],
            'lastname' => $data['last_name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'unit_id' => $data['unit_id'],
            'office_id' => $data['office_id'],

            'department_id' => $data['department_id'],

            'designation_id' => $data['designation_id'],
            'password' => Hash::make($data['password']),
        ]);


    }
}
