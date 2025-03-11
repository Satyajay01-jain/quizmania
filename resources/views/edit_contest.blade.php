@extends('layouts.admin_layout')
@push('css')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
@endpush
@section('content')
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
                <form class="" method="post" action="{{ route('post_edit_contest') }}" enctype="multipart/form-data"
                    onsubmit="return submitForm()">
                    @csrf
                    <input type="hidden" name="id" value="{{$contest->id}}" readonly required>
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
                                        <select class="form-select @error('contest_type') is-invalid @enderror" name="contest_type"
                                            onchange="setTitle(this)" disabled>
                                            <option <?=$contest->contest_id==1?"selected":'';?> value="1">Live Contest</option>
                                            <option <?=$contest->contest_id==2?"selected":'';?> value="2">Time Limit Contest</option>
                                            <option <?=$contest->contest_id==3?"selected":'';?> value="3">Any Time Contest</option>
                                        </select>
                                        <input type="hidden" name="contest_type" value="<?=$contest->contest_id?>">
        
                                        @error('contest_type')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="mb-3">
                                        <label for="contest_name" class="form-label">Contest Name <span
                                                class="text-danger">*</span></label>
                                        
                                        <input type="text" class="form-control" name="contest_name" required value="<?=$contest->name;?>">
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
                                        <input type="file" class="form-control" name="contest_image" accept=".png,.jpg,.jpeg" >
                                       
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
                                        <input type="date" class="form-control start_time @error('start_date') is-invalid @enderror" name="start_date"
                                            value="<?=date('Y-m-d',strtotime($contest->start_time));?>" required>


                                        @error('start_date')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="mb-3">
                                        <label for="start_time" class="form-label">Start Time <span
                                                class="text-danger">*</span></label>
                                        <input type="time" class="form-control @error('start_time') is-invalid @enderror" name="start_time"
                                            value="<?=date('H:i:s',strtotime($contest->start_time));?>" required>


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
                                        <input type="date" class="form-control @error('end_date') is-invalid @enderror " name="end_date" required id="end_date" value="<?=date('Y-m-d',strtotime($contest->end_date_time));?>" required>
                                        @error('end_date')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="mb-3">
                                        <label for="end_time" class="form-label">End Time <span
                                                    class="text-danger">*</span></label>
                                        <input type="time" class="form-control @error('end_time') is-invalid @enderror " name="end_time"  required id="end_time" value="<?=date('H:i:s',strtotime($contest->end_date_time));?>" required>
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
                                            name="entry_fees" value="<?=$contest->entry_fees;?>" required id="entry_fees">


                                        @error('entry_fees')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="mb-3">
                                        <label for="per_question_mark" class="form-label">Per Question Mark <span
                                                class="text-danger">*</span></label>
                                        <input type="number" class="form-control @error('per_question_mark') is-invalid @enderror" name="per_question_mark"
                                            value="<?=$contest->per_question_mark;?>" required id="per_question_mark">


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
                                            class="form-control @error('neg_mark') is-invalid @enderror" name="neg_mark" value="<?=$contest->neg_mark;?>" required
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

                                        <select class="form-select @error('status') is-invalid @enderror" name="status" id="status">
                                            <option <?=$contest->status==1?"selected":'';?> value="1">Active</option>
                                            <option <?=$contest->status==0?"selected":'';?> value="0">Inactive</option>
                                        </select>

                                        @error('status')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description </label>
                                <textarea class="form-control @error('description') is-invalid @enderror" name="description" id="description" rows="2"
                                    placeholder="Enter description and rules for contest..."><?=$contest->description;?></textarea>
                                @error('description')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            @if($contest_type==3)
                                <div class="mb-3">
                                    <label for="slot_count" class="form-label">One Slot Count <span
                                            class="text-danger">*</span></label>
                                    <input type="number" readonly min="2" class="form-control  @error('slot_count') is-invalid @enderror" name="slot_count"
                                        placeholder="No of users participating together" required id="slot_count" value="{{ $contest->slot_count }}">
                                    @error('slot_count')
                                        <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endif
                            <div class="row">
                                <div class="col-12 col-sm-6">
                                    @if($contest_type==1 || $contest_type==3)
                                        <div class="mb-3">
                                            <label for="duration" class="form-label">Duration (In seconds for each question) <span
                                                    class="text-danger">*</span></label>
                                            <input type="number" min="1" class="form-control @error('duration') is-invalid @enderror" name="duration"
                                                placeholder="Duration of live contest" required id="duration" value="<?=$contest->duration;?>">
                                            @error('duration')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        @else
                                        <div class="mb-3">
                                            <label for="per_question_time" class="form-label">Time per question (In seconds)<span
                                                    class="text-danger">*</span></label>
                                            <input type="number" min="1" class="form-control @error('per_question_time') is-invalid @enderror" name="per_question_time"
                                                placeholder="Time per question" required id="per_question_time" value="<?=$contest->per_question_time;?>">
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
                                        <input type="number" min="0" class="form-control @error('deadline') is-invalid @enderror" name="deadline"
                                                placeholder="Enter minutes before start time" required id="deadline" value="0" required value="<?=$contest->registration_deadline;?>">
                                        @error('deadline')
                                                <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="mb-3">
                                        <label for="host" class="form-label">Select Host for contest <span
                                                    class="text-danger">*</span></label>
                                        <select class="users-select form-select @error('host') is-invalid @enderror" aria-label="user select box" name="host" style="width:100%;">
                                          <option value="" >-Select-</option>
                                          @foreach($users as $user)
                                          <option <?=$contest->host_user_id ==$user->id?'selected':'';?> value="{{$user->id}}">{{$user->name}} - {{$user->phone}}</option>
                                          @endforeach
                                        </select>
                                        
                                        @error('host')
                                                <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                @endif
                                
                                 <div class="col-12 col-sm-6">
                                    <div class="mb-3">
                                        <label for="winner_count" class="form-label">Select winners count <span
                                                    class="text-danger">*</span></label>
                                        <select class="form-select @error('winner_count') is-invalid @enderror" aria-label="user select box" name="winner_count" id="winner_count" onchange="setPercentDistribution()">
                                            <option <?=$contest->winner_count ==1?'selected':'';?> value="1">1</option>
                                            <option <?=$contest->winner_count ==2?'selected':'';?> value="2">2</option>
                                            <option <?=$contest->winner_count ==3?'selected':'';?> value="3">3</option>
                                            <option <?=$contest->winner_count ==5?'selected':'';?> value="5">5</option>
                                            <option <?=$contest->winner_count ==10?'selected':'';?> value="10">10</option>
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
                                        <input type="number" min="0" class="form-control @error('distribution_percent') is-invalid @enderror" name="distribution_percent"
                                                placeholder="Enter % for distribution" step="0.001" max="100" required id="distribution_percent" value="<?=$contest->distribution_percent;?>" required oninput="setPercentDistribution()">
                                    
                                    @error('distribution_percent')
                                                <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label  class="form-label">Distribute % to winners (first to last)<span
                                                    class="text-danger">*</span></label>
                                        <div class="container-fluid p-0">
                                            <div class="row" id="winnerDiv">
                                                <?php 
                                                    $winner_per_distribution=json_decode($contest->winner_percent_distribution);
                                                ?>
                                                @foreach($winner_per_distribution as $value)
                                                <div class="col-12 col-sm-4">
                                                    <input type="number" min="0" class="form-control rounded-pill mb-2 shrink-0" name="percent_distribution[]"
                                                    placeholder="Winner no 1" required value="{{$value}}" required>
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        
                                        @error('percent_distribution')
                                                <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
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
                        <div class="table-responsive p-3" style="max-height:500px;">
                            <table class="table table-hover table-nowrap" id="questionsTable">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Action</th>
                                        <th scope="col">Title</th>
                                        <th scope="col">Category</th>

                                    </tr>
                                </thead>
                                <tbody id="questionTbody">

                                    @foreach ($contestQuestions as $question)
                                     @php 
                                            $categories=json_decode($question->category);

                                           if(is_bool(array_search($contest_type, $categories))){
                                            continue;
                                           }
                                        @endphp
                                        <tr>
                                            <td class="">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="questions[]"
                                                        value="{{ $question->id }}" checked>
                                                    <label class="form-check-label">
                                                        #{{ $question->id }}
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <a class="text-heading font-semibold" href="#">

                                                    <?php
                                                    if ($question->title_type == "text") {
                                                        echo $question->title;
                                                    } 
                                                    else if($question->title_type == "image") {
                                                        ?>
                                                    <img src="{{ asset('assets/files/title/' . $question->title) }}"
                                                        style="max-width:120px;" alt="image">
                                                    <?php
                                                    }
                                                    elseif ($question->title_type == "youtubevideolink") {
                                                        $videoId=getYTVideoId($question->title);
                                                    ?>
                                                    <iframe style="max-width:300px;" src="https://www.youtube.com/embed/<?=$videoId?>" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                                                    <?php
                                                    }
                                                    else{
                                                        ?>
                                                    <video width="200" controls>
                                                        <source
                                                            src="{{ asset('assets/files/title/' . $question->title) }}"
                                                            type="video/mp4">
                                                        <source src="movie.ogg" type="video/ogg">
                                                        Your browser does not support the video tag.
                                                    </video>
                                                    <?php
                                                    }
                                                ?>
                                                </a>
                                            </td>
                                            <td>
                                                @php
                                                    $categories = json_decode($question->category);
                                                    foreach ($categories as $value) {
                                                        if (isset($categoriesMap[$value])) {
                                                            echo '<span class="rounded-pill px-2 py-1 border border-1 me-1">' . $categoriesMap[$value] . '</span>';
                                                        } else {
                                                            echo '<span class="rounded-pill px-2 py-1 border border-1 me-1">Others</span>';
                                                        }
                                                    }
                                                @endphp
                                            </td>
                                        </tr>
                                    @endforeach

                                    @foreach ($allQuestions as $question)
                                    
                                        @if(isset($contestQuestionIdMap[$question->id]))
                                        @php continue; @endphp
                                        @endif
                                        @php
                                        $categories=json_decode($question->category);

                                           if(is_bool(array_search($contest_type, $categories))){
                                            continue;
                                           }
                                        @endphp
                                        <tr>
                                            <td class="">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="questions[]"
                                                        value="{{ $question->id }}">
                                                    <label class="form-check-label">
                                                        #{{ $question->id }}
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <a class="text-heading font-semibold" href="#">

                                                    <?php
                                                    if ($question->title_type == "text") {
                                                        echo $question->title;
                                                    } 
                                                    else if($question->title_type == "image") {
                                                        ?>
                                                    <img src="{{ asset('assets/files/title/' . $question->title) }}"
                                                        style="max-width:120px;" alt="image">
                                                    <?php
                                                    }
                                                    elseif ($question->title_type == "youtubevideolink") {
                                                        $videoId=getYTVideoId($question->title);
                                                    ?>
                                                    <iframe style="max-width:300px;" src="https://www.youtube.com/embed/<?=$videoId?>" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                                                    <?php
                                                    }
                                                    else{
                                                        ?>
                                                    <video width="200" controls>
                                                        <source
                                                            src="{{ asset('assets/files/title/' . $question->title) }}"
                                                            type="video/mp4">
                                                        <source src="movie.ogg" type="video/ogg">
                                                        Your browser does not support the video tag.
                                                    </video>
                                                    <?php
                                                    }
                                                ?>
                                                </a>
                                            </td>
                                            <td>
                                                @php
                                                    $categories = json_decode($question->category);
                                                    foreach ($categories as $key => $value) {
                                                        if (isset($categoriesMap[$value])) {
                                                            echo '<span class="rounded-pill px-2 py-1 border border-1 me-1">' . $categoriesMap[$value] . '</span>';
                                                        } else {
                                                            echo '<span class="rounded-pill px-2 py-1 border border-1 me-1">Others</span>';
                                                        }
                                                    }
                                                @endphp
                                            </td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>
                        </div>

                    </div>

                    <br>
                    <button type="submit" class="btn btn-primary" id="submit_btn">Submit</button>
                </form>
            </div>
        </main>
    </div>
@endsection

@push('js')
    <script>
        let allQuestion = {};

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
                    pTag.innerText = "Option " + (i+1);
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
                    pTag.innerText = "Option " + (i+1);
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
                    pTag.innerText = "Option " + (i+1);
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
                    pTag.innerText = "Option " + (i+1);
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

        function fetchAllQuestion() {
            $.ajax({
                url: "{{ route('get.questions') }}",
                method: "GET",
                success: function(data) {
                    let deodedData = JSON.parse(data);

                    deodedData.forEach(element => {
                        allQuestion[element.id] = {
                            id: element.id,
                            titleType: element.title_type,
                            title: element.title,
                            category: JSON.parse(element.category),
                            selcted: 0,
                        }
                        if (JSON.parse(element.category).find((ele) => {
                                return ele == "1";
                            })) {

                        }
                    });


                    console.log(allQuestion);
                },
                error: function(error) {
                    console.log(error);
                }
            });

        }
        // fetchAllQuestion();
    </script>
    <script src="//cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
    <script>
        let table = new DataTable('#questionsTable');
    </script>
@endpush
