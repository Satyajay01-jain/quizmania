<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use App\Helpers;
use App\Models\Users;
use App\Models\Questions;
use App\Models\ContestTypes;
use App\Models\Contests;
use App\Models\Topics;
use App\Models\Education;
use App\Models\Profession;
use App\Models\State;
use App\Models\City;
use App\Models\ContestRegistration;
use App\Models\Faq;
use App\Models\PaymentModel;
use App\Models\WithdrawModel;
use App\Models\LeaderBoard;
use App\Models\WalletModel;
use App\Models\Feedback;
use App\Models\QuizSlot;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
date_default_timezone_set('Asia/Kolkata');

class APIController extends Controller
{
    function fetch_live_user(){
        $live_users=DB::table("analytics")->select('live_users')->first();
        
        return response()->json([
                "success"=>true,
                "live_users"=> $live_users->live_users,
            ], 201);
    }
    
    function live_user_count($count){
        
        $error=[];
        
        
        if(!isset($count) || is_null($count)){
            $error["phone"]="Count is required";
        }
        if(count($error)>0){
            return response()->json([
                "success"=>false,
                "message"=> "Some required information is missing",
                "errors"=>$error
            ], 501);
        }
        
        $live_users=DB::table("analytics")->select('live_users')->first();
        
        if($live_users->live_users<=0 && $count==-1){
            return response()->json([
                "success"=>true,
                "live_users"=> $live_users->live_users,
                "errors"=>$error
            ], 201);
        }
        else{
            DB::table("analytics")->increment('live_users', $count);
            return response()->json([
                "success"=>true,
                "live_users"=> $live_users->live_users+1,
                "errors"=>$error
            ], 201);
        }
    }
    
    function register_user_old_19_8_2023(Request $request){
     
        
        $error=[];
        if(!isset($request->name) || is_null($request->name)){
            $error["name"]="Name is required";
        }
        
        if(!isset($request->email) || is_null($request->email)){
            $error["email"]="Email ID is required";
        }
        
        if(!isset($request->phone) || is_null($request->phone)){
            $error["phone"]="Phone number is required";
        }
        
        if(!isset($request->education) || is_null($request->education)){
            $error["education"]="Education is required";
        }
        
        if(!isset($request->profession) || is_null($request->profession)){
            $error["profession"]="Profession is required";
        }
        
        if(!isset($request->city) || is_null($request->city)){
            $error["city"]="City is required";
        }
        
        if(!isset($request->profile_pic) || is_null($request->profile_pic)){
            $error["profile_pic"]="Profile picture is required";
        }
        
        if(count($error)>0){
            return response()->json([
                "success"=>false,
                "message"=> "Some required information is missing",
                "errors"=>$error
            ], 501);
        }
        
        try{
            $checkUserExist=Users::where("email",$request->email)->orWhere('phone',$request->phone)->count();
            
            if($checkUserExist>0){
                return response()->json([
                    "success"=>false,
                    "message"=> "User already exist with same email or phone number",
                    "errors"=>$error
                ], 501);
            }
            $newUser=new Users();
            
            $newUser->name=trim($request->name);
            $newUser->email=trim($request->email);
            $newUser->phone=trim($request->phone);
            $newUser->education=trim($request->education);
            $newUser->profession=trim($request->profession);
            $newUser->city=trim($request->city);
            
            // $image = $request->profile_pic;  // your base64 encoded
            // $image = str_replace('data:image/png;base64,', '', $image);
            // $image = str_replace(' ', '+', $image);
            // $imageName = time().'.'.'png';
            // $this->uploadImage(base64_decode($image), "storage/users/profile",$imageName);
            
            if(isset($request->profile_pic) && $request->profile_pic!=""){
                $image = $request->profile_pic;  // your base64 encoded
            $image = str_replace('data:image/png;base64,', '', $image);
            $image = str_replace(' ', '+', $image);
            $imageName = time() . '.png';
        
            Storage::disk('local')->put($imageName, base64_decode($image));
            $newUser->profile_pic=trim($imageName);
            }
            
            
            
            if(isset($request->referral_code) || !is_null($request->referral_code)){
                $newUser->name=trim($request->referral_code);
            }
            $newUser->role=1;// 1 is for user
            $newUser->status=1;
            if(isset($request->firebase_auth_token) || !is_null($request->firebase_auth_token)){
                $newUser->firebase_auth_token=$request->firebase_auth_token;
            }
            
            $newUser->save();
            
             return response()->json([
                "success"=>true,
                "message"=> "Account created successfully !",
                "data"=>[
                    "user_id"=>$newUser->id,
                    "profile_pic"=>$imageName
                ],
            ], 201);
        }
        catch(Exception $err){
             return response()->json([
                "success"=>false,
                "message"=> "Internal server error",
                "errors"=>[]
            ], 501);
        }
    }
    
    function register_user(Request $request){
     
        
        $error=[];
        if(!isset($request->name) || is_null($request->name)){
            $error["name"]="Name is required";
        }
        
        if(!isset($request->phone) || is_null($request->phone)){
            $error["phone"]="Phone number is required";
        }
        
        if(!isset($request->state) || is_null($request->state)){
            $error["state"]="State is required";
        }
        
        if(!isset($request->city) || is_null($request->city)){
            $error["city"]="City is required";
        }
        
        if(count($error)>0){
            return response()->json([
                "success"=>false,
                "message"=> "Some required information is missing",
                "errors"=>$error
            ], 501);
        }
        
        try{
            $checkUserExist=Users::where('phone',$request->phone)->count();
            
            if($checkUserExist>0){
                return response()->json([
                    "success"=>false,
                    "message"=> "User already exist with same phone number",
                    "errors"=>$error
                ], 501);
            }
            $timestamp=date('Y-m-d H:i:s');
            $newUser=new Users();
            
            $newUser->name=trim($request->name);
            $newUser->phone=trim($request->phone);
            $newUser->state=trim($request->state);
            $newUser->city=trim($request->city);
            
            // $image = $request->profile_pic;  // your base64 encoded
            // $image = str_replace('data:image/png;base64,', '', $image);
            // $image = str_replace(' ', '+', $image);
            // $imageName = time().'.'.'png';
            // $this->uploadImage(base64_decode($image), "storage/users/profile",$imageName);
            $imageName="";
            if(isset($request->profile_pic) && $request->profile_pic!=""){
                $image = $request->profile_pic;  // your base64 encoded
                $image = str_replace('data:image/png;base64,', '', $image);
                $image = str_replace(' ', '+', $image);
                $imageName = time() . '.png';
            
                Storage::disk('local')->put($imageName, base64_decode($image));
                $newUser->profile_pic=trim($imageName);
            }
            
            
            
            if(isset($request->referral_code) || !is_null($request->referral_code)){
                $newUser->name=trim($request->referral_code);
            }
            $newUser->role=1;// 1 is for user
            $newUser->status=1;
            if(isset($request->firebase_auth_token) || !is_null($request->firebase_auth_token)){
                $newUser->firebase_auth_token=$request->firebase_auth_token;
            }
            $newUser->created_at=$timestamp;
            $newUser->save();
            
             return response()->json([
                "success"=>true,
                "message"=> "Account created successfully !",
                "data"=>[
                    "user_id"=>$newUser->id,
                    "profile_pic"=>$imageName,
                    "wallet_amt"=>0
                ],
            ], 201);
        }
        catch(Exception $err){
            $message="Internal server error: ".$err->getMessage()." On ".date("Y-m-d H:i:s");
            Log::error($message);
            
             return response()->json([
                "success"=>false,
                "message"=> "Internal server error",
                "errors"=>[]
            ], 501);
        }
    }
    
    function login_user(Request $request){
     
        
        $error=[];
        
        
        if(!isset($request->phone) || is_null($request->phone)){
            $error["phone"]="Phone number is required";
        }
        if(count($error)>0){
            return response()->json([
                "success"=>false,
                "message"=> "Some required information is missing",
                "errors"=>$error
            ], 501);
        }
        
        try{
            $checkUserExist=Users::select('name','email','id','profile_pic','wallet_amt')->where('phone',$request->phone)->first();
            
            if($checkUserExist==null){
                return response()->json([
                    "success"=>false,
                    "message"=> "No records found !",
                    "errors"=>$error
                ], 501);
            }
           
            
             return response()->json([
                "success"=>true,
                "message"=> "Successfully loggedin !",
                "data"=>[
                    "user_id"=>$checkUserExist->id,
                    "user_name"=>$checkUserExist->name,
                    "user_email"=>$checkUserExist->email,
                    "profile_pic"=>$checkUserExist->profile_pic,
                    "wallet_amt"=>round($checkUserExist->wallet_amt),
                ],
            ], 201);
        }
        catch(Exception $err){
            $message="Internal server error: ".$err->getMessage()." On ".date("Y-m-d H:i:s");
            Log::error($message);
            
            
             return response()->json([
                "success"=>false,
                "message"=> "Internal server error",
                "errors"=>[]
            ], 501);
        }
    }
    
    function user_details(Request $request){
     
        
        $error=[];
        
        
        if(!isset($request->id) || is_null($request->id)){
            $error["id"]="ID is required";
        }
        if(count($error)>0){
            return response()->json([
                "success"=>false,
                "message"=> "Some required information is missing",
                "errors"=>$error
            ], 501);
        }
        
        try{
            $checkUserExist=Users::find($request->id);
            
            if($checkUserExist==null){
                return response()->json([
                    "success"=>false,
                    "message"=> "No records found !",
                    "errors"=>$error,
                    "id"=>$request->id
                ], 501);
            }
           
            
             return response()->json([
                "success"=>true,
                "message"=> "Successfully loggedin !",
                "data"=>[
                    "user_id"=>$checkUserExist->id,
                    "user_name"=>$checkUserExist->name,
                    "user_email"=>$checkUserExist->email,
                    "user_phone"=>$checkUserExist->phone,
                     "profile_pic"=>$checkUserExist->profile_pic,
                    "education"=>$checkUserExist->education,
                    "profession"=>$checkUserExist->profession,
                    "city"=>$checkUserExist->city,
                    "state"=>$checkUserExist->state,
                    "referral_code"=>$checkUserExist->referral_code,
                ],
            ], 201);
        }
        catch(Exception $err){
             return response()->json([
                "success"=>false,
                "message"=> "Internal server error",
                "errors"=>[]
            ], 501);
        }
    }
    
