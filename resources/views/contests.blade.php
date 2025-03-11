@extends('layouts.admin_layout')

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
                                {{--  <a href="{{ route('new.contest') }}" class="btn d-inline-flex btn-sm btn-primary mx-1">
                                    <span class=" pe-2">
                                        <i class="bi bi-plus"></i>
                                    </span>
                                    <span>Create</span>--}}
                                </a>
                            </div>
                        </div>
                    </div>
                    <!-- Nav -->
                    <ul class="nav nav-tabs mt-4 overflow-x border-0">
                        <li class="nav-item">
                            <a href="{{ route('admin.contests')}}" class="nav-link @if($contest_type==0) active @endif">All</a>
                        </li>
                        <li class="nav-item ">
                            <a href="{{ route('admin.contests',['type'=>'1']) }}"
                                class="nav-link font-regular @if($contest_type==1) active @endif">Live Contest</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.contests',['type'=>'2']) }}" class="nav-link font-regular @if($contest_type==2) active @endif">Time Limit
                                Contest</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.contests',['type'=>'3']) }}" class="nav-link font-regular @if($contest_type==3) active @endif">Any
                                Time</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.contests',['type'=>'4']) }}" class="nav-link font-regular @if($contest_type==4) active @endif">Upcoming Contest</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.contests',['type'=>'5']) }}" class="nav-link font-regular @if($contest_type==5) active @endif">Completed Contest</a>
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
                <div class="row g-6 mb-6">
                    <div class="col-xl-3 col-sm-6 col-12">
                        <div class="card shadow border-0">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col">
                                        <span class="h6 font-semibold text-muted text-sm d-block mb-2">All Contest</span>
                                        <span class="h3 font-bold mb-0">{{ $allContestCount }}</span>
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
                <div class="card shadow border-0 mb-7">
                    <div class="card-header">
                        <h5 class="mb-0">List</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover table-nowrap">
                            <thead class="thead-light">
                                <tr>

                                    <th scope="col">Sno</th>
                                    <th scope="col">Contest Name</th>
                                    <th scope="col">Contest Type</th>
                                    <th scope="col">Start Time</th>
                                    <th scope="col">Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $sno=1;@endphp
                                @foreach ($allContest as $contest)
                                    <tr>
                                        <td>{{ $sno++ }}</td>
                                       <td>{{ $contest->name }}</td>
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
                                        <td class="">
                                            {{ date("d M Y h:i A", strtotime($contest->start_time)) }}
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
                                            <a href="{{route('admin.view.contest',['id'=>$contest->id])}}" class="btn btn-sm btn-primary"><i class="bi bi-eye"></i> View</a>
                                            <a href="{{ route('admin.edit.contest',['id'=>$contest->id]) }}" class="btn d-inline-flex btn-sm  btn-neutral mx-1">
                                                <span class=" pe-2">
                                                    <i class="bi bi-pen"></i>
                                                </span>
                                                <span>Edit</span>
                                            </a>
                                            <button data-contestId="{{ $contest->id }}" class="btn btn-danger d-inline-flex btn-sm mx-1" onclick="deleteContest(this)">
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
                        {{ $allContest->links() }}
                    </div>
                    <div class="card-footer border-0 py-5">
                        <span class="text-muted text-sm">Showing {{ $allContestCount > 10 ? 10 : $allContestCount }}
                            items out
                            of {{ $allContestCount }} results
                            found</span>
                    </div>
                </div>
            </div>
        </main>
    </div>
@endsection

@push('js')
<script>
    function deleteContest(e) {
        // console.log(e.parentElement.parentElement);
        if (confirm('Are you sure you want to delete this contest?')) {
            var contestId = e.getAttribute("data-contestId");
            $.ajax({
                url: 'delete/contest/' + contestId, // Include the ID in the URL
                method: 'POST',
                data: {
                    id: contestId, // Pass the ID in the data object
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
            alert('Contest is not deleted');
        }

    }
</script>
@endpush
