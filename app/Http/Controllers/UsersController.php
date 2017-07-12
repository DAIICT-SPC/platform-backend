<?php

namespace App\Http\Controllers;

use App\ForgotPassword;
use App\Mail\ForgotPasswordRecoveryEmail;
use Faker\Provider\File;
use Illuminate\Http\Request;

use App\Http\Requests\CreateUser;

use App\Helper;

use App\User;

use App\Student;

use App\Admin;

use App\Company;

use App\Activation;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Image;

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

        $input = $request->only('password', 'name', 'alternate_email');                 //creates array

        $input['password'] = bcrypt($input['password']);

        $email = $activation['email'];

        $role = $activation['role'];

        $user = User::where('email',$email)->where('role',$role)->first();

        if(sizeof($user)!=0)
        {

            return $user;

        }

        $input['email'] = $email;

        $input['role'] = $role;

        $activation->delete();

        $user = User::create($input);

        if(!$user){

            return Helper::apiError("User cannot be Created!");

        }

        if($user->role == 'student')
        {

            $input_student = $request->only('enroll_no','category_id','temp_address','perm_address','gender','dob', 'contact_no');

            $input_student['user_id'] = $user->id;

            $this->createStudent($input_student);

        }

        else if($user->role == 'company')
        {

            $input_company = $request->only( 'address', 'company_name', 'contact_no','company_expertise','company_url');

            $input_company['user_id'] = $user->id;

            $this->createCompany($input_company);

        }

        else if($user->role == 'admin')
        {

            $input_admin = $request->only( 'contact_no', 'position');

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

        public function show($id = null)
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

            $input = $request->only('password', 'alternate_email');

            $input['password'] = bcrypt($input['password']);

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

        public function testUser()
        {

            $input['email'] = 'test@gmail.com';

            $input['role'] = 'admin';

            $input['password'] = bcrypt('password');

            $input['alternate_email'] = 'jiandanigaurav@gmail.com';

            $input['name'] = 'test user';

            $testUser = User::where('email',$input['email'])->first();

            if($testUser)
            {
                return $testUser;
            }

            $test_user = User::create($input);

            if(!$test_user)
            {

                return Helper::apiError("Could not create Test User!",null,404);

            }

            return $test_user;

        }


    public function companyUser()
    {

        $input['email'] = 'test@gmail.com';

        $input['role'] = 'admin';

        $input['password'] = bcrypt('password');

        $input['name'] = 'test user';

        $testUser = User::where('email',$input['email'])->first();

        if($testUser)
        {
            return $testUser;
        }

        $test_user = User::create($input);

        if(!$test_user)
        {

            return Helper::apiError("Could not create Test User!",null,404);

        }

        return $test_user;

    }

        public function storeProfilePicture(Request $request, $user_id)
        {

           if($request->hasFile('prof_pic'))
           {

               $pic = $request->file('prof_pic');

               $extension = $pic->getClientOriginalExtension();

               $filename = $user_id.time().'.'.$extension;

               $path = base_path().'/public/uploads/Profile_Pictures/'.$filename;

               \Intervention\Image\Facades\Image::make($pic->getRealPath())->resize(300,300)->save($path);

               $user = User::where('id',$user_id)->first();

               $user->profile_picture = $filename;

               $user->save();

           }

           return response("",200);

        }

        public function viewProfilePicture($user_id)
        {

            $profile_picture = User::where('id',$user_id)->pluck('profile_picture');

            if(sizeof($profile_picture)==0)
            {
                return Helper::apiError("No Student Found!",null,404);
            }

            if($profile_picture == 'default.jpg' or $profile_picture=='null')
            {
                return response("No Profile picture!",200);
            }

            $name = $profile_picture;

            return URL::to('/').'/uploads/Profile_Pictures/'.$name;

        }

        public function removeProfilePicture($user_id)
        {

            $user = User::where('id',$user_id)->first();

            $user->update(array('profile_picture'=>'default.jpg'));

            return $user;

        }

        public function generateCodeForNewPassword(Request $request)
        {

            $input = $request->only('email');

            $code = time().str_random(5);

            $user = User::where('email',$input['email'])->first();

            if(!$user)
            {
                return response("No such Email exist!",200);
            }

            $input['code'] = $code;

            $forgot_entry = ForgotPassword::create($input);

            if(!$forgot_entry)
            {

                return Helper::apiError("Cannot create Entry!",null,404);

            }

            $data = [

                'code' => $code,
                'url' => ""

            ];

    //        Mail::to($input['email'])->send(new ForgotPasswordRecoveryEmail($data));

            return $forgot_entry;

        }

        public function findCodeForForgotPassword($code)
        {

            $entry = ForgotPassword::where('code',$code)->first();

            if(!$entry){
                return response("No such code exist!",404);
            }

            return $entry;

        }

        public function changePassword(Request $request)
        {

            $input_password = $request->only('password');

            $input_code = $request->only('code');

            $forgot_password_entry = ForgotPassword::where('code',$input_code['code'])->first();

            if(sizeof($forgot_password_entry)==0)
            {

                return response("Wrong Code!",200);

            }

           $email = $forgot_password_entry['email'];

           $forgot_password_entry->delete();

            $user = User::where('email',$email)->first();

            if(!$user)
            {

                return Helper::apiError("User not Found!",null,404);

            }

            $password = bcrypt($input_password['password']);

            $input_temp = [];

            $input_temp['password'] = $password;

            $input_temp = array_filter($input_temp, function($value){

                return $value != null;

            });

            $user->update($input_temp);

            return $user;

        }

}
