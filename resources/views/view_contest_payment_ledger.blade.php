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
                            </a> 
                                <a href="{{ route('admin.edit.question',['id'=>$contest->id]) }}" class="btn d-inline-flex btn-sm btn-primary mx-1">
                                    <span class=" pe-2">
                                        <i class="bi bi-pen"></i>
                                    </span>
                                    <span>Edit</span>
                                </a>--}}
                            </div>
                        </div>
                    </div>
                    <!-- Nav -->
                    <ul class="nav nav-tabs mt-4 overflow-x border-0">
                         <li class="nav-item">
                            <a href="{{ route('admin.view.contest',['id'=>$contest->id]) }}" class="nav-link">Contest Details</a>
                        </li>
                        <li class="nav-item ">
                            <a href="{{route('admin.contest.leaderboard', ['id'=>$contest->id])}}" class="nav-link font-regular @if($page=='leaderboard')active @endif">Leader Board</a>
                        </li>
                        
                        <li class="nav-item">
                            <a href="{{route('admin.contest.payment.ledger', ['id'=>$contest->id])}}" class="nav-link font-regular @if($page=='paymentledger')active @endif">Payment Ledger</a>
                        </li>
                        {{-- <li class="nav-item">
                            <a href="{{route('admin.contest.withdraw.ledger', ['id'=>$contest->id])}}" class="nav-link font-regular @if($page=='withdrawledger')active @endif">Withdraw Ledger</a>
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
                                    <th scope="col">Entry Fees</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Contest Date</th>
                                </tr>
                            </thead>
                            <tbody>
                               
                                    
                                    <tr>
                                        <td>{{$contest->name}}</td>
                                        
                                        <td>
                                            <?=$contest->entry_fees;?> INR
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
                        <h5 class="mb-0">Apply Fiter</h5>
                    </div>
                    
                    <form class="px-5 py-2" method="get" action="">
                      <div class="row">
                          <div class="col-12 col-sm-4">
                              <div class="mb-3">
                                <label for="from" class="form-label">Start Date</label>
                                <input type="date" name="from" class="form-control" id="from" value="{{isset($_GET['from'])?$_GET['from']:''}}" >
                              </div>
                      
                          </div>
                          <div class="col-12 col-sm-4">
                              <div class="mb-3">
                                <label for="to" class="form-label">To Date</label>
                                <input type="date" name="to" class="form-control" id="from" value="{{isset($_GET['to'])?$_GET['to']:''}}">
                              </div>
                     
                          </div>
                          <div class="col-12 col-sm-4">
                              <div class="mb-3">
                                <label for="to" class="form-label">Users</label>
                                <select class="users-select form-select " aria-label="user select box" name="userid" style="width:100%; padding:5px">
                                  <option value="" >-Select-</option>
                                  @foreach($users as $user)
                                  <option {{isset($_GET['userid']) && $_GET['userid']==$user->id?'selected':''}} value="{{$user->id}}">{{$user->name}} - {{$user->phone}}</option>
                                  @endforeach
                                  
                                  
                                </select>
                              </div>
                     
                          </div>
                          <div class="col-12 align-items-center align-self-center">
                              
                               <div class="d-flex gap-2">
                                   <button type="submit" class="btn btn-primary mt-5">Apply</button>
                                   <a href="{{route(Route::current()->getName(),['id'=>$contest->id])}}" class="btn btn-neutral mt-5">Clear</a>
                               </div>
                          </div>
                      </div>
                    </form>
                </div> 
                
                @if($page=="paymentledger")
                <div class="card shadow border-0 mb-7">
                    <div class="card-header">
                        <h5 class="mb-0">Payment Ledger</h5>
                    </div>
                    <div class="table-responsive px-5 py-2">
                        <table class="table table-hover table-nowrap" id="dataTable">
                            <thead class="thead-light">
                                <tr>

                                    <th scope="col">Sno</th>
                                    <th scope="col">User Phone</th>
                                    <th scope="col">Payment ID</th>
                                    <th scope="col">Amount</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Mode</th>
                                    <th scope="col">Comment</th>
                                    <th>Date Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php 
                                    $sno=1;
                                    
                                    
                                @endphp
                                    @foreach ($payments as $single)
                                        
                                    
                                    <tr>
                                        <td>{{ $sno++ }}</td>
                                        <td>
                                            <a class="text-heading font-semibold" target="_blank" href="{{route('admin.view.user',['id'=>$single->user_id])}}">
                                                {{"@".$single->phone}}
                                            </a>
                                        </td>
                                        <td>{{ $single->payment_id  }}</td>
                                        <td>{{ $single->amount  }} INR</td>
                                        
                                        
                                       <td>
                                            @php
                                                if ($single->payment_status == 0) {
                                                    echo '<b>Incomplete</b>';
                                                } else {
                                                    echo "<span class='text-success'><b>Done</b></span>";
                                                }
                                            @endphp
                                        </td>
                                        <td>{{ $single->payment_mode  }}</td>
                                        <td>{{ $single->comment  }}</td>

                                        <td class="">
                                           {{date("d M Y h:i A", strtotime($single->created_at))}}
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
                
                @elseif($page=="withdrawledger")
                <div class="card shadow border-0 mb-7">
                    <div class="card-header">
                        <h5 class="mb-0">Withdraw Ledger</h5>
                    </div>
                    <div class="table-responsive px-5 py-2">
                        <table class="table table-hover table-nowrap" id="dataTable">
                            <thead class="thead-light">
                                <tr>

                                    <th scope="col">Sno</th>
                                    <th scope="col">User Phone</th>
                                    <th scope="col">Withdraw ID</th>
                                     <th scope="col">Withdraw Mode</th>
                                    <th scope="col">Amount</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Comment</th>
                                    <th>Date Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php 
                                    $sno=1;
                                    
                                    
                                @endphp
                                    @foreach ($data as $single)
                                        
                                    
                                    <tr>
                                        <td>{{ $sno++ }}</td>
                                        <td>
                                            <a class="text-heading font-semibold" target="_blank" href="{{route('admin.view.user',['id'=>$single->user_id])}}">
                                                {{"@".$single->phone}}
                                            </a>
                                        </td>
                                        <td>{{ $single->withdraw_id  }}</td>
                                        <td>{{ $single->withdraw_mode  }} INR</td>
                                        <td>{{ $single->amount  }} INR</td>
                                        
                                        
                                       <td>
                                            @php
                                                if ($single->withdraw_status == 0) {
                                                    echo '<b>Incomplete</b>';
                                                } else {
                                                    echo "<span class='text-success'><b>Done</b></span>";
                                                }
                                            @endphp
                                        </td>
                                        <td>{{ $single->comment  }}</td>

                                        <td class="">
                                           {{date("d M Y h:i A", strtotime($single->created_at))}}
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
                
                @elseif($page=="leaderboard")
                <div class="card shadow border-0 mb-7">
                    <div class="card-header">
                        <h5 class="mb-0">Leader Board</h5>
                    </div>
                    <div class="table-responsive px-5 py-2">
                        <table class="table table-hover table-nowrap" id="dataTable">
                            <thead class="thead-light">
                                <tr>

                                    <th scope="col">Sno</th>
                                    
                                    <th scope="col">User Phone</th>
                                    <th scope="col">Final Marks</th>
                                    <th scope="col">Time Taken (H:m:s)</th>
                                    <th scope="col">Start Time</th>
                                    <th scope="col">Negative Questions</th>
                                    <th scope="col">Ranks</th>
                                    <th scope="col">Is Winner</th>
                                    @if($contest->type=3)
                                        <th scope="col">Slot No.</th>
                                    @endif
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="leaderBoard">
                                @php 
                                    $sno=1;
                                    
                                    
                                @endphp
                                    @foreach ($data as $single)
                                    
                                    <tr>
                                        <td>{{ $sno++ }}</td>
                                        <td>
                                            <a class="text-heading font-semibold" target="_blank" href="{{route('admin.view.user',['id'=>$single->user_id])}}">
                                                {{"@".$single->phone}}
                                            </a>
                                        </td>
                                        <td>{{ $single->user_final_marks  }}</td>
                                        <?php 
                                            $start=$single->contest_start_date_time;
                                            $end=$single->contest_end_date_time;
                                            
                                            $datetime1 = new DateTime($start); // start time
                                            $datetime2 = new DateTime($end); // end time
                                            $interval = $datetime1->diff($datetime2);
                                        ?>
                                        <td>{{ $interval->format('%H : %i : %s');  }} (H:m:s)</td>
                                        <td>{{ $start }}</td>
                                        <td>
                                            {{ $single->negative_question_count  }}
                                        </td>
                                        <td>
                                            {{ $single->rank_in_this_contest  }}
                                        </td>
                                        <td>
                                            {{ $single->is_winner=1?"Yes":"No"  }}
                                        </td>
                                        <td>
                                            {{ $single->slot_id }}
                                        </td>

                                        <td class="">
                                           <a href="#" class="btn btn-primary btn-sm">Edit</a>
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
                
                @endif

               
            </div>
        </main>
    </div>
@endsection

@push("js")
<script>
        let table = new DataTable('#dataTable');
        
        $(document).ready(function() {
            $select=$('.users-select').select2();
            // $select.data('select2').$container.addClass('form-control');
        });
</script>
{{--
@if($page=="leaderboard")
<script>
    
    $.ajax({
        url: "{{route('admin.contest.leaderboard.api',['id'=>$contest->id])}}",
        success: function (response){
            let responseObj=JSON.parse(response);
            
            if(responseObj.success){
                if(responseObj.data.length>0){
                    let data =responseObj.data;
                    let sno = 1;
                    let str="";
                    data.map((ele) => {
                        
                        str += `
                        <tr>
                            <td>${sno++}</td>
                            <td>
                                <a class="text-heading font-semibold" target="_blank" href="{{route('admin.view.user',['id'=>${ele["phone"]}])}}">
                                    {{"@".${ele["phone"]} }}
                                </a>
                            </td>
                            <td>${ele["user_final_marks"]}</td>
                            
                            <td>${ele["contest_start_date_time"]} (H:m:s)</td>
                            <td>${ele["contest_start_date_time"]}</td>
                            <td>
                                ${ele["negative_question_count"]}
                            </td>
                            <td>
                                ${ele["rank_in_this_contest"]}
                            </td>
                            <td>
                                ${ele["is_winner"]==1?'Yes':'no'}
                
                            <td class="">
                                <a href="#" class="btn btn-primary btn-sm">Edit</a>
                            </td>
                        </tr>
                        
                        `;
                    });
                
                    $("#leaderBoard").html(str);
                }
            }
        },
        error: function (error){
            console.log(error);
        }
    })
</script>
@endif --}}
@endpush