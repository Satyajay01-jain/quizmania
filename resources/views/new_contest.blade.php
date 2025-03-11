@extends('layouts.admin_layout')
    <?php
 if (!function_exists('getYTVideoId')) {
    function getYTVideoId($link){
    $video_id = explode("?v=", $link); // For videos like http://www.youtube.com/watch?v=...
    
    if (!isset($video_id[1]) && empty($video_id[1]))
        $video_id = explode("/v/", $link); // For videos like http://www.youtube.com/watch/v/..
    
    if (!isset($video_id[1]) && empty($video_id[1]))
        $video_id = explode(".be/", $link); // for https://youtu.be/FnoniHwvSA8
        
    if (!isset($video_id[1]) && empty($video_id[1]))
        $video_id = explode("shorts/", $link); // for https://www.youtube.com/shorts/dC_A6kjLfbc	
        
   
    if (!isset($video_id[1]) && empty($video_id[1]))
        $video_id = explode("live/", $link); //https://www.youtube.com/live/87dpg9wlAkk?feature=share
        
    $video_id = explode("&", $video_id[1]); // Deleting any other params
     $video_id = explode("?", $video_id[0]); // Deleting any other params
    $video_id = $video_id[0];
    
    return $video_id;
}
    }


?>
 @if ($errors->any())
     @foreach ($errors->all() as $error)
     <div class="alert alert-danger alert-dismissible fade show" role="alert">
         {{$error}}
         <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
     @endforeach
 @endif
 
