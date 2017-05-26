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


    public function registerUser(Request $request)                             //Creates user in USER Table as well as if role is student creates an entry in student table
    {

        $code = $request->input('code');                                     //while registering user - CODE and ROLE will be hidden but will come with request

        $activation = Activation::where('code',$code)->first();

        if(is_null($activation)){
            return Helper::apiError('No such activation code exist. Please try again.',null,404);
        }

        $input = $request->only('password');                 //creates array

        $input['password'] = bcrypt($input['password']);

        $email = $activation['email'];

        $role = $activation['role'];

        $input['email'] = $email;

        $input['role'] = $role;

        $activation->delete();

        $user = User::create($input);

        if(!$user){

            return Helper::apiError("User cannot be Created!");

        }

        if($user->role == 'student')
        {

            $input_student = $request->only('enroll_no', 'student_name','category_id','temp_address','perm_address','gender','dob');

            $input_student['user_id'] = $user->id;

            $this->createStudent($input_student);

        }

        else if($user->role == 'company')
        {

            $input_company = $request->only('company_name', 'address', 'contact_person', 'contact_no','company_expertise','company_url');

            $input_company['user_id'] = $user->id;

            $this->createCompany($input_company);

        }

        else if($user->role == 'admin')
        {

            $input_admin = $request->only('name', 'contact_no', 'position');

            $input_admin['user_id'] = $user->id;

            $this->createAdmin($input_admin);

        }

        return $user;

        }

        protected function createStudent(array $data)
        {
            return Student::create($data);
        }

        protected function createCompany(array $data)
        {
            return Company::create($data);
        }

        protected function createAdmin(array $data)
        {
            return Admin::create($data);
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
