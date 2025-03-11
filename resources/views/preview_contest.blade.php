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

@push("css")
<style>
/* Style the Image Used to Trigger the Modal */
.clickable-img {
  cursor: pointer;
  transition: 0.3s;
}

.clickable-img:hover {opacity: 0.7;}

/* The Modal (background) */
.img-modal {
  display: none; /* Hidden by default */
  position: fixed; /* Stay in place */
  z-index: 1; /* Sit on top */
  padding-top: 100px; /* Location of the box */
  left: 0;
  top: 0;
  width: 100%; /* Full width */
  height: 100%; /* Full height */
  overflow: auto; /* Enable scroll if needed */
  background-color: rgb(0,0,0); /* Fallback color */
  background-color: rgba(0,0,0,0.9); /* Black w/ opacity */
}

/* Modal Content (Image) */
.img-modal-content {
  margin: auto;
  display: block;
  width: 80%;
  max-width: 700px;
}



@keyframes zoom {
  from {transform:scale(0)}
  to {transform:scale(1)}
}

/* The Close Button */
.close {
  position: absolute;
  top: 15px;
  right: 35px;
  color: #f1f1f1;
  font-size: 40px;
  font-weight: bold;
  transition: 0.3s;
}

.close:hover,
.close:focus {
  color: #bbb;
  text-decoration: none;
  cursor: pointer;
}

