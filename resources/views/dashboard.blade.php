@extends("layouts.admin_layout")

@section("content")
<div class="h-screen flex-grow-1 overflow-y-lg-auto">
    <!-- Header -->
    <header class="bg-surface-primary border-bottom pt-6">
        <div class="container-fluid">
            <div class="mb-npx">
                <div class="row align-items-center">
                    <div class="col-sm-6 col-12 mb-4 mb-sm-0">
                        <!-- Title -->
                        <h1 class="h2 mb-0 ls-tight">{{$pageHeading}}</h1>
                    </div>
                    <!-- Actions -->
                    <div class="col-sm-6 col-12 text-sm-end">
                        <div class="mx-n1">
                            {{-- <a href="#" class="btn d-inline-flex btn-sm btn-neutral border-base mx-1">
                                <span class=" pe-2">
                                    <i class="bi bi-pencil"></i>
                                </span>
                                <span>Edit</span>
                            </a>
                            <a href="#" class="btn d-inline-flex btn-sm btn-primary mx-1">
                                <span class=" pe-2">
                                    <i class="bi bi-plus"></i>
                                </span>
                                <span>Create</span>
                            </a> --}}
                        </div>
                    </div>
                </div>
                <!-- Nav -->
                <ul class="nav nav-tabs mt-4 overflow-x border-0">
                    {{-- <li class="nav-item ">
                        <a href="#" class="nav-link active">All files</a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link font-regular">Shared</a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link font-regular">File requests</a>
                    </li> --}}
                </ul>
            </div>
        </div>
    </header>
    <!-- Main -->
    <main class="py-6 bg-surface-secondary">
        <div class="container-fluid">
            <!-- Card stats -->
            <div class="row g-6 mb-6">
                <div class="col-xl-3 col-sm-6 col-12">
                    <a href="{{route('admin.contests')}}">
                    <div class="card shadow border-0">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <span
                                        class="h6 font-semibold text-muted text-sm d-block mb-2">Contests</span>
                                    <span class="h3 font-bold mb-0">{{$contestCount}}</span>
                                </div>
                                <div class="col-auto">
                                    <div class="icon icon-shape bg-tertiary text-white text-lg rounded-circle">
                                        <i class="bi bi-credit-card"></i>
                                    </div>
                                </div>
                            </div>
                           {{--  <div class="mt-2 mb-0 text-sm">
                                <span class="badge badge-pill bg-soft-success text-success me-2">
                                    <i class="bi bi-arrow-up me-1"></i>13%
                                </span>
                                <span class="text-nowrap text-xs text-muted">Since last month</span>
                            </div>  --}}
                        </div>
                    </div>
                    </a>
                </div>
                <div class="col-xl-3 col-sm-6 col-12">
                    <a href="{{route('admin.questions')}}">
                    <div class="card shadow border-0">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <span
                                        class="h6 font-semibold text-muted text-sm d-block mb-2">Questions</span>
                                    <span class="h3 font-bold mb-0">{{$questionCount}}</span>
                                </div>
                                <div class="col-auto">
                                    <div class="icon icon-shape bg-tertiary text-white text-lg rounded-circle">
                                        <i class="bi bi-credit-card"></i>
                                    </div>
                                </div>
                            </div>
                            {{-- <div class="mt-2 mb-0 text-sm">
                                <span class="badge badge-pill bg-soft-success text-success me-2">
                                    <i class="bi bi-arrow-up me-1"></i>13%
                                </span>
                                <span class="text-nowrap text-xs text-muted">Since last month</span>
                            </div> --}}
                        </div>
                    </div>
                    </a>
                </div>
                <div class="col-xl-3 col-sm-6 col-12">
                    <a href="{{route('admin.users')}}">
                    <div class="card shadow border-0">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <span class="h6 font-semibold text-muted text-sm d-block mb-2">Users</span>
                                    <span class="h3 font-bold mb-0">{{$userCount}}</span>
                                </div>
                                <div class="col-auto">
                                    <div class="icon icon-shape bg-primary text-white text-lg rounded-circle">
                                        <i class="bi bi-people"></i>
                                    </div>
                                </div>
                            </div>
                           {{--  <div class="mt-2 mb-0 text-sm">
                                <span class="badge badge-pill bg-soft-success text-success me-2">
                                    <i class="bi bi-arrow-up me-1"></i>30%
                                </span>
                                <span class="text-nowrap text-xs text-muted">Since last month</span>
                            </div>  --}}
                        </div>
                    </div>
                    </a>
                </div>
            </div>
           
        </div>
    </main>
</div>
@endsection