<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
class APIController extends Controller
{
    public function getUsers($id =null){
        if(empty($id)){
            $users =  User::get();
            return response()->json(["users"=>$users],200);
        }else{
            $getUsers = User::find($id);
            return response()->json(["users"=>$getUsers],200);
        }
       
    }
    public function getUsersList(Request $request){
        $header = $request->header('Authorization');
        if(empty($header)){
            $message = "Header Authorization is missing";
            return response()->json(['status'=>false,'message'=>$message],422);
        }else{
            if($header == "Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyfQ.SflKxwRJSMeKKF2QT4fwpMeJf36POk6yJV_adQssw5c"){
                $users =  User::get();
                return response()->json(["users"=>$users],200);
            }else{
                $message = "Header Authorization is incorrect";
                return response()->json(['status'=>false,'message'=>$message],422);
            }
        }
        
    }
    public function loginUser(Request $request){
        if($request->isMethod('post')){
            $userData = $request->input();
            $rules = [
                'email' => 'required|email|exists:users',
                'password' => 'required',
            ];
            
            $customMessages=[
                'email.required' => 'Email is required',
                'email.email'=> 'Valid email is required',
                'email.unique' => 'Email already exists in database',
                'password.required' => 'Password is required'
            ];
            $validator = Validator::make($userData,$rules,$customMessages);
            if($validator->fails()){
                return response()->json($validator->errors(),422);
            }
           $userDetails = User::where('email',$userData['email'])->first();
           if(password_verify($userData['password'],$userDetails->password)){
             $apiToken = Str::random(60);
             User::where('email',$userData['email'])->update(['api_token' => $apiToken]);
             return response()->json(['status'=>true,'message'=>'User logged in successfully','token'=>$apiToken],201);
           }else{
            return response()->json(['status'=>false,'message'=>'Password is incorrect'],422);
           }
        }
    }
    public function loginUserWithPassport(Request $request){
        if($request->isMethod('post')){
            $userData = $request->input();
            $rules = [
                'email' => 'required|email|exists:users',
                'password' => 'required',
            ];
            
            $customMessages=[
                'email.required' => 'Email is required',
                'email.email'=> 'Valid email is required',
                'email.unique' => 'Email already exists in database',
                'password.required' => 'Password is required'
            ];
            $validator = Validator::make($userData,$rules,$customMessages);
            if($validator->fails()){
                return response()->json($validator->errors(),422);
            }
           if(Auth::attempt(['email'=>$userData['email'],'password'=>$userData['password']])){
            $user = User::where('email',$userData['email'])->first();
            // echo "<pre>"; print_r(Auth::user()); die;
            $authorizationToken = $user->createToken($userData['email'])->accessToken;
            // Update access token in users table
            User::where('email',$userData['email'])->update(['access_token' => $authorizationToken]);
            return response()->json(
                [
                    'status'=>true,
                    'message'=>'User logged in successfully',
                    'token'=>$authorizationToken
                ],201);
        }else{
            $message = "Email or password is incorrect";
            return response()->json(['status'=>false,'message'=>$message],422);
        }
        }
    }
    public function registerUser(Request $request){
        if($request->isMethod('post')){
            $userData = $request->input();
            // echo "<pre>"; print_r($userData); die;
            $rules = [
                'name' => 'required|regex:/^[a-zA-Z]+(?:[\s\'-][a-zA-Z]+)*$/',
                'email' => 'required|email|unique:users',
                'password' => 'required',
            ];
            
            $customMessages=[
                'name.required' => 'Name is required',
                'email.required' => 'Email is required',
                'email.email'=> 'Valid email is required',
                'email.unique' => 'Email already exists in database',
                'password.required' => 'Password is required'
            ];
            $validator = Validator::make($userData,$rules,$customMessages);
            if($validator->fails()){
                return response()->json($validator->errors(),422);
            }
            $apiToken = Str::random(60);
            $user = new User();
            $user->name = $userData['name'];
            $user->email = $userData['email'];
            $user->password = bcrypt($userData['password']);
            $user->access_token = $apiToken;
            $user->save();
            return response()->json(
                [
                    'status'=>true,
                    'message'=>'User registered successfully',
                    'token'=>$apiToken
                ],201);
        }
    }
    public function registerUserWithPassport(Request $request){
        if($request->isMethod('post')){
            $userData = $request->input();
            // echo "<pre>"; print_r($userData); die;
            $rules = [
                'name' => 'required|regex:/^[a-zA-Z]+(?:[\s\'-][a-zA-Z]+)*$/',
                'email' => 'required|email|unique:users',
                'password' => 'required',
            ];
            
            $customMessages=[
                'name.required' => 'Name is required',
                'email.required' => 'Email is required',
                'email.email'=> 'Valid email is required',
                'email.unique' => 'Email already exists in database',
                'password.required' => 'Password is required'
            ];
            $validator = Validator::make($userData,$rules,$customMessages);
            if($validator->fails()){
                return response()->json($validator->errors(),422);
            }
            // $apiToken = Str::random(60);
            $user = new User();
            $user->name = $userData['name'];
            $user->email = $userData['email'];
            $user->password = bcrypt($userData['password']);
            // $user->access_token = $apiToken;
            $user->save();
            if(Auth::attempt(['email'=>$userData['email'],'password'=>$userData['password']])){
                $user = User::where('email',$userData['email'])->first();
                // echo "<pre>"; print_r(Auth::user()); die;
                $accessToken = $user->createToken($userData['email'])->accessToken;
                // Update access token in users table
                User::where('email',$userData['email'])->update(['access_token' => $accessToken]);
                return response()->json(
                    [
                        'status'=>true,
                        'message'=>'User registered successfully',
                        'token'=>$accessToken
                    ],201);
            }else{
                $message = "Something went wrong.Please try again";
                return response()->json(['status'=>false,'message'=>$message],422);
            }
           
        }
    }
    public function addUsers(Request $request){
        if($request->isMethod('post')){
            $userData = $request->input();
            // if(empty($userData['name'])|| empty($userData['email'])|| empty($userData['password'])){
            //     $error_message = "Please enter complete user details";
            // }
            // if(!filter_var($userData['email'],FILTER_VALIDATE_EMAIL)){
            //     $error_message = "Please enter complete user details";
            // }
            // $userCount =  User::where('email',$userData['email'])->count();
            // if($userCount>0){
            //     $error_message = "Email already exists!";
            // }
            // if(isset($error_message)&&!empty($error_message)){
            //     return response()->json(['message'=>$error_message],422);
            // } 
            $rules = [
                'name' => 'required|regex:/^[a-zA-Z]+(?:[\s\'-][a-zA-Z]+)*$/',
                'email' => 'required|email|unique:users',
                'password' => 'required',
            ];
            
            $customMessages=[
                'name.required' => 'Name is required',
                'email.required' => 'Email is required',
                'email.email'=> 'Valid email is required',
                'email.unique' => 'Email already exists in database',
                'password.required' => 'Password is required'
            ];
            $validator = Validator::make($userData,$rules,$customMessages);
            if($validator->fails()){
                return response()->json($validator->errors(),422);
            }
            $user = new User();
            $user->name = $userData['name'];
            $user->email = $userData['email'];
            $user->password = bcrypt($userData['password']);
            $user->save();
           
           return response()->json(['message'=>'User added successfully'],201);
        }
    }
    public function addMultipleUsers(Request $request){
        if($request->isMethod('post')){
            $userData = $request->input();
            // echo "<pre>"; print_r($userData); die;
            $rules =[
                "users.*.name"=>"required|regex:/^[a-zA-Z]+(?:[\s\'-][a-zA-Z]+)*$/",
                "users.*.email"=>"required|email|unique:users",
                "users.*.password"=>"required"
            ];
            $customMessages=[
                'users.*.name.required' => 'Name is required',
                'users.*.email.required' => 'Email is required',
                'users.*.email.email'=> 'Valid email is required',
                'users.*.email.unique' => 'Email already exists in database',
                'users.*.password.required' => 'Password is required'
            ];
            $validator = Validator::make($userData,$rules,$customMessages);
            if($validator->fails()){
                return response()->json($validator->errors(),422);
            }
            foreach($userData['users'] as $key => $value){
                $user = new User();
                $user->name = $value['name'];
                $user->email = $value['email'];
                $user->password = bcrypt($value['password']);
                $user->save();
            }
            return response()->json(['message'=>'Users added successfully'],201);
        }
    }
    public function updateUserDetails(Request $request,$id){
        if($request->isMethod('put')){
            $userData = $request->input();
            User::where('id',$id)->update(
                [
                    'name'=>$userData['name'],
                    'email'=>$userData['email'],
                    'password'=>bcrypt($userData['password'])
                ]);
            return response()->json(['message'=>'Users updated successfully'],200);
        }
    }
    public function updateUserName(Request $request,$id){
    if($request->isMethod('patch')){
        $userData = $request->input();
        $rules = [
            'name' => 'required|regex:/^[a-zA-Z]+(?:[\s\'-][a-zA-Z]+)*$/',
        ];
        $customMessages=[
            'name.required' => 'Name is required',
        ];
        $validator = Validator::make($userData,$rules,$customMessages);
        if($validator->fails()){
            return response()->json($validator->errors(),422);
        }
       User::where('id',$id)->update(['name'=>$userData['name']]);
       return response()->json(['message'=>'Users updated successfully'],202);
    }
    }
    public function deleteUser($id){
        User::where('id',$id)->delete();
        return response()->json(['message'=>'Users deleted successfully'],202);
    }
    public function deleteMultipleUsers($ids){
        $ids = explode(',',$ids);
        User::whereIn('id',$ids)->delete();
        return response()->json(['message'=>'Users deleted successfully'],202);
    }
    public function deleteMultipleUsersWithJson(Request $request){
        if($request->isMethod('delete')){
            $userData = $request->all();
            User::where('id',$userData['ids'])->delete();
            return response()->json(['message'=>'Users deleted successfully'],202);
        }
    }
    public function logoutUser(Request $request){
        $apiToken = $request->header('Authorization');
        if(empty($apiToken)){
            $message = "User Token missing in API Header";
            return response()->json(['status'=>false,'message'=>$message],422);
        }else{
            $apiToken = str_replace("Bearer ","",$apiToken);
            $userCount = User::where('api_token',$apiToken)->count();
            if($userCount>0){
                User::where('api_token',$apiToken)->update(['api_token'=>NULL]);
                $message = "User logged out successfully";
                return response()->json(['status'=>true,'message'=>$message],200);
            }
        }
    }
}