@section('content')
    @php
        // default values
        
        $option_type = 'text';
    @endphp
    <div class="h-screen flex-grow-1 overflow-y-lg-auto">
        <!-- Header -->
        <header class="bg-surface-primary border-bottom pt-6">
            <div class="container-fluid">
                <div class="mb-npx">
                    <div class="row align-items-center mb-4">
                        <div class="col-sm-6 col-12 mb-4 mb-sm-0">
                            <!-- Title -->
                            <h1 class="h2 mb-0 ls-tight">{{ $pageHeading }}</h1>
                        </div>
                        <!-- Actions -->
                        <div class="col-sm-6 col-12 text-sm-end">
                            <div class="mx-n1">
                                <button onclick="history.back()"
                                    class="btn d-inline-flex btn-sm btn-neutral border-base mx-1">
                                    <span class=" pe-2">
                                        <i class="bi bi-arrow-bar-left"></i>
                                    </span>
                                    <span>Go Back</span>
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </header>
        <!-- Main -->
        <main class="py-6 bg-surface-secondary">
            <div class="px-5 my-2">

                @if (Session::has('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ Session::get('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @elseif (Session::has('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ Session::get('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @elseif (Session::has('warning'))
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        {{ Session::get('warning') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
            </div>
            <div class="container-fluid">
                <form class="" method="post" action="{{ route('post_new_contest')}}" onsubmit="return checkFormData()"
                    onsubmit="return submitForm()" enctype="multipart/form-data" >
                    @csrf
                    <div class="card shadow border-0 mb-7">
                        <div class="card-header">
                            <h5 class="mb-0">Enter Details</h5>
                        </div>
                        <div class="py-2 px-4">


                            <div class="row">
                                <div class="col-12 col-sm-6">
                                    <div class="mb-3">
                                        <label for="contest_type" class="form-label">Contest Type <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control"
                                                value="{{$contest_type_text}}" readonly required  id="contest_type" name="contest_type">
                                        <!--<input type="text" class="form-control" name="contest_type" id="contest_type"-->
                                        <!--value="{{$contest_type}}"  readonly required hidden>-->
                                        @error('contest_type')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-12 col-sm-6">
                                    <div class="mb-3">
                                        <label for="contest_name" class="form-label">Contest Name <span
                                                class="text-danger">*</span></label>
                                        
                                        <input type="text" class="form-control @error('contest_name') is-invalid @enderror" name="contest_name" value="{{ old('contest_name') }}" id="contest_name" required >
                                        @error('contest_name')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="contest_type" class="form-label">Contest Image <span
                                                class="text-danger">*</span></label>
                                        <input type="file" class="form-control" name="contest_image" id="contest_type" accept=".png,.jpg,.jpeg" required>
                                       
                                        @error('contest_type')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                
                            </div>

                            <div class="row">
                                <div class="col-12 col-sm-6">
                                    <div class="mb-3">
                                        <label for="start_date" class="form-label">Start Date <span
                                                class="text-danger">*</span></label>
                                        <input type="date" class="form-control @error('start_date') is-invalid @enderror" name="start_date" id="start_date"
                                            value="{{ old('start_date') }}" required>


                                        @error('start_date')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="mb-3">
                                        <label for="start_time" class="form-label">Start Time <span
                                                class="text-danger">*</span></label>
                                        <input type="time" class="form-control @error('start_time') is-invalid @enderror" name="start_time" id="start_time"
                                            value="{{ old('start_time') }}" required>


                                        @error('start_time')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-12 col-sm-6">
                                    <div class="mb-3">
                                        <label for="end_date" class="form-label">End Date <span
                                                    class="text-danger">*</span></label>
                                        <input type="date" class="form-control @error('end_date') is-invalid @enderror " name="end_date" required id="end_date" value="{{ old('end_date') }}" required>
                                        @error('end_date')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="mb-3">
                                        <label for="end_time" class="form-label">End Time <span
                                                    class="text-danger">*</span></label>
                                        <input type="time" class="form-control @error('end_time') is-invalid @enderror " name="end_time"  required id="end_time" value="{{ old('end_time') }}" required>
                                        @error('end_time')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12 col-sm-6">
                                    <div class="mb-3">
                                        <label for="entry_fees" class="form-label">Entry Fees <span
                                                class="text-danger">*</span></label>
                                        <input type="number" step="0.01" min="0" class="form-control @error('entry_fees') is-invalid @enderror"
                                            name="entry_fees" value="{{ old('entry_fees') }}" required id="entry_fees">


                                        @error('entry_fees')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="mb-3">
                                        <label for="per_question_mark" class="form-label">Per Question Mark <span
                                                class="text-danger">*</span></label>
                                        <input type="number" class="form-control  @error('per_question_mark') is-invalid @enderror" name="per_question_mark"
                                            value="{{ old('per_question_mark') }}" required id="per_question_mark">


                                        @error('per_question_mark')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12 col-sm-6">
                                    <div class="mb-3">
                                        <label for="neg_mark" class="form-label">Negative Marks <span
                                                class="text-danger">*</span></label>
                                        <input type="number" step="0.01" min="0" value="0"
                                            class="form-control  @error('neg_mark') is-invalid @enderror" name="neg_mark" value="{{ old('neg_mark') }}" required
                                            id="neg_mark">


                                        @error('neg_mark')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="mb-3">
                                        <label for="status" class="form-label">Status <span
                                                class="text-danger">*</span></label>

                                        <select class="form-select  @error('status') is-invalid @enderror" name="status" id="status">
                                            <option {{ old('status')==1?'selected':'' }} value="1">Active</option>
                                            <option {{ old('status')==0?'selected':'' }} value="0">Inactive</option>
                                        </select>

                                        @error('status')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description </label>
                                <textarea class="form-control  @error('description') is-invalid @enderror" name="description" id="description" rows="2"
                                    placeholder="Enter description and rules for contest...">{{ old('description') }}</textarea>
                                @error('description')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="duration" class="form-label">Duration(In minutes) <span
                                        class="text-danger">*</span></label>
                                <input type="number" min="1" class="form-control" name="duration"
                                    placeholder="Duration of live contest" required id="duration">
                            </div>
                            
                            <div class="row">
                                <div class="col-12 col-sm-6">
                                @if($contest_type==1 || $contest_type==3)
                                        <div class="mb-3">
                                            <label for="duration" class="form-label">Duration (In seconds for each question)<span
                                                    class="text-danger">*</span></label>
                                            <input type="number" min="1" class="form-control  @error('duration') is-invalid @enderror" name="duration"
                                                placeholder="Duration of contest" step="1" required id="duration" value="{{ old('duration') }}">
                                            @error('duration')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        @else
                                        <div class="mb-3">
                                            <label for="per_question_time" class="form-label">Time per question (In seconds)<span
                                                    class="text-danger">*</span></label>
                                            <input type="number" min="1" class="form-control  @error('per_question_time') is-invalid @enderror" name="per_question_time"
                                                placeholder="Time per question" required id="per_question_time" value="{{ old('per_question_time') }}">
                                            @error('per_question_time')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    @endif
                                </div>
                                @if($contest_type==1)
                                <div class="col-12 col-sm-6">
                                    <div class="mb-3">
                                        <label for="deadline" class="form-label">Deadline (Enter minutes before start time)<span
                                                    class="text-danger">*</span></label>
                                        <input type="number" min="0" class="form-control  @error('deadline') is-invalid @enderror" name="deadline"
                                                placeholder="Enter minutes before start time" required id="deadline" value="0" required value="{{ old('deadline') }}">
                                        @error('deadline')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="mb-3">
                                        <label for="host" class="form-label">Select Host for contest <span
                                                    class="text-danger">*</span></label>
                                        <select class="users-select form-select @error('host') is-invalid @enderror" aria-label="user select box" name="host" id="host" style="width:100%;">
                                          <option value="" >-Select-</option>
                                          @foreach($users as $user)
                                          <option {{ old('host')==$user->id?'selected':'' }} value="{{$user->id}}">{{$user->name}} - {{$user->phone}}</option>
                                          @endforeach
                                        </select>
                                        @error('host')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                @endif

                                @if($contest_type==2)
                                <div class="col-12 col-sm-6">
                                    <div class="mb-3">
                                        <label for="deadline" class="form-label">Deadline (Enter minutes before start time)<span
                                                    class="text-danger">*</span></label>
                                        <input type="number" min="0" class="form-control  @error('deadline') is-invalid @enderror" name="deadline"
                                                placeholder="Enter minutes before start time" required id="deadline" value="0" required value="{{ old('deadline') }}">
                                        @error('deadline')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="mb-3">
                                        <label for="host" class="form-label">Select Host for contest <span
                                                    class="text-danger">*</span></label>
                                        <select class="users-select form-select @error('host') is-invalid @enderror" aria-label="user select box" name="host" id="host" style="width:100%;">
                                          <option value="" >-Select-</option>
                                          @foreach($users as $user)
                                          <option {{ old('host')==$user->id?'selected':'' }} value="{{$user->id}}">{{$user->name}} - {{$user->phone}}</option>
                                          @endforeach
                                        </select>
                                        @error('host')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                @endif
                                
                                @if($contest_type==3)
                                        <div class="mb-3">
                                            <label for="slot_count" class="form-label">One Slot Count <span
                                                    class="text-danger">*</span></label>
                                            <input type="number" min="2" class="form-control  @error('slot_count') is-invalid @enderror" name="slot_count"
                                                placeholder="No of users participating together" required id="slot_count" value="{{ old('slot_count') }}">
                                            @error('slot_count')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>
                                @endif
                                 <div class="col-12 col-sm-6">
                                    <div class="mb-3">
                                        <label for="no_of_contestant" class="form-label">No. of contestant <span
                                                    class="text-danger">*</span></label>
                                        <select class="form-select  @error('no_of_contestant') is-invalid @enderror" aria-label="user select box" name="no_of_contestant" id="no_of_contestant" onchange="setPercentDistribution()">
                                            <option {{ old('no_of_contestant')==1?'selected':'' }} value="1">1</option>
                                            <option {{ old('no_of_contestant')==2?'selected':'' }} value="2">2</option>
                                            <option {{ old('no_of_contestant')==3?'selected':'' }} value="3">3</option>
                                            <option {{ old('no_of_contestant')==5?'selected':'' }} value="5">5</option>
                                            <option {{ old('no_of_contestant')==10?'selected':'' }} value="10">10</option>
                                        </select>
                                        
                                        @error('no_of_contestant')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                 <div class="col-12 col-sm-6">
                                    <div class="mb-3">
                                        <label for="winner_count" class="form-label">Select winners count <span
                                                    class="text-danger">*</span></label>
                                        <select class="form-select  @error('winner_count') is-invalid @enderror" aria-label="user select box" name="winner_count" id="winner_count"  onchange="setPercentDistribution()"> // id commented 
                                            <option {{ old('winner_count')==1?'selected':'' }} value="1">1</option>
                                            <option {{ old('winner_count')==2?'selected':'' }} value="2">2</option>
                                            <option {{ old('winner_count')==3?'selected':'' }} value="3">3</option>
                                            <option {{ old('winner_count')==5?'selected':'' }} value="5">5</option>
                                            <option {{ old('winner_count')==10?'selected':'' }} value="10">10</option>
                                        </select>
                                        
                                        @error('winner_count')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-12 col-sm-6">
                                    <div class="mb-3">
                                        <label for="distribution_percent" class="form-label">Max % for distribution <span
                                                    class="text-danger">*</span></label>
                                        <input type="number" min="0" class="form-control  @error('distribution_percent') is-invalid @enderror" name="distribution_percent"
                                                placeholder="Enter % for distribution" max="100" required id="distribution_percent" value="50" required oninput="setPercentDistribution()" value="{{ old('distribution_percent') }}">
                                        @error('distribution_percent')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="percent_distribution[]"  class="form-label">Distribute % to winners (first to last)<span
                                                    class="text-danger">*</span></label>
                                        <div class="container-fluid p-0">
                                            <div class="row" id="winnerDiv">
                                                <div class="col-12 col-sm-4">
                                                    <input type="number" min="0" step="0.001" class="form-control rounded-pill mb-2 shrink-0" name="percent_distribution[]" id="percent_distribution[]"
                                                    placeholder="Winner no 1" required value="50" required>
                                                </div>
                                            </div>
                                        </div>
                                        @error('percent_distribution')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                
                                
                                <div class="col-12 col-sm-6 col-md-4">
                                    
                                    <div class="mb-3">
                                        <label for="topics" class="form-label">Select topic wise question<span
                                                    class="text-danger">*</span></label>
                                        <select class="topics-select form-select" aria-label="topics select box" name="topics" id="topics" style="width:100%;">
                                          <option value="allquestion" >-Select-</option>
                                          @foreach($allTopics as $topic)
                                          <option  value="{{$topic->id}}">{{$topic->name}}</option>
                                          @endforeach
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-12 col-sm-6 col-md-4">
                                    
                                    <div class="mb-3">
                                        <label for="selection_type" class="form-label">Manual / Auto<span
                                                    class="text-danger">*</span></label>
                                        <select class=" form-select" id="selection_type" name="selection_type" style="width:100%;" onchange="changeSelectionType(this)">
                                          <option value="manual" >Manual</option>
                                          <option value="automatic" >Automatic</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6 col-sm-6 col-md-2 d-none" id="topic_question_count_div">
                                    
                                    <div class="mb-3">
                                        <label for="topic_question_count" class="form-label">Question Count<span
                                                    class="text-danger">*</span></label>
                                        <input type="number" class="form-control" name="topic_question_count" id="topic_question_count" placeholder="Topic question count">
                                    </div>
                                </div>
                                
                                <div class="col-6 col-sm-6 col-md-2 d-flex align-items-center" >
                                    
                                    
                                        <button type="button" class="btn btn-primary " onclick="addQuestion()" id="add_question_btn">Add</button>
                                </div>
                                
                                
                            </div>
                            


                        </div>
                        <div class="card-footer border-0 py-2">
                            {{-- <span class=" text-danger text-sm">* Allowed file type - .png,.jpg,.jpeg,.mp4 and max size is 5 mb.</span> --}}
                        </div>
                    </div>

                    <div class="card shadow border-0 mb-7">
                        <div class="card-header">
                            <h5 class="mb-0">Select Questions</h5>
                        </div>
                        <div class="table-responsive p-3" style="">
                            <table class="table table-hover table-nowrap" id="questionsTable">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Action</th>
                                        <th scope="col">Title</th>
                                        <th scope="col">Category</th>

                                    </tr>
                                </thead>
                                <tbody id="questionTbody">


                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer">
                           
                            @error('questions')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                            
                            @error('selected_question_ids')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror

                    </div>

                    </div>
                    
                    <div class="card shadow border-0 mb-7 p-3">
                        
                        <h5 class="mb-2">Selected Questions: </h5>
                        <table class="table table-hover table-nowrap" id="selectedQuestionsTable">
                                <thead class="thead-light">
                                    <tr>
                                         
                                        <th scope="col">Question Title</th>
                                        <th scope="col">Action</th>

                                    </tr>
                                </thead>
                                <tbody id="selectedQuestionBody">


                                </tbody>
                        </table>
                        
                    </div>
                    
                    <input type="hidden" name="selected_question_ids" id="selected_question_ids">

                    <br>
                    <button type="submit" class="btn btn-primary" id="submit_btn">Submit</button>
                </form>
            </div>
        </main>
    </div>
@endsection

@push('js')
    <script>
        let allQuestion = [];
        let topicWiseQuestion = [];
        let currentQuestion=[];
        let selectedQuestions = [];
        const categoriesMap = {
            1: "Live Contest",
            2: "Time Limit Contest",
            3: "Any Time Contest",
            4: "Question Bank"
        };
        
        $.ajax({
                url: "{{ route('get.questions', ['contest_type'=> $contest_type]) }}",
                method: "GET",
                success: function(data) {
                    
                    let deodedData = JSON.parse(data);
                    if(deodedData.success==true){
                        let key=0;
                        allQuestion=[];
                        deodedData.data.forEach(element => {
                            allQuestion[key] = {
                                id: element.id,
                                title_type: element.title_type,
                                title: element.title,
                                category: JSON.parse(element.category),
                                selcted: 0,
                            }
                            key++;
                        });
                        var table = $('#questionsTable').DataTable();
                       table.clear().draw();
                        
                        allQuestion.map((ele)=>{
                            appendQuestionToTable(ele);
                        });
                        currentQuestion=allQuestion;
                        return true;
                    }
                    else{
                        return false;
                    }
                },
                error: function(error) {
                    console.log(error);
                }
        });
        
        function checkFormData(){
            let selected_question=JSON.stringify(selectedQuestions);
            document.getElementById('selected_question_ids').value=selected_question;
            
            if(selected_question=="" || selected_question==null){
                alert("Please select questions for contest");
                return false;
            }
            return true;
        }
        
        function setTitle(e) {
            let titleType = e.value;
            let titleDiv = document.getElementById("titleDiv");
            if (titleType == "text") {
                titleDiv.innerHTML = '';
                let textInput = document.createElement('input');
                textInput.name = "title";
                textInput.type = "text";
                textInput.placeholder = "Question title here...";
                textInput.className = "form-control";
                textInput.setAttribute("required", "");
                titleDiv.appendChild(textInput);
                console.log(titleType);
            } else if (titleType == "image") {
                titleDiv.innerHTML = '';
                let textInput = document.createElement('input');
                textInput.name = "title";
                textInput.type = "file";
                textInput.className = "form-control";
                textInput.setAttribute("required", "");
                textInput.setAttribute("accept", ".png,.jpg,.jpeg");
                titleDiv.appendChild(textInput);
                console.log(titleType)
            } else if (titleType == "youtubevideolink") {
                titleDiv.innerHTML = '';
                let textInput = document.createElement('input');
                textInput.name = "title";
                textInput.type = "text";
                textInput.placeholder = "Question Youtube link here...";
                textInput.className = "form-control";
                textInput.setAttribute("required", "");
                titleDiv.appendChild(textInput);
                console.log(titleType)
            } else {
                titleDiv.innerHTML = '';
                let textInput = document.createElement('input');
                textInput.name = "title";
                textInput.type = "file";
                textInput.className = "form-control";
                textInput.setAttribute("required", "");
                textInput.setAttribute("accept", ".mp4");
                titleDiv.appendChild(textInput);
                console.log(titleType)
            }
        }

        function setOptions() {
            let optionType = document.getElementById("option_type").value;
            let optionCount = document.getElementById("option_count").value;
            let optionDiv = document.getElementById("optionsDiv");
            optionDiv.innerHTML = '';
            i = 1;
            while (i <= optionCount) {
                if (optionType == "text") {

                    let parentDiv = document.createElement('div');
                    parentDiv.className = "mb-2";
                    let pTag = document.createElement('p');
                    pTag.innerText = "Option " + i;
                    let textInput = document.createElement('input');
                    textInput.name = "option[]";
                    textInput.type = "text";
                    textInput.placeholder = "Option here...";
                    textInput.className = "form-control";
                    textInput.setAttribute("required", "");
                    parentDiv.appendChild(pTag);
                    parentDiv.appendChild(textInput);
                    optionDiv.appendChild(parentDiv);
                    console.log(optionType);
                } else if (optionType == "image") {

                    let parentDiv = document.createElement('div');
                    parentDiv.className = "mb-2";
                    let pTag = document.createElement('p');
                    pTag.innerText = "Option " + i;
                    let textInput = document.createElement('input');
                    textInput.name = "option[]";
                    textInput.type = "file";
                    textInput.className = "form-control";
                    textInput.setAttribute("required", "");
                    textInput.setAttribute("accept", ".png,.jpg,.jpeg");
                    parentDiv.appendChild(pTag);
                    parentDiv.appendChild(textInput);
                    optionDiv.appendChild(parentDiv);

                    console.log(optionType)
                } else if (optionType == "youtubevideolink") {
                    let parentDiv = document.createElement('div');
                    parentDiv.className = "mb-2";
                    let pTag = document.createElement('p');
                    pTag.innerText = "Option " + i;
                    let textInput = document.createElement('input');
                    textInput.name = "option[]";
                    textInput.type = "text";
                    textInput.placeholder = "Option Youtube link here...";
                    textInput.className = "form-control";
                    textInput.setAttribute("required", "");
                    parentDiv.appendChild(pTag);
                    parentDiv.appendChild(textInput);
                    optionDiv.appendChild(parentDiv);
                } else {
                    let parentDiv = document.createElement('div');
                    parentDiv.className = "mb-2";
                    let pTag = document.createElement('p');
                    pTag.innerText = "Option " + i;
                    let textInput = document.createElement('input');
                    textInput.name = "option[]";
                    textInput.type = "file";
                    textInput.className = "form-control";
                    textInput.setAttribute("required", "");
                    textInput.setAttribute("accept", ".mp4");
                    parentDiv.appendChild(pTag);
                    parentDiv.appendChild(textInput);
                    optionDiv.appendChild(parentDiv);
                    console.log(optionType)
                }
                i++;
            }

        }

        function submitForm() {
            let submit_btn = document.getElementById("submit_btn");
            submit_btn.disabled = true;
            submit_btn.innerText = "Submiting...";
            return true;
        }
        
        function setPercentDistribution(){
            let winner_count = document.getElementById("winner_count").value;
            let distribution_percent = document.getElementById("distribution_percent").value;
            let winnerDiv = document.getElementById("winnerDiv");
            winnerDiv.innerHTML = '';
            i = 1;
            while (i <= winner_count) {
                
                
                    let inDiv = document.createElement('div');
                    inDiv.className = "col-12 col-sm-4";
                    let textInput = document.createElement('input');
                    textInput.name = "percent_distribution[]";
                    textInput.type = "text";
                    textInput.placeholder = "Winner No " + i;
                    textInput.className = "form-control rounded-pill mb-2";
                    textInput.setAttribute("required", "");
                    textInput.setAttribute("step", "0.001");
                    textInput.value = (distribution_percent/winner_count).toFixed(3);
                    
                    inDiv.appendChild(textInput);
                    winnerDiv.appendChild(inDiv);
                
                i++;
            }
        }

        function fetchAllQuestion(automatic=false) {
            
            $.ajax({
                url: "{{ route('get.questions', ['contest_type'=> $contest_type]) }}",
                method: "GET",
                success: function(data) {
                    let deodedData = JSON.parse(data);
                    let key=0;
                    allQuestion=[];
                    if(deodedData.success==true){
                        // document.getElementById("questionTbody").innerHTML="";
                        deodedData.data.forEach(element => {
                            allQuestion[key] = {
                                id: element.id,
                                title_type: element.title_type,
                                title: element.title,
                                category: JSON.parse(element.category),
                                selcted: 0,
                            }
                            
                            key++;
                        });
                        var table = $('#questionsTable').DataTable();
                       // Clear all rows from the DataTable
                        table.clear().draw();
                        allQuestion.map((ele)=>{
                            appendQuestionToTable(ele);
                        })
                        currentQuestion=allQuestion;
                        
                        if(automatic){
                            let topic_question_count=document.getElementById('topic_question_count').value;
                            console.log("selecing question 23id: ");
                            const n = topic_question_count>allQuestion.length?allQuestion.length:topic_question_count; // Number of random different numbers
                            const n1 = allQuestion.length-1; // Range from 0 to n1 - 1
                            let  randomNumbers = getRandomDifferentNumbers(n, n1);
                            if(n1==0 ){
                                randomNumbers[0]=0;
                            }
                            randomNumbers.map((ele)=>{
                                selectQuestion2(allQuestion[ele].id);
                            });
                        }
                        return true;
                    }
                    else{
                        return false;
                    }


                },
                error: function(error) {
                    console.log(error);
                }
            });
        }
        
        function fetchTopicWiseQuestion(topic_id,automatic=false) {
           
            $.ajax({
                url: "https://quizmania.jain.software/admin/get/question?contest_type={{$contest_type}}&topic_id="+topic_id,
                method: "GET",
                success: function(data) {
                    
                    let deodedData = JSON.parse(data);
                    let key2=0;
                    topicWiseQuestion=[];
                    
                    if(deodedData.success==true){
                        
                        // document.getElementById("questionTbody").innerHTML="";
                        
                        deodedData.data.forEach(element => {
                            topicWiseQuestion[key2++] = {
                                id: element.id,
                                title_type: element.title_type,
                                title: element.title,
                                category: JSON.parse(element.category),
                                selcted: 0,
                            }
                        });
                        
                        var table = $('#questionsTable').DataTable();
                        table.clear().draw();
                        
                        topicWiseQuestion.map((ele)=>{
                            appendQuestionToTable(ele);
                        });
                        currentQuestion=topicWiseQuestion;
                        
                        if(automatic){
                            let topic_question_count=document.getElementById('topic_question_count').value;
                            console.log("selecing question 2id: ");
                            const n = topic_question_count>topicWiseQuestion.length?topicWiseQuestion.length:topic_question_count; // Number of random different numbers
                            const n1 = topicWiseQuestion.length-1; // Range from 0 to n1 - 1
                            let randomNumbers = getRandomDifferentNumbers(n, n1);
                            if(n1==0 ){
                                randomNumbers[0]=0;
                            }
                            randomNumbers.map((ele)=>{
                                selectQuestion2(topicWiseQuestion[ele].id);
                            });
                        }
                        
                        
                        return true;
                    }
                    else{
                        return false;
                    }
                },
                error: function(error) {
                    console.log(error);
                    return false;
                }
            });
        }
        
        function changeSelectionType(ele){
            let selection_type=ele.value; // the value can be automatic or manual
            if(selection_type=="manual"){
                // show hide topic topic question count button
                document.getElementById('topic_question_count_div').classList.add('d-none');
            }
            else{
                document.getElementById('topic_question_count_div').classList.remove('d-none');
            }
            console.log(ele, selection_type);
        }
        
        async function addQuestion(){
            
            let topicType=document.getElementById('topics').value;
            let selectionType=document.getElementById('selection_type').value;
            
            
            if(selectionType=="manual"){
                if(topicType=="allquestion"){
                   
                   await fetchAllQuestion(); 
                }
                else{
                    await fetchTopicWiseQuestion(topicType);
                }
            }
            else{
                if(topicType=="allquestion"){
                    await fetchAllQuestion(true);
                }
                else{
                    await fetchTopicWiseQuestion(topicType, true);
                }
            }
            
        }
        
        function appendQuestionToTable(questionData) {
            
            var questionTbody = document.getElementById("questionTbody");
        
            // Create a new table row
            var newRow = document.createElement("tr");
        
            // Create the first table data cell for the checkbox
            var checkboxCell = document.createElement("td");
            checkboxCell.className = "";
        
            var checkboxDiv = document.createElement("div");
            checkboxDiv.className = "form-check";
        
            var checkboxInput = document.createElement("input");
            checkboxInput.className = "form-check-input";
            checkboxInput.type = "checkbox";
            checkboxInput.name = "questions[]";
            checkboxInput.value = questionData.id;
            checkboxInput.id = "checkbox"+questionData.id;
            
            if (selectedQuestions.includes(questionData.id)) {
                checkboxInput.setAttribute('checked','')
            }
            
            checkboxInput.setAttribute('onclick', "selectQuestion("+questionData.id+")");
        
            var checkboxLabel = document.createElement("label");
            checkboxLabel.className = "form-check-label";
            checkboxLabel.textContent = "#" + questionData.id;
        
            checkboxDiv.appendChild(checkboxInput);
            checkboxDiv.appendChild(checkboxLabel);
            checkboxCell.appendChild(checkboxDiv);
            
            let col1=checkboxCell.outerHTML;
        
            // Create the second table data cell for the content
            var contentCell = document.createElement("td");
        
            var contentAnchor = document.createElement("a");
            contentAnchor.className = "text-heading font-semibold";
            contentAnchor.href = "#";
        
            if (questionData.title_type == "text") {
                contentAnchor.textContent = questionData.title;
            } else if (questionData.title_type == "image") {
                var image = document.createElement("img");
                image.src = "https://quizmania.jain.software/assets/files/title/" + questionData.title;
                image.style.maxWidth = "120px";
                image.alt = "image";
                contentAnchor.appendChild(image);
            } else if (questionData.title_type == "youtubevideolink") {
                var videoId = getYTVideoId(questionData.title);
                var iframe = document.createElement("iframe");
                iframe.style.maxWidth = "300px";
                iframe.src = "https://www.youtube.com/embed/" + videoId;
                iframe.title = "YouTube video player";
                iframe.frameBorder = "0";
                iframe.allow = "accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share";
                iframe.allowFullscreen = true;
                contentAnchor.appendChild(iframe);
            } else {
                var video = document.createElement("video");
                video.width = "200";
                video.controls = true;
                
                var sourceMp4 = document.createElement("source");
                sourceMp4.src = "https://quizmania.jain.software/assets/files/title/" + questionData.title;
                sourceMp4.type = "video/mp4";
        
                var sourceOgg = document.createElement("source");
                sourceOgg.src = "movie.ogg";
                sourceOgg.type = "video/ogg";
        
                var videoText = document.createTextNode("Your browser does not support the video tag.");
                
                video.appendChild(sourceMp4);
                // video.appendChild(sourceOgg);
                video.appendChild(videoText);
                contentAnchor.appendChild(video);
            }
        
            contentCell.appendChild(contentAnchor);
            
            
            var col2 = contentCell.outerHTML;
        
            // Create the third table data cell for categories
            var categoriesCell = document.createElement("td");
        
            questionData.category.forEach(function (value) {
                var categorySpan = document.createElement("span");
                categorySpan.className = "rounded-pill px-2 py-1 border border-1 me-1";
        
                if (categoriesMap[value]) {
                    categorySpan.textContent = categoriesMap[value];
                } else {
                    categorySpan.textContent = "Others";
                }
        
                categoriesCell.appendChild(categorySpan);
            });
            
            var col3 = categoriesCell.outerHTML;
            // Add the cells to the row
            newRow.appendChild(checkboxCell);
            newRow.appendChild(contentCell);
            newRow.appendChild(categoriesCell);
            table.row.add([
                col1,
                col2,
                col3
            ]).draw();
           
            // Append the row to the table body
            // questionTbody.appendChild(newRow);
        }
        
        function getYTVideoId(youtubeUrl) {
            // Regular expression to match YouTube video IDs in various URL formats
            var youtubeRegex = /^(?:https?:\/\/)?(?:www\.)?(?:youtube\.com|youtu\.be)\/(?:watch\?v=|embed\/|v\/|youtu\.be\/|\/)([^\?&"'>]+)/;
        
            var match = youtubeUrl.match(youtubeRegex);
            if (match && match[1]) {
                return match[1];
            } else {
                return null; // Invalid or unrecognized YouTube URL
            }
        }
        
        function selectQuestion(qId){
             
            let ele = document.getElementById('checkbox' + qId);
        
        
            if(ele.checked){
                if (!selectedQuestions.includes(qId)) {
                    selectedQuestions.push(qId);
                    appendSelectedQuestion(qId);
                }
            }
            else{
                selectedQuestions=selectedQuestions.filter(function (id) {
                    return id != qId;
                });
                removeSelectedQuestion(qId);
            }
           
        }
        
        function selectQuestion2(qId){
            console.log("selecing question id: "+qId);
             // Store the current page
            const currentPage = table.page();
        
            let ele = findCheckboxOnAllPages(qId);
        
            // Return to the original page
            table.page(currentPage).draw('page');
            
            
           if(ele){
                ele.checked=true;
            
                if (!selectedQuestions.includes(qId)) {
                    selectedQuestions.push(qId);
                    appendSelectedQuestion(qId);
                }
            
           }
        }
        
        
        function appendSelectedQuestion(qId){
           
            const foundObject = allQuestion.find(item => item.id === qId);
            
            if (foundObject) {
                appendQuestionToTable2(foundObject);
            } else {
                alert("Question details not found !");
                return 0;
            }
        }
        
        function removeSelectedQuestion(qId){
            
            
                selectedQuestions=selectedQuestions.filter(function (id) {
                    return id != qId;
                });
                
                let table2=$("#selectedQuestionsTable").DataTable();
                table2.clear().draw();
                selectedQuestions.map((ele)=>{
                    appendSelectedQuestion(ele);
                });
                let tempInput=document.getElementById('checkbox'+qId);
                tempInput.checked=false;
        }
        
        function appendQuestionToTable2(questionData) {
            
            // Create the second table data cell for the content
            var contentCell = document.createElement("td");
        
            var contentAnchor = document.createElement("a");
            contentAnchor.className = "text-heading font-semibold";
            contentAnchor.href = "#";
        
            if (questionData.title_type == "text") {
                contentAnchor.textContent = questionData.title;
            } else if (questionData.title_type == "image") {
                var image = document.createElement("img");
                image.src = "https://quizmania.jain.software/assets/files/title/" + questionData.title;
                image.style.maxWidth = "120px";
                image.alt = "image";
                contentAnchor.appendChild(image);
            } else if (questionData.title_type == "youtubevideolink") {
                var videoId = getYTVideoId(questionData.title);
                var iframe = document.createElement("iframe");
                iframe.style.maxWidth = "300px";
                iframe.src = "https://www.youtube.com/embed/" + videoId;
                iframe.title = "YouTube video player";
                iframe.frameBorder = "0";
                iframe.allow = "accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share";
                iframe.allowFullscreen = true;
                contentAnchor.appendChild(iframe);
            } else {
                var video = document.createElement("video");
                video.width = "200";
                video.controls = true;
                
                var sourceMp4 = document.createElement("source");
                sourceMp4.src = "https://quizmania.jain.software/assets/files/title/" + questionData.title;
                sourceMp4.type = "video/mp4";
        
                var sourceOgg = document.createElement("source");
                sourceOgg.src = "movie.ogg";
                sourceOgg.type = "video/ogg";
        
                var videoText = document.createTextNode("Your browser does not support the video tag.");
                
                video.appendChild(sourceMp4);
                // video.appendChild(sourceOgg);
                video.appendChild(videoText);
                contentAnchor.appendChild(video);
            }
        
            contentCell.appendChild(contentAnchor);
            
           
            var col2 = contentCell.outerHTML;
            
            var col3 = `<button type="button" class="btn btn-danger btn-sm" onclick="removeSelectedQuestion(${questionData.id})">Remove</button>`;
            
            
            table2.row.add([
                col2,
                col3
            ]).draw();
           
            // Append the row to the table body
            // questionTbody.appendChild(newRow);
        }
        
        function getRandomDifferentNumbers(n, n1) {
            // Create an array of numbers from 0 to n1 - 1
            const numbers = Array.from({ length: n1 }, (_, i) => i);
        
            // Shuffle the array (Fisher-Yates shuffle)
            for (let i = numbers.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [numbers[i], numbers[j]] = [numbers[j], numbers[i]];
            }
        
            // Return the first n elements of the shuffled array
            return numbers.slice(0, n);
        }
        
        function findCheckboxOnAllPages(qId) {
            let ele;
            const totalPages = table.page.info().pages;
        
            for (let page = 0; page < totalPages; page++) {
                table.page(page).draw('page');
                ele = document.getElementById('checkbox' + qId);
        
                if (ele) {
                    // Element found on this page, exit the loop
                    break;
                }
            }
        
            return ele;
        }
    </script>
   
    <script>
        let table = new DataTable('#questionsTable');
        let table2 = new DataTable('#selectedQuestionsTable');
        $(document).ready(function() {
            $select=$('.users-select').select2();
            $select=$('.topics-select').select2();
            // $select.data('select2').$container.addClass('form-control');
        });
    </script>
@endpush
