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
use App\Models\Faq;
use App\Models\PaymentModel;
use App\Models\WithdrawModel;
use App\Models\LeaderBoard;
use App\Models\QuestionTopicMapping;
use App\Models\QuizSlot;
use DB;
date_default_timezone_set('Asia/Kolkata');

class AdminController extends Controller
{
    function showAdminControllerForm(){

    }

    function postAdminController(Request $request){
        $credentials = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string|min:6',
        ]);
        $credentials["status"]=1;
        $credentials["role"]=2;  // 2 is for admin
        $email=trim($request->email);
        $password=trim($request->password);

        
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
 
            return redirect::route('admin.dashboard');
        }
 
        return redirect()->back()->withErrors([
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

    function questions($topic=null){

        $pageHeading="Questions List";
        $page="questions";
        $topicidwithquestionid=[];
        if($topic!=null){
            $topicidwithquestionid=QuestionTopicMapping::find($topic);
        }
        
        $topicIds = []; 
        $questionIds = []; 
        
        // if($topicidwithquestionid!=null && $topicidwithquestionid!=[]){
        //     foreach ($topicidwithquestionid as $item) {
        //     $topicIds[] = $item['topic_id']; // Adjust according to your array structure
        //     $questionIds[] = $item['question_id']; // Adjust according to your array structure
        // }

        // }
        
       $allQuestions=Questions::whereNull('deleted_at')->orderBy('created_at', 'DESC')->paginate(10);
        // Fetch questions using the extracted IDs
    //     $allQuestions = Questions::whereIn('id', $questionIds)
    // ->whereNull('deleted_at')
    // ->orderBy('created_at', 'DESC')
    // ->paginate(10);

        if($allQuestions!=null){

            $allQuestionCount=count($allQuestions);
        }
        else{
            $allQuestionCount=0; 
        }

        $allCategories=ContestTypes::select('id','name')->whereNull('deleted_at')->get()->all();
        $allTopics = Topics::select('id','name')->whereNull('deleted_at')->get()->all();

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
            // $categoriesMap[4]="Question Bank";
        }

        return view("questions", compact('pageHeading','page','allQuestions','allQuestionCount','categoriesMap','allTopics','topicidwithquestionid'));
    }
    
  public function getfilteredquestions(Request $request) {
    $pageHeading = "Questions List";
    $page = "questions";

    // Get selected topic IDs from request
    $selectedTopics = $request->input('topics', []);

    // Fetch all questions, optionally filtering by selected topics
    $query = Questions::whereNull('deleted_at')->orderBy('created_at', 'DESC');

    if (!empty($selectedTopics)) {
        $query->whereHas('categories', function ($query) use ($selectedTopics) {
            $query->whereIn('id', $selectedTopics);
        });
    }

    $allQuestions = $query->paginate(10);

    $allQuestionCount = $allQuestions->total();

    $allCategories = ContestTypes::select('id', 'name')->whereNull('deleted_at')->get()->all();

    $categoriesMap = [];

    if ($allCategories != null) {
        foreach ($allCategories as $key => $value) {
            $categoriesMap[$value->id] = $value->name;
        }
    } else {
        $categoriesMap[1] = "Live Contest";
        $categoriesMap[2] = "Time Limit Contest";
        $categoriesMap[3] = "Any Time Contest";
        // $categoriesMap[4]="Question Bank";
    }

    return view("questions", compact('pageHeading', 'page', 'allQuestions', 'allQuestionCount', 'categoriesMap', 'selectedTopics'));
}

public function search(Request $request)
{
if($request->ajax())
{
    $output="";
    $questions = DB::table('questions')
    ->join('topics', 'questions.topic_id', '=', 'topics.id')
    ->where(function ($query) use ($request) {
        $query->where('questions.title', 'LIKE', '%' . $request->search . '%')
              ->orWhere('topics.name', 'LIKE', '%' . $request->search . '%');
    })
    ->where('questions.status', 1) // Filter by status
    ->select('questions.*', 'topics.name as topic_name')
    ->get();
    //dd($questions);
    
    if($questions)
    {
    foreach ($questions as $key => $topic) {
        $categoryIds = json_decode($topic->category, true);
         // Convert the category array into a comma-separated string
    $categoryNames = ContestTypes::whereIn('id', $categoryIds)->pluck('name')->toArray(); // Retrieve the category names based on the IDs
     // Build the category names HTML structure with <span> tags
    $categorySpans = array_map(function($name) {
        return '<span class="rounded-pill px-2 py-1 border border-1 me-1">' . htmlspecialchars($name) . '</span>';
    }, $categoryNames);

    // Join the spans with a space
    $categoryString = implode(' ', $categorySpans);
    $output.='<tr>'.
    '<td>'.$topic->id.'</td>'.
    '<td>'.$topic->topic_name.'</td>'.
    '<td>'.$topic->title.'</td>'.
    '<td>'.$categoryString.'</td>'.
    '<td><span class="text-success"><b>Active</span></td>'.
    '<td class=""><a href="' . route("admin.view.question", ["id" => $topic->id]) . '" class="btn btn-sm btn-primary"><i class="bi bi-eye"></i> View</a><a href="' . route("admin.edit.question", ["id" => $topic->id]) . '" class="btn d-inline-flex btn-sm btn-neutral mx-1">
        <span class=" pe-2">
            <i class="bi bi-pen"></i>
        </span>
        <span>Edit</span>
    </a>
    <button data-questionId="{{ $topic->id }}" class="btn btn-danger d-inline-flex btn-sm mx-1" onclick="deleteQuestion(this)">
        <span class=" pe-2">
            <i class="bi bi-trash-fill"></i>
        </span>
        <span>Delete</span>
    </button>
</td>'.
    '</tr>';
    }
    return Response($output);
    }
   }
}

// public function showEmployee(Request $request)
//   {
//       $topics = Topics::all();
//       dd($topics);
//       if($request->keyword !== '')
//       {
//       $topics = Topics::where('name','LIKE','%'.$request->keyword.'%')->get();
      
//       }
//       return response()->json([
//          '$topics' => $topics
//       ]);
//     }
    
    function new_question(){
        
        $pageHeading="New Question";
        $page="newquestion";

        $allCategories=ContestTypes::select('id','name')->whereNull('deleted_at')->get()->all();
       
        $allTopics=Topics::select('id','name')->whereNull('deleted_at')->get();
         
        return view("new_question", compact('pageHeading','page','allTopics', 'allCategories'));
    }
    
    
    function importquestions(){
        
       return view('importquestions');
    }
    
   public function processimport(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xls,xlsx|max:10240', // max 10MB
        ]);

        $file = $request->file('excel_file');

        Excel::import(new ExcelImport, $file);

        return redirect()->back()->with('success', 'Excel file imported successfully.');
    }
    
    
    function postNewQuestion(Request $request){

       try {
        
        
        $validate = $request->validate([
            'title_type' => 'required|string',
            'title' => 'required',
            'option_type' => 'required',
            'option_count' => 'required',
            'option' => 'required',
            'correct_option' => 'required',
            'ans_desc' => 'required',
            'status' => 'required',
            'topics' => 'required',
        ]);


        $title_type=$request->title_type;
        $option_type=$request->option_type;
        $option_count=$request->option_count;
        $ans_desc = trim($request->ans_desc);
        $title="";
        $options=[];

        
        if($title_type=="text" || $title_type=="youtubevideolink"){
            $title=trim($request->title);
        }
        else if($title_type=="image" || $title_type=="video"){
            if ($request->hasFile('title')) {
                if(($request->file('title')->getSize()/1000000) >5){
                     return redirect()->back()->withInput($request->input())->withError("File size is more than allowed file size.");
                }
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
         else if($option_type=="youtubevideolink"){  
            foreach ($inputOptions as $key => $value) {
                array_push($options,trim($value));
            }
        }
        else if($option_type=="image" || $option_type=="video"){
            foreach ($inputOptions as $key => $value) {
                if(($value->getSize()/1000000) >5){
                     return redirect()->back()->withInput($request->input())->withError("File size is more than allowed file size.");
                }
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
        if($title_type=="text" && !empty($request->hi_title)){
            $questions->hi_title = trim($request->hi_title);
        }
        $questions->option_type = trim($option_type);
        $questions->option_count = trim($option_count);
        $questions->options = json_encode($options);
        $questions->correct_option = json_encode($correct_option);
        $questions->category = json_encode($question_category);
        $questions->status = ($request->status);
        $questions->ans_desc = $ans_desc;
        $questions->created_at = date("Y-m-d H:i:s");
        $questions->updated_at = date("Y-m-d H:i:s");
        $questions->save();
        
        
        
        $topicsForthisQuestio=$request->topics;
        $questionId = $questions->id;
        $createdAt = date("Y-m-d H:i:s");
        
        $mappingData = [];
        foreach($topicsForthisQuestio as $topicId){
            $mappingData[] = [
                'question_id' => $questionId,
                'topic_id' => $topicId,
                'created_at' => $createdAt,
                'updated_at' => $createdAt, // You may set updated_at if needed
            ];
        }
        
        
        // mapping the question with topics
        QuestionTopicMapping::insert($mappingData);
        
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
            return redirect()->back()->withError('Id do not match our records.');
        } 

        $allCategories=ContestTypes::select('id','name')->whereNull('deleted_at')->get()->all();

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
            return redirect()->back()->withError('Id do not match our records.');
        } 

        $allCategories=ContestTypes::select('id','name')->whereNull('deleted_at')->get()->all();

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
        
        // selecting topics
        $allTopics=Topics::select('id','name')->whereNull('deleted_at')->get();
        
        
        // selecting mapped topic for this question 
        $topicsId=QuestionTopicMapping::where('question_id', $id)->pluck('topic_id')->toArray();
        
        return view("edit_question", compact('pageHeading','page','question','categoriesMap','topicsId','allTopics'));
    }

    function post_edit_question(Request $request){

        
        try {
            
         $validate = $request->validate([
             'qid'=>"required|integer",
             'title_type' => 'required|string',
             'option_type' => 'required',
             'option_count' => 'required',
             'correct_option' => 'required',
             'ans_desc' => 'required',
             'status' => 'required',
             'topics' => 'required',
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
         $ans_desc = trim($request->ans_desc);
 
         if(($title_type=="text" || $title_type=="youtubevideolink") && !empty($request->title)){
             $title=trim($request->title);
         }
         else if(($title_type=="image" || $title_type=="video")){
             if ($request->hasFile('title')) {
                 if(($request->file('title')->getSize()/1000000) >5){
                     return redirect()->back()->withInput($request->input())->withError("File size is more than allowed file size.");
                 }
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
                 if(($value->getSize()/1000000) >5){
                     return redirect()->back()->withInput($request->input())->withError("File size is more than allowed file size.");
                 }
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
         $questions->ans_desc = $ans_desc;
         $questions->updated_at = date("Y-m-d H:i:s");
         
         // creating map for question and topic 
         $topicIds=$request->topics;
         $questions->topics()->sync($topicIds);
         $questions->save();
            
        
        
         return redirect::route('admin.questions')->withSuccess("Question has been saved successfully");
         
        } catch (Exception $err) {
         //throw $th;
         Log::error("Exception occurred: ".$err->getMeassage()." on ".date("Y-m-d H:i:s"));
         return redirect::route('admin.questions')->withError("Internal server error.");
        }
     }
 
    function delete_question($id){
        $question = Questions::find($id);
        
        // Manually set the 'deleted_at' column using a raw SQL query
        $affected = Questions::where('id', $id)
       ->update([
           'deleted_at' => date("Y-m-d H:i:s"),
           'status' => 0
        ]);
    
        if ($affected) {
            return response()->json(['status' => true, 'message' => 'Question deleted successfully']);
        } else {
            return response()->json(['status' => false, 'message' => 'Question not found'], 404);
        }

    }

    function contests(Request $request){

        $pageHeading="Contest List";
        $page="contests";
        
        $contest_type=(isset($request->type))?$request->type:0;

        $allContest=Contests::whereNull('deleted_at')->orderBy("created_at",'desc')->where(function($data) use ($request){
            
            
            if(isset($request->type) && !empty($request->type)){
                $type=$request->type;
                $data->where('contests.contest_id',$type);
            }
            
            
        
        })->paginate(10);

        if($allContest!=null){

            $allContestCount=count($allContest);
        }
        else{
            $allContestCount=0; 
        }

        $allCategories=ContestTypes::select('id','name')->whereNull('deleted_at')->get()->all();
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
        

        return view("contests", compact('pageHeading','page','allContest','allContestCount','categoriesMap','contest_type'));
    }

    function newContests(){

        $pageHeading="New Contest";
        $page="newcontest";

        
       
        $allCategories=ContestTypes::select('id','name')->whereNull('deleted_at')->get()->all();
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

        
       
        $allCategories=ContestTypes::select('id','name')->whereNull('deleted_at')->get()->all();
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
        $users=Users::select('id','phone','name')->whereNull('deleted_at')->get();
        $contest_type=1;
        $contest_type_text="Live Contest";
        $allTopics=Topics::where('status',1)->whereNull('deleted_at')->get();
        
        return view("new_contest", compact('pageHeading','page','categoriesMap','allQuestions','contest_type_text','contest_type','users','allTopics'));
    }

    function newTimeLimitContests(){

        $pageHeading="New Time Limit Contest";
        $page="newtimelimitcontest";

        
       
        $allCategories=ContestTypes::select('id','name')->whereNull('deleted_at')->get()->all();
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
        $users=Users::select('id','phone','name')->whereNull('deleted_at')->get();
        $contest_type=2;
        $contest_type_text="Time Limit Contest"; 
        $allTopics=Topics::where('status',1)->whereNull('deleted_at')->get();
        return view("new_contest", compact('pageHeading','page','categoriesMap','allQuestions','contest_type_text','contest_type','users','allTopics'));
    }

    function newAnyTimeContests(){

        $pageHeading="Challenges";
        $page="newanytimecontest";

        
       
        $allCategories=ContestTypes::select('id','name')->whereNull('deleted_at')->get()->all();
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
       
        $users=Users::select('id','phone','name')->whereNull('deleted_at')->get();
        $contest_type=3;
        $contest_type_text="Challenges";
        
        $allTopics=Topics::where('status',1)->whereNull('deleted_at')->get();
        
        return view("new_contest", compact('pageHeading','page','categoriesMap','allQuestions','contest_type_text','contest_type','users','allTopics'));
    }

    function postNewContest(Request $request){
        try {
 
            $validate = $request->validate([
                'contest_type' => 'required',
                'contest_name' => 'required',
                'contest_image' => 'required|image|mimes:jpeg,png,jpg',
                'start_time' => 'required',
                'start_date' => 'required',
                'end_time' => 'required',
                'end_date' => 'required',
                'entry_fees' => 'required|numeric|min:0',
                'per_question_mark' => 'required|numeric|min:0',
                'duration'=>'required',
                // 'per_question_time'=>'required',
                'neg_mark' => 'required|numeric|min:0',
                'status' => 'required|numeric|min:0',
                'selected_question_ids' => 'required',
                'winner_count'=> 'required|numeric|min:1',
                'distribution_percent'=> 'required|numeric|min:1',
                'percent_distribution'=> 'required',
                'slot_count'=> 'sometimes|integer',
            ]);
            //if Contest Type name is same as contest Id
            $contestModel = DB::table('contest_types')
		->get();
	
	$contest_type=$request->contest_type;
            foreach($contestModel as $type){
                if($contest_type==$type->name){
                $contest_type=$type->id;
               $validate = $request->validate([
                'host'=> 'required',
              ]); 
            
            }
            
            if($contest_type==3 && $request->winner_count>$request->slot_count){
                return redirect()->back()->withInput($request->input())->withError("Winner count can not be more than slot count !");
            }   
            }
              
            $distribution_percent=$request->distribution_percent;
            $percent_distribution=$request->percent_distribution;
            $total_per=0;
            foreach($percent_distribution as $per){
              $total_per +=$per;
            }
            
            if($total_per<$distribution_percent){
              return redirect()->back()->withInput($request->input())->withError("Sum of winner percent distribution is more than total distribution percent");
            }
            
            $start_time=date("H:i:s",strtotime($request->start_time));
            $start_date=date("Y-m-d",strtotime($request->start_date));
            $start_date_time=$start_date." ".$start_time;
            
            $end_date=date("Y-m-d",strtotime($request->end_date));
            $end_time=date("H:i:s",strtotime($request->end_time));
            $end_date_time=$end_date." ".$end_time;
            
            

            $entry_fees=$request->entry_fees;
            $per_question_mark=$request->per_question_mark;
            $neg_mark=$request->neg_mark;
            $status=$request->status;

            $duration=isset($request->duration)?$request->duration:0;
            $per_question_time=isset($request->per_question_time)?$request->per_question_time:0;
            $q_count=count($request->questions);
            
            // I have sum both $duration and $per_question_time*$q_count because only one will be >0 both can not.  5-8-2023
            $contest_time=($duration*60)+($per_question_time*$q_count);
            
            // $time=strtotime($start_date_time,"+".$contest_time);
            $time=strtotime($start_date_time." + ".$contest_time." seconds");
            
            
            if(strtotime($end_date_time)<$time){
                $lessMinutes=date('i',strtotime($end_date_time)-$time);
                 return redirect()->back()->withInput($request->input())->withError("End date time must be greater than or equal to start date time + duration of contest.");
            }
            
            $description=isset($request->description)?$request->description:null;
            
            $contest_image="contest_image_".time();
            if ($request->hasFile('contest_image')) {
                if(($request->file('contest_image')->getSize()/1000000) >5){
                     return redirect()->back()->withInput($request->input())->withError("File size is more 5mb.");
                 }
                $contest_image = $this->uploadContestImageFile($request->file('contest_image'));
            }
            else{
               return redirect()->back()->withInput($request->input())->withError("Please upload contest image."); 
            }
    
            $contests = new Contests();
            $contests->contest_id = $contest_type;
            $contests->contest_image = trim($contest_image);
            $contests->name = trim($request->contest_name);
            $contests->start_time = ($start_date_time);
            $contests->end_date_time = ($end_date_time);
            $contests->entry_fees = trim($entry_fees);
            $contests->questions_count = count($request->questions);
            $contests->question_ids = $request->selected_question_ids;
            $contests->duration = trim($duration);
            $contests->per_question_time = trim($per_question_time);
            $contests->per_question_mark = trim($per_question_mark);
            $contests->neg_mark = trim($neg_mark);
            $contests->description = trim($description);
            $contests->status = trim($status);
            
            if($contest_type==1){
                $contests->host_user_id = trim($request->host);
                $contests->registration_deadline = trim($request->deadline);
                
            }
            $contests->winner_count = trim($request->winner_count);
            $contests->distribution_percent = (float)($request->distribution_percent);
            $contests->winner_percent_distribution = json_encode($request->percent_distribution);
            if($contest_type==3){
               
                $contests->slot_count = trim($request->slot_count);
                
            }
            
            $contests->created_at = date("Y-m-d H:i:s");
            $contests->updated_at = date("Y-m-d H:i:s");
            $contests->save();
            return redirect::route('admin.contests')->withSuccess("Contest has been saved successfully");
         } catch (Exception $err) {
             //throw $th;
             //  echo "Exception occurred: ".$err->getMeassage()." on ".date("Y-m-d H:i:s");
             Log::error("Exception occurred: ".$err->getMeassage()." on ".date("Y-m-d H:i:s"));
             return redirect()->back()->withInput($request->input())->withError("Internal server error.");
         }
    }

 
    function view_contest($id){
 
         $pageHeading="Contest Details";
         $page="contestdetails";
 
         $contest=Contests::select('contests.*','users.name as host_name')->leftJoin('users','users.id','contests.host_user_id')->where('contests.id',$id)->first();

         if (!$contest) {
             return redirect()->back()->withError('Id do not match our records.');
         } 
         $contestQuestions=[];
         $allQuestions=json_decode($contest->question_ids);
         if(!is_null($allQuestions)){
            foreach ($allQuestions as $key => $id) {
                $question_details=Questions::find($id);
                array_push($contestQuestions,$question_details);
             }
         }
        
 
         $allCategories=ContestTypes::select('id','name')->whereNull('deleted_at')->get()->all();
 
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
             return redirect()->back()->withError('Id do not match our records.');
         }
         $contest_type=$contest->contest_id;
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
       
        $allCategories=ContestTypes::select('id','name')->whereNull('deleted_at')->get()->all();
 
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
         $users=Users::select('id','phone','name')->whereNull('deleted_at')->get();
         return view("edit_contest", compact('pageHeading','page','contest','allQuestions','contestQuestionIdMap','contestQuestions','categoriesMap','contest_type',
        'users'));
     }
 
    function post_edit_contest(Request $request){
         try {
 
            $validate = $request->validate([
                'id'=>"required",
                'contest_type' => 'required',
                'contest_name' => 'required',
                'start_time' => 'required',
                'start_date' => 'required',
                'entry_fees' => 'required|numeric|min:0',
                'per_question_mark' => 'required|numeric|min:0',
                'neg_mark' => 'required|numeric|min:0',
                'status' => 'required|numeric|min:0',
                'questions' => 'required',
                'winner_count'=> 'required|numeric|min:1',
                'distribution_percent'=> 'required|numeric|min:1',
                'percent_distribution'=> 'required',
            ]);
            
            
            $contests = Contests::find($request->id);
            if($contests==null){
                return redirect()->back()->withError("Invalid Error !");
            }
            
            $contest_type=$request->contest_type;
          
            
            if($contest_type==1){
               $validate = $request->validate([
                'host'=> 'required',
                'deadline'=> 'required|numeric|min:0',
              ]); 
            
            } 
              
            $distribution_percent=$request->distribution_percent;
            $percent_distribution=$request->percent_distribution;
            $total_per=0;
            foreach($percent_distribution as $per){
              $total_per +=$per;
            }
            
            if($total_per>$distribution_percent){
              return redirect()->back()->withInput($request->input())->withError("Sum of winner percent distribution is more than total distribution percent");
            }
    
    
           
            
            
            $start_time=date("H:i:s",strtotime($request->start_time));
            $start_date=date("Y-m-d",strtotime($request->start_date));
            $start_date_time=$start_date." ".$start_time;
            $end_date=date("Y-m-d",strtotime($request->end_date));
            $end_time=date("H:i:s",strtotime($request->end_time));
            $end_date_time=$end_date." ".$end_time;
            
            $entry_fees=$request->entry_fees;
            $per_question_mark=$request->per_question_mark;
            $neg_mark=$request->neg_mark;
            $status=$request->status;

            $duration=isset($request->duration)?$request->duration:0;
            $per_question_time=isset($request->per_question_time)?$request->per_question_time:0;
            $q_count=count($request->questions);
            
            // I have sum both $duration and $per_question_time*$q_count because only one will be >0 both can not.  5-8-2023
            $contest_time=($duration*60)+($per_question_time*$q_count);
            
            
            $time=strtotime($start_date_time." + ".$contest_time." seconds");
            
           
            if(strtotime($end_date_time)<$time){
                $lessMinutes=date('i',strtotime($end_date_time)-$time);
                 return redirect()->back()->withInput($request->input())->withError("End date time must be greater than or equal to start date time + duration of contest.");
            }
            $description=isset($request->description)?$request->description:null;
            
             $contest_image="contest_image_".time();
            if ($request->hasFile('contest_image')) {
                if(($request->file('contest_image')->getSize()/1000000) >5){
                     return redirect()->back()->withInput($request->input())->withError("File size is more 5mb.");
                 }
                $contest_image = $this->uploadContestImageFile($request->file('contest_image'));
                
                $contests->contest_image = trim($contest_image);
            }
            
            
            $contests->contest_id = trim($contest_type);
            $contests->name = trim($request->contest_name);
            $contests->start_time = ($start_date_time);
            $contests->end_date_time = ($end_date_time);
            $contests->entry_fees = trim($entry_fees);
            $contests->questions_count = count($request->questions);
            $contests->question_ids = json_encode($request->questions);
            $contests->duration = trim($duration);
            $contests->per_question_time = trim($per_question_time);
            $contests->per_question_mark = trim($per_question_mark);
            $contests->neg_mark = trim($neg_mark);
            $contests->description = trim($description);
            $contests->status = trim($status);
            
            if($contest_type==1){
                $contests->host_user_id = trim($request->host);
                $contests->registration_deadline = trim($request->deadline);
                $contests->winner_count = trim($request->winner_count);
                $contests->distribution_percent = trim($request->distribution_percent);
                $contests->winner_percent_distribution = json_encode($request->percent_distribution);
            }
            
            $contests->created_at = date("Y-m-d H:i:s");
            $contests->updated_at = date("Y-m-d H:i:s");
            $contests->save();
    
            return redirect::route('admin.contests')->withSuccess("Contest has been saved successfully");
         } catch (Exception $err) {
             //throw $th;
             //  echo "Exception occurred: ".$err->getMeassage()." on ".date("Y-m-d H:i:s");
             Log::error("Exception occurred: ".$err->getMeassage()." on ".date("Y-m-d H:i:s"));
             return redirect()->back()->withError("Internal server error.");
         }
      }
      
    function delete_contest($id){
        $contest = Contests::find($id);
        
        // Manually set the 'deleted_at' column using a raw SQL query
        $affected = Contests::where('id', $contest->id)
        ->update([
           'deleted_at' => date("Y-m-d H:i:s"),
           'status' => 0
        ]);
        
        
    
        if ($affected) {
            return response()->json(['status' => true, 'message' => 'Contest deleted successfully']);
        } else {
            return response()->json(['status' => false, 'message' => 'Contest not found'], 404);
        }

    }
    
    function delete_user($id){
        $user = Users::find($id);
        
        // Manually set the 'deleted_at' column using a raw SQL query
        $affected = Users::where('id', $user->id)
        ->update([
           'deleted_at' => date("Y-m-d H:i:s"),
           'status' => 0
        ]);
    
        if ($affected) {
            return response()->json(['status' => true, 'message' => 'User deleted successfully']);
        } else {
            return response()->json(['status' => false, 'message' => 'User not found'], 404);
        }

    }
    
    
    function contest_payment_ledger(Request $request){
        
        
        $contest_id=$request->id;
        $pageHeading="Contest Payment Ledger";
        $page="paymentledger";
        
        $contest=Contests::select('id','name','entry_fees','status','start_time')->find($contest_id);
        
        if (!$contest) {
         return redirect()->back()->withError('Id do not match our records.');
        }
        
        $payments=PaymentModel::select('payment_id','gateway_payment_id','payments.amount','payments.user_id','payments.payment_status','payment_mode','comment','payments.created_at','payments.updated_at','users.phone')->join('users',"users.id",'=','payments.user_id')->whereNull('payments.deleted_at')->where("contest_id",$contest_id)->where(function($payments) use ($request){
            
            
            if(isset($request->from) && !empty($request->from)){
                $from=$request->from." 00:00:01";
                $payments->where('payments.created_at','>=',$from);
            }
            
            if(isset($request->to) && !empty($request->to)){
                $to=$request->to." 23:59:59";
                $payments->where('payments.created_at','<=',$to);
            }
            
            if(isset($request->userid) && !empty($request->userid)){
                $payments->where('payments.user_id',$request->userid);
            }
        
        })->get();
        
        $users=Users::select('id','phone','name')->whereNull('deleted_at')->get();
        
        return view("view_contest_payment_ledger", compact('pageHeading','page','contest','payments','users'));
    }
    
    function contest_withdraw_ledger(Request $request){
        
        
        $contest_id=$request->id;
        $pageHeading="Contest Withdraw Ledger";
        $page="withdrawledger";
        
        $contest=Contests::select('id','name','entry_fees','status','start_time')->find($contest_id);
        
        if (!$contest) {
         return redirect()->back()->withError('Id do not match our records.');
        }
        
        $data=WithdrawModel::select('withdraw_id','gateway_withdraw_id','withdraws.amount','withdraws.user_id','withdraws.withdraw_status','withdraw_mode','comment','withdraws.created_at','withdraws.updated_at','users.phone')->join('users',"users.id",'=','withdraws.user_id')->whereNull('withdraws.deleted_at')->where("contest_id",$contest_id)->where(function($data) use ($request){
            
            
            if(isset($request->from) && !empty($request->from)){
                $from=$request->from." 00:00:01";
                $data->where('withdraws.created_at','>=',$from);
            }
            
            if(isset($request->to) && !empty($request->to)){
                $to=$request->to." 23:59:59";
                $data->where('withdraws.created_at','<=',$to);
            }
            
            if(isset($request->userid) && !empty($request->userid)){
                $data->where('withdraws.user_id',$request->userid);
            }
        
        })->get();
        
        $users=Users::select('id','phone','name')->whereNull('deleted_at')->get();
        
        return view("view_contest_payment_ledger", compact('pageHeading','page','contest','data','users'));
    }
    
    function contest_leaderboard_ledger(Request $request){
        
        
        $contest_id=$request->id;
        $pageHeading="Contest Leader Board";
        $page="leaderboard";
        
        $contest=Contests::select('id','contest_id','name','entry_fees','status','start_time')->find($contest_id);
        
        if (!$contest) {
         return redirect()->back()->withError('Id do not match our records.');
        }
        
        $data=LeaderBoard::select('contest_leaderboard.*','users.phone')
        ->join('users',"users.id",'=','contest_leaderboard.user_id')
        ->whereNotNull('contest_leaderboard.ans_key')
        ->whereNull('contest_leaderboard.deleted_at')->where("contest_leaderboard.contest_id",$contest_id)->where(function($data) use ($request){
            
            
            if(isset($request->from) && !empty($request->from)){
                $from=$request->from." 00:00:01";
                $data->where('contest_leaderboard.created_at','>=',$from);
            }
            
            if(isset($request->to) && !empty($request->to)){
                $to=$request->to." 23:59:59";
                $data->where('contest_leaderboard.created_at','<=',$to);
            }
            
            if(isset($request->userid) && !empty($request->userid)){
                $data->where('contest_leaderboard.user_id',$request->userid);
            }
        
        })->orderBy('contest_leaderboard.rank_in_this_contest')->get();
        
        if($contest->contest_id==3){
            // for any time quiz
            foreach($data as $key=>$leaderboard){
                $slot_details=QuizSlot::select('slot_id')->whereNull('deleted_at')->where('user_id',$leaderboard['user_id'])
                ->where('contest_id',$leaderboard['contest_id'])
                ->first();
                
                $data[$key]['slot_id']="Slot No. ".$slot_details['slot_id'];
            }
        }
        
        
        $users=Users::select('id','phone','name')->whereNull('deleted_at')->get();
        
        return view("view_contest_payment_ledger", compact('pageHeading','page','contest','data','users'));
    }
    
    
    function contest_leaderboard_ledger_api(Request $request){
       
        $contest_id=$request->id;
        $pageHeading="Contest Leader Board";
        $page="leaderboard";
        
        $contest=Contests::select('id','name','entry_fees','status','start_time')->find($contest_id);
        
        if (!$contest) {
         return json_encode(["success"=>false, "message"=>"Id do not match our records."]);
        }
        
        $data=LeaderBoard::select('contest_leaderboard.*','users.phone')->join('users',"users.id",'=','contest_leaderboard.user_id')->whereNotNull('contest_leaderboard.ans_key')->whereNull('contest_leaderboard.deleted_at')->where("contest_leaderboard.contest_id",$contest_id)->where(function($data) use ($request){
            
            
            if(isset($request->from) && !empty($request->from)){
                $from=$request->from." 00:00:01";
                $data->where('contest_leaderboard.created_at','>=',$from);
            }
            
            if(isset($request->to) && !empty($request->to)){
                $to=$request->to." 23:59:59";
                $data->where('contest_leaderboard.created_at','<=',$to);
            }
            
            if(isset($request->userid) && !empty($request->userid)){
                $data->where('contest_leaderboard.user_id',$request->userid);
            }
        
        })->orderBy('contest_leaderboard.rank_in_this_contest')->get();
        
        
        return json_encode(["success"=>true, "data"=>$data]);
    }
      
    function contest_preview($id){
 
         $pageHeading="Contest Preview";
         $page="contestpreview";
 
         $contest=Contests::select('contests.*','users.name as host_name')->leftJoin('users','users.id','contests.host_user_id')->where('contests.id',$id)->first();

         if (!$contest) {
             return redirect()::Route('admin.contests')->withError('Id do not match our records.');
         } 
         
         $contestQuestions=[];
         $allQuestions=json_decode($contest->question_ids);
         if(!is_null($allQuestions)){
            foreach ($allQuestions as $key => $id) {
                $question_details=Questions::find($id);
                array_push($contestQuestions,$question_details);
             }
         }
        
 
         $allCategories=ContestTypes::select('id','name')->whereNull('deleted_at')->get()->all();
 
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
 
         return view("preview_contest", compact('pageHeading','page','contest','categoriesMap','contestQuestions'));
     }
     
     function contest_submit(Request $request){
        //  dd($request->all());
         return redirect::route('admin.contests')->withSuccess("Thank you for participating in contest !");
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
    
    public function delete_topic_study_material($id)  
       { 
          DB::delete('DELETE FROM topics WHERE id = ?', [$id]); 
          echo ("Topic Record deleted successfully."); 
          return redirect()->route('admin.topics'); 
       } 
    
    public function destroy($id)  
       { 
          DB::delete('DELETE FROM topics WHERE id = ?', [$id]); 
          echo ("Topic Record deleted successfully."); 
          return redirect()->route('admin.topics'); 
       } 
    
    function newTopic(Request $request){
        $pageHeading="Topics List";
        $page="topics";
        $validate = $request->validate([
                'topic'=>"required",
                'status' => 'required',
        ]);
        
        try{
        $name=trim(strtolower($request->topic));
        $status=trim(strtolower($request->status));

        $allTopic=Topics::whereNull('deleted_at')->where("name",$name)->first();

        if($allTopic!=null){

            return redirect()->back()->withError('Topic Already Exist.');
        }
        
        $timestamp=date("Y-m-d H:i:s");
        $newTopic=new Topics();
        $newTopic->name=$name;
        $newTopic->status=$status;
        if(isset($request->image) && !empty($request->image)){
            if(($request->image->getSize()/1000000) >5){
                return redirect()->back()->withInput($request->input())->withError("File size is more than allowed file size.");
            }
            $fileName = $this->uploadTopicFile($request->image);
            $newTopic->image=$fileName;
        }
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
        $validate = $request->validate([
                'topic'=>"required",
                'status' => 'required',
                
        ]);
        
        try{
        $name=trim(strtolower($request->topic));
        $status=trim(strtolower($request->status));

      
        
        $timestamp=date("Y-m-d H:i:s");
        $editTopic=Topics::find($request->id);
        if($editTopic==null){
            return redirect()->back()->withError('Invalid Id');
        }
        $editTopic->name=$name;
        $editTopic->status=$status;
        
        if ($request->hasFile('image')) {
            if(($request->file('image')->getSize()/1000000) >5){
                 return redirect()->back()->withInput($request->input())->withError("File size is more than allowed file size.");
             }
            $fileName = $this->uploadTopicFile($request->file('image'));
            $editTopic->image=$fileName;
        }
            
     
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
    
    function add_topic_study_material($topic_id,$type){
        $pageHeading="Add PDF";
        $page="topics";
        $arr=[];
        $title="Questions";
        $data=Topics::whereNull('deleted_at')->find($topic_id);

       
        if($data==null){
             return redirect()->back()->withError('Invalid Id');
        }
        if($type=="pdf"){
            $arr=$data->pdf==null?[]:json_decode($data->pdf);
            $title="PDF";
        }
        elseif($type=="videofile"){
            $arr=$data->video_files==null?[]:json_decode($data->video_files);
            $title="Video Files";
        }
        elseif($type=="youtubelinks"){
            $arr=$data->yt_video==null?[]:json_decode($data->yt_video);
            $title="YT Video";
        }
        elseif($type=="externallinks"){
            $arr=$data->external_links==null?[]:json_decode($data->external_links);
            $title="External Links";
        }
        elseif($type=="questions"){
            $arr=$data->questions==null?[]:json_decode($data->questions);
            $title="Questions";
        }
        
        $contestQuestionIdMap=[];
         $topicQuestions=[];
         $allContestQuestions=json_decode($data->questions);
         if(!is_null($allContestQuestions)){
            foreach ($allContestQuestions as $key => $id) {
                $question_details=Questions::find($id);
                array_push($topicQuestions,$question_details);
               $contestQuestionIdMap[$id]=true;
             }
         }
         $count=count($arr)+1;
         $allQuestions=Questions::where('status',1)->whereNull('deleted_at')->get()->all();
       
       
        return view("add_topic_study_material", compact('pageHeading','page','data','arr','type','title','topicQuestions','allQuestions','count','contestQuestionIdMap'));
    }
    
    function post_add_topic_study_material(Request $request){
        
        try {
            
         $validate = $request->validate([
             'topic_id'=>"required|integer",
             'type' => 'required|string',
         ]);
         $edit = Topics::find($request->topic_id);
         if(!$edit){
            return redirect::route('admin.topics')->withError("Id not found !");
         }
 
         $type=$request->type;
         $files=[];
 
 
         $inputFiles=$request->study_files;
         
        
         // saving options
         if(($type=="externallinks" ) && !is_null($inputFiles)){ 
             $title=$request->link_title;
             foreach ($inputFiles as $key => $value) {
                 array_push($files,json_encode([$title[$key],$value]));
             }
         }
         else if(($type=="youtubelink") && !is_null($inputFiles)){  
             foreach ($inputFiles as $key => $value) {
                 array_push($files,trim($value));
             }
         }
         else if(($type=="pdf" || $type=="videofile") && !is_null($inputFiles)){
             foreach ($inputFiles as $key => $value) {
                 if(($value->getSize()/1000000) >5){
                     return redirect()->back()->withInput($request->input())->withError("File size is more than allowed file size.");
                 }
                 $fileName = $this->uploadTopicFile($value);
                 array_push($files,$fileName);
             }
         }
         else if($type=="questions"){
             $inputFiles=$request->questions;
             foreach($inputFiles as $key => $value) {
                 array_push($files,$value);
             }
         }
         
         if(!empty($files)){
            $edit=Topics::find($request->topic_id);
            if($type=="externallinks"){  
                $edit->external_links=json_encode($files);
            }
            if($type=="youtubelink"){  
                $edit->yt_videos=json_encode($files);
            }
            if($type=="pdf"){  
                $edit->pdf=json_encode($files);
            }
            if($type=="videofile"){  
                $edit->video_files=json_encode($files);
            }
            if($type=="questions"){  
                $edit->questions=json_encode($files);
            }
            
            $edit->save();
         }
         return redirect::route('admin.topics')->withSuccess("Topic has been saved successfully");
         
         
         } catch (Exception $err) {
             //throw $th;
             Log::error("Exception occurred: ".$err->getMeassage()." on ".date("Y-m-d H:i:s"));
             return redirect::route('admin.questions')->withError("Internal server error.");
         }
     }
 
    
    function education(){
        $pageHeading="Education List";
        $page="education";

        $allData=Education::whereNull('deleted_at')->orderBy("name")->get();

        if($allData!=null){

            $allDataCount=count($allData);
        }
        else{
            $allDataCount=0; 
        }
       
        return view("education", compact('pageHeading','page','allData','allDataCount'));
    }
    
    function newEducation(Request $request){
        $pageHeading="Education List";
        $page="education";
        $validate = $request->validate([
                'name'=>"required",
        ]);
        
        try{
        $name=trim(strtolower($request->name));

        $allData=Education::whereNull('deleted_at')->where("name",$name)->first();

        if($allData!=null){

            return redirect()->back()->withError('Already Exist.');
        }
        
        $timestamp=date("Y-m-d H:i:s");
        $new=new Education();
        $new->name=$name;
        $new->created_at=$timestamp;
        $new->updated_at=$timestamp;
        $new->save();
       
        return redirect::route('admin.education')->withSuccess("Saved successfully !");
        }
        catch (Exception $err) {
            //throw $th;
            Log::error("Exception occurred: ".$err->getMeassage()." on ".date("Y-m-d H:i:s"));
            return redirect::route('admin.education')->withError("Internal server error.");
        }
    }
    
    function post_edit_education(Request $request){
         $pageHeading="Education List";
        $page="education";
        
        $validate = $request->validate([
                'name'=>"required",
        ]);
        
        try{
        $name=trim(strtolower($request->name));
        
        $timestamp=date("Y-m-d H:i:s");
        $edit=Education::find($request->id);
        if($edit==null){
            return redirect()->back()->withError('Invalid Id');
        }
        $edit->name=$name;
        $edit->updated_at=$timestamp;
        $edit->save();
       
        return redirect::route('admin.education')->withSuccess("Updated successfully !");
        }
        catch (Exception $err) {
            //throw $th;
            Log::error("Exception occurred: ".$err->getMeassage()." on ".date("Y-m-d H:i:s"));
            return redirect::route('admin.education')->withError("Internal server error.");
        }
    }
    
    
    function profession(){
        $pageHeading="Profession List";
        $page="profession";

        $allData=Profession::whereNull('deleted_at')->orderBy("name")->get();

        if($allData!=null){

            $allDataCount=count($allData);
        }
        else{
            $allDataCount=0; 
        }
       
        return view("profession", compact('pageHeading','page','allData','allDataCount'));
    }
    
    function newProfession(Request $request){
        $pageHeading="Profession List";
        $page="profession";
        $validate = $request->validate([
                'name'=>"required",
        ]);
        
        try{
        $name=trim(strtolower($request->name));

        $allData=Profession::whereNull('deleted_at')->where("name",$name)->first();

        if($allData!=null){

            return redirect()->back()->withError('Already Exist.');
        }
        
        $timestamp=date("Y-m-d H:i:s");
        $new=new Profession();
        $new->name=$name;
        $new->created_at=$timestamp;
        $new->updated_at=$timestamp;
        $new->save();
       
        return redirect::route('admin.profession')->withSuccess("Saved successfully !");
        }
        catch (Exception $err) {
            //throw $th;
            Log::error("Exception occurred: ".$err->getMeassage()." on ".date("Y-m-d H:i:s"));
            return redirect::route('admin.profession')->withError("Internal server error.");
        }
    }
    
    function post_edit_profession(Request $request){
         $pageHeading="Profession List";
        $page="profession";
        
        $validate = $request->validate([
                'name'=>"required",
        ]);
        
        try{
        $name=trim(strtolower($request->name));
        
        $timestamp=date("Y-m-d H:i:s");
        $edit=Profession::find($request->id);
        if($edit==null){
            return redirect()->back()->withError('Invalid Id');
        }
        $edit->name=$name;
        $edit->updated_at=$timestamp;
        $edit->save();
       
        return redirect::route('admin.profession')->withSuccess("Updated successfully !");
        }
        catch (Exception $err) {
            //throw $th;
            Log::error("Exception occurred: ".$err->getMeassage()." on ".date("Y-m-d H:i:s"));
            return redirect::route('admin.profession')->withError("Internal server error.");
        }
    }
    
    function state(){
        $pageHeading="State List";
        $page="state";

        $allData=State::whereNull('deleted_at')->orderBy("name")->get();

        if($allData!=null){

            $allDataCount=count($allData);
        }
        else{
            $allDataCount=0; 
        }
       
        return view("state", compact('pageHeading','page','allData','allDataCount'));
    }
    
    function newState(Request $request){
        $pageHeading="State List";
        $page="state";
        $validate = $request->validate([
            'name'=>"required",
        ]);
        
        try{
        $name=trim(strtolower($request->name));

        $allData=State::whereNull('deleted_at')->where("name",$name)->first();

        if($allData!=null){

            return redirect()->back()->withError('Already Exist.');
        }
        
        $timestamp=date("Y-m-d H:i:s");
        $new=new State();
        $new->name=$name;
        $new->created_at=$timestamp;
        $new->updated_at=$timestamp;
        $new->save();
       
        return redirect::route('admin.state')->withSuccess("Saved successfully !");
        }
        catch (Exception $err) {
            //throw $th;
            Log::error("Exception occurred: ".$err->getMeassage()." on ".date("Y-m-d H:i:s"));
            return redirect::route('admin.state')->withError("Internal server error.");
        }
    }
    
    function post_edit_state(Request $request){
         $pageHeading="State List";
        $page="state";
        
        $validate = $request->validate([
                'name'=>"required",
        ]);
        
        try{
        $name=trim(strtolower($request->name));
        
        $timestamp=date("Y-m-d H:i:s");
        $edit=State::find($request->id);
        if($edit==null){
            return redirect()->back()->withError('Invalid Id');
        }
        $edit->name=$name;
        $edit->updated_at=$timestamp;
        $edit->save();
       
        return redirect::route('admin.state')->withSuccess("Updated successfully !");
        }
        catch (Exception $err) {
            //throw $th;
            Log::error("Exception occurred: ".$err->getMeassage()." on ".date("Y-m-d H:i:s"));
            return redirect::route('admin.state')->withError("Internal server error.");
        }
    }
    
    function city(){
        $pageHeading="City List";
        $page="city";

        $allData=City::whereNull('deleted_at')->orderBy("name")->get();
        $allState=State::whereNull('deleted_at')->orderBy("name")->get();
        if($allData!=null){
            $allDataCount=count($allData);
        }
        else{
            $allDataCount=0; 
        }
       
        return view("city", compact('pageHeading','page','allData','allDataCount','allState'));
    }
    
    function newCity(Request $request){
        $pageHeading="City List";
        $page="city";
        $validate = $request->validate([
            'name'=>"required",
            'state'=>"required",
        ]);
        
        try{
        $name=trim(strtolower($request->name));
        $state=trim(strtolower($request->state));

        $allData=City::whereNull('deleted_at')->where("name",$name)->where('state_id',$state)->first();

        if($allData!=null){

            return redirect()->back()->withError('Already Exist.');
        }
        
        $timestamp=date("Y-m-d H:i:s");
        $new=new City();
        $new->name=$name;
        $new->state_id=$state;
        $new->created_at=$timestamp;
        $new->updated_at=$timestamp;
        $new->save();
       
        return redirect::route('admin.city')->withSuccess("Saved successfully !");
        }
        catch (Exception $err) {
            //throw $th;
            Log::error("Exception occurred: ".$err->getMeassage()." on ".date("Y-m-d H:i:s"));
            return redirect::route('admin.city')->withError("Internal server error.");
        }
    }
    
    function post_edit_city(Request $request){
         $pageHeading="City List";
        $page="city";
        
        $validate = $request->validate([
            'name'=>"required",
            'state'=>"required",
        ]);
        
        try{
        $name=trim(strtolower($request->name));
        $state=trim(strtolower($request->state));
        $timestamp=date("Y-m-d H:i:s");
        $edit=City::find($request->id);
        if($edit==null){
            return redirect()->back()->withError('Invalid Id');
        }
        $edit->name=$name;
        $edit->state_id=$state;
        $edit->updated_at=$timestamp;
        $edit->save();
       
        return redirect::route('admin.city')->withSuccess("Updated successfully !");
        }
        catch (Exception $err) {
            //throw $th;
            Log::error("Exception occurred: ".$err->getMeassage()." on ".date("Y-m-d H:i:s"));
            return redirect::route('admin.city')->withError("Internal server error.");
        }
    }
    
    
    
    
    
    function faq(){
        $pageHeading="FAQ List";
        $page="faqs";

        $allData=Faq::whereNull('deleted_at')->orderBy("created_at",'desc')->get();

        if($allData!=null){

            $allDataCount=count($allData);
        }
        else{
            $allDataCount=0; 
        }
       
        return view("faq", compact('pageHeading','page','allData','allDataCount'));
    }
    
    function newFAQ(Request $request){
        $pageHeading="FAQ List";
        $page="newfaq";
        $validate = $request->validate([
                'question'=>"required",
                'ans'=>"required",
                'status' => 'required',
        ]);
        try{
        $question=trim(strtolower($request->question));
        $ans=trim(strtolower($request->ans));
        $status=trim(strtolower($request->status));

        $allData=Faq::whereNull('deleted_at')->where("question",$question)->first();

        if($allData!=null){

            return redirect()->back()->withError('Topic Already Exist.');
        }
        
        $timestamp=date("Y-m-d H:i:s");
        $newRow=new Faq();
        $newRow->question=$question;
        $newRow->ans=$ans;
        $newRow->status=$status;
        $newRow->created_at=$timestamp;
        $newRow->updated_at=$timestamp;
        $newRow->save();
       
        return redirect::route('admin.faq')->withSuccess("Faq saved successfully !");
        }
        catch (Exception $err) {
            //throw $th;
            Log::error("Exception occurred: ".$err->getMeassage()." on ".date("Y-m-d H:i:s"));
            return redirect::route('admin.faq')->withError("Internal server error.");
        }
    }
    
    function post_edit_faq(Request $request){
        $pageHeading="Topics List";
        $page="topics";
        $validate = $request->validate([
                "id"=>"required",
                'question'=>"required",
                'ans'=>"required",
                'status' => 'required',
        ]);
        try{
        $question=trim(strtolower($request->question));
        $ans=trim(strtolower($request->ans));
        $status=trim(strtolower($request->status));
        
        $timestamp=date("Y-m-d H:i:s");
        $editRow=Faq::find($request->id);
        if($editRow==null){
            return redirect()->back()->withError('Invalid Id');
        }
        $editRow->question=$question;
        $editRow->ans=$ans;
        $editRow->status=$status;
        $editRow->created_at=$timestamp;
        $editRow->updated_at=$timestamp;
        $editRow->save();
       
        return redirect::route('admin.faq')->withSuccess("FAQ saved successfully !");
        }
        catch (Exception $err) {
            //throw $th;
            Log::error("Exception occurred: ".$err->getMeassage()." on ".date("Y-m-d H:i:s"));
            return redirect::route('admin.faq')->withError("Internal server error.");
        }
    }
    
    function users(){
        $pageHeading="Users List";
        $page="users";

        $allData=Users::whereNull('deleted_at')->orderBy("created_at",'desc')->get();

        if($allData!=null){

            $allDataCount=count($allData);
        }
        else{
            $allDataCount=0; 
        }
       
        return view("users", compact('pageHeading','page','allData','allDataCount'));
    }
    
    function financialReports(){
        $pageHeading="Financial Reports";
        $page="financialreports";

        $allData=PaymentModel::whereNull('deleted_at')->orderBy("created_at",'desc')->get();

        if($allData!=null){

            $allDataCount=count($allData);
        }
        else{
            $allDataCount=0; 
        }
       
        return view("financialreports", compact('pageHeading','page','allData','allDataCount'));
    }
    
    function block_user(Request $request){
        try{
            $pageHeading="Users List";
            $page="users";
            $validate = $request->validate([
                    "id"=>"required",
                    'status' => 'required',
            ]);
            $editRow=Users::find($request->id);
    
            if($editRow==null){
                return redirect()->back()->withError('Invalid Id !');
            }
            
            $status=$request->status==0?1:0;
            $editRow->status=$status;
            $editRow->save();
           
             return redirect::route('admin.users')->withSuccess("User Status Updated Successfully !");
        }
        catch (Exception $err) {
            //throw $th;
            Log::error("Exception occurred: ".$err->getMeassage()." on ".date("Y-m-d H:i:s"));
            return redirect::route('admin.users')->withError("Internal server error.");
        }
    }
    
    function view_user($id){
        $pageHeading="User Details";
        $page="users";

        $allData=Users::find($id);

        if($allData==null){

             return redirect()->back()->withError('Invalid Id');
        }
       
        return view("view_user", compact('pageHeading','page','allData'));
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    

    function getAllQuestion(Request $request){
        $categoryId=$request->contest_type;
        $topicId=0;
        
        if(isset($request->topic_id)){
            $topicId=$request->topic_id;
        }
        
        $data=[
            'cat'=>$categoryId,
            'topic'=>$topicId
        ];
        
        // echo "hello";
        // $allQuestions = Questions::select('questions.*','question_topic_mapping.topic_id')->whereNull('questions.deleted_at')->leftJoin('question_topic_mapping', 'question_topic_mapping.question_id','=','questions.id')
        //         ->where(function ($query) use ($data) {
        //             $cat=$data['cat'];
        //             $query->where(function ($subQuery) use ($cat) {
        //                       $subQuery->where('questions.category', 'LIKE', '%"'.$cat.'"%');
        //                   });
        //             if($data['topic']!=0){
        //               $query->where('question_topic_mapping.topic_id', $data['topic']);
        //             }
        // })
        // ->groupBy('questions.id','questions.title_type','questions.title')
        // ->get();
        
        $allQuestions = Questions::select('questions.*')
        ->whereNull('questions.deleted_at')
        ->where(function ($query) use ($data) {
            $cat = $data['cat'];
            $query->where(function ($subQuery) use ($cat) {
                $subQuery->where('questions.category', 'LIKE', '%"'.$cat.'"%');
            });
            if ($data['topic'] != 0) {
                // Subquery to get distinct question IDs for the selected topic
                $subQuery = QuestionTopicMapping::select('question_id')
                    ->where('topic_id', $data['topic']);
    
                // Use the subquery to filter questions
                $query->whereIn('questions.id', $subQuery);
            }
        })
        ->distinct()
        ->get();
        
        echo json_encode(['success'=>true, 'data'=>$allQuestions, "topic_id"=>$topicId ]);
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
        
        $data['contest_image'] = [
            'path' => 'assets/files/contest/images',
            'size' => '400x300',
        ];
        
        $data['topic'] = [
            'path' => 'assets/files/topic',
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
    
    public  function uploadContestImageFile($image, $old = null)
    {
        $path = $this->imagePath()['contest_image']['path'];
        $size =$this->imagePath()['contest_image']['size'];
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
    
    public  function uploadTopicFile($image, $old = null)
    {
        $path = $this->imagePath()['topic']['path'];
        $size =$this->imagePath()['topic']['size'];
        $thumbnail = $this->uploadImage($image, $path, $size);

        return $thumbnail;
    }

    function makeDirectory($path)
    {
        if (file_exists($path)) return true;
        return mkdir($path, 0755, true);
    }
}
