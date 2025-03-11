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
                <p>Last Updated:- <b>{{date("d M Y h:i A", strtotime($allData->updated_at))}}</b></p>
                <div class="card shadow border-0 mb-7">
                    <div class="card-header">
                        <h5 class="mb-0">Details</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover table-nowrap" >
                            <thead class="thead-light">
                                <tr>

                                    <th scope="col">Sno</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Phone</th>
                                    <th scope="col">Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php 
                                    $sno=1;
                                @endphp
                                
                                    <tr>
                                        <td>{{ $sno++ }}</td>
                                        <td style="flex-shrink: 0">
                                            {{$allData->name}}
                                        </td>
                                        <td>
                                           {{$allData->email}}
                                        </td>
                                        <td>
                                           {{$allData->phone}}
                                        </td>
                                        <td>
                                            @php
                                                if ($allData->status == 0) {
                                                    echo '<b>Inactive</b>';
                                                } else {
                                                    echo "<span class='text-success'><b>Active</b></span>";
                                                }
                                            @endphp
                                        </td>


                                        <td class="">
                                           {{date("d M Y h:i A", strtotime($allData->created_at))}}
                                        </td>
                                    </tr>
                               
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

