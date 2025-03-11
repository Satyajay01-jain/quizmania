@extends('layouts.admin_layout')

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
            <div class="container-fluid">
             <form class="" method="post" action="{{route('post_new_question')}}" enctype="multipart/form-data" onsubmit="return submitForm()">
                            @csrf
                        <!--    <div class="card shadow border-0 mb-7">-->
                        <!--<div class="card-header">-->
                        <!--    <h5 class="mb-0">Select Topics</h5>-->
                        <!--    <h6 class="mb-0">Select All</h6>-->
                        <!--    <span>-->
                        <!--        <input class="form-check-input" type="checkbox" name="topics[]" value="">-->
                        <!--    </span>-->
                            
                        <!--</div>-->
                        
                         <!--modified select all header tab-->

                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem; background-color: #f8f9fa; border: 1px solid #dee2e6;">
                            <h5 style="margin-bottom: 0;">Select Topics</h5>
                            <div style="display: flex; align-items: center;">
                                <h6 style="margin-bottom: 0; margin-right: 0.5rem;">Select All</h6>
                                <span>
                                    <input class="form-check-input" type="checkbox" id="selectAll" name="multiple_select">
                                </span>
                            </div>
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
                                                    <input class="form-check-input topic-checkbox" type="checkbox" name="topics[]"
                                                        value="{{ $topic->id }}">
                                                </div>
                                            </td>
                                            
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>
                        </div>
                        <div class="table-responsive p-3" style="max-height:500px;">
                            
                          
                        <div class="card-footer">
                           
                            @error('topics')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror

                    </div>

            </div>
                <div class="card shadow border-0 mb-7">
                    <div class="card-header">
                        <h5 class="mb-0">Enter Details</h5>
                    </div>
                    <div class="py-2 px-4">
                       

                            <div class="row">
                                <div class="col-12 col-sm-6">
                                    <div class="mb-3">
                                        <label for="title_type" class="form-label">Title Type <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select @error('title_type') is-invalid @enderror" name="title_type" onchange="setTitle(this)">
                                            <option value="text">Text</option>
                                            <option value="image">Image (.png,.jpg,.jpeg)</option>
                                            <option value="audio">audio (.mp3,M4A,FLAC)</option>
                                            <option value="youtubevideolink">Video (youtube video link)</option>
                                            <option value="video">Video (.mp4 file)</option>
                                            <option value="other">Other</option>
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
                                        <label class="form-label">Enter Title <span class="text-danger">*</span></label>
                                        <div class="mt-1" id="titleDiv">
                                            <div class="row">
                                                <div class="col-12 col-sm-6">
                                                    <input type="text" class="form-control @error('title') is-invalid @enderror" name="title" required placeholder="Question title here...">
                                                </div>
                                                <div class="col-12 col-sm-6">
                                                    <input type="text" class="form-control @error('title') is-invalid @enderror" name="hi_title" required placeholder="Question title in hindi here...">
                                                </div>
                                            </div>
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
                                        <select class="form-select" name="option_type" id="option_type" onchange="setOptions()">
                                            <option value="text">Text</option>
                                            <option value="text">Audio  (.mp3,M4A,FLAC)</option>
                                            <option value="image">Image (.png,.jpg,.jpeg)</option>
                                            <option value="youtubevideolink">Video (youtube video link)</option>
                                            <option value="video">Video (.mp4 file)</option>
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
                                        <select class="form-select" name="option_count" id="option_count" onchange="setOptions()">
                                            <option value="1">1</option>
                                            <option value="2">2</option>
                                            <option value="3">3</option>
                                            <option value="4" selected>4</option>
                                            <option value="5">5</option>
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
                                    
                                    <div class="row">
                                        <div class="col-12 col-sm-6">
                                            <input type="text" class="form-control" name="option[]" placeholder="Question options here..." required>
                                        </div>
                                        <div class="col-12 col-sm-6">
                                            <input type="text" class="form-control" name="hi_option[]" placeholder="Question options in hindi here..." required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Correct Option No. <span class="text-danger">*</span></label>
                                    <div class="mt-1" id="correctOptionDiv">
                                        <div class="form-check form-check-inline">
                                            <input type="checkbox" class="form-check-input" id="o1" value="0" name="correct_option[]">
                                            <label class="form-check-label" for="correct_option">Option 1</label>
                                        </div>
                                    </div>
                                    @error('correct_option')
                                        <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>


                            <!--<div class="mb-3">-->
                            <!--    <label for="question_category" class="form-label">Question Category </label>-->
                            <!--            <br>-->
                            <!--    @foreach ($allCategories as $allCategorie)-->
                            <!--        <div class="form-check form-check-inline">-->
                            <!--            <input type="checkbox" class="form-check-input" id="question_category{{$allCategorie->id}}" value="{{$allCategorie->id}}" name="question_category[]">-->
                            <!--            <label class="form-check-label" for="question_category{{$allCategorie->id}}">{{$allCategorie->name}}</label>-->
                            <!--        </div>-->
                            <!--    @endforeach-->

                            <!--    {{-- if user do not select any one of this the we assume he want all. --}}-->
                            <!--</div>-->

                            <div class="mb-3">
                                <label for="status" class="form-label">Status <span
                                        class="text-danger">*</span></label>

                                <select class="form-select" name="status" id="status">
                                    <option  value="1">Active</option>
                                    <option  value="0">Inactive</option>
                                </select>

                                @error('status')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!--<div class="mb-3">-->
                            <!--    <label for="status" class="form-label">Ans Description (Write a brief decription of correct ans. It will be displayed after question) <span-->
                            <!--            class="text-danger">*</span></label>-->

                            <!--    <textarea class="form-control" rows="2" name="ans_desc" placeholder="Write correct answer description" required></textarea>-->

                            <!--    @error('ans_desc')-->
                            <!--        <p class="text-danger">{{ $message }}</p>-->
                            <!--    @enderror-->
                            <!--</div>-->
                          
                            <div class="">
                                <div class="col-lg-12">
                                    <div id="row">
                                         <label for="status" class="form-label" style="display:block;width:100%;">Ans Description (Write a brief decription of correct ans. It will be displayed after question) <span
                                      class="text-danger">*</span></label>
                                        <div class="input-group mt-3 mb-3">
                                            
                                            <textarea class="form-control m-input" rows="2" name="ans_desc" placeholder="Write correct answer description" required></textarea> 
                                            <div class="input-group-prepend">
                                                <button class="btn btn-danger"
                                                        id="DeleteRow"
                                                        type="button">
                                                    <i class="bi bi-trash"></i>
                                                    Delete
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="newinput"></div>
                                    <button id="rowAdder" type="button" class="btn btn-dark">
                                        <span class="bi bi-plus-square-dotted">
                                        </span> ADD
                                    </button>
                                </div>
                            </div>
                        

                           
                    </div>
                    <div class="card-footer border-0 py-5">
                        <span class=" text-danger text-sm">* Allowed file type - .png,.jpg,.jpeg,.mp4 and max size is 5 mb.</span>
                    </div>
                </div>
                
                
            
                <button type="submit" class="btn btn-primary mt-2" id="submit_btn">Submit</button>
            </form>
        </main>
    </div>