/* 100% Image Width on Smaller Screens */
@media only screen and (max-width: 700px){
  .img-modal-content{
    width: 100%;
  }
}
</style>
@endpush
@section('content')
    <div class="h-screen flex-grow-1 overflow-y-lg-auto">
        <!-- Header -->
        <header class="bg-surface-primary border-bottom pt-6">
            <div class="container-fluid">
                <div class="mb-npx">
                    <div class="row align-items-center">
                        <div class="col-sm-6 col-12 mb-4 mb-sm-0">
                            <!-- Title -->
                            <h1 class="h2 mb-0 ls-tight">{{ $pageHeading }}</h1>
                        </div>
                        <!-- Actions -->
                        <div class="col-sm-6 col-12 text-sm-end">
                            <div class="mx-n1">
                                {{-- <a href="#" class="btn d-inline-flex btn-sm btn-neutral border-base mx-1">
                                <span class=" pe-2">
                                    <i class="bi bi-pencil"></i>
                                </span>
                                <span>Edit</span>
                            </a> --}}
                                <a href="{{ route('admin.edit.contest',['id'=>$contest->id]) }}" class="btn d-inline-flex btn-sm btn-primary mx-1">
                                    <span class=" pe-2">
                                        <i class="bi bi-pen"></i>
                                    </span>
                                    <span>Edit</span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <!-- Nav -->
                    <ul class="nav nav-tabs mt-4 overflow-x border-0">
                         <li class="nav-item">
                            <a href="{{ route('admin.view.contest',['id'=>$contest->id]) }}" class="nav-link ">Contest Details</a>
                        </li>
                        <li class="nav-item ">
                            <a href="{{route('admin.contest.leaderboard', ['id'=>$contest->id])}}" class="nav-link font-regular">Leader Board</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{route('admin.contest.payment.ledger', ['id'=>$contest->id])}}" class="nav-link font-regular">Payment Ledger</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{route('admin.contest.withdraw.ledger', ['id'=>$contest->id])}}" class="nav-link font-regular">Withdraw Ledger</a>
                        </li>
                        <li class="nav-item">
                            <a href="" class="nav-link font-regular active">Preview Contest</a>
                        </li> 
                    </ul>
                </div>
            </div>
        </header>
        <!-- Main -->
        <main class="py-6 bg-surface-secondary">
            {{-- message alerts container  --}}
            <div class="continer-fluid my-2 px-5">
                <div class="row">
                    <div class="col-12">
                        @if (Session::has('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ Session::get('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @elseif (Session::has('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ Session::get('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @elseif (Session::has('warning'))
                            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                {{ Session::get('warning') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>


            <div class="container-fluid">
                <!-- Card stats -->


                <div class="row">
                    <div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3">
                        <div class="card shadow border-0 p-5 text-dark">
                            <h2 class="text-center my-2"><u>{{$contest->name}}</u></h2>
                            <br>
                            <div id="contest_details" class="text-center">
                                <div class="d-flex gap-2 flex-wrap text-dark justify-content-center">
                                    <?php
                                    if($contest->contest_id==1 || $contest->contest_id==3){
                                        echo "<p class='p-2 rounded-pill border border-1'>Duration: ".$contest->duration." mins  </p>";
                                    }
                                    elseif ($contest->contest_id==2) {
                                        echo "<p class='p-2 rounded-pill border border-1'>Per Question Time: ".$contest->per_question_time." seconds  </p>";
                                    }
                                    
                                    ?>
                                    <p class='p-2 rounded-pill border border-1'>Per Question Marks:  <?=$contest->per_question_mark?> </p>
                                    <p class='p-2 rounded-pill border border-1'>Negative Marking:  <?=$contest->neg_mark * -1?></p>
                                    <?php
                                     if($contest->contest_id==1){
                                         
                                         echo "<p class='p-2 rounded-pill border border-1'>Deadline: ".$contest->registration_deadline." Mins before start time  </p>";
                                         echo "<p class='p-2 rounded-pill border border-1'>Winner Count: ".$contest->winner_count."  </p>";
                                         echo "<p class='p-2 rounded-pill border border-1'>Distribution Percent: ".$contest->distribution_percent." %  </p>";
                                         ?><p class='p-2 rounded-pill border border-1'>Host: <a href="{{route('admin.view.user',['id'=>$contest->host_user_id])}}">{{$contest->host_name}}</a>  </p><?php
                                     }
                            
                                    ?>
                            
                                </div>
                                <br>
                                <p class="text-dark">Prizes : </p>
                                <?php $prizes=json_decode($contest->winner_percent_distribution);?>
                                @foreach($prizes as $key=> $prize)
                                <p class="text-dark">Winner No {{$key+1}} - {{$prize}}%</p>
                                @endforeach
                            </div>
                            <br>
                            @if($contest->contest_id==2)
                                <div class="text-center">
                                    30
                                </div>
                            @else
                            <div class="text-center" >Time: <span id="time">{{$contest->duration}}:00</span> minutes!</div>
                            @endif
                            <br>
                            @php 
                                $sno=1;
                                $allQuestions=json_decode($contest->questions);
                            @endphp
                            <form class="" action="" method="post">
                                @csrf
                                <div id="questions" class="d-none ">
                                    @foreach($contestQuestions as $key=> $question)
                                    <div class="question mb-3 active ">
                                        <input type="hidden" value="{{$question->id}}" class="" name="questions[]">
                                        <div class="d-flex gap-2">
                                            <h4 style="flex-shrink: 0;">Q {{$key+1}}. </h4>
                                            <div style="max-height:250px; max-width:100%; overflow-y:auto;">
                                                <?php
                                                    if($question->title_type == "text") {
                                                        echo "<h4>".$question->title."</h4>";
                                                    } 
                                                    else if($question->title_type == "image") {
                                                        ?>
                                                        <img src="{{asset('assets/files/title/'.$question->title)}}" style="max-width:100%;" class="clickable-img" onclick="makeImgBigger(this)" alt="image">
                                                        <?php
                                                    }
                                                    elseif ($question->title_type == "youtubevideolink") {
                                                        $videoId=getYTVideoId($question->title);
                                                        ?>
                                                        <iframe style="max-width: 100%" src="https://www.youtube.com/embed/<?=$videoId?>" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                                                        <?php
                                                    }
                                                    else{
                                                        ?>
                                                        <video  style="max-width: 100%" controls>
                                                            <source src="{{asset('assets/files/title/'.$question->title)}}" type="video/mp4">
                                                            <source src="movie.ogg" type="video/ogg">
                                                            Your browser does not support the video tag.
                                                          </video>
                                                        <?php
                                                    }
                                                ?>
                                            </div>
                                        </div>
                                        <br>
                                        <h5>Options: </h5>
                                        <br>
                                        @php 
                                            $sno=1;
                                            $options=json_decode($question->options);
                                            $correct_option=json_decode($question->correct_option);
                                        @endphp
                                        @foreach ($options as $key=>$item)
                                        <div class="d-flex gap-2">
                                            <span style="flex-shrink: 0;">{{$key+1}}. </span>
                                            <div class="form-check mb-2">
                                              <input class="form-check-input" type="checkbox" value="{{$key+1}}" name="{{$question->id}}_options[]">
                                              <div style="max-height:250px; max-width:100%; overflow-y:auto;">
                                                <?php
                                                    if ($question->option_type == "text") {
                                                        echo '<label class="form-check-label">'.$item[0].' </label>';
                                                    } 
                                                    else if($question->option_type == "image") {
                                                        ?>
                                                        <img  src="{{asset('assets/files/option/'.$item)}}" style="max-width:100%;" class="clickable-img" onclick="makeImgBigger(this)" alt="image">
                                                        <?php
                                                    }
                                                    elseif ($question->option_type == "youtubevideolink") {
                                                        $videoId=getYTVideoId($item);
                                                        ?>
                                                        <iframe style="max-width: 100%" src="https://www.youtube.com/embed/<?=$videoId?>" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                                                        <?php
                                                    }
                                                    else{
                                                        ?>
                                                        <video style="max-width: 100%" controls>
                                                            <source src="{{asset('assets/files/option/'.$item)}}" type="video/mp4">
                                                            <source src="movie.ogg" type="video/ogg">
                                                            Your browser does not support the video tag.
                                                            </video>
                                                        <?php
                                                    }
                                                ?>
                                            </div>
                                        </div>
                                            
                                        </div>
                                        @endforeach 
                                    </div>
                                    @endforeach
                                    
                                    <div class="text-center mt-3">
                                        <button type="submit" class="btn btn-primary mt-2" id="submit_quiz" onclick="submitQuiz()">Submit Quiz</button>
                                    </div>
                                </div>
                            </form>
                            
                            <div class="text-center mt-1">
                                <button type="button" class="btn btn-primary mt-2" onclick="startQuiz(this, '{{$contest->contest_id}}','{{$contest->duration}}')">Start Quiz</button>
                            </div>
                            
                        </div>
                    </div>
                </div>

               
            </div>
        </main>
    </div>
@endsection

@push("modal")
<!-- The Modal -->
<div id="imgModal" class="img-modal">

  <!-- The Close Button -->
  <span class="close">&times;</span>

  <!-- Modal Content (The Image) -->
  <img class="img-modal-content" id="img01">

  <!-- Modal Caption (Image Text) -->
  <div id="caption"></div>
</div>
@endpush


@push("js")
<script>
    function makeImgBigger(img){
        // Get the modal
        var modal = document.getElementById("imgModal");
        
        // Get the image and insert it inside the modal - use its "alt" text as a caption
        var img = img;
        var modalImg = document.getElementById("img01");
        // img.onclick = function(){
        //   modal.style.display = "block";
        //   modalImg.src = this.src;
        // }
         modal.style.display = "block";
          modalImg.src = img.src;
        
        // Get the <span> element that closes the modal
        var span = document.getElementsByClassName("close")[0];
        
        // When the user clicks on <span> (x), close the modal
        span.onclick = function() {
          modal.style.display = "none";
        }
       }
       
    function startQuiz(btn,type,duration){
        let contest_details=document.getElementById('contest_details');
        let questions=document.getElementById('questions');
        contest_details.classList.add("d-none");
        questions.classList.remove("d-none");
        btn.classList.add("d-none");
        
        if(type!=2){
            var fiveMinutes = 60 *  parseInt(duration),
            display = document.querySelector('#time');
            startTimer(fiveMinutes, display);
        }
        
    }
    
    function startTimer(duration, display) {
    var timer = duration, minutes, seconds;
    setInterval(function () {
            minutes = parseInt(timer / 60, 10);
            seconds = parseInt(timer % 60, 10);
    
            minutes = minutes < 10 ? "0" + minutes : minutes;
            seconds = seconds < 10 ? "0" + seconds : seconds;
    
            display.textContent = minutes + ":" + seconds;
    
            if (--timer < 0) {
                timer = duration;
                $("#submit_quiz").click();
            }
        }, 1000);
    }
    
    window.onbeforeunload = function() {
        console.log("Page Refresh !");
        return "Are you sure ? You want to end test !";
    }
    
    function submitQuiz(){
        window.onbeforeunload = function() {
        return null;
        }
        return true;
    }

</script>
@endpush