    function wallet_details(Request $request){
     
        
        $error=[];
        
        
        if(!isset($request->id) || is_null($request->id)){
            $error["id"]="ID is required";
        }
        if(count($error)>0){
            return response()->json([
                "success"=>false,
                "message"=> "Some required information is missing",
                "errors"=>$error
            ], 501);
        }
        
        try{
            $checkUserExist=Users::find($request->id);
            
            if($checkUserExist==null){
                return response()->json([
                    "success"=>false,
                    "message"=> "No records found !",
                    "errors"=>$error,
                    "id"=>$request->id
                ], 501);
            }
           
            $wallet=WalletModel::where('user_id',$request->id)->orderBy('created_at', 'DESC')->get();
             return response()->json([
                "success"=>true,
                "message"=> "data fetched !",
                "data"=>$wallet,
            ], 201);
        }
        catch(Exception $err){
             return response()->json([
                "success"=>false,
                "message"=> "Internal server error",
                "errors"=>[]
            ], 501);
        }
    }
    
    function feedback(Request $request){
        $error=[];
        try{
            if(!isset($request->user_id) || is_null($request->user_id)){
                $error["user_id"]="User ID is required";
            }
            if(!isset($request->subject) || is_null($request->subject)){
                $error["subject"]="Subject is required";
            }
            if(!isset($request->feedback) || is_null($request->feedback)){
                $error["feedback"]="Feedback is required";
            }
            
            if(count($error)>0){
                return response()->json([
                    "success"=>false,
                    "message"=> "Some required information is missing",
                    "errors"=>$error
                ], 501);
            }
            
            $checkUser=Users::whereNull('deleted_at')->find($request->user_id);
                
            if($checkUser==null){
                return response()->json([
                    "success"=>false,
                    "message"=> "User not found !",
                    "errors"=>$error,
                ], 501);
            }
            
            
            $timestamp=date('Y-m-d H:i:s');
            $new= new Feedback();
            $new->user_id=$request->user_id;
            $new->subject=trim($request->subject);
            $new->message=trim($request->feedback);
            $new->created_at=$timestamp;
            $new->save();
            return response()->json([
                    "success"=>true,
                    "message"=> "Feedback submitted !",
                ], 201);
        }
        catch(Exception $err){
            $message="Internal server error: ".$err->getMessage()." On ".date("Y-m-d H:i:s");
            Log::error($message);
            
                 return response()->json([
                    "success"=>false,
                    "message"=> "Internal server error",
                    "errors"=>[]
                ], 501);
            }
    }
    
    function getBasicProfileDetails(){
        // to fetch eduction, profession etc
        
        $error=[];
        
        
        // if(!isset($request->id) || is_null($request->id)){
        //     $error["id"]="ID is required";
        // }
        if(count($error)>0){
            return response()->json([
                "success"=>false,
                "message"=> "Some required information is missing",
                "errors"=>$error
            ], 501);
        }
        
        try{
            $education=Education::select('name')->distinct('name')->whereNull('deleted_at')->orderBy("name")->get();
            $profession=Profession::select('name')->distinct('name')->whereNull('deleted_at')->orderBy("name")->get();
            $state=State::select('name')->distinct('name')->whereNull('deleted_at')->orderBy("name")->get();
            $city=City::select('name')->distinct('name')->whereNull('deleted_at')->orderBy("name")->get();
            
            // if($allData==null){
            //     return response()->json([
            //         "success"=>false,
            //         "message"=> "No records found !",
            //         "errors"=>$error,
            //     ], 501);
            // }
           
            
             return response()->json([
                "success"=>true,
                "message"=> "Details Fetched !",
                "education"=> $education,
                "profession"=> $profession,
                "state"=> $state,
                "city"=> $city,
            ], 201);
        }
        catch(Exception $err){
             return response()->json([
                "success"=>false,
                "message"=> "Internal server error",
                "errors"=>[]
            ], 501);
        }
    }
    
    function getBasicRegisterDetails(){
        // to fetch eduction, profession etc
        
        $error=[];
        
        
        // if(!isset($request->id) || is_null($request->id)){
        //     $error["id"]="ID is required";
        // }
        if(count($error)>0){
            return response()->json([
                "success"=>false,
                "message"=> "Some required information is missing",
                "errors"=>$error
            ], 501);
        }
        
        try{

            $state=State::select('name')->distinct('name')->whereNull('deleted_at')->orderBy("name")->get();
            $city=City::select('name')->distinct('name')->whereNull('deleted_at')->orderBy("name")->get();
            
            // if($allData==null){
            //     return response()->json([
            //         "success"=>false,
            //         "message"=> "No records found !",
            //         "errors"=>$error,
            //     ], 501);
            // }
           
            
             return response()->json([
                "success"=>true,
                "message"=> "Details Fetched !",
                "state"=> $state,
                "city"=> $city,
            ], 201);
        }
        catch(Exception $err){
             return response()->json([
                "success"=>false,
                "message"=> "Internal server error",
                "errors"=>[]
            ], 501);
        }
    }
    
    function getTopics(){
        // to fetch eduction, profession etc
        
        $error=[];
       
        if(count($error)>0){
            return response()->json([
                "success"=>false,
                "message"=> "Some required information is missing",
                "errors"=>$error
            ], 501);
        }
        
        try{

            $topics=Topics::distinct('name')->whereNull('deleted_at')->where('status',1)->orderBy("name")->get();
            
            if($topics==null){
                return response()->json([
                "success"=>false,
                "message"=> "No details found !",
                "errors"=>[]
               ], 401);
            }
            
            
            return response()->json([
                "success"=>true,
                "message"=> "Details Fetched !",
                "data"=> $topics,
            ], 201);
        }
        catch(Exception $err){
             return response()->json([
                "success"=>false,
                "message"=> "Internal server error",
                "errors"=>[]
            ], 501);
        }
    }
    
    function getTopicStudyMaterial(Request $request){
        // to fetch eduction, profession etc
        
        $error=[];
       
        if(count($error)>0){
            return response()->json([
                "success"=>false,
                "message"=> "Some required information is missing",
                "errors"=>$error
            ], 501);
        }
        
        try{

            $topics=Topics::whereNull('deleted_at')->where('status',1)->find($request->topic_id);
            
            if($topics==null){
                return response()->json([
                "success"=>false,
                "message"=> "No details found !",
                "errors"=>[]
               ], 401);
            }
            
            $questions=[];
            $topic_questions=$topics->questions;
            if($topic_questions!=null){
                $topic_questions=json_decode($topic_questions);
                
                $questions=Questions::whereIn('id',$topic_questions)->whereNull('deleted_at')->get();
            }
            
            return response()->json([
                "success"=>true,
                "message"=> "Details Fetched !",
                "data"=> $topics,
                'questions'=>$questions
            ], 201);
        }
        catch(Exception $err){
             return response()->json([
                "success"=>false,
                "message"=> "Internal server error",
                "errors"=>[]
            ], 501);
        }
    }
    
   
    function update_user_details(Request $request){
     
        
        $error=[];
        if(!isset($request->id) || is_null($request->id)){
            $error["id"]="User ID is required";
        }
        if(!isset($request->name) || is_null($request->name)){
            $error["name"]="Name is required";
        }
        
        if(!isset($request->email) || is_null($request->email)){
            $error["email"]="Email ID is required";
        }
        
        
        if(!isset($request->education) || is_null($request->education)){
            $error["education"]="Education is required";
        }
        
        if(!isset($request->profession) || is_null($request->profession)){
            $error["profession"]="Profession is required";
        }
        
        if(!isset($request->city) || is_null($request->city)){
            $error["city"]="City is required";
        }
        if(!isset($request->state) || is_null($request->state)){
            $error["state"]="State is required";
        }
        
       
        if(count($error)>0){
            return response()->json([
                "success"=>false,
                "message"=> "Some required information is missing",
                "errors"=>$error
            ], 501);
        }
        
        try{
            $checkUserExist=Users::where('id',$request->id)->count();
            
            if($checkUserExist<=0){
                return response()->json([
                    "success"=>false,
                    "message"=> "No records found !",
                    "errors"=>$error
                ], 501);
            }
            $user=Users::find($request->id);
            
            $user->name=trim($request->name);
            $user->email=trim($request->email);
            $user->education=trim($request->education);
            $user->profession=trim($request->profession);
            $user->city=trim($request->city);
            $user->state=trim($request->state);
            
            // $image = $request->profile_pic;  // your base64 encoded
            // $image = str_replace('data:image/png;base64,', '', $image);
            // $image = str_replace(' ', '+', $image);
            // $imageName = time().'.'.'png';
            // $this->uploadImage(base64_decode($image), "storage/users/profile",$imageName);
            
            
            if(isset($request->profile_pic) && $request->profile_pic!=""){
                
                $image = $request->profile_pic;  // your base64 encoded
                $image = str_replace('data:image/png;base64,', '', $image);
                $image = str_replace(' ', '+', $image);
                $imageName = time() . '.png';
            
                Storage::disk('local')->put($imageName, base64_decode($image));
                
                // deleting old file
                // unlink(storage_path('app/'.$user->profile_pic));
                
                $user->profile_pic=trim($imageName);
            
            }
            else{
                $imageName=$user->profile_pic;
            }
            
           
            if(isset($request->referral_code) || !is_null($request->referral_code)){
                $user->referral_code=trim($request->referral_code);
            }
            
            
            
            $user->save();
            
             return response()->json([
                "success"=>true,
                "message"=> "Account updated successfully !",
                "data"=>[
                    "user_id"=>$user->id,
                    "profile_pic"=>$imageName,
                     "wallet_amt"=>round($user->wallet_amt)
                ],
            ], 201);
        }
        catch(Exception $err){
            $message="Internal server error: ".$err->getMessage()." On ".date("Y-m-d H:i:s");
            Log::error($message);
             return response()->json([
                "success"=>false,
                "message"=> "Internal server error",
                "errors"=>[]
            ], 501);
        }
    }
    
    function contests(Request $request){
     
        
        $error=[];
        
        
        // if(!isset($request->id) || is_null($request->id)){
        //     $error["id"]="ID is required";
        // }
        
        if(count($error)>0){
            return response()->json([
                "success"=>false,
                "message"=> "Some required information is missing",
                "errors"=>$error
            ], 501);
        }
        
        try{
            $contests=Contests::where("status",1)->whereNull('deleted_at')->get()->groupBy('contest_id');
            
            if($contests==null){
                return response()->json([
                    "success"=>false,
                    "message"=> "No records found !",
                    "errors"=>$error,
                ], 501);
            }
            
           
             return response()->json([
                "success"=>true,
                "message"=> "Data found !",
                "data"=>[$contests],
            ], 201);
        }
        catch(Exception $err){
             return response()->json([
                "success"=>false,
                "message"=> "Internal server error",
                "errors"=>[]
            ], 501);
        }
    }
    
