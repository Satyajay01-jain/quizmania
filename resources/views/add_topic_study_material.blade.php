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
                            <h1 class="h2 mb-0 ls-tight">Add {{$title}}</h1>
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

                <div class="card shadow border-0 mb-7">
                    <div class="card-header">
                        <h5 class="mb-0">Add topic related {{$title}}</h5>
                    </div>
                    <div class="py-2 px-4">
                        <form class="" method="post" action="{{ route('admin.update.topic.material') }}"
                            enctype="multipart/form-data" onsubmit="return submitForm()">
                            @csrf
                            <input type="hidden" name="topic_id" value="{{$data->id}}">
                            
                            <input type="hidden" name="type" value="{{$type}}">
                            
                           
                            <div class="mb-3">
                                    <?php 
                                        
                                    foreach ($arr as $key => $value) {
                                            
                                            if($type=="pdf"){
                                                ?>
                                    <div class="row" id="nodeId<?=$key?>">
                                        <div class="col-12  mb-1" style="max-height: 300px; overflow:auto;">
                                                                    <a href="{{ asset('assets/files/topic/' . $value) }}" class="" target="_blank">View PDF</a>
                                                                    <!--<a href="" class="btn btn-danger" style="padding: 4px 10px;margin-left: 10px;font-size: 13px;"><i class="bi bi-trash">Delete PDF</i></a>-->
                                                                </div>
                                        <div class="col-12 col-sm-10 mb-sm-1 mb-sm-0 ">
                                            <div class="mb-2">
                                                <p>PDF {{$key+1}}</p>
                                                <input name="study_files[]" type="file" class="form-control" accept=".pdf,.docx,.docs">
                                            </div>
                                        </div>
                                        <div class="col-2" style="margin-top:40px;">
                                            <button class="btn btn-danger" onclick="removeNode('<?=$key?>')">Remove</button>
                                        </div>
                                    </div>
                                    
                                    <?php
                                        }
                                        elseif($type=="videofile"){
                                    ?>
                                     <div class="row" id="nodeId<?=$key?>">
                                                                <div class="col-12  mb-1" style="max-height: 300px; overflow:auto;">
                                                                    <video width="200" controls>
                                                                        <source src="{{ asset('assets/files/topic/' . $value) }}"
                                                                            type="video/mp4">
                                                                        <source src="movie.ogg" type="video/ogg">
                                                                        Your browser does not support the video tag.
                                                                    </video>
                                                                </div>
                                                                <div class="col-12 col-sm-10 mb-sm-1 mb-sm-0 ">
                                                                    <div class="mb-2">
                                                                        <p>Video {{$key+1}}</p>
                                                                        <input name="study_files[]" type="file" class="form-control"  accept=".mp4">
                                                                    </div>
                                                                </div>
                                                                <div class="col-2" style="margin-top:40px;">
                                            <button class="btn btn-danger" onclick="removeNode('<?=$key?>')">Remove</button>
                                        </div>
                                                            </div>
                                    <?php
                                        }
                                        elseif($type=="youtubelink"){
                                    ?>
                                    <div class="row" id="nodeId<?=$key?>">
                                           <div class="col-12">
                                                <div class="mb-2">
                                                   <?php $videoId=getYTVideoId($value);
                                                        ?>
                                                        <iframe style="max-width:300px;" src="https://www.youtube.com/embed/<?=$videoId?>" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                                                        
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-10 mb-sm-1 mb-sm-0 ">
                                                <div class="mb-2">
                                                    <p>Youtube Link {{$key+1}}</p>
                                                    <input name="study_files[]" type="text"
                                                        placeholder="Youtube link here..." class="form-control"  value="<?=$value?>">
                                                </div>
                                            </div>
                                            <div class="col-2" style="margin-top:40px;">
                                            <button class="btn btn-danger" onclick="removeNode('<?=$key?>')">Remove</button>
                                        </div>
                                        </div>
                                    <?php
                                        }
                                     elseif($type=="externallinks"){
                                    ?>
                                    <div class="row" id="nodeId<?=$key?>">
                                       <div class="col-12">
                                            <div class="mb-2">
                                               <a href="<?= json_decode($value)[1]; ?>" target="_blank" class=""><?= json_decode($value)[0]; ?></a>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-10 mb-sm-1 mb-sm-0 ">
                                            <div class="mb-2">
                                                <p>External link {{$key+1}}</p>
                                                 <input name="link_title[]" type="text"
                                                    placeholder="External link title here..." class="form-control mb-1"  value="<?=json_decode($value)[0] ?>">
                                                <input name="study_files[]" type="text"
                                                    placeholder="External link here..." class="form-control"  value="<?=json_decode($value)[1] ?>">
                                            </div>
                                        </div>
                                        <div class="col-2" style="margin-top:40px;">
                                            <button class="btn btn-danger" onclick="removeNode('<?=$key?>')">Remove</button>
                                        </div>
                                    </div>
                                     <?php
                                        }
                                    }
                                    ?>
                                   
                                
                            </div>
                            
                            <div class="mb-3" id="moreDiv">
                                
                            </div>

                            <?php
                            
                             if($type=="questions"){
                                 ?>
                                 <div class="card shadow border-0 mb-7">
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

                                    @foreach ($topicQuestions as $question)
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
                                 
                                 <?php
                             }
                             else{
                                 ?>
                                 <button type="button" class="btn btn-success btn-sm" id="add_more_btn" onClick="addMore()">+ Add More</button>
                                 <?php
                             }
                             
                             
                            ?>



                            <br><br><br>
                            <button type="submit" class="btn btn-primary" id="submit_btn">Update</button>
                        </form>
                    </div>
                    <div class="card-footer border-0 py-5">
                        <span class=" text-danger text-sm">* max size is 5mb *</span>
                    </div>
                </div>
            </div>
        </main>
    </div>
