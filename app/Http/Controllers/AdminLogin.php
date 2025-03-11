<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use App\Helpers;
use App\Models\Users;
use App\Models\Questions;
use App\Models\QuestionCategory;
use App\Models\Contests;
use App\Models\Topics;
date_default_timezone_set('Asia/Kolkata');
class AdminLogin extends Controller
{
    function showAdminLoginForm(){

    }

    function postAdminLogin(Request $request){
        $credentials = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string|min:6',
        ]);
        $credentials["status"]=1;
        $email=trim($request->email);
        $password=trim($request->password);

        
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
 
            return redirect::route('admin.dashboard');
        }
 
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');

    }

    function dashboard(){

        $pageHeading="Dashboard";
        $page="dashboard";
        
        $questionCount=Questions::whereNull('deleted_at')->count();
        
        $contestCount=Contests::whereNull('deleted_at')->count();
        
        $userCount=Users::whereNull('deleted_at')->count();


        return view("dashboard", compact('pageHeading','page','questionCount','contestCount','userCount'));
    }

    function questions(){

        $pageHeading="Questions List";
        $page="questions";

        $allQuestions=Questions::whereNull('deleted_at')->paginate(10);

        if($allQuestions!=null){

            $allQuestionCount=count($allQuestions);
        }
        else{
            $allQuestionCount=0; 
        }

        $allCategories=QuestionCategory::select('id','name')->whereNull('deleted_at')->get()->all();

        $categoriesMap=[];

        if($allCategories!=null){
            foreach ($allCategories as $key => $value) {
                $categoriesMap[$value->id]=$value->name;
            }
        }
        else{
            $categoriesMap[1]="Live Contest";
            $categoriesMap[2]="Time Limit Contest";
            $categoriesMap[3]="Any Time Contest";
            $categoriesMap[4]="Question Bank";
        }

        return view("questions", compact('pageHeading','page','allQuestions','allQuestionCount','categoriesMap'));
    }
    

    function postNewQuestion(Request $request){
        
        dd($request->all());

       try {

        $validate = $request->validate([
            'title_type' => 'required|string',
            'title' => 'required',
            'option_type' => 'required',
            'option_count' => 'required',
            'option' => 'required',
            'correct_option' => 'required',
            'status' => 'required',
        ]);


        $title_type=$request->title_type;
        $option_type=$request->option_type;
        $option_count=$request->option_count;
        $title="";
        $options=[];


        if($title_type=="text" || $title_type=="youtubevideolink"){
            $title=trim($request->title);
        }
        else if($title_type=="image" || $title_type=="video"){
            if ($request->hasFile('title')) {
                $title = $this->uploadTitleFile($request->file('title'));
            }
        }

        $inputOptions=$request->option;
        // saving options
        if($option_type=="text"){
            $inputHiOptions=$request->hi_option;
            foreach ($inputOptions as $key => $value) {
                $temp=[trim($value),trim($inputHiOptions[$key])];
                array_push($options,$temp);
            }
        }
        else if($option_type=="youtubevideolink"){
            foreach ($inputOptions as $key => $value) {
                array_push($options,trim($value));
            }
        }
        else if($option_type=="image" || $option_type=="video"){
            foreach ($inputOptions as $key => $value) {
                $fileName = $this->uploadOptionFile($value);
                array_push($options,$fileName);
            }
        }
        $correct_option=[];
        if(isset($request->correct_option)){
            foreach ($request->correct_option as $key => $value) {
                array_push($correct_option,$value);
            }
        }

        $question_category=[];
        if(isset($request->question_category)){
            foreach ($request->question_category as $key => $value) {
                array_push($question_category,$value);
            }
        }
        else{
            $question_category=["1","2","3","4"];
        }

        $questions = new Questions();
        $questions->title_type = trim($title_type);
        
        $questions->title = trim($title);
        
        if($title_type=="text"){
            $questions->hi_title = trim($request->hi_title);
        }
        
        $questions->option_type = trim($option_type);
        $questions->option_count = trim($option_count);
        $questions->options = json_encode($options);
        $questions->correct_option = json_encode($correct_option);
        $questions->category = json_encode($question_category);
        $questions->status = ($request->status);
        $questions->created_at = date("Y-m-d H:i:s");
        $questions->updated_at = date("Y-m-d H:i:s");
        $questions->save();

        return redirect::route('admin.questions')->withSuccess("Question has been saved successfully");
        } catch (Exception $err) {
            //throw $th;
            Log::error("Exception occurred: ".$err->getMeassage()." on ".date("Y-m-d H:i:s"));
            return redirect::route('admin.questions')->withError("Internal server error.");
        }
    }

    function view_question($id){

        $pageHeading="Question Details";
        $page="questiondetails";

        $allQuestions=Questions::find($id);
        if (!$allQuestions) {
            return back()->withError('Id do not match our records.');
        } 

        $allCategories=QuestionCategory::select('id','name')->whereNull('deleted_at')->get()->all();

        $categoriesMap=[];

        if($allCategories!=null){
            foreach ($allCategories as $key => $value) {
                $categoriesMap[$value->id]=$value->name;
            }
        }
        else{
            $categoriesMap[1]="Live Contest";
            $categoriesMap[2]="Time Limit Contest";
            $categoriesMap[3]="Any Time Contest";
            $categoriesMap[4]="Question Bank";
        }

        return view("view_question", compact('pageHeading','page','allQuestions','categoriesMap'));
    }

    function edit_question($id){

        $pageHeading="Edit Question";
        $page="editquestion";

        $question=Questions::find($id);
        if (!$question) {
            return back()->withError('Id do not match our records.');
        } 

        $allCategories=QuestionCategory::select('id','name')->whereNull('deleted_at')->get()->all();

        $categoriesMap=[];

        if($allCategories!=null){
            foreach ($allCategories as $key => $value) {
                $categoriesMap[$value->id]=$value->name;
            }
        }
        else{
            $categoriesMap[1]="Live Contest";
            $categoriesMap[2]="Time Limit Contest";
            $categoriesMap[3]="Any Time Contest";
            $categoriesMap[4]="Question Bank";
        }

        return view("edit_question", compact('pageHeading','page','question','categoriesMap'));
    }

    function post_edit_question(Request $request){

        try {
         //code...
       
 
         $validate = $request->validate([
             'qid'=>"required|integer",
             'title_type' => 'required|string',
             'option_type' => 'required',
             'option_count' => 'required',
             'correct_option' => 'required',
             'status' => 'required',
         ]);
         $questions = Questions::find($request->qid);
         if(!$questions){
            return redirect::route('admin.questions')->withError("Id not found !");
         }
 
         $title_type=$request->title_type;
         $option_type=$request->option_type;
         $option_count=$request->option_count;
         $title="";
         $options=[];
 
 
         if(($title_type=="text" || $title_type=="youtubevideolink") && !is_null($request->title)){
             $title=trim($request->title);
         }
         else if(($title_type=="image" || $title_type=="video") && !is_null($request->title)){
             if ($request->hasFile('title')) {
                 $title = $this->uploadTitleFile($request->file('title'));
             }
         }
 
         $inputOptions=$request->option;
         // saving options
         if($option_type=="text" && !is_null($inputOptions)){
            $inputHiOptions=$request->hi_option;
            foreach ($inputOptions as $key => $value) {
                $temp=[trim($value),trim($inputHiOptions[$key])];
                array_push($options,$temp);
            }
         }
         else if(($option_type=="youtubevideolink") && !is_null($inputOptions)){  
             foreach ($inputOptions as $key => $value) {
                 array_push($options,trim($value));
             }
         }
         else if(($option_type=="image" || $option_type=="video") && !is_null($inputOptions)){
             foreach ($inputOptions as $key => $value) {
                 $fileName = $this->uploadOptionFile($value);
                 array_push($options,$fileName);
             }
         }
         $correct_option=[];
         if(isset($request->correct_option) ){
             foreach ($request->correct_option as $key => $value) {
                 array_push($correct_option,$value);
             }
         }
 
         $question_category=[];
         if(isset($request->question_category)){
             foreach ($request->question_category as $key => $value) {
                 array_push($question_category,$value);
             }
         }
         else{
             $question_category=["1","2","3","4"];
         }
 
         
         if(!empty($title)){
            $questions->title_type = trim($title_type);
            $questions->title = trim($title);
            
            if($title_type=="text" && !empty($request->hi_title)){
                $questions->hi_title = trim($request->hi_title);
            }
         }
         if(!empty($options)){

            $questions->option_type = trim($option_type);
            $questions->option_count = trim($option_count);
            $questions->options = json_encode($options);
         }
         
         $questions->correct_option = json_encode($correct_option);
         $questions->category = json_encode($question_category);
         $questions->status = $request->status;
         $questions->updated_at = date("Y-m-d H:i:s");
         $questions->save();
 
         return redirect::route('admin.questions')->withSuccess("Question has been saved successfully");
         } catch (Exception $err) {
             //throw $th;
             Log::error("Exception occurred: ".$err->getMeassage()." on ".date("Y-m-d H:i:s"));
             return redirect::route('admin.questions')->withError("Internal server error.");
         }
     }
 

    function contests(){

        $pageHeading="Contest List";
        $page="contests";

        $allContest=Contests::whereNull('deleted_at')->orderBy("start_time")->paginate(10);

        if($allContest!=null){

            $allContestCount=count($allContest);
        }
        else{
            $allContestCount=0; 
        }

        $allCategories=QuestionCategory::select('id','name')->whereNull('deleted_at')->get()->all();
        $categoriesMap=[];

        if($allCategories!=null){
            foreach ($allCategories as $key => $value) {
                $categoriesMap[$value->id]=$value->name;
            }
        }
        else{
            $categoriesMap[1]="Live Contest";
            $categoriesMap[2]="Time Limit Contest";
            $categoriesMap[3]="Any Time Contest";
            $categoriesMap[4]="Question Bank";
        }

        $allQuestions=Questions::whereNull('deleted_at')->paginate(10);
        

        return view("contests", compact('pageHeading','page','allContest','allContestCount','categoriesMap'));
    }

    function newContests(){

        $pageHeading="New Contest";
        $page="newcontest";

        
       
        $allCategories=QuestionCategory::select('id','name')->whereNull('deleted_at')->get()->all();
        $categoriesMap=[];

        if($allCategories!=null){
            foreach ($allCategories as $key => $value) {
                $categoriesMap[$value->id]=$value->name;
            }
        }
        else{
            $categoriesMap[1]="Live Contest";
            $categoriesMap[2]="Time Limit Contest";
            $categoriesMap[3]="Any Time Contest";
            $categoriesMap[4]="Question Bank";
        }

        $allQuestions=Questions::where('status',1)->whereNull('deleted_at')->get()->all();
       
            
        return view("new_contest", compact('pageHeading','page','categoriesMap','allQuestions'));
    }

    function newLiveContests(){

        $pageHeading="New Live Contest";
        $page="newlivecontest";

        
       
        $allCategories=QuestionCategory::select('id','name')->whereNull('deleted_at')->get()->all();
        $categoriesMap=[];

        if($allCategories!=null){
            foreach ($allCategories as $key => $value) {
                $categoriesMap[$value->id]=$value->name;
            }
        }
        else{
            $categoriesMap[1]="Live Contest";
            $categoriesMap[2]="Time Limit Contest";
            $categoriesMap[3]="Any Time Contest";
            $categoriesMap[4]="Question Bank";
        }

        $allQuestions=Questions::where('status',1)->whereNull('deleted_at')->get()->all();
       
            
        return view("new_live_contest", compact('pageHeading','page','categoriesMap','allQuestions'));
    }

    function newTimeLimitContests(){

        $pageHeading="New Time Limit Contest";
        $page="newtimelimitcontest";

        
       
        $allCategories=QuestionCategory::select('id','name')->whereNull('deleted_at')->get()->all();
        $categoriesMap=[];

        if($allCategories!=null){
            foreach ($allCategories as $key => $value) {
                $categoriesMap[$value->id]=$value->name;
            }
        }
        else{
            $categoriesMap[1]="Live Contest";
            $categoriesMap[2]="Time Limit Contest";
            $categoriesMap[3]="Any Time Contest";
            $categoriesMap[4]="Question Bank";
        }

        $allQuestions=Questions::where('status',1)->whereNull('deleted_at')->get()->all();
       
            
        return view("new_time_limit_contest", compact('pageHeading','page','categoriesMap','allQuestions'));
    }

    function newAnyTimeContests(){

        $pageHeading="New Any Time Contest";
        $page="newanytimecontest";

        
       
        $allCategories=QuestionCategory::select('id','name')->whereNull('deleted_at')->get()->all();
        $categoriesMap=[];

        if($allCategories!=null){
            foreach ($allCategories as $key => $value) {
                $categoriesMap[$value->id]=$value->name;
            }
        }
        else{
            $categoriesMap[1]="Live Contest";
            $categoriesMap[2]="Time Limit Contest";
            $categoriesMap[3]="Any Time Contest";
            $categoriesMap[4]="Question Bank";
        }

        $allQuestions=Questions::where('status',1)->whereNull('deleted_at')->get()->all();
       
            
        return view("new_any_time_contest", compact('pageHeading','page','categoriesMap','allQuestions'));
    }

    function postNewContest(Request $request){

        try {
 
            $validate = $request->validate([
                'contest_type' => 'required',
                'contest_name' => 'required',
                'start_time' => 'required',
                'start_date' => 'required',
                'entry_fees' => 'required',
                'per_question_mark' => 'required',
                'neg_mark' => 'required',
                'status' => 'required',
                'questions' => 'required',
            ]);
    
    
            $contest_type=$request->contest_type;
            $start_time=date("H:i:s",strtotime($request->start_time));
            $start_date=date("Y-m-d",strtotime($request->start_date));
            $start_date_time=$start_date." ".$start_time;

            $entry_fees=$request->entry_fees;
            $per_question_mark=$request->per_question_mark;
            $neg_mark=$request->neg_mark;
            $status=$request->status;

            $duration=isset($request->duration)?$request->duration:0;
            $per_question_time=isset($request->per_question_time)?$request->per_question_time:0;

            $description=isset($request->description)?$request->description:null;
    
            $contests = new Contests();
            $contests->contest_id = trim($contest_type);
            $contests->name = trim($request->contest_name);
            $contests->start_time = ($start_date_time);
            $contests->entry_fees = trim($entry_fees);
            $contests->questions_count = count($request->questions);
            $contests->question_ids = json_encode($request->questions);
            $contests->duration = trim($duration);
            $contests->per_question_time = trim($per_question_time);
            $contests->per_question_mark = trim($per_question_mark);
            $contests->neg_mark = trim($neg_mark);
            $contests->description = trim($description);
            $contests->status = trim($status);
            $contests->created_at = date("Y-m-d H:i:s");
            $contests->updated_at = date("Y-m-d H:i:s");
            $contests->save();
    
            return redirect::route('admin.contests')->withSuccess("Contest has been saved successfully");
         } catch (Exception $err) {
             //throw $th;
             //  echo "Exception occurred: ".$err->getMeassage()." on ".date("Y-m-d H:i:s");
             Log::error("Exception occurred: ".$err->getMeassage()." on ".date("Y-m-d H:i:s"));
             return back()->withError("Internal server error.");
         }
    }

 
    function view_contest($id){
 
         $pageHeading="Contest Details";
         $page="contestdetails";
 
         $contest=Contests::find($id);

         if (!$contest) {
             return back()->withError('Id do not match our records.');
         } 
         $contestQuestions=[];
         $allQuestions=json_decode($contest->question_ids);
         if(!is_null($allQuestions)){
            foreach ($allQuestions as $key => $id) {
                $question_details=Questions::find($id);
                array_push($contestQuestions,$question_details);
             }
         }
        
 
         $allCategories=QuestionCategory::select('id','name')->whereNull('deleted_at')->get()->all();
 
         $categoriesMap=[];
 
         if($allCategories!=null){
             foreach ($allCategories as $key => $value) {
                 $categoriesMap[$value->id]=$value->name;
             }
         }
         else{
             $categoriesMap[1]="Live Contest";
             $categoriesMap[2]="Time Limit Contest";
             $categoriesMap[3]="Any Time Contest";
             $categoriesMap[4]="Question Bank";
         }
 
         return view("view_contest", compact('pageHeading','page','contest','categoriesMap','contestQuestions'));
     }
 
    function edit_contest($id){
 
         $pageHeading="Edit Contest";
         $page="editcontest";
 
         $contest=Contests::find($id);
         if (!$contest) {
             return back()->withError('Id do not match our records.');
         }
         $contestQuestionIdMap=[];
         $contestQuestions=[];
         $allContestQuestions=json_decode($contest->question_ids);
         if(!is_null($allContestQuestions)){
            foreach ($allContestQuestions as $key => $id) {
                $question_details=Questions::find($id);
                array_push($contestQuestions,$question_details);
                $contestQuestionIdMap[$id]=true;
             }
         }
         
         $allQuestions=Questions::where('status',1)->whereNull('deleted_at')->get()->all();
       
            
 
         return view("edit_contest", compact('pageHeading','page','contest','allQuestions','contestQuestionIdMap','contestQuestions'));
     }
 
    function post_edit_contest(Request $request){
 
          try {
 
            $validate = $request->validate([
                'id'=>"required",
                'contest_type' => 'required',
                'contest_name' => 'required',
                'start_time' => 'required',
                'start_date' => 'required',
                'entry_fees' => 'required',
                'per_question_mark' => 'required',
                'neg_mark' => 'required',
                'status' => 'required',
                'questions' => 'required',
            ]);
    
    
            $contest_type=$request->contest_type;
            $start_time=date("H:i:s",strtotime($request->start_time));
            $start_date=date("Y-m-d",strtotime($request->start_date));
            $start_date_time=$start_date." ".$start_time;

            $entry_fees=$request->entry_fees;
            $per_question_mark=$request->per_question_mark;
            $neg_mark=$request->neg_mark;
            $status=$request->status;

            $duration=isset($request->duration)?$request->duration:0;
            $per_question_time=isset($request->per_question_time)?$request->per_question_time:0;

            $description=isset($request->description)?$request->description:null;
    
            $contests = Contests::find($request->id);
            $contests->contest_id = trim($contest_type);
            $contests->name = trim($request->contest_name);
            $contests->start_time = ($start_date_time);
            $contests->entry_fees = trim($entry_fees);
            $contests->questions_count = count($request->questions);
            $contests->question_ids = json_encode($request->questions);
            $contests->duration = trim($duration);
            $contests->per_question_time = trim($per_question_time);
            $contests->per_question_mark = trim($per_question_mark);
            $contests->neg_mark = trim($neg_mark);
            $contests->description = trim($description);
            $contests->status = trim($status);
            $contests->created_at = date("Y-m-d H:i:s");
            $contests->updated_at = date("Y-m-d H:i:s");
            $contests->save();
    
            return redirect::route('admin.contests')->withSuccess("Contest has been saved successfully");
         } catch (Exception $err) {
             //throw $th;
             //  echo "Exception occurred: ".$err->getMeassage()." on ".date("Y-m-d H:i:s");
             Log::error("Exception occurred: ".$err->getMeassage()." on ".date("Y-m-d H:i:s"));
             return back()->withError("Internal server error.");
         }
      }
      
      
    function topics(){
        $pageHeading="Topics List";
        $page="topics";

        $allTopic=Topics::whereNull('deleted_at')->orderBy("name")->get();

        if($allTopic!=null){

            $allTopicCount=count($allTopic);
        }
        else{
            $allTopicCount=0; 
        }
       
        return view("topics", compact('pageHeading','page','allTopic','allTopicCount'));
    }
    
    function newTopic(Request $request){
        $pageHeading="Topics List";
        $page="topics";
        
        try{
        $name=trim(strtolower($request->topic));
        $status=trim(strtolower($request->status));

        $allTopic=Topics::whereNull('deleted_at')->where("name",$name)->first();

        if($allTopic!=null){

            return back()->withError('Topic Already Exist.');
        }
        
        $timestamp=date("Y-m-d H:i:s");
        $newTopic=new Topics();
        $newTopic->name=$name;
        $newTopic->status=$status;
        $newTopic->created_at=$timestamp;
        $newTopic->updated_at=$timestamp;
        $newTopic->save();
       
        return redirect::route('admin.topics')->withSuccess("Topic saved successfully !");
        }
        catch (Exception $err) {
            //throw $th;
            Log::error("Exception occurred: ".$err->getMeassage()." on ".date("Y-m-d H:i:s"));
            return redirect::route('admin.topics')->withError("Internal server error.");
        }
    }
    
    function post_edit_topic(Request $request){
        $pageHeading="Topics List";
        $page="topics";
        
        try{
        $name=trim(strtolower($request->topic));
        $status=trim(strtolower($request->status));

      
        
        $timestamp=date("Y-m-d H:i:s");
        $editTopic=Topics::find($request->id);
        if($editTopic==null){
            return back()->withError('Invalid Id');
        }
        $editTopic->name=$name;
        $editTopic->status=$status;
        $editTopic->updated_at=$timestamp;
        $editTopic->save();
       
        return redirect::route('admin.topics')->withSuccess("Topic saved successfully !");
        }
        catch (Exception $err) {
            //throw $th;
            Log::error("Exception occurred: ".$err->getMeassage()." on ".date("Y-m-d H:i:s"));
            return redirect::route('admin.topics')->withError("Internal server error.");
        }
    }

    function getAllQuestion(){
        // echo "hello";
        $allQuestions=Questions::where('status',1)->whereNull('deleted_at')->get()->all();
        echo json_encode($allQuestions);
    }

   
    // helper function

    function uploadImage($file, $location, $size = null, $old = null)
    {
        try{
            $path = $this->makeDirectory($location);
            if (!$path) throw new Exception('File could not been created.');

            if ($old) {
                unlink($location . '/' . $old);
            }

            $filename = uniqid() . time() . '.' . $file->getClientOriginalExtension();

            // $image = Image::make($file);
            // if ($size) {
            //     $size = explode('x', strtolower($size));
            //     $image->resize($size[0], $size[1]);
            // }
            // $image->save($location . '/' . $filename);

            $file->move($location ,$filename);
            return $filename;
        } catch (Exception $err) {
            Log::error("File error occurred: ".$err->getMeassage()." on ".date("Y-m-d H:i:s"));
            return redirect::route('admin.questions')->withError("Internal server error.");
        }
       
    }

    
    function imagePath()
    {
        $data['title'] = [
            'path' => 'assets/files/title',
            'size' => '400x400',
        ];

        $data['option'] = [
            'path' => 'assets/files/option',
            'size' => '400x400',
        ];
        $data['verify'] = [
            'withdraw'=>[
                'path'=>'assets/images/verify/withdraw'
            ],
            'deposit'=>[
                'path'=>'assets/images/verify/deposit'
            ]
        ];
        $data['image'] = [
            'default' => 'assets/images/default.png',
        ];
        $data['withdraw'] = [
            'method' => [
                'path' => 'assets/images/withdraw/method',
                'size' => '800x800',
            ]
        ];
        
        
        
        $data['slider'] = [
            'path' => 'assets/images/slider',
            'size' => '1400x700',
            'thumb' => '1400x700'
        ];
        return $data;
    }

    public  function uploadTitleFile($image, $old = null)
    {
        $path = $this->imagePath()['title']['path'];
        $size =$this->imagePath()['title']['size'];
        $thumbnail = $this->uploadImage($image, $path, $size);

        return $thumbnail;
    }

    public  function uploadOptionFile($image, $old = null)
    {
        $path = $this->imagePath()['option']['path'];
        $size =$this->imagePath()['option']['size'];
        $thumbnail = $this->uploadImage($image, $path, $size);

        return $thumbnail;
    }

    function makeDirectory($path)
    {
        if (file_exists($path)) return true;
        return mkdir($path, 0755, true);
    }
}