    function upcoming_contest_list(Request $request){
     
        
        $error=[];
        
        
        // if(!isset($request->id) || is_null($request->id)){
        //     $error["id"]="ID is required";
        // }
        
        if(count($error)>0){
            return response()->json([
                "success"=>false,
                "message"=> "Some required information is missing",
                "errors"=>$error
            ], 501);
        }
        
        try{
            $timestamp=date("Y-m-d H:i:s", strtotime("+2 minutes"));
            
            $contests=Contests::where("status",1)->whereNull('deleted_at')->where('start_time','>',$timestamp)->get();
            
            if($contests==null){
                return response()->json([
                    "success"=>false,
                    "message"=> "No records found !",
                    "errors"=>$error,
                ], 501);
            }
            
           
             return response()->json([
                "success"=>true,
                "message"=> "Data found !",
                "data"=>$contests,
            ], 201);
        }
        catch(Exception $err){
            
            return response()->json([
                "success"=>false,
                "message"=> "Internal server error",
                "errors"=>[]
            ], 501);
            
        }
    }
    
    
    function contest_list(Request $request){
     
        
        $error=[];
        
        
        
        if(count($error)>0){
            return response()->json([
                "success"=>false,
                "message"=> "Some required information is missing",
                "errors"=>$error
            ], 501);
        }
        
        try{
            $timestamp=date("Y-m-d H:i:s");
            $timestamp_2=date("Y-m-d H:i:s", strtotime("+2 minutes"));
            $contests=Contests::where("status",1)->whereNull('deleted_at')->get();
            
            if($contests==null){
                return response()->json([
                    "success"=>false,
                    "message"=> "No records found !",
                    "errors"=>$error,
                ], 501);
            }
            
            $pastContest=[];
            $upcomingContest=[];
            
            foreach($contests as $contest){
                if($contest->end_date_time <= $timestamp){
                    array_push($pastContest,$contest);
                }
                else if($contest->start_time>=$timestamp_2){
                    array_push($upcomingContest,$contest);
                }
            }
            
            
           
             return response()->json([
                "success"=>true,
                "message"=> "Data found !",
                "data"=>$pastContest,
                "upcoming_data"=>$upcomingContest,
            ], 201);
        }
        catch(Exception $err){
             return response()->json([
                "success"=>false,
                "message"=> "Internal server error",
                "errors"=>[]
            ], 501);
        }
    }
    
    function user_participated_contest_list(Request $request){
     
        
        $error=[];
        if(!isset($request->user_id) || is_null($request->user_id)){
            $error["user_id"]="User ID is required";
        }
        
        
        
        if(count($error)>0){
            return response()->json([
                "success"=>false,
                "message"=> "Some required information is missing",
                "errors"=>$error
            ], 501);
        }
        
        
        
        try{
            
            $checkUser=Users::whereNull('deleted_at')->find($request->user_id);
            
            if($checkUser==null){
                return response()->json([
                    "success"=>false,
                    "message"=> "User not found !",
                    "errors"=>$error,
                ], 501);
            }
            
            
            $timestamp=date("Y-m-d H:i:s");
            
            $contests=Contests::select('contests.*')
            ->join('contest_registration','contest_registration.contest_id','contests.id')
            ->where("contests.status",1)->whereNull('contest_registration.deleted_at')->whereNull('contests.deleted_at')->where('contests.end_date_time','<=',$timestamp)->where('contest_registration.user_id', $request->user_id)->get();
            
            if($contests==null){
                return response()->json([
                    "success"=>false,
                    "message"=> "No records found !",
                    "errors"=>$error,
                ], 501);
            }
            
           
             return response()->json([
                "success"=>true,
                "message"=> "Data found !",
                "data"=>$contests,
            ], 201);
        }
        catch(Exception $err){
             return response()->json([
                "success"=>false,
                "message"=> "Internal server error",
                "errors"=>[]
            ], 501);
        }
    }
    
    
    function singleCategoryContest($categoryId,$user_id){
        
        $error=[];
        
        
        if(!isset($categoryId) || is_null($categoryId)){
            $error["id"]="category ID is required";
        }
        
        if(!isset($user_id) || is_null($user_id)){
            $error["user_id"]="user ID is required";
        }
        
        if(count($error)>0){
            return response()->json([
                "success"=>false,
                "message"=> "Some required information is missing",
                "errors"=>$error
            ], 501);
        }
        
        try{
            $timestamp=date("Y-m-d H:i:s");
            $contests=Contests::select('contests.*')->where("contests.status",1)->where("contests.contest_id",$categoryId)->where('contests.end_date_time','>',$timestamp)
                ->orderBy('contests.start_time')->whereNull('deleted_at')->get()->all();
            
            if($contests==null){
                return response()->json([
                    "success"=>false,
                    "message"=> "No records found !",
                    "errors"=>$error,
                ], 501);
            }
            
            foreach($contests as $key=>$contest){
                $c_id=$contest->id;
                
                $user_registration=ContestRegistration::where("contest_id",$c_id)->whereNull('deleted_at')->where("user_id",$user_id)->where("status",1)->count();
                if($user_registration==0){
                    $contests[$key]["is_registered"]=false;
                }
                else{
                    $contests[$key]["is_registered"]=true;
                }
                
                $user_leaderboard=LeaderBoard::select('is_completed')->where("contest_id",$c_id)->where("user_id",$user_id)->whereNull('deleted_at')->first();
                
                if($user_leaderboard==null){
                    $contests[$key]["player_status"]=null;
                }
                elseif($user_leaderboard->is_completed==1){
                    $contests[$key]["player_status"]=$user_leaderboard->is_completed;
                }
                else{
                    $contests[$key]["player_status"]=$user_leaderboard->is_completed;
                }
            }
           
             return response()->json([
                "success"=>true,
                "message"=> "Data found !",
                "data"=>$contests,
            ], 201);
        }
        catch(Exception $err){
             return response()->json([
                "success"=>false,
                "message"=> "Internal server error",
                "errors"=>[]
            ], 501);
        }
    }
    
    function practice_quiz(Request $request){
     
        
        $error=[];
        
        
        // if(!isset($request->id) || is_null($request->id)){
        //     $error["id"]="ID is required";
        // }
        
        if(count($error)>0){
            return response()->json([
                "success"=>false,
                "message"=> "Some required information is missing",
                "errors"=>$error
            ], 501);
        }
        
        try{
            
            
            $categoryId = 4; // The category for question bank

            $allQuestions = Questions::whereNull('deleted_at')
                ->where(function ($query) use ($categoryId) {
                    $query->whereJsonContains('category', $categoryId)
                          ->orWhere(function ($subQuery) use ($categoryId) {
                              $subQuery->where('category', 'LIKE', '%"'.$categoryId.'"%');
                          });
                })
                ->inRandomOrder()->limit(10)->get();
            
            if($allQuestions==null){
                return response()->json([
                    "success"=>false,
                    "message"=> "No questions found for practice !",
                    "errors"=>$error,
                ], 501);
            }
            
           
            
             return response()->json([
                "success"=>true,
                "message"=> "Questions fetched !",
                "questions"=>$allQuestions
            ], 201);
        }
        catch(Exception $err){
             return response()->json([
                "success"=>false,
                "message"=> "Internal server error",
                "errors"=>[]
            ], 501);
        }
    }
    
    function quiz_details(Request $request){
     
        
        $error=[];
        
        
        if(!isset($request->id) || is_null($request->id)){
            $error["id"]="ID is required";
        }
        
        if(count($error)>0){
            return response()->json([
                "success"=>false,
                "message"=> "Some required information is missing",
                "errors"=>$error
            ], 501);
        }
        
        try{
            $checkExist=Contests::find($request->id);
            
            if($checkExist==null){
                return response()->json([
                    "success"=>false,
                    "message"=> "No records found !",
                    "errors"=>$error,
                    "id"=>$request->id
                ], 501);
            }
            
            $questionIds=json_decode($checkExist->question_ids);
            
            $allQuestions=Questions::whereNull('deleted_at')->get();
            
            if($allQuestions==null){
                return response()->json([
                    "success"=>false,
                    "message"=> "No questions found for this contest !",
                    "errors"=>$error,
                    "id"=>$request->id
                ], 501);
            }
            
            $quizQuestions=[];
            foreach($allQuestions as $key => $q){
                if(!is_bool(array_search($q->id,$questionIds))){
                    $temp["id"]=$q->id;
                    $temp["title_type"]=$q->title_type;
                    $temp["title"]=$q->title;
                    $temp["hi_title"]=$q->hi_title;
                    $temp["option_type"]=$q->option_type;
                    $temp["options"]=$q->options;
                    $temp["ans_desc"]=$q->ans_desc;
                    array_push($quizQuestions,$temp);
                }
            }
            
             return response()->json([
                "success"=>true,
                "message"=> "Questions fetched !",
                "data"=>$checkExist,
                "questions"=>$quizQuestions
            ], 201);
        }
        catch(Exception $err){
             return response()->json([
                "success"=>false,
                "message"=> "Internal server error",
                "errors"=>[]
            ], 501);
        }
    }
    
    function quiz_questions(Request $request){
     
        
        $error=[];
        
        
        if(!isset($request->id) || is_null($request->id)){
            $error["id"]="ID is required";
        }
        
        if(count($error)>0){
            return response()->json([
                "success"=>false,
                "message"=> "Some required information is missing",
                "errors"=>$error
            ], 501);
        }
        
        try{
            $checkExist=Contests::find($request->id);
            
            if($checkExist==null){
                return response()->json([
                    "success"=>false,
                    "message"=> "No records found !",
                    "errors"=>$error,
                    "id"=>$request->id
                ], 501);
            }
            
            $questionIds=json_decode($checkExist->question_ids);
            
            
            $quizQuestions = Questions::whereNull('deleted_at')
            ->whereIn('id', $questionIds)
            ->select('id', 'title_type', 'title', 'hi_title', 'option_type', 'options', 'ans_desc')
            ->get()->all();
            
            if($quizQuestions==null){
                return response()->json([
                    "success"=>false,
                    "message"=> "No questions found for this contest !",
                    "errors"=>$error,
                    "id"=>$request->id
                ], 501);
            }
            
           
             return response()->json([
                "success"=>true,
                "message"=> "Questions fetched !",
                "data"=>$checkExist,
                "questions"=>$quizQuestions
            ], 201);
        }
        catch(Exception $err){
             return response()->json([
                "success"=>false,
                "message"=> "Internal server error",
                "errors"=>[]
            ], 501);
        }
    }
    
