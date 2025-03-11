@extends('layouts.admin_layout')

@section('content')
    @php
        // default values
        $contest_type = '1';
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
                <form class="" method="post" action="{{ route('post_new_contest') }}" enctype="multipart/form-data"
                    onsubmit="return submitForm()">
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
                                                value="Live Contest" readonly required>
                                        <input type="text" class="form-control" name="contest_type"
                                        value="1" readonly required hidden>
                                        @error('contest_type')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-12 col-sm-6">
                                    <div class="mb-3">
                                        <label for="contest_name" class="form-label">Contest Name <span
                                                class="text-danger">*</span></label>
                                        
                                        <input type="text" class="form-control" name="contest_name" required >
                                        @error('contest_name')
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
                                        <input type="date" class="form-control" name="start_date"
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
                                        <input type="time" class="form-control" name="start_time"
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
                                        <label for="entry_fees" class="form-label">Entry Fees <span
                                                class="text-danger">*</span></label>
                                        <input type="number" step="0.01" min="0" class="form-control"
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
                                        <input type="number" class="form-control" name="per_question_mark"
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
                                            class="form-control" name="neg_mark" value="{{ old('neg_mark') }}" required
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

                                        <select class="form-select" name="status" id="status">
                                            <option value="1">Active</option>
                                            <option value="0">Inactive</option>
                                        </select>

                                        @error('status')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description </label>
                                <textarea class="form-control" name="description" id="description" rows="2"
                                    placeholder="Enter description and rules for contest..."></textarea>
                                @error('description')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="duration" class="form-label">Duration(In minutes) <span
                                        class="text-danger">*</span></label>
                                <input type="number" min="1" class="form-control" name="duration"
                                    placeholder="Duration of live contest" required id="duration">
                                    @error('duration')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>

                            <!--{{-- <div class="mb-3">-->
                            <!--    <label for="per_question_time" class="form-label">Time per question (In seconds)<span-->
                            <!--            class="text-danger">*</span></label>-->
                            <!--    <input type="number" min="1" class="form-control" name="per_question_time"-->
                            <!--        placeholder="Time per question" required id="per_question_time">-->
                            <!--</div> --}}-->



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


                                    @foreach ($allQuestions as $question)
                                    @php 
                                            $categories=json_decode($question->category);

                                           if(is_bool(array_search(1, $categories))){
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
                                                        echo $question->title;
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
                        <div class="card-footer">
                           
                            @error('questions')
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
        fetchAllQuestion();
    </script>
   
    <script>
        let table = new DataTable('#questionsTable');
    </script>
@endpush
