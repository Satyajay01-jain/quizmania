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
                                <a href="{{ route('new.question') }}" class="btn d-inline-flex btn-sm btn-primary mx-1">
                                    <span class=" pe-2">
                                        <i class="bi bi-plus"></i>
                                    </span>
                                    <span>Create</span>
                                </a>
                               <!--<a href="{{ route('importquestions.show') }}" class="btn d-inline-flex btn-sm mx-1" style="background-color: black; color: white; border-color: black;">-->
                               <!--     <span class="pe-2">-->
                               <!--         <i class="bi bi-upload"></i>-->
                               <!--     </span>-->
                               <!--     <span>Import</span>-->
                               <!-- </a>-->
                                <!--<form action="{{ route('admin.questions') }}" method="POST" enctype="multipart/form-data">-->
                                <!--    @csrf-->
                                <!--    <div class="form-group">-->
                                <!--        <label for="file">Choose Excel File</label>-->
                                <!--        <input type="file" name="file" id="file" class="form-control">-->
                                <!--    </div>-->
                                <!--    <button type="submit" class="btn btn-primary">Import</button>-->
                                <!--</form>-->
                            </div>
                        </div>
                       
                        
                        
                        
                    </div>
                    <!-- Nav -->
                    <ul class="nav nav-tabs mt-4 overflow-x border-0">
                        <li class="nav-item">
                            <a href="{{ route('admin.questions') }}" class="nav-link active">All</a>
                        </li>
                       <!-- <li class="nav-item ">
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
                        </li>-->
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
                <div class="row g-6 mb-6">
                    <div class="col-xl-3 col-sm-6 col-12">
                        <div class="card shadow border-0">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col">
                                        <span class="h6 font-semibold text-muted text-sm d-block mb-2">All Question</span>
                                        <span class="h3 font-bold mb-0">{{ $allQuestionCount }}</span>
                                    </div>
                                    <div class="col-auto">
                                        <div class="icon icon-shape bg-tertiary text-white text-lg rounded-circle">
                                            <i class="bi bi-credit-card"></i>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                

                </div>
                <!--<form action="" method="POST">-->
                <!--       <div class="row">-->
                <!--         <div class="col-md-6">-->
                <!--            <div class="input-group mb-3">-->
                <!--                <input type="text" class="form-control"   placeholder="Search Topic" id="search1">-->
                <!--            </div>-->
                <!--        </div>-->
                <!--    </div>-->
                <!--</form>-->
                <!--<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">-->
                <!--    <input type="text" id="searchInput" onkeyup="searchTopics()" placeholder="Search for topics..." style="padding: 0.5rem; width: 100%;">-->
                <!-- </div>-->
                <div class="card shadow border-0 mb-7">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">List</h5>
                        <div class="dropdown">
                            <input type="text" id="search" name="search" onkeyup="searchTopics()" placeholder="Search for topics..." style="border: 1px solid #c6c6c6;padding: 9px 10px;border-radius: 8px;margin-top: -3px;">
                            <button class="btn btn-secondary dropdown-toggle" type="button" id="topicDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                Filter by Topic
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="topicDropdown">
                                @foreach ($allTopics as $topic)
                                <li><a class="dropdown-item topic-item" href="#" data-topic-id="{{ $topic['id'] }}">{{ $topic['name'] }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>

                    <div class="table-responsive">
                        <table class="table table-hover table-nowrap">
                            <thead class="thead-light">
                                <tr>

                                    <th scope="col">Sno</th>
                                    <th scope="col">Topic</th>
                                    <th scope="col">Question</th>
                                    <th scope="col">Category</th>
                                    <th scope="col">Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $sno=1;@endphp
                                @foreach ($allQuestions as $question)
                                    <tr>
                                        <td>{{ $sno++ }}</td>
                                        <td></td>
                                        <td>
                                            {{-- <img alt="..."
                                            src="https://images.unsplash.com/photo-1502823403499-6ccfcf4fb453?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=3&w=256&h=256&q=80"
                                            class="avatar avatar-sm rounded-circle me-2"> --}}
                                            <a class="text-heading font-semibold" href="{{route('admin.view.question',['id'=>$question->id])}}">

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
                                                        <video width="200" controls>
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
                                            <a href="{{route('admin.view.question',['id'=>$question->id])}}" class="btn btn-sm btn-primary"><i class="bi bi-eye"></i> View</a>
                                            <a href="{{ route('admin.edit.question',['id'=>$question->id]) }}" class="btn d-inline-flex btn-sm  btn-neutral mx-1">
                                                <span class=" pe-2">
                                                    <i class="bi bi-pen"></i>
                                                </span>
                                                <span>Edit</span>
                                            </a>
                                            <button data-questionId="{{ $question->id }}" class="btn btn-danger d-inline-flex btn-sm mx-1" onclick="deleteQuestion(this)">
                                                <span class=" pe-2">
                                                    <i class="bi bi-trash-fill"></i>
                                                </span>
                                                <span>Delete</span>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach


                            </tbody>
                        </table>
                       
                        <br><br>
                      
                       <div class="pagination d-flex justify-content-center my-2">
                        {{ $allQuestions->links() }}
                       </div>
                    </div>
                    <div class="card-footer border-0 py-5">
                        <span class="text-muted text-sm">Showing {{ $allQuestionCount > 10 ? 10 : $allQuestionCount }}
                            items out
                            of {{ $allQuestionCount }} results
                            found</span>
                    </div>
                </div>
            </div>
        </main>
    </div>
@endsection

@push('js')
<!--dropdown selector-->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script type="text/javascript">
    $('#search').on('keyup',function(){
    $value=$(this).val();
    $.ajax({
    type : 'get',
    url : '{{route('admin.questions.search')}}',
    data:{'search':$value},
    success:function(data){
    $('tbody').html(data);
    }
    });
    })
</script>
<script type="text/javascript">
    $.ajaxSetup({ headers: { 'csrftoken' : '{{ csrf_token() }}' } });
</script>

<script>
    $(document).ready(function() {
        $('.topic-item').click(function(event) {
            event.preventDefault();
            var selectedTopicId = $(this).attr('data-topic-id');
            var url = "{{ route('admin.questions', ':topicId') }}".replace(':topicId', selectedTopicId);
            window.location.href = url;
        });
    });
</script>

<script>
    function deleteQuestion(e) {
        // console.log(e.parentElement.parentElement);
        if (confirm('Are you sure you want to delete this question?')) {
            var questionId = e.getAttribute("data-questionId");
            $.ajax({
                url: 'delete/question/' + questionId, // Include the ID in the URL
                method: 'POST',
                data: {
                    id: questionId, // Pass the ID in the data object
                    _token: '{{ csrf_token() }}' // Correct way to pass CSRF token
                },
                success: function(response) {
                    if (response.status) {
                        e.parentElement.parentElement.remove();
                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText); // Log any errors to the console
                }
            });
        } else {
            alert('Question is not deleted');
        }

    }
</script>

<!--<script>-->
<!--$('#search').on('keyup', function(){-->
<!--    search();-->
<!--});-->
<!--search();-->
<!--function search(){-->
<!--     var keyword = $('#search').val();-->
  
<!--     $.post('{{ route("admin.questions.search") }}',-->
<!--      {-->
<!--         _token: $('meta[name="csrf-token"]').attr('content'),-->
<!--         keyword:keyword-->
<!--       },-->
<!--       function(data){-->
<!--        table_post_row(data);-->
<!--          console.log(data);-->
<!--       });-->
<!--}-->

<!--function table_post_row(res){-->
<!--let htmlView = '';-->
<!--if(res.topics.length <= 0){-->
<!--    htmlView+= `-->
<!--       <tr>-->
<!--          <td colspan="4">No data.</td>-->
<!--      </tr>`;-->
<!--}-->
<!--for(let i = 0; i < res.topics.length; i++){-->
<!--    htmlView += `-->
<!--        <tr>-->
<!--           <td>`+ (i+1) +`</td>-->
<!--              <td>`+res.topics[i].name+`</td>-->
              
<!--        </tr>`;-->
<!--}-->
    
<!--}-->
<!--</script>-->

@endpush