    function check_registration(Request $request){
     
        
        $error=[];
        
        
        if(!isset($request->user_id) || is_null($request->user_id)){
            $error["user_id"]="User ID is required";
        }
        
        if(!isset($request->quiz_id) || is_null($request->quiz_id)){
            $error["quiz_id"]="Quiz ID is required";
        }
        
        
        if(count($error)>0){
            return response()->json([
                "success"=>false,
                "message"=> "Some required information is missing",
                "errors"=>$error
            ], 501);
        }
        
        try{
            $timestamp=date("Y-m-d H:i:s");
           
            
            $checkUser=Users::whereNull('deleted_at')->find($request->user_id);
            
            if($checkUser==null){
                return response()->json([
                    "success"=>false,
                    "message"=> "User not found !",
                    "errors"=>$error,
                ], 501);
            }
            
            $checkIsHost=Contests::whereNull('deleted_at')->where('host_user_id',$request->user_id)->where('id',$request->quiz_id)->first();
            if($checkIsHost!=null){
                return response()->json([
                    "success"=>true,
                    "message"=> "Welcome ! You are host for this quiz.",
                    "is_quiz_host"=>true
                ], 201);
            }
            
            $checkUser=ContestRegistration::whereNull('deleted_at')->where('user_id',$request->user_id)->where('contest_id',$request->quiz_id)->where("status",1)->count();
            
            if($checkUser==0){
                // for quiz host 
                // ->where('end_date_time','<',$timestamp)
                $checkIsHost=Contests::whereNull('deleted_at')->where('host_user_id',$request->user_id)->where('id',$request->quiz_id)->first();
                if($checkIsHost!=null){
                    return response()->json([
                        "success"=>true,
                        "message"=> "Welcome ! You are host for this quiz.",
                        "is_quiz_host"=>true
                    ], 201);
                }
                
                $checkExist=Contests::whereNull('deleted_at')->where('start_time','>',$timestamp)->find($request->quiz_id);
            
                if($checkExist==null){
                    return response()->json([
                        "success"=>false,
                        "message"=> "Quiz has started ! Now you can not register.",
                        "errors"=>$error,
                        "quiz_started"=>true
                    ], 501);
                }
            
                return response()->json([
                    "success"=>true,
                    "message"=> "You have not registered for this quiz. Please register first.",
                    "is_registered"=>false
                ], 201);
            }
            else{
                 return response()->json([
                    "success"=>true,
                    "message"=> "You have registered for this quiz.",
                    "is_registered"=>true
                ], 201);
            }
        }
        catch(Exception $err){
            
            $message="Internal server error: ".$err->getMessage()." On ".date("Y-m-d H:i:s");
            Log::error($message);
            
            
             return response()->json([
                "success"=>false,
                "message"=> "Internal server error",
                "errors"=>[]
            ], 501);
        }
    }
    
    function register_for_quiz_wallet(Request $request){
        
        $error=[];
        
        
        if(!isset($request->user_id) || is_null($request->user_id)){
            $error["user_id"]="User ID is required";
        }
        
        if(!isset($request->quiz_id) || is_null($request->quiz_id)){
            $error["quiz_id"]="Quiz ID is required";
        }
        
        
        if(count($error)>0){
            return response()->json([
                "success"=>false,
                "message"=> "Some required information is missing",
                "errors"=>$error
            ], 501);
        }
        
        try{
            $timestamp=date("Y-m-d H:i:s");
            $checkExist=Contests::whereNull('deleted_at')->where('start_time','>',$timestamp)->find($request->quiz_id);
            
            if($checkExist==null){
                return response()->json([
                    "success"=>false,
                    "message"=> "Quiz is already started ! Now you can not register for quiz !",
                    "errors"=>$error,
                ], 501);
            }
            
            $checkUser=Users::whereNull('deleted_at')->find($request->user_id);
            
            if($checkUser==null){
                return response()->json([
                    "success"=>false,
                    "message"=> "User not found !",
                    "errors"=>$error,
                ], 501);
            }
            
            $checkRegistration=ContestRegistration::whereNull('deleted_at')->where('user_id',$request->user_id)->where('contest_id',$request->quiz_id)->where("status",1)->count();
            
            if($checkRegistration!=0){
                return response()->json([
                    "success"=>true,
                    "message"=> "You have already registered for this quiz."
                ], 201);
            }
            
            
            
            if($checkExist->entry_fees > $checkUser->wallet_amt){
                return response()->json([
                    "success"=>false,
                    "message"=> "Low wallet amount ! Please add more amount to your wallet",
                    "errors"=>$error,
                ], 501);
            }
            
            
            
            $timestamp=date("Y-m-d H:i:s");
            $new=new ContestRegistration();
            $new->user_id=$request->user_id;
            $new->contest_id=$request->quiz_id;
            $new->status=1; // hard codded 1 25-8-2023
            $new->remark="Registration completed !";
            $new->created_at=$timestamp;
            $new->save();
            
            $old_amt=$checkUser->wallet_amt;
            // decreasing user wallet amt
            $checkUser->wallet_amt -=$checkExist->entry_fees;
            $checkUser->save();
            
            // maintaining logs
            $new_amt=$old_amt-$checkExist->entry_fees;
            $log=new WalletModel();
            $log->user_id=$request->user_id;
            $log->old_wallet_amt=$old_amt;
            $log->new_wallet_amt=$new_amt;
            $log->type=2;  // for substraction
            $log->contest_id=$request->quiz_id;
            $log->comment="Amount substracted for quiz registration";
            $log->created_at=$timestamp;
            $log->save();
            
            // check if the quiz is any time quiz then insert data for maintaing slot id
            if($checkExist->contest_id==3){
                // mean the quiz is any time quiz
                $defined_slot_count=$checkExist->slot_count;
                if($defined_slot_count==0 || $defined_slot_count==null || $defined_slot_count==""){
                    return response()->json([
                        "success"=>false,
                        "message"=> "Slot limit is not defined for this quiz. Please ask admin to define the slot limit.",
                        "errors"=>$error,
                    ], 501);
                }
                
                $slot_id_for_registration=$checkExist->current_slot_id;
                if($checkExist->current_slot_id==0){
                    // this mean no one has registered till now
                    $checkExist->current_slot_id++;
                }
                else{
                    // some users has reitered so check if current slot is full
                    $current_registered_user_count= QuizSlot::where('contest_id', $request->quiz_id)->where('slot_id', $slot_id_for_registration)->whereNull('deleted_at')->count();   //_for_slot_id
                    if($current_registered_user_count>=$defined_slot_count){
                        // this slot is full
                        $checkExist->current_slot_id++;
                    }
                }
                
                // new registration in for this slot id
                $slot_id_for_registration=$checkExist->current_slot_id;
                $new=new QuizSlot();
                $new->user_id=$request->user_id;
                $new->contest_id=$request->quiz_id;
                $new->slot_id=$slot_id_for_registration;
                $new->created_at=$timestamp;
                $new->save();
                
            }
            
            // increasing registration count and total_amt
            $checkExist->total_amt=$checkExist->total_amt+$checkExist->entry_fees;
            $checkExist->total_registration=$checkExist->total_registration+1;
            $checkExist->save();
            
            return response()->json([
                    "success"=>true,
                    "message"=> "You have successfully registered for the quiz !",
                    "wallet_amt"=> round($new_amt)
            ], 201);
            
        }
        catch(Exception $err){
            $message="Internal server error: ".$err->getMessage()." On ".date("Y-m-d H:i:s");
            Log::error($message);
             return response()->json([
                "success"=>false,
                "message"=> "Internal server error",
                "errors"=>[]
            ], 501);
        }
    }
    
