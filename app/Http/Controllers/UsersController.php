<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\CreateUser;

use App\Helper;

use App\User;

use App\Student;

use App\Admin;

use App\Company;

use App\Activation;

class UsersController extends Controller
{

    public function index()
    {
        $users = User::all();
        return $users;
    }

    public function destroyActivation($code)
    {
        $activation = Activation::where('code',$code)->first();

        if(!$activation){
            Helper::apiError('No such activation code exist. Please try again.',null,404);
        }



        $activation->delete();

        return response("",204);
    }



    public function registerUser(CreateUser $request)                             //Creates user in USER Table as well as if role is student creates an entry in student table
    {
        $code = $request->only('code');                                     //while registering user - CODE and ROLE will be hidden but will come with request

        $this->destroyActivation($code);

        $input = $request->only('email','role','password');                 //creates array

        $input['password'] = bcrypt($input['password']);

        $user = User::create($input);

        if(!$user){

            return Helper::apiError("User cannot be Created!");

        }

        if($user->role == 'student')
        {
            $this->createStudent(['id' => $user->id]);
        }

        else if($user->role == 'company')
        {
            $this->createCompany(['id' => $user->id]);
        }

        else if($user->role == 'admin')
        {
            $this->createAdmin(['id' => $user->id]);
        }

        return $user;

        }

        protected function createStudent(array $data)
        {
            return Student::create([
                'user_id' => $data['id'],
            ]);
        }

        protected function createCompany(array $data)
        {
            return Company::create([
                'user_id' => $data['id'],
            ]);
        }

        protected function createAdmin(array $data)
        {
            return Admin::create([
                'user_id' => $data['id'],
            ]);
        }

        public function show($id)
        {
            if(is_null($id)){
                $user = request()->user();
            }else{
                $user = User::find($id);                                        //particular user
            }

            if(!$user){
                return Helper::apiError('User not Found!',null,404);
            }
            return $user;

        }

        public function update(Request $request, $user_id)                      //This method will be called only by Student, Company, Admin thus there foreign key is user_id thus using same
        {

            $user = User::find($user_id);

            if(!$user){
                return Helper::apiError('User not Found!',null,404);
            }

            $input = $request->only('username','email','password');

            $input = array_filter($input, function($value){
                return $value != null;
            });

            $user->update($input);

            return $user;

        }

        public function destroy($user_id)                   //should be able to destroy entries from student/company/admin table - ondeletecascade.. ask from kunal
        {

            $user = User::find($user_id);

            if(!$user){

                return Helper::apiError('User not Found!',null,404);

            }

            $user->delete();

            return response("",204);
        }

}
