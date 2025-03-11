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
use App\Models\GeneralSettings;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

date_default_timezone_set('Asia/Kolkata');
class PaymentController extends Controller
{
    //
    function addAmount(Request $request){
     
        
        $error=[];
        if(!isset($request->user_id) || is_null($request->user_id)){
            $error["user_id"]="User ID is required";
        }
        
        if(!isset($request->order_id) || is_null($request->order_id)){
            $error["order_id"]="Order ID is required";
        }
        
        if(!isset($request->amount) || is_null($request->amount) || $request->amount<=0){
            $error["amount"]="Amount must be greater than 0";
        }
        $request->amount=round($request->amount); // rounding off the value
        if($request->amount<=0){
             return response()->json([
                "success"=>false,
                "message"=> "Minimum amount limit is 1 INR!",
                "errors"=>$error
            ], 501);
        }
        
        if(!isset($request->type) || is_null($request->type)){
            $error["type"]="Type is required";
        }
        if(!isset($request->payment_mode) || is_null($request->payment_mode)){
            $error["payment_mode"]="Payment Mode is required";
        }
        $payment_id="payment_".time()."_".uniqid();
        if(count($error)>0){
            return response()->json([
                "success"=>false,
                "message"=> "Some required information is missing",
                "errors"=>$error
            ], 501);
        }
        
        try{
            $checkUserExist=Users::find($request->user_id);
            
            if($checkUserExist==null){
                return response()->json([
                    "success"=>false,
                    "message"=> "No user found for requested is !",
                    "errors"=>$error
                ], 501);
            }
            
            $timestamp=date("Y-m-d H:i:s");
            $new=new PaymentModel();
            
            $new->user_id=trim($request->user_id);
            $new->amount=trim($request->amount);
            $new->order_id=trim($request->order_id);
            $new->payment_id=trim($payment_id);
            $new->payment_status=0;
            $new->payment_mode=$request->payment_mode;
            $new->type=trim($request->type);
            $new->created_at=$timestamp;
            
            $new->save();
            
             return response()->json([
                "success"=>true,
                "message"=> "Payment initiated !",
                "custom_payment_id"=>$payment_id,
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
    
    function updatePayment(Request $request){
     
        
        $error=[];
        if(!isset($request->user_id) || is_null($request->user_id)){
            $error["user_id"]="User ID is required";
        }
        
        if(!isset($request->order_id) || is_null($request->order_id)){
            $error["order_id"]="Order ID is required";
        }
        
        if(!isset($request->custom_payment_id) || is_null($request->custom_payment_id) ){
            $error["custom_payment_id"]="Custom payment id is required";
        }
        
        if(!isset($request->payment_id) || is_null($request->payment_id)){
            $error["payment_id"]="Payment Id is required";
        }
        if(!isset($request->signature) || is_null($request->signature)){
            $error["signature"]="Signature is required";
        }
        
        if(!isset($request->payment_status) || is_null($request->payment_status)){
            $error["payment_status"]="Payment Mode is required";
        }
        
        if(!isset($request->type) || is_null($request->type)){
            $error["type"]="Payment type is required";
        }
        
        
        if(count($error)>0){
            return response()->json([
                "success"=>false,
                "message"=> "Some required information is missing",
                "errors"=>$error
            ], 501);
        }
        
        try{
            $checkUserExist=Users::find($request->user_id);
            
            if($checkUserExist==null){
                return response()->json([
                    "success"=>false,
                    "message"=> "No user found for requested is !",
                    "errors"=>$error
                ], 501);
            }
            
            $timestamp=date("Y-m-d H:i:s");
            $payment=PaymentModel::select('amount')->where('order_id',$request->order_id)->where('payment_id',$request->custom_payment_id)->first();
            if(!$payment){
                return response()->json([
                    "success"=>false,
                    "message"=> "No info found !",
                ], 501);
            }
            
            
            $update=PaymentModel::where('order_id',$request->order_id)->where('payment_id',$request->custom_payment_id)->update(
                [
                    "gateway_payment_id"=>trim($request->payment_id),
                    "gateway_signature"=>trim($request->signature),
                    "payment_status"=>1,
                    "updated_at"=>$timestamp,
                    "contest_id"=>$request->quiz_id
                ]
            );
            
            if(!$update){
                return response()->json([
                    "success"=>false,
                    "message"=> "Failed to save the information!",
                ], 501);
            }
            
            
            $message="Success !";
            $new_amt=$checkUserExist->wallet_amt;
            // updating user wallet amount if he/she added the amount to wallet
            if($request->payment_status==1 && $request->type==1){
                $old_amt=$checkUserExist->wallet_amt;
                $checkUserExist->wallet_amt += $payment->amount;
                $checkUserExist->save();
                
                $new_amt=$old_amt+$payment->amount;
                $log=new WalletModel();
                $log->user_id=$request->user_id;
                $log->old_wallet_amt=$old_amt;
                $log->new_wallet_amt=$new_amt;
                $log->type=1;
                $log->payment_id=$request->custom_payment_id;
                $log->comment="Amount added to wallet";
                $log->created_at=$timestamp;
                $log->save();
                $message="Payment done ! Amount addedd to wallet";
            }
            
            // registering user to the contest if he/she paid fo registration
            if($request->payment_status==1 && $request->type==3){
                
                $checkExist=Contests::whereNull('deleted_at')->where('start_time','>',$timestamp)->find($request->quiz_id);
            
                if($checkExist==null){
                    $old_amt=$checkUserExist->wallet_amt;
                    $checkUserExist->wallet_amt += $payment->amount;
                    $checkUserExist->save();
                    
                    $new_amt=$old_amt+$payment->amount;
                    $log=new WalletModel();
                    $log->user_id=$request->user_id;
                    $log->old_wallet_amt=$old_amt;
                    $log->new_wallet_amt=$new_amt;
                    $log->type=1;
                    $log->payment_id=$request->custom_payment_id;
                    $log->comment="Amount added to wallet. Because registration is closed. ";
                    $log->created_at=$timestamp;
                    $log->save();
                    $message="Payment done ! Amount addedd to wallet";
                    return response()->json([
                        "success"=>true,
                        "message"=> "Quiz is already started ! Now you can not register for quiz ! We have added the amount to your wallet.",
                        "errors"=>$error,
                        "wallet_amt"=>$new_amt,
                    ], 201);
                }
                
                if($checkExist->entry_fees > $payment->amount){
                    return response()->json([
                        "success"=>true,
                        "message"=> "Low amount ! We have added to your wallet. Please pay complete entry fees for immediate registration",
                        "errors"=>$error,
                         "wallet_amt"=>$new_amt,
                    ], 201);
                }
                
                $new=new ContestRegistration();
                $new->user_id=$request->user_id;
                $new->contest_id=$request->quiz_id;
                $new->status=1; // hard codded 1 25-8-2023
                $new->remark="Registration completed !";
                $new->created_at=$timestamp;
                $new->save();
                $message="Registration completed for quiz.";
                
                 // increasing registration count and total_amt
                $checkExist->total_amt=$checkExist->total_amt+$checkExist->entry_fees;
                $checkExist->total_registration=$checkExist->total_registration+1;
                $checkExist->save();
            }
            
            return response()->json([
                "success"=>true,
                "message"=> $message,
                "wallet_amt"=>$new_amt,
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
    
    function withdrawAmount(Request $request){
     
        $error=[];
        if(!isset($request->user_id) || is_null($request->user_id)){
            $error["user_id"]="User ID is required";
        }
       
        if(!isset($request->amount) || is_null($request->amount) || $request->amount<=0){
            $error["amount"]="Amount must be greater than 0";
        }
        
        $request->amount=round($request->amount); // rounding off the value
        if($request->amount<=0){
             return response()->json([
                "success"=>false,
                "message"=> "Minimum amount limit is 1 INR",
                "errors"=>$error
            ], 501);
        }
        
        if(!isset($request->upi_id) || is_null($request->upi_id)){
            $error["upi_id"]="UPI ID is required !";
        }
        
        $general_settings=GeneralSettings::first();
        // razorpay key and secret
        $key=$general_settings->razorpay_key;
        $secret=$general_settings->razorpay_secret;
        
        $opkey = "4d265ed0-2eeb-11ef-ad71-2d52d66cfac9";
        $opsecret = "04b2c24fbd129103903fd08845f8ffebc094d98a";
        
        if($this->checkUPI($opkey,$opsecret,$request->upi_id)){
            $error["upi_id"]="UPI ID is invalid !";
            return response()->json([
                "success"=>false,
                "message"=> "UPI ID is invalid !",
                "errors"=>$error
            ], 501);
        }
        
        if(!isset($request->type) || is_null($request->type)){
            $error["type"]="Type is required";
        }
        
        if(!isset($request->withdraw_mode) || is_null($request->withdraw_mode)){
            $error["withdraw_mode"]="Payment Mode is required";
        }
        
        $payment_id="withdraw_".time()."_".uniqid();
        if(count($error)>0){
            return response()->json([
                "success"=>false,
                "message"=> "Some required information is missing",
                "errors"=>$error
            ], 501);
        }
        
        
        
        
        try{
            // check user
            $checkUserExist=Users::find($request->user_id);
            if($checkUserExist==null){
                return response()->json([
                    "success"=>false,
                    "message"=> "No user found for requested is !",
                    "errors"=>$error,
                ], 501);
            }
            
            
            if($request->amount>$checkUserExist->wallet_amt){
                $checkUserExist->upi_id=$request->upi_id;
                $checkUserExist->save();
                return response()->json([
                    "success"=>false,
                    "message"=> "Low wallet balance !",
                    "errors"=>$error,
                    "low_balance"=>true,
                    "wallet_amt"=> $checkUserExist->wallet_amt
                ], 501);
            }
            
            
            
            
            if($general_settings==null){
                $max_instant_withdraw_limit=9999; // limit for instant withdraw
            }
            else{
                $max_instant_withdraw_limit=$general_settings->max_instant_withdraw_amt; // limit for instant withdraw
            }
            
            if($request->amount>$max_instant_withdraw_limit){
                $checkUserExist->upi_id=$request->upi_id;
                $checkUserExist->save();
                // if rquested amount is more than limit
                return response()->json([
                    "success"=>false,
                    "message"=> "The amount is more than the limit for instant withdraw. Please enter you bank account for large transactions !",
                    "errors"=>$error,
                    "withdraw_limit_crossed"=>true
                ], 501);
            }
            
            
            
            
            $timestamp=date("Y-m-d H:i:s");
            
            // updating user wallet
            $old_amt=$checkUserExist->wallet_amt;
            $checkUserExist->wallet_amt -= $request->amount;
            $checkUserExist->upi_id=$request->upi_id;
            $checkUserExist->save();
            
            $new_amt=$old_amt-$request->amount;
            
            // maintaining wallet logs
            $log=new WalletModel();
            $log->user_id=$request->user_id;
            $log->old_wallet_amt=$old_amt;
            $log->new_wallet_amt=$new_amt;
            $log->type=2;
            $log->payment_id=$payment_id;
            $log->comment="Withdraw request on ".$timestamp;
            $log->created_at=$timestamp;
            $log->save();
            
           
            $new=new WithdrawModel();
            $new->user_id=trim($request->user_id);
            $new->amount=trim($request->amount);
            $new->withdraw_id=trim($payment_id);
            $new->withdraw_status=0;
            $new->user_upi_id=$request->upi_id;
            $new->withdraw_mode=$request->withdraw_mode;
            $new->created_at=$timestamp;
            $new->save();
            
            
            $contact_data = array(
                'name' => $checkUserExist->name,
                'email' => $checkUserExist->email,
                'contact' => $checkUserExist->phone,
                'type' => 'customer', // or 'vendor' or any other relevant type
            );
            
            $contact_result=$this->makeContact($key,$secret,$contact_data);
            
            if(!$contact_result["success"]){
                return response()->json([
                    "success"=>true,
                    "message"=> "We have proccessed your request, soon you will receive amount in your account !",
                    "errors"=>$contact_result["error"],
                    "wallet_amt"=>$new_amt
                ], 201);
            }
            
            $contact_id=$contact_result["contact_id"];
            
            $data = array(
                'contact_id' => $contact_id,
                'account_type' => 'vpa',
                'vpa' => array(
                    'address' => $request->upi_id, // Replace with the actual UPI Virtual Payment Address
                ),
            );
            
            $fund_account_result=$this->makeFundAccount($key,$secret,$data);
            
            
            
            if(!$fund_account_result["success"]){
                return response()->json([
                    "success"=>true,
                    "message"=> "We have proccessed your request, soon you will receive amount in your account !",
                    "errors"=>$fund_account_result["error"],
                    "wallet_amt"=>$new_amt
                ], 201);
            }
            $fund_account_id=$fund_account_result["fund_account_id"];
            
            $new->razorpay_contact_id=$contact_id;
            $new->razorpay_fund_id=$fund_account_id;
            
            // make immediate payout
            $data = array(
                "account_number" => $general_settings->our_account_number,
                "fund_account_id" => $fund_account_id,
                "amount" => round($request->amount*100),
                "currency" => "INR",
                "mode" => "UPI",
                "purpose" => "payout",
                "queue_if_low_balance" => true,
                
            );
            $payout_result=$this->makePayout($key,$secret,$data);
            
            if(!$payout_result["success"]){
                return response()->json([
                    "success"=>true,
                    "message"=> "We have proccessed your request, soon you will receive amount in your account !",
                    "errors"=>$payout_result["error"],
                    "wallet_amt"=>$new_amt
                ], 201);
            }
            
            
            $new->gateway_withdraw_id=$payout_result["withdraw_payout_id"];
            $new->withdraw_status=$payout_result["status"];
            $new->save();
            
            return response()->json([
                "success"=>true,
                "message"=> "Withdraw initiated !",
                "custom_payment_id"=>$payment_id,
                "wallet_amt"=>$new_amt
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
    
     function withdrawAmountByOp(Request $request){
         
         $userid = $request->user_id;
         $mtxid = $request->mtxid;
         $amount = $request->amount;
         
         
         
        
         
        
        $openpaymentaccesskey = "4d265ed0-2eeb-11ef-ad71-2d52d66cfac9";
        $openpaymentsecretkey = "04b2c24fbd129103903fd08845f8ffebc094d98a";
        
         $error=[];
        if(!isset($request->user_id) || is_null($request->user_id)){
            $error["user_id"]="User ID is required";
        }
    
       
        if(!isset($request->amount) || is_null($request->amount) || $request->amount<=0){
            $error["amount"]="Amount must be greater than 0";
        }
        
        
        
        $request->amount=round($request->amount); // rounding off the value
        if($request->amount<=0){
             return response()->json([
                "success"=>false,
                "message"=> "Minimum amount limit is 1 INR",
                "errors"=>$error
            ], 501);
        }
        
        
        
        if(!isset($request->upi_id) || is_null($request->upi_id)){
            $error["upi_id"]="UPI ID is required !";
        }
        
        
        
        try{
            //check user
            $checkUserExist=Users::find($request->user_id);
            if($checkUserExist==null){
                return response()->json([
                    "success"=>false,
                    "message"=> "No user found for requested is !",
                    "error"=>$error
                ], 501);
            }
            
             
            $phone=$checkUserExist->phone;
            $email=$checkUserExist->email;
            
            
            if($request->amount>$checkUserExist->wallet_amt){
                $checkUserExist->save();
                return response()->json([
                    "success"=>false,
                    "message"=> "Low wallet balance !",
                    "errors"=>$error,
                    "low_balance"=>true,
                    "wallet_amt"=> $checkUserExist->wallet_amt
                ], 501);
            }
            
           
            
             $general_settings=GeneralSettings::first();
            if($general_settings==null){
                $max_instant_withdraw_limit=9999; // limit for instant withdraw
            }
            else{
                $max_instant_withdraw_limit=$general_settings->max_instant_withdraw_amt; // limit for instant withdraw
            }
            
            
            
              
            
            if($request->amount>$max_instant_withdraw_limit){
                $checkUserExist->save();
                // if rquested amount is more than limit
                return response()->json([
                    "success"=>false,
                    "message"=> "The amount is more than the limit for instant withdraw. Please enter you bank account for large transactions !",
                    "errors"=>$error,
                    "withdraw_limit_crossed"=>true
                ], 501);
            }
          
            
            
            $timestamp=date("Y-m-d H:i:s");
            
            
            // updating user wallet
            $old_amt=$checkUserExist->wallet_amt;
            $checkUserExist->wallet_amt -= $request->amount;
            $checkUserExist->upi_id=$request->upi_id;
            $checkUserExist->save();
            
            
            
            $new_amt=$old_amt-$request->amount;
             $data = [
            "name" => $checkUserExist->name,
            "email" => $checkUserExist->email,
            "phone" => $checkUserExist->email
            ];
            
             
             
            
            
            
            $response = Http::withHeaders([
                             'Authorization' => "Bearer $openpaymentaccesskey:$openpaymentsecretkey ",
                            'accept' => 'application/json',
                            'content-type' => 'application/json',
                        ])->post('https://sandbox-icp-api.bankopen.co/api/payment_token', [
                            'amount' => $amount,
                            'contact_number' => $phone,
                            'email_id' => $email,
                            'currency' => 'INR',
                            'mtx' => $mtxid,
                            'udf'=>$data
                        ]);
                        
                       
           
             
            
            
            if($response['status']=='created'){
              
              // maintaining wallet logs
            $log=new WalletModel();
            $log->user_id=$request->user_id;
            $log->old_wallet_amt=$old_amt;
            $log->new_wallet_amt=$new_amt;
            $log->type=2;
            $log->payment_id=$response['id'];
            $log->comment="Withdraw request on ".$timestamp;
            $log->created_at=$timestamp;
            $log->save();
             
             
            
             $new=new WithdrawModel();
            $new->user_id=trim($request->user_id);
            $new->amount=trim($request->amount);
            $new->withdraw_id=trim($request->mtxid);
            $new->withdraw_status=1;
            $new->user_upi_id=$request->upi_id;
            $new->withdraw_mode=$request->withdraw_mode;
            $new->created_at=$timestamp;
            
            
            $new->gateway_withdraw_id=$response['id'];
             
            $new->withdraw_status=1;
            $new->save();
            
            
            return response()->json([
                "success"=>true,
                "message"=> "Withdraw initiated !",
                "custom_payment_id"=>$response['id'],
                "wallet_amt"=>$new_amt
            ], 201);
           
            }else{
              return response()->json([
                "success"=>false,
                "message"=> "Withdraw failed!",
                "custom_payment_id"=>$response['id'],
                "wallet_amt"=>$old_amt
            ], 201);   
            }
            
            
        }
        catch(Exception $err){
             return response()->json([
                "success"=>false,
                "message"=> "Internal server error",
                "errors"=>"Exception thrwoned "
            ], 501);
        }
        
        



// To get the response body
$responseBody = $response->body();

// To get the response as JSON
$responseJson = $response->json();

// To get the status code
$statusCode = $response->status();

echo $response->getBody();
    

    }
    
    function withdrawLargeAmount(Request $request){
     
        
        $error=[];
        if(!isset($request->user_id) || is_null($request->user_id)){
            $error["user_id"]="User ID is required";
        }
       
        if(!isset($request->amount) || is_null($request->amount) || $request->amount<=0){
            $error["amount"]="Amount must be greater than 0";
        }
        
        $request->amount=round($request->amount); // rounding off the value
        if($request->amount<=0){
             return response()->json([
                "success"=>false,
                "message"=> "Minimum amount limit is 1 INR",
                "errors"=>$error
            ], 501);
        }
        
        if(!isset($request->bank_account_number) || is_null($request->bank_account_number)){
            $error["bank_account_number"]="Bank account number is required !";
        }
        
        if(!isset($request->type) || is_null($request->type)){
            $error["type"]="Type is required";
        }
        
        if(!isset($request->withdraw_mode) || is_null($request->withdraw_mode)){
            $error["withdraw_mode"]="Payment Mode is required";
        }
        
        $payment_id="withdraw_".time()."_".uniqid();
        if(count($error)>0){
            return response()->json([
                "success"=>false,
                "message"=> "Some required information is missing",
                "errors"=>$error
            ], 501);
        }
        
        
        
        
        try{
            // check user
            $checkUserExist=Users::find($request->user_id);
            if($checkUserExist==null){
                return response()->json([
                    "success"=>false,
                    "message"=> "No user found for requested is !",
                    "errors"=>$error,
                ], 501);
            }
            
            
            if($request->amount>$checkUserExist->wallet_amt){
                $checkUserExist->bank_account_number=$request->bank_account_number;
                $checkUserExist->save();
                return response()->json([
                    "success"=>false,
                    "message"=> "Low wallet balance !",
                    "errors"=>$error,
                    "low_balance"=>true,
                    "wallet_amt"=>$checkUserExist->wallet_amt
                ], 501);
            }
            
            // here we are not checking limit
            // $general_settings=GeneralSettings::first();
            // if($general_settings==null){
            //     $max_instant_withdraw_limit=9999; // limit for instant withdraw
            // }
            // else{
            //     $max_instant_withdraw_limit=$general_settings->max_instant_withdraw_amt; // limit for instant withdraw
            // }
            
            // if($request->amount>$max_instant_withdraw_limit){
            //     $checkUserExist->upi_id=$request->upi_id;
            //     $checkUserExist->save();
            //     // if rquested amount is more than limit
            //     return response()->json([
            //         "success"=>false,
            //         "message"=> "The amount is more than the limit for instant withdraw. Please enter you bank account for large transactions !",
            //         "errors"=>$error,
            //         "withdraw_limit_crossed"=>true
            //     ], 501);
            // }
            
            
            
            
            $timestamp=date("Y-m-d H:i:s");
            
            
            $general_settings=GeneralSettings::first();
            // razorpay key and secret
            $key=$general_settings->razorpay_key;
            $secret=$general_settings->razorpay_secret;
            
            $contact_data = array(
                'name' => $checkUserExist->name,
                'email' => $checkUserExist->email,
                'contact' => $checkUserExist->phone,
                'type' => 'customer', // or 'vendor' or any other relevant type
            );
            
             $contact_result=$this->makeContact($key,$secret,$contact_data);
            
            if(!$contact_result["success"]){
                return response()->json([
                    "success"=>false,
                    "message"=>json_decode($contact_result["error"])->error->description,
                    "errors"=>$contact_result["error"],
                ], 201);
            }
            
            $contact_id=$contact_result["contact_id"];
            
            $data = array(
                'contact_id' => $contact_id,
                'account_type' => 'bank_account',
                'bank_account' => array(
                    'name' => $request->account_holder_name, 
                    'ifsc' => $request->bank_ifsc_code,
                    'account_number' => $request->bank_account_number,
                ),
            );
            
            $fund_account_result=$this->makeFundAccount($key,$secret,$data);
            
            if(!$fund_account_result["success"]){
                return response()->json([
                    "success"=>false,
                    "message"=> json_decode($fund_account_result["error"])->error->description,
                    "errors"=>$fund_account_result["error"],
                ], 201);
            }
            $fund_account_id=$fund_account_result["fund_account_id"];
            
            // updating user wallet
            $old_amt=$checkUserExist->wallet_amt;
            $checkUserExist->wallet_amt -= $request->amount;
            $checkUserExist->upi_id=$request->upi_id;
            $checkUserExist->save();
            
            $new_amt=$old_amt-$request->amount;
            
            // maintaining wallet logs
            $log=new WalletModel();
            $log->user_id=$request->user_id;
            $log->old_wallet_amt=$old_amt;
            $log->new_wallet_amt=$new_amt;
            $log->type=2;
            $log->payment_id=$payment_id;
            $log->comment="Withdraw request on ".$timestamp;
            $log->created_at=$timestamp;
            $log->save();
            
           
            $new=new WithdrawModel();
            $new->user_id=trim($request->user_id);
            $new->amount=trim($request->amount);
            $new->withdraw_id=trim($payment_id);
            $new->withdraw_status=0;
            $new->user_upi_id=$request->upi_id;
            $new->withdraw_mode=$request->withdraw_mode;
            $new->created_at=$timestamp;
            $new->save();
            
            
            
           
            
            $new->razorpay_contact_id=$contact_id;
            $new->razorpay_fund_id=$fund_account_id;
            
            // make immediate payout
            $data = array(
                "account_number" => $general_settings->our_account_number,
                "fund_account_id" => $fund_account_id,
                "amount" => round($request->amount*100),
                "currency" => "INR",
                "mode" => "IMPS",
                "purpose" => "payout",
                "queue_if_low_balance" => true,
                
            );
            $payout_result=$this->makePayout($key,$secret,$data);
            
            if(!$payout_result["success"]){
                return response()->json([
                    "success"=>true,
                    "message"=> "We have proccessed your request, soon you will receive amount in your account !",
                    "errors"=>$payout_result["error"],
                    "wallet_amt"=>$new_amt
                ], 201);
            }
            
            
            $new->gateway_withdraw_id=$payout_result["withdraw_payout_id"];
            $new->withdraw_status=$payout_result["status"];
            $new->save();
            
             return response()->json([
                "success"=>true,
                "message"=> "Withdraw initiated !",
                "custom_payment_id"=>$payment_id,
                "wallet_amt"=>$new_amt
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
    
    
    // razorpay make contact
    function makeContact($api_key,$api_secret,$contact_data){
        
        $contact_data_json = json_encode($contact_data);
        
        $ch = curl_init('https://api.razorpay.com/v1/contacts');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $contact_data_json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Basic ' . base64_encode($api_key . ':' . $api_secret)
        ));
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        // Handle the response
        $response_data = json_decode($response, true);
        if ($response_data && isset($response_data['id'])) {
            $contact_id = $response_data['id'];
            $data=[
                "success"=>true,
                "contact_id"=>$contact_id
            ];
            return $data;
        } else {
            $data=[
                "success"=>false,
                "error"=>$response
            ];
            return $data;
        }
    }
    
    
    // razorpay make fund account
    function makeFundAccount($api_key,$api_secret,$data){
        
        
        $data_json = json_encode($data);
        $curl = curl_init();
        
        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.razorpay.com/v1/fund_accounts',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>$data_json,
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Authorization: Basic ' . base64_encode($api_key . ':' . $api_secret)
        ),
        ));
        
        $response = curl_exec($curl);
        
        curl_close($curl);
        
        // Handle the response
        $response_data = json_decode($response, true);
        if ($response_data && isset($response_data['id'])) {
            $fund_account_id = $response_data['id'];
            $data=[
                "success"=>true,
                "fund_account_id"=>$fund_account_id
            ];
            return $data;
        } else {
            $data=[
                "success"=>false,
                "error"=>$response
            ];
            return $data;
        }
    }
    
    // razorpay make payout
    function makePayout($api_key,$api_secret,$data){
        
        
        $razorpayKey = "$api_key"; // Replace with your Razorpay Key
        $razorpaySecret = $api_secret; // Replace with your Razorpay Secret
        
        $openpaymentkey = "4d265ed0-2eeb-11ef-ad71-2d52d66cfac9";
        $openpaymentsecretkey = "04b2c24fbd129103903fd08845f8ffebc094d98a";

        $headers = array(
            "Authorization: Basic " . base64_encode("$razorpayKey:$razorpaySecret"),
            "Content-Type: application/json"
        );
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://api.razorpay.com/v1/payouts',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS =>json_encode($data),
          CURLOPT_HTTPHEADER => $headers,
        ));
        
         $response = curl_exec($curl);
         if (curl_errno($curl)) {
            $data=[
                "success"=>false,
                "error"=>curl_error($curl)
            ];
            return $data;
         } 
         else {
                $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                 if ($http_code === 200) {
                    $response=json_decode($response);
                    if(isset($response->id)){
                        $status=$response->status=="queued"?0:1;
                        $withdraw_payout_id=$response->id;
                        // maintaining logs of payout
                        $data=[
                            "success"=>true,
                            "withdraw_payout_id"=>$withdraw_payout_id,
                            "status"=>$status
                        ];
                        return $data;
                    }
                    
                 }
                 else{
                     $data=[
                            "success"=>false,
                            "error"=>$response
                        ];
                        return $data;
                 } 
        }
            
        curl_close($curl);
    }
    
    
   
    
    
    // razorpay check UPI
    function checkUPI($api_key,$api_secret,$upi_vpa){
        
        
       
        // API endpoint URL
        $url = 'https://ifsc.razorpay.com/upi/' . $upi_vpa;
        
        // Set up cURL session
        $ch = curl_init($url);
        
        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $api_key . ':' . $api_secret);
        
        // Execute cURL session and get the response
        $response = curl_exec($ch);
        
        // Get HTTP response code
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        // Close cURL session
        curl_close($ch);
        
        // Decode the JSON response
        $response_data = json_decode($response, true);
        
        // Check if the response is successful
        if ($http_code === 200 && isset($response_data['valid']) && $response_data['valid'] === true) {
           
            return true;
        } else {
            echo false;
        }
    }
    
    
    
    
    
    
}