    function start_quiz(Request $request){
        $error=[];
        
        
        if(!isset($request->user_id) || is_null($request->user_id)){
            $error["user_id"]="User ID is required";
        }
        
        if(!isset($request->quiz_id) || is_null($request->quiz_id)){
            $error["quiz_id"]="Quiz ID is required";
        }
        
        
        if(count($error)>0){
            return response()->json([
                "success"=>false,
                "message"=> "Some required information is missing.",
                "errors"=>$error
            ], 501);
        }
        
        try{
            
            $timestamp=date("Y-m-d H:i:s");
            $checkExist=Contests::whereNull('deleted_at')->where('start_time','<=',$timestamp)->find($request->quiz_id);
            
            if($checkExist==null){
                return response()->json([
                    "success"=>false,
                    "message"=> "Quiz is not started yet !",
                    "errors"=>$error,
                ], 501);
            }
            
            $checkUser=Users::whereNull('deleted_at')->find($request->user_id);
            
            if($checkUser==null){
                return response()->json([
                    "success"=>false,
                    "message"=> "User not found !",
                    "errors"=>$error,
                ], 501);
            }
            
            $checkRegistration=ContestRegistration::whereNull('deleted_at')->where('user_id',$request->user_id)->where('contest_id',$request->quiz_id)->count();
            
            if($checkRegistration==0){
                return response()->json([
                    "success"=>false,
                    "message"=> "You have not registered for this quiz. Please register first.",
                    "errors"=>$error,
                ], 501);
            }
            
            // if($checkExist->contest_id==1){
                // user can enter within 5 mins only
                $start_time=$checkExist->start_time;
                $current_time=date("Y-m-d H:i:s", strtotime('-5 minutes'));
                
                if($current_time>$start_time){
                    return response()->json([
                        "success"=>false,
                        "message"=> "Quiz has already started , Now you can not enetr in quiz.",
                        "errors"=>$error,
                    ], 501);
                }
                
            // }
            
            
            
            $checkUserHasPlayed=LeaderBoard::whereNull('deleted_at')->where('user_id',$request->user_id)->where('contest_id',$request->quiz_id)->first();
            
            if($checkUserHasPlayed!=null){
                if($checkUserHasPlayed->is_completed==2){
                    return response()->json([
                        "success"=>false,
                        "message"=> "You have already submitted  the quiz !",
                        "is_submitted"=>true
                    ], 201);
                }
                
                
                return response()->json([
                    "success"=>false,
                    "message"=> "You have already started the quiz !",
                ], 201);
            }
            else{
                
                if($checkExist->contest_id==3){
                    // new logic for any time quiz. If user have registered then check his slot is full or not ? if not then he can not play the quiz and money will be returned
                    $slot_details=QuizSlot::select('slot_id')->whereNull('deleted_at')->where('user_id',$request->user_id)->where('contest_id',$request->quiz_id)->first();
                    if($slot_details==null){
                        return response()->json([
                            "success"=>false,
                            "message"=> "You are not found in any slot. Please contact admin.",
                        ], 201);  
                    }
                    $user_slot_id=$slot_details['slot_id'];
                    
                    $slot_registration_count = QuizSlot::whereNull('deleted_at')->where('slot_id',$user_slot_id)->where('contest_id',$request->quiz_id)->count();
                    
                    if($slot_registration_count != $checkExist->slot_count){
                        // then cancel his registration and return money to wallet
                        $return_amout=$checkExist->entry_fees;
                        
                        $old_amt=$checkUser->wallet_amt;
                        // adding user wallet amt
                        $checkUser->wallet_amt +=$return_amout;
                        $checkUser->save();
                        
                        // maintaining logs
                        $new_amt=$old_amt+$return_amout;
                        $log=new WalletModel();
                        $log->user_id=$request->user_id;
                        $log->old_wallet_amt=$old_amt;
                        $log->new_wallet_amt=$new_amt;
                        $log->type=1;  // for addition
                        $log->contest_id=$request->quiz_id;
                        $log->comment="Amount added for auto quiz(any time) cancellation on ".$timestamp;
                        $log->created_at=$timestamp;
                        $log->save();
                        
                        // decreasing registrtion count and registration number
                        $checkExist->total_amt -= $checkExist->entry_fees;
                        $checkExist->total_registration -= 1;
                        $checkExist->save();
                        
                        // deleting user registration
                        ContestRegistration::where('user_id',$request->user_id)->where('contest_id',$request->quiz_id)->update(['deleted_at'=>$timestamp]);
                        QuizSlot::where('user_id',$request->user_id)->where('contest_id',$request->quiz_id)->update(['deleted_at'=>$timestamp]);
                        
                        return response()->json([
                            "success"=>false,
                            "message"=> "Money is added to wallet ! Your slot was incomplete so your registration has been cancelled.",
                            "money_added"=>true,
                            "new_amount"=>$new_amt
                        ], 201); 
                    }
                }
                
                $startQuiz=new LeaderBoard();
                $startQuiz->user_id=$request->user_id;
                $startQuiz->contest_id=$request->quiz_id;
                $startQuiz->contest_start_date_time=$timestamp;
                $startQuiz->created_at=$timestamp;
                $startQuiz->save();
                
                return response()->json([
                    "success"=>true,
                    "message"=> "Quiz started",
                ], 201);
            }
        }
        catch(Exception $err){
            $message="Internal server error: ".$err->getMessage()." On ".date("Y-m-d H:i:s");
            Log::error($message);
            
            
             return response()->json([
                "success"=>false,
                "message"=> "Internal server error",
                "errors"=>[]
            ], 501);
        }
    }
    
    function update_live_quiz(Request $request){
     
        
        $error=[];
        
        
        if(!isset($request->user_id) || is_null($request->user_id)){
            $error["user_id"]="User ID is required";
        }
        
        if(!isset($request->quiz_id) || is_null($request->quiz_id)){
            $error["quiz_id"]="Quiz ID is required";
        }
        
        if(!isset($request->ans_key) || is_null($request->ans_key)){
            $error["ans_key"]="Ans Key is required";
        }
        
        
        if(count($error)>0){
            return response()->json([
                "success"=>false,
                "message"=> "Some required information is missing",
                "errors"=>$error
            ], 501);
        }
        
        try{
            $timestamp=date("Y-m-d H:i:s");
            $checkExist=Contests::whereNull('deleted_at')->where('end_date_time','>=',$timestamp)->find($request->quiz_id);
            
            if($checkExist==null){
                return response()->json([
                    "success"=>false,
                    "message"=> "Sorry ! Quiz has been ended. Now we can not take your submission",
                    "errors"=>$error,
                    "quiz_ended"=>true
                ], 501);
            }
            
            $checkUser=Users::whereNull('deleted_at')->find($request->user_id);
            
            if($checkUser==null){
                return response()->json([
                    "success"=>false,
                    "message"=> "User not found !",
                    "errors"=>$error,
                ], 501);
            }
            
            
            $checkUser=LeaderBoard::whereNull('deleted_at')->where('user_id',$request->user_id)->where('contest_id',$request->quiz_id)->first();
            
            if($checkUser==null){
                return response()->json([
                    "success"=>false,
                    "message"=> "You have not started this quiz ! Start first",
                ], 201);
            }
            else{
                $id=$checkUser->id;
                $endQuiz=LeaderBoard::find($id);
                $endQuiz->ans_key=$request->ans_key;
                $endQuiz->is_completed=1; // marked as playing
                $endQuiz->updated_at=$timestamp;
                $endQuiz->save();
                
                return response()->json([
                "success"=>true,
                "message"=> "Quiz saved successfully !",
            ], 201);
            }
        }
        catch(Exception $err){
            
            $message="Internal server error: ".$err->getMessage()." On ".date("Y-m-d H:i:s");
            Log::error($message);
            
             return response()->json([
                "success"=>false,
                "message"=> "Internal server error",
                "errors"=>[]
            ], 501);
        }
    }
    
    // start video call
    function start_video_call(Request $request){
     
        
        $error=[];
        
        
        if(!isset($request->call_id) || is_null($request->call_id)){
            $error["call_id"]="Call ID is required";
        }
        
        if(!isset($request->quiz_id) || is_null($request->quiz_id)){
            $error["quiz_id"]="Quiz ID is required";
        }
        
        if(!isset($request->participants) || is_null($request->participants)){
            $error["participants"]="Call participants are required is required";
        }
        
        
        if(count($error)>0){
            return response()->json([
                "success"=>false,
                "message"=> "Some required information is missing",
                "errors"=>$error
            ], 501);
        }
        
        try{
            $timestamp=date("Y-m-d H:i:s");
            // ->where('end_date_time','<=',$timestamp)
            $checkExist=Contests::whereNull('deleted_at')->Where('contest_id',1)->find($request->quiz_id);
            
            if($checkExist==null){
                return response()->json([
                    "success"=>false,
                    "message"=> "No Live quiz found for given id !",
                    "errors"=>$error,
                ], 501);
            }
            
            // if($checkExist->video_call_start_timestamp!=null){
            //     // one video call completed
            //     return response()->json([
            //         "success"=>false,
            //         "message"=> "You have already completed one video call for this quiz.",
            //         "errors"=>$error,
            //     ], 501);
            // }
            
            
            $videoCallId=trim($request->call_id);
            
            $participants=json_decode($request->participants);
            $result=Users::whereIn('id',$participants)->update([
                "in_video_call"=>1,
                "video_call_contest_id"=>$request->quiz_id,
                "video_call_id"=>$videoCallId
            ]);
            if(!$result){
                return response()->json([
                    "success"=>false,
                    "message"=> "Unable to send invitation to the users ! Please try again later",
                    "errors"=>$error,
                ], 501);
            }
            $null=null;
            $checkExist->video_call_start_timestamp=$timestamp;
            $checkExist->video_call_end_timestamp=$null;
            $checkExist->video_call_id=$videoCallId;
            $checkExist->save();
            
                
            return response()->json([
                "success"=>true,
                "message"=> "Invitation sent !",
            ], 201);
            
        }
        catch(Exception $err){
            $message="Internal server error: ".$err->getMessage()." On ".date("Y-m-d H:i:s");
            Log::error($message);
            
            
             return response()->json([
                "success"=>false,
                "message"=> "Internal server error",
                "errors"=>[]
            ], 501);
        }
    }
    
    function start_streaming(Request $request){
     
        
        $error=[];
        
        
        if(!isset($request->call_id) || is_null($request->call_id)){
            $error["call_id"]="Call ID is required";
        }
        
        if(!isset($request->quiz_id) || is_null($request->quiz_id)){
            $error["quiz_id"]="Quiz ID is required";
        }
       
        if(count($error)>0){
            return response()->json([
                "success"=>false,
                "message"=> "Some required information is missing",
                "errors"=>$error
            ], 501);
        }
        
        try{
            $timestamp=date("Y-m-d H:i:s");
            // ->where('end_date_time','<=',$timestamp)
            $checkExist=Contests::whereNull('deleted_at')->Where('contest_id',1)->find($request->quiz_id);
            
            if($checkExist==null){
                return response()->json([
                    "success"=>false,
                    "message"=> "No Live quiz found for given id !",
                    "errors"=>$error,
                ], 501);
            }
            
            // if($checkExist->video_call_start_timestamp!=null){
            //     // one video call completed
            //     return response()->json([
            //         "success"=>false,
            //         "message"=> "You have already completed one video call for this quiz.",
            //         "errors"=>$error,
            //     ], 501);
            // }
            
            
            $videoCallId=trim($request->call_id);
            
            
            $null=null;
            $checkExist->video_call_start_timestamp=$timestamp;
            $checkExist->video_call_end_timestamp=$null;
            $checkExist->video_call_id=$videoCallId;
            $checkExist->save();
            
                
            return response()->json([
                "success"=>true,
                "message"=> "Invitation sent !",
            ], 201);
            
        }
        catch(Exception $err){
            $message="Internal server error: ".$err->getMessage()." On ".date("Y-m-d H:i:s");
            Log::error($message);
            
            
             return response()->json([
                "success"=>false,
                "message"=> "Internal server error",
                "errors"=>[]
            ], 501);
        }
    }
    
    function end_video_call(Request $request){
     
        
        $error=[];
        
        if(!isset($request->quiz_id) || is_null($request->quiz_id) && $request->quiz_id<=0){
            $error["quiz_id"]="Valid Quiz ID is required";
        }
        
        if(count($error)>0){
            return response()->json([
                "success"=>false,
                "message"=> "Some required information is missing",
                "errors"=>$error
            ], 501);
        }
        
        try{
            $timestamp=date("Y-m-d H:i:s");
            $checkExist=Contests::Where('contest_id',1)->find($request->quiz_id);
            
            if($checkExist==null){
                return response()->json([
                    "success"=>false,
                    "message"=> "No Live quiz found for given id !",
                    "errors"=>$error,
                ], 501);
            }
            
            if($checkExist->video_call_end_timestamp!=null){
                // one video call completed
                return response()->json([
                    "success"=>false,
                    "message"=> "Video call already ended !",
                    "errors"=>$error,
                ], 501);
            }
            $checkExist->video_call_end_timestamp=$timestamp;
            $checkExist->save();
            
            $videoCallId=trim($request->call_id);
            
            $participants=json_decode($request->participants);
            $result=Users::where('video_call_contest_id',$request->quiz_id)->update([
                "in_video_call"=>0,
                "video_call_contest_id"=>0,
                "video_call_id"=>""
            ]);
            if(!$result){
                return response()->json([
                    "success"=>false,
                    "message"=> "Unable to end video call ! Please try again later",
                    "errors"=>$error,
                ], 501);
            }
           
            return response()->json([
                "success"=>true,
                "message"=> "Video call ended !",
            ], 201);
            
        }
        catch(Exception $err){
            $message="Internal server error: ".$err->getMessage()." On ".date("Y-m-d H:i:s");
            Log::error($message);
            
            
             return response()->json([
                "success"=>false,
                "message"=> "Internal server error",
                "errors"=>[]
            ], 501);
        }
    }
    
