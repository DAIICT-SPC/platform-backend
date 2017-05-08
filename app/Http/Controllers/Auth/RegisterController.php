<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use App\Student;
use App\Company;
use App\Admin;

class RegisterController extends Controller
{

use RegistersUsers;

    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest');
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'username' => 'required|string|max:50|unique:users',
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required',
        ]);
    }

    protected function create(array $data)
    {
        return User::create([
            'username' => $data['username'],
            'email' => $data['email'],
            'role' => $data['role'],
            'password' => bcrypt($data['password']),
        ]);
    }

    protected function createStudent(array $data)
    {
        return Student::create([
           'user_id' => $data['id'],
        ]);
    }

    protected function createCompany(array $data){
        return Company::create([
            'user_id' => $data['id'],
        ]);
    }

    protected function createAdmin(array $data){
        return Admin::create([
            'user_id' => $data['id'],
        ]);
    }

}
