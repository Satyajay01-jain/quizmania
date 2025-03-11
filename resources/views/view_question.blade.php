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
        
   
    $video_id = explode("&", $video_id[1]); // Deleting any other params
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
                                <a href="{{ route('admin.edit.question',['id'=>$allQuestions->id]) }}" class="btn d-inline-flex btn-sm btn-primary mx-1">
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
                        {{-- <li class="nav-item">
                            <a href="{{ route('admin.questions') }}" class="nav-link active">All</a>
                        </li>
                        <li class="nav-item ">
                            <a href="#" class="nav-link font-regular">Live Contest</a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link font-regular">Time Limit Contest</a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link font-regular">Any Time</a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link font-regular">Question Bank</a>
                        </li> --}}
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
                <p>Last Updated:- <b>{{date("d M Y h:i A", strtotime($allQuestions->updated_at))}}</b></p>
                <div class="card shadow border-0 mb-7">
                    <div class="card-header">
                        <h5 class="mb-0">Details</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover table-nowrap">
                            <thead class="thead-light">
                                <tr>

                                    <th scope="col">Sno</th>
                                    <th scope="col">Title</th>
                                    <th scope="col">Category</th>
                                    <th scope="col">Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php 
                                    $sno=1;
                                    $question=$allQuestions;
                                @endphp
                                
                                    <tr>
                                        <td>{{ $sno++ }}</td>
                                        <td style="flex-shrink: 0">
                                            {{-- <a class="text-heading font-semibold" href="#"> --}}

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
                                            {{-- </a> --}}
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
                                        <td>
                                            @php
                                                if ($question->status == 0) {
                                                    echo '<b>Inactive</b>';
                                                } else {
                                                    echo "<span class='text-success'><b>Active</b></span>";
                                                }
                                            @endphp
                                        </td>


                                        <td class="">
                                           {{date("d M Y h:i A", strtotime($question->created_at))}}
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
                        <h5 class="mb-0">Options</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover table-nowrap">
                            <thead class="thead-light">
                                <tr>

                                    <th scope="col">Sno</th>
                                    <th scope="col">Option</th>
                                    <th scope="col">Is Correct</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php 
                                    $sno=1;
                                    $options=json_decode($allQuestions->options);
                                    $correct_option=json_decode($allQuestions->correct_option);
                                @endphp
                                @foreach ($options as $key=>$item)
                                    
                                
                                    <tr>
                                        <td>{{ $sno++ }} </td>
                                        <td>
                                            <div class="text-heading font-semibold" style="max-height: 200px; overflow:auto;">
                                        
                                            <?php
                                                if ($question->option_type == "text") {
                                                    echo $item[0];
                                                } 
                                                else if($question->option_type == "image") {
                                                    ?>
                                                    <img src="{{asset('assets/files/option/'.$item)}}" style="max-width:120px;" alt="image">
                                                    <?php
                                                }
                                                elseif ($question->option_type == "youtubevideolink") {
                                                    $videoId=getYTVideoId($item);
                                                    ?>
                                                    <iframe style="max-width:300px;" src="https://www.youtube.com/embed/<?=$videoId?>" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                                                    <?php
                                                }
                                                else{
                                                    ?>
                                                    <video width="200" controls>
                                                        <source src="{{asset('assets/files/option/'.$item)}}" type="video/mp4">
                                                        <source src="movie.ogg" type="video/ogg">
                                                        Your browser does not support the video tag.
                                                        </video>
                                                    <?php
                                                }
                                            ?>
                                        </div>
                                        </td>
                                        <td>
                                            
                                            <?php 
                                                if(!is_bool(array_search($key,$correct_option))){
                                                    echo "Yes";
                                                }
                                                else{
                                                    echo "No";
                                                }
                                                
                                            ?>
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