    // check user video call
    function check_user_video_call(Request $request){
     
        
        $error=[];
        
        
        if(!isset($request->user_id) || is_null($request->user_id)){
            $error["user_id"]="User ID is required";
        }
        
       
        if(count($error)>0){
            return response()->json([
                "success"=>false,
                "message"=> "Some required information is missing",
                "errors"=>$error
            ], 501);
        }
        
        try{
            $timestamp=date("Y-m-d H:i:s");
           
            
            $checkUser=Users::select('in_video_call','video_call_id','video_call_contest_id')->whereNull('deleted_at')->find($request->user_id);
            
            if($checkUser==null){
                return response()->json([
                    "success"=>false,
                    "message"=> "User not found !",
                    "errors"=>$error,
                ], 401);
            }
            
            if($checkUser->in_video_call==1){
               $quiz_id= $checkUser->video_call_contest_id;
               $contests=Contests::select('video_call_end_timestamp')->whereNull('deleted_at')->where('id',$quiz_id)->first();
               if($contests->video_call_end_timestamp!=null){
                 return response()->json([
                    "success"=>false,
                    "message"=> "Not in video call !",
                    "errors"=>$error,
                 ], 201); 
               }
               else{
                   $call_id= $checkUser->video_call_id;
                   return response()->json([
                    "success"=>true,
                    "message"=> "In video call !",
                    "call_id"=>$call_id,
                    "quiz_id"=>$quiz_id,
                    "errors"=>$error,
                ], 201); 
               }
            }
            else{
               return response()->json([
                    "success"=>false,
                    "message"=> "Not in video call !",
                    "errors"=>$error,
                ], 201); 
            }
        }
        catch(Exception $err){
            
            $message="Internal server error: ".$err->getMessage()." On ".date("Y-m-d H:i:s");
            Log::error($message);
            
            
             return response()->json([
                "success"=>false,
                "message"=> "Internal server error",
                "errors"=>[]
            ], 501);
        }
    }
    
    function user_leave_video_call(Request $request){
        $error=[];
        
        
        if(!isset($request->user_id) || is_null($request->user_id)){
            $error["user_id"]="User ID is required";
        }
        
       
        if(count($error)>0){
            return response()->json([
                "success"=>false,
                "message"=> "Some required information is missing",
                "errors"=>$error
            ], 501);
        }
        
        try{
            $timestamp=date("Y-m-d H:i:s");
           
            
            $checkUser=Users::find($request->user_id);
            
            if($checkUser==null){
                return response()->json([
                    "success"=>false,
                    "message"=> "User not found !",
                    "errors"=>$error,
                ], 401);
            }
            $quiz_id=$checkUser->video_call_contest_id;
            
            // checking if user is the host
            $checkExist=Contests::whereNull('deleted_at')->Where('host_user_id',$request->user_id)->find($quiz_id);
            $null=null;
            if($checkExist!=null){
                // then video call
                $checkExist->video_call_end_timestamp=$timestamp;
                $checkExist->video_call_id=$null;
                $checkExist->save();
            }
            
            $checkUser->in_video_call=0;
            $checkUser->save();
           
                return response()->json([
                    "success"=>true,
                    "message"=> "You have exited from video call !",
                    "errors"=>$error,
                ], 201); 
        }
        catch(Exception $err){
            
            $message="Internal server error: ".$err->getMessage()." On ".date("Y-m-d H:i:s");
            Log::error($message);
            
            
             return response()->json([
                "success"=>false,
                "message"=> "Internal server error",
                "errors"=>[]
            ], 501);
        }
    }
    
    
    function submit_quiz(Request $request){
     
        
        $error=[];
        
        
        if(!isset($request->user_id) || is_null($request->user_id)){
            $error["user_id"]="User ID is required";
        }
        
        if(!isset($request->quiz_id) || is_null($request->quiz_id)){
            $error["quiz_id"]="Quiz ID is required";
        }
        
        if(!isset($request->ans_key) || is_null($request->ans_key)){
            $error["ans_key"]="Ans Key is required";
        }
        
        
        if(count($error)>0){
            return response()->json([
                "success"=>false,
                "message"=> "Some required information is missing",
                "errors"=>$error
            ], 501);
        }
        
        try{
            $timestamp=date("Y-m-d H:i:s");
            $checkExist=Contests::whereNull('deleted_at')->where('end_date_time','>=',$timestamp)->find($request->quiz_id);
            
            if($checkExist==null){
                return response()->json([
                    "success"=>true,
                    "message"=> "Sorry ! Quiz has been ended. Now we can not take your submission",
                    "errors"=>$error,
                    "quiz_ended"=>true
                ], 501);
            }
            
            $checkUser=Users::whereNull('deleted_at')->find($request->user_id);
            
            if($checkUser==null){
                return response()->json([
                    "success"=>false,
                    "message"=> "User not found !",
                    "errors"=>$error,
                ], 501);
            }
            
            
            $checkUser=LeaderBoard::whereNull('deleted_at')->where('user_id',$request->user_id)->where('contest_id',$request->quiz_id)->first();
            
            if($checkUser==null){
                return response()->json([
                    "success"=>false,
                    "message"=> "You have not started this quiz ! Start first",
                ], 201);
            }
            else{
                $id=$checkUser->id;
                $endQuiz=LeaderBoard::find($id);
                $endQuiz->ans_key=$request->ans_key;
                $endQuiz->is_completed=2; // marked as done
                $endQuiz->contest_end_date_time=$timestamp;
                $endQuiz->updated_at=$timestamp;
                $endQuiz->save();
                
                return response()->json([
                "success"=>true,
                "message"=> "Quiz submitted successfully !",
            ], 201);
            }
        }
        catch(Exception $err){
            
            $message="Internal server error: ".$err->getMessage()." On ".date("Y-m-d H:i:s");
            Log::error($message);
            
             return response()->json([
                "success"=>false,
                "message"=> "Internal server error",
                "errors"=>[]
            ], 501);
        }
    }
    
    function quiz_prizepoll(Request $request){
     
        
        $error=[];
        
        
        if(!isset($request->quiz_id) || is_null($request->quiz_id)){
            $error["quiz_id"]="Quiz ID is required";
        }
        
        
        if(count($error)>0){
            return response()->json([
                "success"=>false,
                "message"=> "Some required information is missing",
                "errors"=>$error
            ], 501);
        }
        
        try{
            $timestamp=date("Y-m-d H:i:s");
            $checkExist=Contests::whereNull('deleted_at')->find($request->quiz_id);
            
            if($checkExist==null){
                return response()->json([
                    "success"=>false,
                    "message"=> "Quiz not found !",
                    "errors"=>$error,
                ], 501);
            }
            
            if($checkExist["total_amt"]<500){
                return response()->json([
                    "success"=>false,
                    "message"=> "Total registration count is too low. Please Increase number of registration to view prize pool.",
                    "errors"=>$error,
                ], 401);
            }
            
            $prize_amt=$checkExist->total_amt;
            $prize_distribution=json_decode($checkExist->winner_percent_distribution);
            
            $prize_pool=[];
            
            foreach($prize_distribution as $key=> $p){
                $prize_pool[$key]["rank"]=$key+1;
                $prize_pool[$key]["amt"]=$p*$prize_amt/100;
            }
            
           
            
            return response()->json([
                "success"=>true,
                "message"=> "Details fetched !",
                "data"=> $prize_pool,
                "contest_details"=>$checkExist
            ], 201);
        }
        catch(Exception $err){
            $message="Internal server error: ".$err->getMessage()." On ".date("Y-m-d H:i:s");
            Log::error($message);
            
            
             return response()->json([
                "success"=>false,
                "message"=> "Internal server error",
                "errors"=>[]
            ], 501);
        }
    }
    
    function host_quiz_leaderboard(Request $request){

        $error=[];
        
        if(!isset($request->quiz_id) || is_null($request->quiz_id)){
            $error["quiz_id"]="Quiz ID is required";
        }
        
        
        if(count($error)>0){
            return response()->json([
                "success"=>false,
                "message"=> "Some required information is missing",
                "errors"=>$error
            ], 501);
        }
        
        try{
            
            $timestamp=date("Y-m-d H:i:s");
            $checkExist=Contests::whereNull('deleted_at')->find($request->quiz_id);
            
            if($checkExist==null){
                return response()->json([
                    "success"=>false,
                    "message"=> "Quiz not found !",
                    "errors"=>$error,
                ], 501);
            }
            
           
            
            $prize_amt=$checkExist->total_amt;
            $prize_distribution=json_decode($checkExist->winner_percent_distribution);
            

            $userRank=0;
            $is_winner=false; // for specific user
            $my_earning=0;
            
            
            
            $quizLeaderboard=LeaderBoard::select("contest_leaderboard.rank_in_this_contest","contest_leaderboard.is_winner","contest_leaderboard.user_final_marks",'users.name','users.id')
            ->join("users","users.id","contest_leaderboard.user_id")->orderBy("contest_leaderboard.rank_in_this_contest")
            ->whereNull('contest_leaderboard.deleted_at')->whereNotNull('contest_leaderboard.ans_key')->whereNotNull('contest_leaderboard.rank_in_this_contest')->where('contest_leaderboard.contest_id',$request->quiz_id)->where('contest_leaderboard.is_final_marks_calculated',1)->get();
            
            foreach($quizLeaderboard as $key=>$row){
                $earning=0;
                if($row["is_winner"]==1){
                    $index=$row["rank_in_this_contest"]-1;
                    $earning=$prize_amt*$prize_distribution[$index]/100; // no need to validate index because of checked is_winner 
                }
                
                // adding earning column
                $quizLeaderboard[$key]["earning"]=$earning;
                
            }
            
            return response()->json([
                "success"=>true,
                "message"=> "Details fetched !",
                "data"=> $quizLeaderboard,
                "my_rank"=>$userRank,
                "is_winner"=>$is_winner,
                "my_earning"=>$my_earning,
                "contest_details"=>$checkExist
            ], 201);
        }
        catch(Exception $err){
            $message="Internal server error: ".$err->getMessage()." On ".date("Y-m-d H:i:s");
            Log::error($message);
            
            
             return response()->json([
                "success"=>false,
                "message"=> "Internal server error",
                "errors"=>[]
            ], 501);
        }
    }
    
