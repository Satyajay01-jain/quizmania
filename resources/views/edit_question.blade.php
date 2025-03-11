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
@section('content')
    @php
        // default values
        $title_type = 'text';
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
 <form class="" method="post" action="{{ route('post_edit_question') }}"
                            enctype="multipart/form-data" onsubmit="return submitForm()">
                            @csrf
                <div class="card shadow border-0 mb-7">
                    <div class="card-header">
                        <h5 class="mb-0">Enter Details</h5>
                    </div>
                    <div class="py-2 px-4">
                       
                            <input type="hidden" name="qid" value="{{$question->id}}">
                            @error('title_type')
                                            <p class="text-danger">{{ $message }}</p>
                            @enderror
                            <div class="mb-2 text-start">
                                <label for="title_type" class="form-label">Title</label>
                                <br>
                                <div style="max-heigth: 300; overflow:auto;">
                                    <?php
                                        if ($question->title_type == "text") {
                                            echo $question->title;
                                        } 
                                        else if($question->title_type == "image") {
                                            ?>
                                    <img src="{{ asset('assets/files/title/' . $question->title) }}"
                                        style="max-width:200px;" alt="image">
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
                                    <video width="400" controls>
                                        <source src="{{ asset('assets/files/title/' . $question->title) }}"
                                            type="video/mp4">
                                        <source src="movie.ogg" type="video/ogg">
                                        Your browser does not support the video tag.
                                    </video>
                                    <?php
                                        }
                                    ?>
                                </div>
                            </div>
                            <div class="row">

                                <div class="col-12 col-sm-6">
                                    <div class="mb-3">
                                        <label for="title_type" class="form-label">New Title Type <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select @error('title_type') is-invalid @enderror"
                                            name="title_type" onchange="setTitle(this)">
                                            <option <?= $question->title_type == 'text' ? 'selected' : '' ?> value="text">
                                                Text</option>
                                            <option <?= $question->title_type == 'image' ? 'selected' : '' ?>
                                                value="image">Image (.png,.jpg,.jpeg)</option>
                                            <option <?= $question->title_type == 'youtubevideolink' ? 'selected' : '' ?>
                                                value="youtubevideolink">Video (youtube video link)</option>
                                            <option <?= $question->title_type == 'video' ? 'selected' : '' ?>
                                                value="video">Video (.mp4 file)</option>
                                        </select>
                                        {{-- <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="title_type"
                                                id="title_type1" value="text" checked>
                                            <label class="form-check-label" for="title_type1">Text</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="title_type"
                                                id="title_type2" value="image">
                                            <label class="form-check-label" for="title_type2">Image</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                                id="title_type3" value="video">
                                            <label class="form-check-label" for="title_type3">Video</label>
                                        </div> --}}
                                        @error('title_type')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label">New Title <span class="text-danger">*</span></label>
                                        <div class="" id="titleDiv">
                                            <?php 
                                               if($question->title_type=="text"){
                                                ?>
                                            
                                                <div class="row">
                                                    <div class="col-12 col-sm-6">
                                                        <input type="text" class="form-control @error('title') is-invalid @enderror"
                                                name="title" value="<?= $question->title ?>"  placeholder="Question title here...">
                                                    </div>
                                                    <div class="col-12 col-sm-6">
                                                        <input type="text" class="form-control @error('title') is-invalid @enderror" name="hi_title" required value="<?= $question->hi_title ?>" placeholder="Question title in hindi here...">
                                                    </div>
                                                </div>
                                            <?php
                                               }
                                               else if($question->title_type=="image"){
                                                ?>
                                            <input name="title" type="file" class="form-control" 
                                                accept=".png,.jpg,.jpeg">
                                            <?php
                                               }
                                               elseif($question->title_type=="video"){
                                                ?>
                                            <input name="title" type="file" class="form-control" 
                                                accept=".mp4">
                                            <?php
                                               }
                                               else{
                                                ?>
                                            <input name="title" type="text" placeholder="Question Youtube link here..."
                                                class="form-control" >
                                            <?php
                                               }
                                                
                                            ?>

                                        </div>
                                        @error('title')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>




                            <div class="row">
                                <div class="col-12 col-sm-6">
                                    <div class="mb-3">
                                        <label for="option_type" class="form-label">Select option type <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select" name="option_type" id="option_type"
                                            onchange="setOptions()">
                                            <option <?= $question->option_type == 'text' ? 'selected' : '' ?>
                                                value="text">Text</option>
                                            <option <?= $question->option_type == 'image' ? 'selected' : '' ?>
                                                value="image">Image (.png,.jpg,.jpeg)</option>
                                            <option <?= $question->option_type == 'youtubevideolink' ? 'selected' : '' ?>
                                                value="youtubevideolink">Video (youtube video link)</option>
                                            <option <?= $question->option_type == 'video' ? 'selected' : '' ?>
                                                value="video">Video (.mp4 file)</option>
                                        </select>

                                        @error('option_type')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>


                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="mb-3">
                                        <label for="option_count" class="form-label">Option count <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select" name="option_count" id="option_count"
                                            onchange="setOptions()">
                                            <option <?= $question->option_count == '1' ? 'selected' : '' ?> value="1">
                                                1</option>
                                            <option <?= $question->option_count == '2' ? 'selected' : '' ?> value="2">
                                                2</option>
                                            <option <?= $question->option_count == '3' ? 'selected' : '' ?> value="3">
                                                3</option>
                                            <option <?= $question->option_count == '4' ? 'selected' : '' ?> value="4">
                                                4</option>
                                            <option <?= $question->option_count == '5' ? 'selected' : '' ?> value="5">
                                                5</option>
                                        </select>
                                        @error('option_count')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Enter options <span class="text-danger">*</span></label>
                                <div class="mt-1" id="optionsDiv">
                                    <?php 
                                        $options=json_decode($question->options);
                                        foreach ($options as $key => $value) {
                                            
                                            if($question->option_type=="text"){
                                                ?>
                                    <div class="row">
                                        
                                        <div class="col-12 ">
                                            <div class="mb-2">
                                                <p>Option {{$key+1}}</p>
                                               
                                                    <div class="row">
                                        <div class="col-12 col-sm-6">
                                            <input name="option[]" type="text" placeholder="Option here..." class="form-control" required value="<?=$value[0]?>">
                                        </div>
                                        <div class="col-12 col-sm-6">
                                            <input type="text" class="form-control" name="hi_option[]" placeholder="Question options in hindi here..." required value="<?=$value[1]?>">
                                        </div>
                                    </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                                        }
                                                        else if($question->option_type=="image"){
                                                            ?>
                                  
                                        <div class="row">
                                            <div class="col-12 col-sm-4 mb-1" style="max-height: 300px;overflow:auto;">
                                                <img src="{{ asset('assets/files/option/' . $value) }}"
                                        style="max-width:200px;" alt="image">
                                            </div>
                                            <div class="col-12 col-sm-8">
                                                <div class="mb-2">
                                                    <p>Option {{$key+1}}</p>
                                                    <input name="option[]" type="file" class="form-control"  accept=".png,.jpg,.jpeg">
                                                </div>
                                            </div>
                                        </div>
                                    <?php
                                                        }
                                                        elseif($question->option_type=="video"){
                                                            ?>
                                                            <div class="row">
                                                                <div class="col-12 col-sm-4 mb-1" style="max-height: 300px; overflow:auto;">
                                                                    <video width="200" controls>
                                                                        <source src="{{ asset('assets/files/option/' . $value) }}"
                                                                            type="video/mp4">
                                                                        <source src="movie.ogg" type="video/ogg">
                                                                        Your browser does not support the video tag.
                                                                    </video>
                                                                </div>
                                                                <div class="col-12 col-sm-8">
                                                                    <div class="mb-2">
                                                                        <p>Option {{$key+1}}</p>
                                                                        <input name="option[]" type="file" class="form-control"  accept=".mp4">
                                                                    </div>
                                                                </div>
                                                            </div>
                                    
                                    <?php
                                                        }
                                                        else{
                                                            ?>
                                    <div class="row">
                                       <div class="col-12">
                                            <div class="mb-2">
                                               <?php $videoId=getYTVideoId($value);
                                                    ?>
                                                    <iframe style="max-width:300px;" src="https://www.youtube.com/embed/<?=$videoId?>" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                                                    
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="mb-2">
                                                <p>Option {{$key+1}}</p>
                                                <input name="option[]" type="text"
                                                    placeholder="Option here..." class="form-control"  value="<?=$value?>">
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                                        }
                                                        
                                                    }
                                                ?>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Correct Option No. <span
                                            class="text-danger">*</span></label>

                                        @php 
                                            $sno=1;
                                            $correct_option=json_decode($question->correct_option);
                                            
                                        @endphp
                                    <div class="mt-1" id="correctOptionDiv">
                                        <?php 
                                            for ($i=0; $i < $question->option_count; $i++) { 
                                                ?>
                                                <div class="form-check form-check-inline">
                                                    <input type="checkbox" class="form-check-input" id="o<?=$i?>"
                                                        value="<?=$i?>" name="correct_option[]" <?php echo !is_bool(array_search($i,$correct_option))?"checked":''?>>
                                                    <label class="form-check-label" for="correct_option">Option <?=$i+1?></label>
                                                </div>
                                                <?php
                                            }
                                        ?>
                                        
                                    </div>
                                    @error('correct_option')
                                        <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>


                            <div class="mb-3">
                                <label for="question_category" class="form-label">Question Category </label>
                                <br>
                                @php
                                    $categories = json_decode($question->category);
                                    
                                @endphp
                                <div class="form-check form-check-inline">
                                    <input type="checkbox" class="form-check-input" id="question_category"
                                        value="1" name="question_category[]" <?php echo !is_bool(array_search(1,$categories))?"checked":''?>>
                                    <label class="form-check-label" for="question_category">Live Contest</label>
                                </div>

                                <div class="form-check form-check-inline">
                                    <input type="checkbox" class="form-check-input" id="question_category2"
                                        value="2" name="question_category[]" <?php echo !is_bool(array_search(2,$categories))?"checked":''?> >
                                    <label class="form-check-label" for="question_category2">Time Limit Contest</label>
                                </div>

                                <div class="form-check form-check-inline">
                                    <input type="checkbox" class="form-check-input" id="question_category3"
                                        value="3" name="question_category[]" <?php echo !is_bool(array_search(3,$categories))?"checked":''?> >
                                    <label class="form-check-label" for="question_category3">Any Time Contest</label>
                                </div>

                                <div class="form-check form-check-inline">
                                    <input type="checkbox" class="form-check-input" id="question_category4"
                                        value="4" name="question_category[]" <?php echo !is_bool(array_search(4,$categories))?"checked":''?>>
                                    <label class="form-check-label" for="question_category4">Question Bank</label>
                                </div>

                                {{-- if user do not select any one of this the we assume he want all. --}}
                            </div>
                            <div class="mb-3">
                                <label for="status" class="form-label">Status <span
                                        class="text-danger">*</span></label>

                                <select class="form-select" name="status" id="status">
                                    <option <?= $question->status == '1' ? 'selected' : '' ?> value="1">Active</option>
                                    <option <?= $question->status == '0' ? 'selected' : '' ?> value="0">Inactive</option>
                                </select>

                                @error('status')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="status" class="form-label">Ans Description (Write a brief decription of correct ans. It will be displayed after question) <span
                                        class="text-danger">*</span></label>

                                <textarea class="form-control" rows="2" name="ans_desc" placeholder="Write correct answer description" required>{{$question->ans_desc}}</textarea>

                                @error('ans_desc')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>


                            
                        
                    </div>
                    <div class="card-footer border-0 py-5">
                        <span class=" text-danger text-sm">* Allowed file type - .png,.jpg,.jpeg,.mp4 and max size is 5
                            mb.</span>
                    </div>
                </div>
                
                <div class="card shadow border-0 mb-7">
                        <div class="card-header">
                            <h5 class="mb-0">Select Topics</h5>
                        </div>
                        <div class="table-responsive p-3" style="max-height:500px;">
                            <table class="table table-hover table-nowrap text-dark" id="topicsTable">
                                <thead class="thead-light">
                                    <tr>
                                        
                                        <th>Sno</th>
                                        <th scope="col">Topic Name</th>
                                        <th>Select</th>
                                    </tr>
                                </thead>
                                <tbody id="topicBody">


                                    @foreach($allTopics as $key => $topic)
                                        <tr>
                                           
                                            <td>
                                               {{$key+1}}
                                            </td>
                                            <td>
                                               {{$topic->name}}
                                            </td>
                                             <td class="">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="topics[]"
                                                        value="{{ $topic->id }}"  {{in_array( $topic->id, $topicsId)?"checked":""}}>
                                                </div>
                                            </td>
                                            
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer">
                           
                            @error('topics')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror

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
        function setTitle(e) {
            let titleType = e.value;
            let titleDiv = document.getElementById("titleDiv");
            if (titleType == "text") {
                titleDiv.innerHTML = '';
                
                // let textInput = document.createElement('input');
                // textInput.name = "title";
                // textInput.type = "text";
                // textInput.placeholder = "Question title here...";
                // textInput.className = "form-control";
                // textInput.setAttribute("required", "");
                
                let row=document.createElement('div');
                row.className="row";
                
                let col1=document.createElement('div');
                col1.className="col-12 col-sm-6";
                let col2=document.createElement('div');
                col2.className="col-12 col-sm-6";
                
                let textInput=document.createElement('input');
                textInput.name="title";
                textInput.type="text";
                textInput.placeholder="Question title here...";
                textInput.className="form-control";
                textInput.setAttribute("required","");
                
                col1.appendChild(textInput);
                
                let textHiInput=document.createElement('input');
                textHiInput.name="hi_title";
                textHiInput.type="text";
                textHiInput.placeholder="Question title in hindi here...";
                textHiInput.className="form-control";
                textHiInput.setAttribute("required","");
                col2.appendChild(textHiInput);
                
                row.appendChild(col1);
                row.appendChild(col2);
                
                
                titleDiv.appendChild(row);
                
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

        // function setOptions() {
        //     let optionType = document.getElementById("option_type").value;
        //     let optionCount = document.getElementById("option_count").value;
        //     let optionDiv = document.getElementById("optionsDiv");
        //     let correctOptionDiv = document.getElementById("correctOptionDiv");
        //     optionDiv.innerHTML = '';
        //     correctOptionDiv.innerHTML = '';
        //     i = 0;
        //     while (i < optionCount) {

        //         if (optionType == "text") {

        //             let parentDiv = document.createElement('div');
        //             parentDiv.className = "mb-2";
        //             let pTag = document.createElement('p');
        //             pTag.innerText = "Option " + (i+1);
        //             let textInput = document.createElement('input');
        //             textInput.name = "option[]";
        //             textInput.type = "text";
        //             textInput.placeholder = "Option here...";
        //             textInput.className = "form-control";
        //             textInput.setAttribute("required", "");
        //             parentDiv.appendChild(pTag);
        //             parentDiv.appendChild(textInput);
        //             optionDiv.appendChild(parentDiv);
        //             console.log(optionType);
        //         } else if (optionType == "image") {

        //             let parentDiv = document.createElement('div');
        //             parentDiv.className = "mb-2";
        //             let pTag = document.createElement('p');
        //             pTag.innerText = "Option " + (i+1);
        //             let textInput = document.createElement('input');
        //             textInput.name = "option[]";
        //             textInput.type = "file";
        //             textInput.className = "form-control";
        //             textInput.setAttribute("required", "");
        //             textInput.setAttribute("accept", ".png,.jpg,.jpeg");
        //             parentDiv.appendChild(pTag);
        //             parentDiv.appendChild(textInput);
        //             optionDiv.appendChild(parentDiv);

        //             console.log(optionType)
        //         } else if (optionType == "youtubevideolink") {
        //             let parentDiv = document.createElement('div');
        //             parentDiv.className = "mb-2";
        //             let pTag = document.createElement('p');
        //             pTag.innerText = "Option " + (i+1);
        //             let textInput = document.createElement('input');
        //             textInput.name = "option[]";
        //             textInput.type = "text";
        //             textInput.placeholder = "Option Youtube link here...";
        //             textInput.className = "form-control";
        //             textInput.setAttribute("required", "");
        //             parentDiv.appendChild(pTag);
        //             parentDiv.appendChild(textInput);
        //             optionDiv.appendChild(parentDiv);
        //         } else {
        //             let parentDiv = document.createElement('div');
        //             parentDiv.className = "mb-2";
        //             let pTag = document.createElement('p');
        //             pTag.innerText = "Option " + (i+1);
        //             let textInput = document.createElement('input');
        //             textInput.name = "option[]";
        //             textInput.type = "file";
        //             textInput.className = "form-control";
        //             textInput.setAttribute("required", "");
        //             textInput.setAttribute("accept", ".mp4");
        //             parentDiv.appendChild(pTag);
        //             parentDiv.appendChild(textInput);
        //             optionDiv.appendChild(parentDiv);
        //             console.log(optionType)
        //         }


        //         // setting correction tab


        //         let parentDiv2 = document.createElement('div');
        //         parentDiv2.className = "form-check form-check-inline";

        //         let labelTag = document.createElement('label');
        //         labelTag.className = "form-check-label";
        //         labelTag.setAttribute("for", "o" + (i+1));
        //         labelTag.innerText = "Option " + (i+1);

        //         let checkboxInput = document.createElement('input');
        //         checkboxInput.name = "correct_option[]";
        //         checkboxInput.type = "checkbox";
        //         checkboxInput.value = i;
        //         checkboxInput.id = "o" + (i+1);
        //         checkboxInput.className = "form-check-input";
        //         parentDiv2.appendChild(checkboxInput);
        //         parentDiv2.appendChild(labelTag);
        //         correctOptionDiv.appendChild(parentDiv2);
        //         i++;
        //     }

        // }
        
        function setOptions(){
            let optionType=document.getElementById("option_type").value;
            let optionCount=document.getElementById("option_count").value;
            let optionDiv=document.getElementById("optionsDiv");
            let correctOptionDiv=document.getElementById("correctOptionDiv");
            optionDiv.innerHTML = '';
            correctOptionDiv.innerHTML = '';
            i=0;
            while(i<optionCount){

                if(optionType=="text"){
                    
                    
                    let parentDiv=document.createElement('div');
                    parentDiv.className="mb-2";
                    
                    let row=document.createElement('div');
                    row.className="row";
                    
                    let col1=document.createElement('div');
                    col1.className="col-12 col-sm-6";
                    let col2=document.createElement('div');
                    col2.className="col-12 col-sm-6";
                    
                    
                    let pTag=document.createElement('p');
                    pTag.innerText="Option "+(i+1);
                    
                    
                    let textInput=document.createElement('input');
                    textInput.name="option[]";
                    textInput.type="text";
                    textInput.placeholder="Option here...";
                    textInput.className="form-control";
                    textInput.setAttribute("required","");
                    col1.appendChild(textInput);
                    
                    let textHiInput=document.createElement('input');
                    textHiInput.name="hi_option[]";
                    textHiInput.type="text";
                    textHiInput.placeholder="Option in hindi here...";
                    textHiInput.className="form-control";
                    textHiInput.setAttribute("required","");
                    
                    col2.appendChild(textHiInput);
                    
                    row.appendChild(col1);
                    row.appendChild(col2);
                    
                    parentDiv.appendChild(pTag);
                    parentDiv.appendChild(row);
                    
                    
                    optionDiv.appendChild(parentDiv);
                    console.log(optionType);
                }
                else if(optionType=="image"){

                    let parentDiv=document.createElement('div');
                    parentDiv.className="mb-2";
                    let pTag=document.createElement('p');
                    pTag.innerText="Option "+(i+1);
                    let textInput=document.createElement('input');
                    textInput.name="option[]";
                    textInput.type="file";
                    textInput.className="form-control";
                    textInput.setAttribute("required","");
                    textInput.setAttribute("accept",".png,.jpg,.jpeg");
                    parentDiv.appendChild(pTag);
                    parentDiv.appendChild(textInput);
                    optionDiv.appendChild(parentDiv);

                    console.log(optionType)
                }
                else if(optionType=="youtubevideolink"){
                    let parentDiv=document.createElement('div');
                    parentDiv.className="mb-2";
                    let pTag=document.createElement('p');
                    pTag.innerText="Option "+(i+1);
                    let textInput=document.createElement('input');
                    textInput.name="option[]";
                    textInput.type="text";
                    textInput.placeholder="Option Youtube link here...";
                    textInput.className="form-control";
                    textInput.setAttribute("required","");
                    parentDiv.appendChild(pTag);
                    parentDiv.appendChild(textInput);
                    optionDiv.appendChild(parentDiv);
                }
                else{
                    let parentDiv=document.createElement('div');
                    parentDiv.className="mb-2";
                    let pTag=document.createElement('p');
                    pTag.innerText="Option "+(i+1);
                    let textInput=document.createElement('input');
                    textInput.name="option[]";
                    textInput.type="file";
                    textInput.className="form-control";
                    textInput.setAttribute("required","");
                    textInput.setAttribute("accept",".mp4");
                    parentDiv.appendChild(pTag);
                    parentDiv.appendChild(textInput);
                    optionDiv.appendChild(parentDiv);
                    console.log(optionType)
                }


                // setting correction tab
                

                let parentDiv2=document.createElement('div');
                    parentDiv2.className="form-check form-check-inline";

                    let labelTag=document.createElement('label');
                    labelTag.className="form-check-label";
                    labelTag.setAttribute("for","o"+i);
                    labelTag.innerText="Option "+(i+1);

                    let checkboxInput=document.createElement('input');
                    checkboxInput.name="correct_option[]";
                    checkboxInput.type="checkbox";
                    checkboxInput.value=i;
                    checkboxInput.id="o"+i;
                    checkboxInput.className="form-check-input";
                    parentDiv2.appendChild(checkboxInput);
                    parentDiv2.appendChild(labelTag);
                    correctOptionDiv.appendChild(parentDiv2);
                    i++;
            }
            
        }

        function submitForm() {
            let submit_btn = document.getElementById("submit_btn");
            submit_btn.disabled = true;
            submit_btn.innerText = "Submiting...";
            return true;
        }
    </script>
@endpush
