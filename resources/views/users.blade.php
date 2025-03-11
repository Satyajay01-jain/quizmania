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
                                <!--<button data-bs-toggle="modal" data-bs-target="#newModal" class="btn d-inline-flex btn-sm btn-primary mx-1">-->
                                <!--    <span class=" pe-2">-->
                                <!--        <i class="bi bi-plus"></i>-->
                                <!--    </span>-->
                                <!--    <span>Create</span>-->
                                <!--</button>-->
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
                                        <span class="h6 font-semibold text-muted text-sm d-block mb-2">Total</span>
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
                    <div class="table-responsive p-2">
                        <table class="table table-hover table-nowrap" id="usersTable">
                            <thead class="thead-light">
                                <tr>

                                    <th scope="col">Sno</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Created On</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $sno=1;@endphp
                                @foreach ($allData as $single)
                                    <tr>
                                        <td>{{ $sno++ }}</td>
                                       
                                        <td>
                                            {{$single->name}}
                                        </td>
                                        <td>
                                            {{$single->email}}
                                        </td>
                                        <td>
                                            @php
                                                if ($single->status == 0) {
                                                    echo '<b>Inactive</b>';
                                                } else {
                                                    echo "<span class='text-success'><b>Active</b></span>";
                                                }
                                            @endphp
                                        </td>
                                        <td>
                                            {{date('d M Y h:i A',strtotime($single->created_at))}}
                                        </td>

                                        <td class="">
                                       
                                        @if($single->role!=2)
                                            @if ($single->status == 1)
                                                <button class="btn btn-sm btn-danger" data-id="{{$single->id}}" data-name="{{$single->name}}"  data-status="{{$single->status}}" onclick="openEditModal(this)" > 
                                                Block User
                                                </button>
                                            @else
                                            <button class="btn btn-sm btn-primary" data-id="{{$single->id}}" data-name="{{$single->name}}"  data-status="{{$single->status}}" onclick="openEditModal(this)" > 
                                            Unblock User
                                            </button>
                                            @endif
                                        @else
                                        Action not allowed
                                        @endif
                                            <button data-userId="{{ $single->id }}" class="btn btn-danger d-inline-flex btn-sm mx-1" onclick="deleteUser(this)">
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
                    </div>
                    <div class="card-footer border-0 py-5">
                        
                    </div>
                </div>
            </div>
        </main>
    </div>
@endsection
@push("modal")


<div class="modal fade" id="alertModal" tabindex="-1" aria-labelledby="editModal" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-danger" id="editModal">Alert !</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="post" action="{{route('admin.block.user')}}" >
          @csrf
      <div class="modal-body">
        <input type="hidden" name="id" id="userId">
        <input type="hidden" name="status" id="current_status">
        <h5 class="" id="alertMessage"></h5>
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
        <button type="submit" class="btn btn-primary">Yes</button>
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
        let status=button.getAttribute('data-status');
        let alertMessage="";
        if(status==1){
            alertMessage="Are you sure, you want to block the user - "+name;
        }else{
            alertMessage="Are you sure, you want to unblock the user - "+name;
        }
        
        $("#userId").val(id);
        $("#current_status").val(status);
        $("#alertMessage").text(alertMessage);
        $("#alertModal").modal("show");
    }
    
    function deleteUser(e) {
        // console.log(e.parentElement.parentElement);
        if (confirm('Are you sure you want to delete this contest?')) {
            var userId = e.getAttribute("data-userId");
            $.ajax({
                url: 'delete/user/' + userId, // Include the ID in the URL
                method: 'POST',
                data: {
                    id: userId, // Pass the ID in the data object
                    _token: '{{ csrf_token() }}' // Correct way to pass CSRF token
                },
                success: function(response) {
                    if (response.status) {
                        e.parentElement.parentElement.remove();
                    } else {
                        alert('User is not deleted');
                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText); // Log any errors to the console
                }
            });
        } else {
            alert('User is not deleted');
        }

    }
</script>

<script>
        let table = new DataTable('#usersTable');
    </script>
@endpush