    function quiz_leaderboard(Request $request){
     
        
        $error=[];
        
        
        if(!isset($request->user_id) || is_null($request->user_id)){
            $error["user_id"]="User ID is required";
        }
        
        if(!isset($request->quiz_id) || is_null($request->quiz_id)){
            $error["quiz_id"]="Quiz ID is required";
        }
        
        
        if(count($error)>0){
            return response()->json([
                "success"=>false,
                "message"=> "Some required information is missing",
                "errors"=>$error
            ], 501);
        }
        
        try{
            $timestamp=date("Y-m-d H:i:s");
            $checkExist=Contests::whereNull('deleted_at')->find($request->quiz_id);
            
            if($checkExist==null){
                return response()->json([
                    "success"=>false,
                    "message"=> "Quiz not found !",
                    "errors"=>$error,
                ], 501);
            }
            
            if($checkExist->contest_id!=1 && $checkExist->result_declared==0){
                 return response()->json([
                    "success"=>false,
                    "message"=> "Result is not declared yet !",
                    "errors"=>$error,
                ], 401);
            }
            
            $prize_amt=$checkExist->total_amt;
            $prize_distribution=json_decode($checkExist->winner_percent_distribution);
            
            
            
            $checkUser=Users::whereNull('deleted_at')->find($request->user_id);
            
            if($checkUser==null){
                return response()->json([
                    "success"=>false,
                    "message"=> "User not found !",
                    "errors"=>$error,
                ], 501);
            }
            
            
            
            $userRank=0;
            $is_winner=false; // for specific user
            $my_earning=0;
            $checkUser=LeaderBoard::whereNull('deleted_at')->whereNotNull('ans_key')->where('user_id',$request->user_id)->where('contest_id',$request->quiz_id)->first();
            
            if($checkUser!=null){
               $userRank=$checkUser->rank_in_this_contest;
               $is_winner=$checkUser->is_winner==1?true:false;
                if($is_winner){
                    $index=$userRank-1;
                    $my_earning=$prize_amt*($prize_distribution[$index])/100; // no need to validate index because of checked is_winner 
                }
            }
            
            $otherParticipant=[];
            
            if($checkExist->contest_id==3){
                $prize_amt=$checkExist->entry_fees*$checkExist->slot_count;
                
                $slot_details=QuizSlot::whereNull('deleted_at')->where('user_id',$request->user_id)->where('contest_id',$request->quiz_id)->first();
                $slot_id=$slot_details['slot_id'];
                
                
                $otherParticipant=QuizSlot::select('user_id')->whereNull('deleted_at')->where('slot_id',$slot_id)->where('contest_id',$request->quiz_id)->pluck('user_id');
                
                $otherParticipant=$otherParticipant ?? [];
                
                $quizLeaderboard=LeaderBoard::select("contest_leaderboard.rank_in_this_contest","contest_leaderboard.is_winner","contest_leaderboard.user_final_marks",'users.name','users.id')
                ->join("users","users.id","contest_leaderboard.user_id")->orderBy("contest_leaderboard.rank_in_this_contest")
                ->whereNull('contest_leaderboard.deleted_at')->whereNotNull('contest_leaderboard.ans_key')
                ->whereNotNull('contest_leaderboard.rank_in_this_contest')->where('contest_leaderboard.contest_id',$request->quiz_id)
                ->where('contest_leaderboard.is_final_marks_calculated',1)
                ->whereIn('contest_leaderboard.user_id',$otherParticipant)
                ->get();
                
            }
            else{
                $quizLeaderboard=LeaderBoard::select("contest_leaderboard.rank_in_this_contest","contest_leaderboard.is_winner","contest_leaderboard.user_final_marks",'users.name','users.id')
                ->join("users","users.id","contest_leaderboard.user_id")->orderBy("contest_leaderboard.rank_in_this_contest")
                ->whereNull('contest_leaderboard.deleted_at')->whereNotNull('contest_leaderboard.ans_key')->whereNotNull('contest_leaderboard.rank_in_this_contest')->where('contest_leaderboard.contest_id',$request->quiz_id)->where('contest_leaderboard.is_final_marks_calculated',1)->get();
            }
            
            
            
            foreach($quizLeaderboard as $key=>$row){
                $earning=0;
                if($row["is_winner"]==1){
                    $index=$row["rank_in_this_contest"]-1;
                    $earning=$prize_amt*$prize_distribution[$index]/100; // no need to validate index because of checked is_winner 
                }
                
                // adding earning column
                $quizLeaderboard[$key]["earning"]=$earning;
                
            }
            
            return response()->json([
                "success"=>true,
                "message"=> "Details fetched !",
                "data"=> $quizLeaderboard,
                "my_rank"=>$userRank,
                "is_winner"=>$is_winner,
                "my_earning"=>$my_earning,
                "contest_details"=>$checkExist
            ], 201);
        }
        catch(Exception $err){
            $message="Internal server error: ".$err->getMessage()." On ".date("Y-m-d H:i:s");
            Log::error($message);
            
            
             return response()->json([
                "success"=>false,
                "message"=> "Internal server error",
                "errors"=>[]
            ], 501);
        }
    }
    
    function live_quiz_leaderboard(Request $request){
     
        
        $error=[];
        
        if(!isset($request->quiz_id) || is_null($request->quiz_id)){
            $error["quiz_id"]="Quiz ID is required";
        }
        
        
        if(count($error)>0){
            return response()->json([
                "success"=>false,
                "message"=> "Some required information is missing",
                "errors"=>$error
            ], 501);
        }
        
        try{
            $timestamp=date("Y-m-d H:i:s");
            $checkExist=Contests::whereNull('deleted_at')->where('end_date_time','>',$timestamp)->find($request->quiz_id);
            
            if($checkExist==null){
                return response()->json([
                    "success"=>false,
                    "message"=> "This quiz has ended. Please try again later.",
                    "errors"=>$error,
                ], 401);
            }
            
            // if($checkExist->result_declared==0){
            //      return response()->json([
            //         "success"=>false,
            //         "message"=> "Result is not declared yet !",
            //         "errors"=>$error,
            //     ], 401);
            // }
            
            $prize_amt=$checkExist->total_amt;
            $prize_distribution=json_decode($checkExist->winner_percent_distribution);
            
            $checkUser=Users::whereNull('deleted_at')->find($request->user_id);
            
            if($checkUser==null){
                return response()->json([
                    "success"=>false,
                    "message"=> "User not found !",
                    "errors"=>$error,
                ], 501);
            }
            
            $userRank=0;
            $is_winner=false; // for specific user
            $my_earning=0;
            $checkUser=LeaderBoard::whereNull('deleted_at')->whereNotNull('ans_key')->where('user_id',$request->user_id)->where('contest_id',$request->quiz_id)->first();
            
            if($checkUser!=null){
               $userRank=$checkUser->rank_in_this_contest;
               $is_winner=$checkUser->is_winner==1?true:false;
                if($is_winner){
                    $index=$userRank-1;
                    $my_earning=$prize_amt*($prize_distribution[$index])/100; // no need to validate index because of checked is_winner 
                }
            }
            
            $quizLeaderboard=LeaderBoard::select("contest_leaderboard.rank_in_this_contest","contest_leaderboard.is_winner","contest_leaderboard.user_final_marks",'users.name','users.id')
            ->join("users","users.id","contest_leaderboard.user_id")->orderBy("contest_leaderboard.rank_in_this_contest")
            ->whereNull('contest_leaderboard.deleted_at')->whereNotNull('contest_leaderboard.ans_key')->whereNotNull('contest_leaderboard.rank_in_this_contest')->where('contest_leaderboard.contest_id',$request->quiz_id)->where('contest_leaderboard.is_final_marks_calculated',1)->get();
            
            foreach($quizLeaderboard as $key=>$row){
                $earning=0;
                if($row["is_winner"]==1){
                    $index=$row["rank_in_this_contest"]-1;
                    $earning=$prize_amt*$prize_distribution[$index]/100; // no need to validate index because of checked is_winner 
                }
                
                // adding earning column
                $quizLeaderboard[$key]["earning"]=$earning;
                
            }
            
            return response()->json([
                "success"=>true,
                "message"=> "Details fetched !",
                "data"=> $quizLeaderboard,
                "my_rank"=>$userRank,
                "is_winner"=>$is_winner,
                "my_earning"=>$my_earning,
                "contest_details"=>$checkExist
            ], 201);
        }
        catch(Exception $err){
            $message="Internal server error: ".$err->getMessage()." On ".date("Y-m-d H:i:s");
            Log::error($message);
            
            
             return response()->json([
                "success"=>false,
                "message"=> "Internal server error",
                "errors"=>[]
            ], 501);
        }
    }
    
    function user_leaderboard(Request $request){
     
        
        $error=[];
        
        
        if(!isset($request->user_id) || is_null($request->user_id)){
            $error["user_id"]="User ID is required";
        }
        
        
        
        if(count($error)>0){
            return response()->json([
                "success"=>false,
                "message"=> "Some required information is missing",
                "errors"=>$error
            ], 501);
        }
        
        try{
            $timestamp=date("Y-m-d H:i:s");
            
            
            
            $checkUser=Users::whereNull('deleted_at')->find($request->user_id);
            
            if($checkUser==null){
                return response()->json([
                    "success"=>false,
                    "message"=> "User not found !",
                    "errors"=>$error,
                ], 501);
            }
            
            
            $quizLeaderboard=LeaderBoard::select("contest_leaderboard.rank_in_this_contest","contest_leaderboard.is_winner","contest_leaderboard.user_final_marks",
            'contests.id','contests.total_amt','contests.winner_percent_distribution')
            ->join("contests","contests.id","contest_leaderboard.contest_id")->orderBy("contest_leaderboard.contest_end_date_time", 'DESC')
            ->whereNull('contest_leaderboard.deleted_at')->where('contest_leaderboard.user_id',$request->user_id)
            ->whereNotNull('contest_leaderboard.rank_in_this_contest')->whereNotNull('contest_leaderboard.ans_key')->where('contest_leaderboard.is_final_marks_calculated',1)->get();
            $totalEarning=0;
            foreach($quizLeaderboard as $key=>$row){
                $prize_amt=$row["total_amt"];
                $prize_distribution=json_decode($row["winner_percent_distribution"]);
                $earning=0;
                if($row["is_winner"]==1){
                    $index=$row["rank_in_this_contest"]-1;
                    $earning=$prize_amt*($prize_distribution[$index]/100); // no need to validate index because of checked is_winner 
                }
                
                // adding earning column
                $quizLeaderboard[$key]["earning"]=$earning;
                $totalEarning+=$earning;
            }
            
            return response()->json([
                "success"=>true,
                "message"=> "Details fetched !",
                "data"=> $quizLeaderboard,
                "total_earning"=>$totalEarning
            ], 201);
        }
        catch(Exception $err){
            $message="Internal server error: ".$err->getMessage()." On ".date("Y-m-d H:i:s");
            Log::error($message);
            
            
             return response()->json([
                "success"=>false,
                "message"=> "Internal server error",
                "errors"=>[]
            ], 501);
        }
    }
    
    
    