@endsection

@push('js')
                         <script>
document.getElementById('selectAll').addEventListener('change', function() {
    var checkboxes = document.querySelectorAll('input[name="topics[]"]');
    for (var checkbox of checkboxes) {
        checkbox.checked = this.checked;
    }
});
</script>
  <script>
        $("#rowAdder").click(function () {
            newRowAdd =
                '<div id="row"> <div class="input-group mt-3 mb-3">' +
                '<textarea class="form-control m-input" rows="2" name="ans_desc" placeholder="Write correct answer description" required></textarea>'+
                '<div class="input-group-prepend">' +
                '<button class="btn btn-danger" id="DeleteRow" type="button"><i class="bi bi-trash"></i> Delete</button></div></div> </div>';
 
            $('#newinput').append(newRowAdd);
        });
        $("body").on("click", "#DeleteRow", function () {
            $(this).parents("#row").remove();
        })
    </script>
<script>
        function setTitle(e){
            let titleType=e.value;
            let titleDiv=document.getElementById("titleDiv");
            
            if(titleType=="text"){
                titleDiv.innerHTML = '';
                
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
                console.log(titleType);
            }
            else if(titleType=="image"){
                titleDiv.innerHTML = '';
                let textInput=document.createElement('input');
                textInput.name="title";
                textInput.type="file";
                textInput.className="form-control";
                textInput.setAttribute("required","");
                textInput.setAttribute("accept",".png,.jpg,.jpeg");
                titleDiv.appendChild(textInput);
                console.log(titleType)
            }
            else if(titleType=="youtubevideolink"){
                titleDiv.innerHTML = '';
                let textInput=document.createElement('input');
                textInput.name="title";
                textInput.type="text";
                textInput.placeholder="Question Youtube link here...";
                textInput.className="form-control";
                textInput.setAttribute("required","");
                titleDiv.appendChild(textInput);
                console.log(titleType)
            }
            else if(titleType=="audionlink"){
                titleDiv.innerHTML = '';
                let textInput=document.createElement('input');
                textInput.name="title";
                textInput.type="text";
                textInput.placeholder="Question Audio link here...";
                textInput.className="form-control";
                textInput.setAttribute("required","");
                titleDiv.appendChild(textInput);
                console.log(titleType)
            }
            else{
                titleDiv.innerHTML = '';
                let textInput=document.createElement('input');
                textInput.name="title";
                textInput.type="file";
                textInput.className="form-control";
                textInput.setAttribute("required","");
                textInput.setAttribute("accept",".mp4");
                titleDiv.appendChild(textInput);
                console.log(titleType)
            }
        }

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
        function submitForm(){
            let submit_btn=document.getElementById("submit_btn");
            submit_btn.disabled =true;
            submit_btn.innerText="Submiting...";
            return true;
        }
</script>
<script>
        let table = new DataTable('#topicsTable');
        $(document).ready(function() {
            $select=$('.users-select').select2();
            // $select.data('select2').$container.addClass('form-control');
        });
    </script>
@endpush