@endsection

@push('js')
    <script>
       
        let i = "{{$count}}";
        function addMore() {
            let type = "{{$type}}";
            let moreDiv = document.getElementById("moreDiv");
           
            // while (i < optionCount) {

                if (type == "externallinks") {

                    let parentDiv = document.createElement('div');
                    parentDiv.className = "mb-2 row";
                    parentDiv.id = "nodeId"+i;
                    
                    let col10Div = document.createElement('div');
                    col10Div.className = "col-12 mb-1 mb-sm-0 col-sm-10 ";
                    let col2Div = document.createElement('div');
                    col2Div.className = "col-2";
                    
                    let pTag = document.createElement('p');
                    pTag.innerText = "External Link " + (i);
                    let textInput = document.createElement('input');
                    textInput.name = "link_title[]";
                    textInput.type = "text";
                    textInput.placeholder = "Link title here...";
                    textInput.className = "form-control mb-1";
                    textInput.setAttribute("required", "");
                    
                    let textInput2 = document.createElement('input');
                    textInput2.name = "study_files[]";
                    textInput2.type = "text";
                    textInput2.placeholder = "Link here...";
                    textInput2.className = "form-control";
                    textInput2.setAttribute("required", "");
                    
                    col10Div.appendChild(pTag);
                    col10Div.appendChild(textInput);
                    col10Div.appendChild(textInput2);
                    
                    // delete button
                    let buttonTag = document.createElement('button');
                    buttonTag.innerText = "Remove";
                    buttonTag.setAttribute("type", "button");
                    buttonTag.setAttribute("onclick", "removeNode("+i+")");
                    col2Div.appendChild(buttonTag);
                    
                    parentDiv.appendChild(col10Div);
                    parentDiv.appendChild(col2Div);
                    
                    moreDiv.appendChild(parentDiv);
                } else if (type == "pdf") {

                    let parentDiv = document.createElement('div');
                    parentDiv.className = "mb-2 row";
                    parentDiv.id = "nodeId"+i;
                     let col10Div = document.createElement('div');
                    col10Div.className = "col-12 mb-1 mb-sm-0 col-sm-10 ";
                    let col2Div = document.createElement('div');
                    col2Div.className = "col-2";
                    
                    
                    
                    let pTag = document.createElement('p');
                    pTag.innerText = "PDF " + (i);
                    let textInput = document.createElement('input');
                    textInput.name = "study_files[]";
                    textInput.type = "file";
                    textInput.className = "form-control";
                    textInput.setAttribute("required", "");
                    textInput.setAttribute("accept", ".pdf,.docx,.docs");
                   
                   
                    col10Div.appendChild(pTag);
                    col10Div.appendChild(textInput);
                    
                    // delete button
                    let buttonTag = document.createElement('button');
                    buttonTag.innerText = "Remove";
                    buttonTag.setAttribute("onclick", "removeNode("+i+")");
                    buttonTag.setAttribute("type", "button");
                    col2Div.appendChild(buttonTag);
                    
                    parentDiv.appendChild(col10Div);
                    parentDiv.appendChild(col2Div);
                    
                    moreDiv.appendChild(parentDiv);

                } else if (type == "youtubelink") {
                    let parentDiv = document.createElement('div');
                    parentDiv.className = "mb-2 row";
                    parentDiv.id = "nodeId"+i;
                     let col10Div = document.createElement('div');
                    col10Div.className = "col-12 mb-1 mb-sm-0 col-sm-10 ";
                    let col2Div = document.createElement('div');
                    col2Div.className = "col-2";
                    
                    
                    
                    let pTag = document.createElement('p');
                    pTag.innerText = "Youtube link " + (i);
                    let textInput = document.createElement('input');
                    textInput.name = "study_files[]";
                    textInput.type = "text";
                    textInput.placeholder = "Youtube link here...";
                    textInput.className = "form-control";
                    textInput.setAttribute("required", "");
                    
                    
                     col10Div.appendChild(pTag);
                    col10Div.appendChild(textInput);
                    
                    // delete button
                    let buttonTag = document.createElement('button');
                    buttonTag.innerText = "Remove";
                    buttonTag.setAttribute("onclick", "removeNode("+i+")");
                    buttonTag.setAttribute("type", "button");
                    col2Div.appendChild(buttonTag);
                    
                    parentDiv.appendChild(col10Div);
                    parentDiv.appendChild(col2Div);
                    moreDiv.appendChild(parentDiv);
                }  else if (type == "videofile") {

                    let parentDiv = document.createElement('div');
                    parentDiv.className = "mb-2 row";
                    parentDiv.id = "nodeId"+i;
                     let col10Div = document.createElement('div');
                    col10Div.className = "col-12 mb-1 mb-sm-0 col-sm-10 ";
                    let col2Div = document.createElement('div');
                    col2Div.className = "col-2";
                    
                    
                    let pTag = document.createElement('p');
                    pTag.innerText = "Video file " + (i);
                    let textInput = document.createElement('input');
                    textInput.name = "study_files[]";
                    textInput.type = "file";
                    textInput.className = "form-control";
                    textInput.setAttribute("required", "");
                    textInput.setAttribute("accept", ".mp4");
                    
                    
                     col10Div.appendChild(pTag);
                    col10Div.appendChild(textInput);
                    
                    // delete button
                    let buttonTag = document.createElement('button');
                    buttonTag.innerText = "Remove";
                    buttonTag.setAttribute("onclick", "removeNode("+i+")");
                    buttonTag.setAttribute("type", "button");
                    col2Div.appendChild(buttonTag);
                    
                    parentDiv.appendChild(col10Div);
                    parentDiv.appendChild(col2Div);
                    
                    
                    moreDiv.appendChild(parentDiv);
                }
                i++;
            // }

        }
        
        function removeNode(nodeId) {
            document.getElementById("nodeId"+nodeId).remove();
        }

        function submitForm() {
            let submit_btn = document.getElementById("submit_btn");
            submit_btn.disabled = true;
            submit_btn.innerText = "Submiting...";
            return true;
        }
    </script>
    
    <style>
        #moreDiv .col-2 button{
            margin-top: 32px;
            padding: 4px 20px;
            border-radius: 8px;
            background: red;
            color: #fff;
        }
    </style>
    
@endpush