    function calculate_final_marks(Request $request){
        // for calculation final marks of users
        
        $contests=Contests::where("result_declared",0)->get();
        
        foreach($contests as $key=> $contest){
            $c_id=$contest->id;
            $contest_leaderboard=LeaderBoard::where('contest_id',$c_id)->whereNotNull("ans_key")->get();
           
            $questions=json_decode($contest->question_ids);
            $question_count=count($questions);
            $per_question_mark=$contest->per_question_mark;
            $negative_mark=$contest->neg_mark;
            $qustionAnsMap=[];
            
            if(!is_null($questions)){
                foreach ($questions as $key => $id) {
                    $question_details=Questions::find($id);
                    $c_option=json_decode($question_details->correct_option);
                    sort($c_option);
                    $qustionAnsMap[$id]=$c_option; // sort is important
                 }
            }  
            
            foreach($contest_leaderboard as $key=> $participants){
                
                if($participants->is_final_marks_calculated==1 || $participants->ans_key==null){
                    continue;
                }
                else{
                    $total_right_ans=0;
                    $total_negative_ans=0;
                    $final_mark=0;
                    $temp=json_decode($participants->ans_key); // all the ans questions
                    
                    foreach ($temp as $key=> $value){
                        if($value!=null && count($value)>0){
                            // checking if user have selected any option
                            $attemptedQuestions[$key] = $value;
                        }
                    } 
                        
                        
                    
                    foreach($attemptedQuestions as $key => $selectedOptions){ 
                        $questionId=$key; // answered question id
                        sort($selectedOptions); // selected options array
                        
                        // echo "<pre>";
                        // echo "selected: ".json_encode($selectedOptions)." correct ans: ".json_encode($qustionAnsMap[$questionId]);
                        
                        if(isset($qustionAnsMap[$questionId]) && (count($selectedOptions)>0)){
                            $right_ans=$qustionAnsMap[$questionId];
                            // checking both array are same or not. Keep in mind that array must be sorted only then this logic will work
                            if($right_ans==$selectedOptions){
                                // user have selected right option
                                $final_mark = $final_mark + $per_question_mark;
                                $total_right_ans++;
                            }
                            else{
                                $final_mark = $final_mark - $negative_mark;
                                $total_negative_ans++;
                            }
                        }
                    }
                    
                    
                    
                    $edit=Leaderboard::find($participants->id);
                    $edit->negative_ans_count=$total_negative_ans;
                    $edit->correct_ans_count=$total_right_ans;
                    $edit->user_final_marks=$final_mark;
                    $edit->is_final_marks_calculated=$edit->is_completed==2?1:0;
                    $edit->save();
                }
            }
        }
        
        echo "Marks calculated !";
    }
    
    function calculate_final_ranks(Request $request){
        // for calculation final ranks of participants
        
        $contests=Contests::where("result_declared",0)->get();
        
        foreach($contests as $key=> $contest){
            $c_id=$contest->id;
            $c_type=$contest->contest_id;
            
            
            $query=LeaderBoard::select('contest_leaderboard.*','quiz_slot.slot_id')
            ->leftJoin('quiz_slot', function ($join) {
                $join->on('quiz_slot.contest_id','=','contest_leaderboard.contest_id')
                     ->on('quiz_slot.user_id','=','contest_leaderboard.user_id');
            })
            ->where('contest_leaderboard.contest_id',$c_id)
            ->whereNotNull("user_final_marks")->whereNull("contest_leaderboard.deleted_at");
            
            if($c_type==3){
                $query->whereNull("quiz_slot.deleted_at");
            }
            $contest_leaderboard=$query->orderBy("user_final_marks")->get();
            
            // $contest_leaderboard=LeaderBoard::select('contest_leaderboard.*','quiz_slot.slot_id')
            // ->leftJoin('quiz_slot', 'quiz_slot.user_id','=','contest_leaderboard.user_id')
            // ->where('quiz_slot.contest_id',$c_id)->whereNull('quiz_slot.deleted_at')
            // ->where('contest_leaderboard.contest_id',$c_id)
            // ->whereNotNull("user_final_marks")->orderBy("user_final_marks")->get();
           
            
            
            $questions=json_decode($contest->question_ids);
            $question_count=count($questions);
            $per_question_mark=$contest->per_question_mark;
            $negative_mark=$contest->neg_mark;
            $qustionAnsMap=[];
            $winner_count=$contest->winner_count;
           
            $arr=[];
            
            foreach($contest_leaderboard as $key=> $participants){
                
                $temp["leaderboard_id"]=$participants->id;
                $temp["id"]=$participants->user_id;
                $temp["slot_id"]=$participants->slot_id;
                $temp["marks"]=$participants->user_final_marks;
                $temp["total_negative"]=$participants->negative_ans_count;
                $temp["duration"]=strtotime($participants->contest_end_date_time)-strtotime($participants->contest_start_date_time);
                $temp["start_time"]=$participants->contest_start_date_time;
                array_push($arr,$temp);
            }
            
            // sort array based on different parameter
            usort($arr, function ($a, $b) {
                if ($a["marks"] !== $b["marks"]) {
                    return $b["marks"]-$a["marks"]; // marks in decending order
                }
                
                if ($a["duration"] !== $b["duration"]) {
                    return $a["duration"] - $b["duration"]; // duration in asecending order
                }
                
                if ($a["total_negative"] !== $b["total_negative"]) {
                    return $a["total_negative"] - $b["total_negative"]; // total negative in asc order
                }
                
                return strcmp($a["start_time"], $b["start_time"]); // time in asc order
            });
            
            
            
            $anyTimeQuizMap=[];
            
            foreach($arr as $key => $winner){
                
                $edit=Leaderboard::find($winner['leaderboard_id']);
                
                // new any time contest logic
                if($c_type==3){
                   // any time contest
                   if(isset($anyTimeQuizMap[$winner['slot_id']])){
                       $edit->rank_in_this_contest=$anyTimeQuizMap[$winner['slot_id']]+1;
                       $edit->is_winner=(($anyTimeQuizMap[$winner['slot_id']]+1)<=$winner_count)?($winner['marks']>0?1:0):0;
                       $anyTimeQuizMap[$winner['slot_id']]+=1;
                   }
                   else{
                       $edit->rank_in_this_contest=1;
                       $edit->is_winner=(1<=$winner_count)?($winner['marks']>0?1:0):0;
                       $anyTimeQuizMap[$winner['slot_id']]=1;
                   }
                }
                else{
                    // for normal quiz 
                    $edit->rank_in_this_contest=$key+1;
                    $edit->is_winner=($key<$winner_count)?($winner['marks']>0?1:0):0;
                }
                
                
                $edit->save();
            }
            
            if($contest->end_date_time<date("Y-m-d H:i:s")){
                $edit=Contests::find($contest->id);
                $edit->result_declared=1;
                $edit->save();
            }
           
            
        }
        
       echo "Ranks calculated !";
    }
    
    
    function cron_transfer_money_to_wallet(Request $request){
        // for calculation participants
        $quizLeaderboard=LeaderBoard::select("contest_leaderboard.rank_in_this_contest","contest_leaderboard.id as l_id","contest_leaderboard.is_winner","contest_leaderboard.user_final_marks",
        'contests.id','contests.contest_id','contests.entry_fees','contests.slot_count','contests.total_amt','contests.winner_percent_distribution',"contest_leaderboard.user_id")
        ->join("contests","contests.id","contest_leaderboard.contest_id")->orderBy("contest_leaderboard.contest_end_date_time")
        ->whereNull('contest_leaderboard.deleted_at')->where('contest_leaderboard.money_transfered_to_wallet',0)
        ->whereNotNull('contest_leaderboard.rank_in_this_contest')->whereNotNull('contest_leaderboard.ans_key')->where('contest_leaderboard.is_final_marks_calculated',1)->get();
        
        
        foreach($quizLeaderboard as $key=>$row){
            $prize_amt=$row["total_amt"];
            
            if($row['contest_id']==3){
                $prize_amt=$row['entry_fees']*$row['slot_count'];
            }
            $prize_distribution=json_decode($row["winner_percent_distribution"]);
            $earning=0;
            if($row["is_winner"]==1){
                $index=$row["rank_in_this_contest"]-1;
                $earning=$prize_amt*($prize_distribution[$index]/100); // no need to validate index because of checked is_winner 
            }
            
            
            
          if($earning>0){
                // adding earning to user wallet
                $user=Users::find($row['user_id']);
                $user->wallet_amt+=$earning;
                $user->save();
          }
            
            
            // mark as money transffered as done.
            $leaderboard=LeaderBoard::find($row['l_id']);
            $leaderboard->money_transfered_to_wallet=1;
            $leaderboard->save();
        }
        
        echo "Money transffered to wallet";
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    function uploadImage($file, $location, $imageName = null, $old = null)
    {
        try{
            $path = $this->makeDirectory($location);
            if (!$path) throw new Exception('File could not been created.');

            if ($old) {
                unlink($location . '/' . $old);
            }

            $filename = $imageName;


            $file->move($location ,$filename);
        } catch (Exception $err) {
            Log::error("File error occurred: ".$err->getMeassage()." on ".date("Y-m-d H:i:s"));
            return redirect::route('admin.questions')->withError("Internal server error.");
        }
       
    }
    
    function makeDirectory($path)
    {
        if (file_exists($path)) return true;
        return mkdir($path, 0755, true);
    }
}
