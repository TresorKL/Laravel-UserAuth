<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Doctor;
use App\Models\User;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Hash;
use Validator;
use Auth;

class DorctorController extends Controller
{
    // add doctor
    function addDoctor(Request $request){

        
        $doctor = new Doctor;
        
        $rules=array(
           "lastName"=>"required",
           "firstName"=>"required",
           "emailAddress"=>"required|email",
           "officeNo"=>"required"
        );

        $validator = Validator::make($request->all(),$rules);

        if($validator){
            // create user as doctor
            $doctor->firstName=$request->firstName;
            $doctor->lastName=$request->lastName;
            $doctor->specialisation=$request->specialisation;
            $doctor->emailAddress=$request->emailAddress;
            $doctor->officeNo=$request->officeNo;
            $doctor->password='******';
            $result=$doctor->save();
            
            // create user for auth
            $user=User::create([
                'name'=>$request->firstName,
                'email'=>$request->emailAddress,
                'password'=>Hash::make($request->password)
            ]);


 
          return response()->json([
                "status"=>true,
                "message"=>"doctor successfully added",
                "token"=>$user->createToken("API TOKEN")->plainTextToken
          ],200);
          
        }else{
            return response()->json($validator->errors(),401);
        }
        

    }

    function login(Request $request){

        

        $rules=array(
            'email'=>'required|email',
            'password'=>'required'
         );

         $validator = Validator::make($request->all(),$rules);

         if(!$validator){
            return response()->json([
               "status"=>false,
               "message"=>"invalid credentials",
               "error"=>$validator->errors()
            ],401);
         }

         if(!Auth::attempt($request->only(['email','password']))){
            return response()->json([
                "status"=>false,
                "message"=>"invalid credentials",
                "error"=>$validator->errors()
             ],401);
         }
         //"doctor"=>$doctor,
         $doctor =Doctor::where('emailAddress', $request->email)->get();

         $user =User::where('email', $request->email)->first();
         return response()->json([
            "status"=>true,
            "message"=>"Logged successfully",
            "doctor"=>$doctor,
            "token"=>$user->createToken("API TOKEN")->plainTextToken
      ],200);
    }

    function getDoctors(){
        return Doctor::all();
    }

}
