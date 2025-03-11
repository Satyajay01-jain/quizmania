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
                            <a href="{{ route('admin.view.contest',['id'=>$contest->id]) }}" class="nav-link active">Contest Details</a>
                        </li>
                        <li class="nav-item ">
                            <a href="{{route('admin.contest.leaderboard', ['id'=>$contest->id])}}" class="nav-link font-regular">Leader Board</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{route('admin.contest.payment.ledger', ['id'=>$contest->id])}}" class="nav-link font-regular">Payment Ledger</a>
                        </li>
                        {{--<li class="nav-item">
                            <a href="{{route('admin.contest.withdraw.ledger', ['id'=>$contest->id])}}" class="nav-link font-regular">Withdraw Ledger</a>
                        </li> --}}
                        <li class="nav-item">
                            <a href="{{ route('admin.preview.contest',['id'=>$contest->id]) }}" class="nav-link font-regular" target="_blank">Preview Contest</a>
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

                <div class="card shadow border-0 mb-7">
                    <div class="card-header">
                        <h5 class="mb-0">Contest Details</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover table-nowrap">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col">Name</th>
                                    <th scope="col">Type</th>
                                    <th scope="col">Entry Fees</th>
                                    <th scope="col">Total Questions</th>
                                    <th scope="col">Status</th>
                                    <th>Contest Date</th>
                                </tr>
                            </thead>
                            <tbody>
                               
                                    
                                    <tr>
                                        <td>{{$contest->name}}</td>
                                        <td>
                                            @php
                                                $category = $contest->contest_id;
                                                if (isset($categoriesMap[$category])) {
                                                        echo '<span class="rounded-pill px-2 py-1 border border-1 me-1">' . $categoriesMap[$category] . '</span>';
                                                } else {
                                                    echo '<span class="rounded-pill px-2 py-1 border border-1 me-1">Others</span>';
                                                }
                                            @endphp
                                        </td>
                                        <td>
                                            <?=$contest->entry_fees;?> INR
                                        </td>
                                        <td>
                                            <?=$contest->questions_count;?>
                                        </td>
                                        <td>
                                            @php
                                                if ($contest->status == 0) {
                                                    echo '<b>Inactive</b>';
                                                } else {
                                                    echo "<span class='text-success'><b>Active</b></span>";
                                                }
                                            @endphp
                                        </td>
                                        <td class="">
                                          <p>Starts on: {{date("d M Y h:i A", strtotime($contest->start_time))}}</p>
                                          <p>Ends on: {{date("d M Y h:i A", strtotime($contest->end_date_time))}}</p>
                                        </td>
                                    </tr>
                                  
                            </tbody>
                        </table>
                       
                        <br><br>
                      
                       
                    </div>
                    <div class="card-footer border-0 py-1">
                        
                    </div>
                </div>

                <div class="card shadow border-0 mb-7">
                    <div class="card-header">
                        <h5 class="mb-0">Contest Description</h5>
                    </div>
                    <div class="card-body">
                            <p class="text-dark">{{$contest->description}}</p>
                            <br>
                        <div class="d-flex gap-2 flex-wrap text-dark">
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
                    <div class="card-footer border-0 py-1">
                        
                    </div>
                </div>


                <div class="card shadow border-0 mb-7">
                    <div class="card-header">
                        <h5 class="mb-0">Question Details</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover table-nowrap">
                            <thead class="thead-light">
                                <tr>

                                    <th scope="col">Sno</th>
                                    <th scope="col">Title</th>
                                    <th scope="col">Category</th>
                                    <th>Last Updated</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php 
                                    $sno=1;
                                    $allQuestions=json_decode($contest->questions);
                                    
                                @endphp
                                    @foreach ($contestQuestions as $question)
                                        
                                    
                                    <tr>
                                        <td>{{ $sno++ }}</td>
                                        <td style="flex-shrink: 0">
                                            <a class="text-heading font-semibold" target="_blank" href="{{route('admin.view.question',['id'=>$question->id])}}">

                                                <?php
                                                    if ($question->title_type == "text") {
                                                        echo $question->title;
                                                    } 
                                                    else if($question->title_type == "image") {
                                                        ?>
                                                        <img src="{{asset('assets/files/title/'.$question->title)}}" style="max-width:120px;" alt="image">
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
                                                        <video width="200" style="min-width: 200px" controls>
                                                            <source src="{{asset('assets/files/title/'.$question->title)}}" type="video/mp4">
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


                                        <td class="">
                                           {{date("d M Y h:i A", strtotime($question->updated_at))}}
                                        </td>
                                    </tr>
                                    @endforeach
                            </tbody>
                        </table>
                       
                        <br><br>
                      
                       
                    </div>
                    <div class="card-footer border-0 py-1">
                        
                    </div>
                </div>

               
            </div>
        </main>
    </div>
@endsection
