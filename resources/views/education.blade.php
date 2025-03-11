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
                                <button data-bs-toggle="modal" data-bs-target="#newModal" class="btn d-inline-flex btn-sm btn-primary mx-1">
                                    <span class=" pe-2">
                                        <i class="bi bi-plus"></i>
                                    </span>
                                    <span>Create</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- Nav -->
                    <ul class="nav nav-tabs mt-4 overflow-x border-0">
                        
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
                                        <span class="h6 font-semibold text-muted text-sm d-block mb-2">All</span>
                                        <span class="h3 font-bold mb-0">{{ $allDataCount }}</span>
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
                                    <th scope="col">Name</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $sno=1;@endphp
                                @foreach ($allData as $data)
                                    <tr>
                                        <td>{{ $sno++ }}</td>
                                       
                                        <td>
                                            {{$data->name}}
                                        </td>

                                        <td class="">
                                          <button class="btn btn-sm btn-primary" data-id="{{$data->id}}" data-name="{{$data->name}}"  onclick="openEditModal(this)"><i class="bi bi-pen"></i> Edit</button>
                                            
                                        </td>
                                    </tr>
                                @endforeach


                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer border-0 py-5">
                        
                    </div>
                </div>
            </div>
        </main>
    </div>
@endsection
@push("modal")
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editTopicModal" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Education</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="post" action="{{route('post_edit_education')}}">
          @csrf
      <div class="modal-body">
         <input type="hidden" class="form-control"  name="id" id="editid" aria-describedby="id" required>
          <div class="mb-3">
            <label for="editName" class="form-label">Education Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control"  name="name" id="editName" aria-describedby="name" required>
          </div>
          
           <!--<div class="mb-3">-->
           <!--     <label for="editstatus" class="form-label">Status <span class="text-danger">*</span></label>-->
                
           <!--     <select class="form-select" id="editstatus" name="status" required>-->
           <!--         <option value="1">Active</option>-->
           <!--         <option value="0">Inactive</option>-->
           <!--     </select>-->
           <!-- </div>-->
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Save</button>
      </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="newModal" tabindex="-1" aria-labelledby="newModal" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editTopicModal">New Education</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="post" action="{{route('post_new_education')}}" >
          @csrf
      <div class="modal-body">
        
          <div class="mb-3">
            <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control" id="name" aria-describedby="name" required>
          </div>
          
           <!--<div class="mb-3">-->
           <!--     <label for="status" class="form-label">Status <span class="text-danger">*</span></label>-->
                
           <!--     <select class="form-select" id="status" name="status" required>-->
           <!--         <option value="1">Active</option>-->
           <!--         <option value="0">Inactive</option>-->
           <!--     </select>-->
           <!-- </div>-->
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Create</button>
      </div>
      </form>
    </div>
  </div>
</div>
@endpush

@push("js")
<script>
    function openEditModal(button){
        let id=button.getAttribute('data-id');
        let name=button.getAttribute('data-name');
        // let status=button.getAttribute('data-status');
        $("#editid").val(id);
        $("#editName").val(name);
        // $("#editstatus").val(status);
        $("#editModal").modal("show");
    }
</script>
@endpush
