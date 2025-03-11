<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quizmania - Knowledge Bank</title>
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <link rel="stylesheet" href="{{ url('/resources/css/common.css') }}">
    <link rel="stylesheet" href="https://unpkg.com/@webpixels/css@1.1.5/dist/index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.4.0/font/bootstrap-icons.min.css">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
    
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    
    <style>
        .outline-none{
            outline: none !important;
        }
        .outline-none:focus{
            outline: none !important;
        }
        
        .sidebar li .submenu{ 
        	list-style: none; 
        	margin: 0; 
        	padding: 0; 
        	padding-left: 1rem; 
        	padding-right: 1rem;
        }
        .active2{
            background-color: #f5f9fc;
        }
    </style>
    @stack('css')
</head>

<body>
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
    <!-- Dashboard -->
    <div class="d-flex flex-column flex-lg-row h-lg-full bg-surface-secondary">
        <!-- Vertical Navbar -->
        <nav class="navbar show navbar-vertical h-lg-screen navbar-expand-lg px-0 py-3 navbar-light bg-white border-bottom border-bottom-lg-0 border-end-lg"
            id="navbarVertical" style="flex-shrink: 0;">
            <div class="container-fluid">
                <!-- Toggler -->
                <button class="navbar-toggler ms-n2" type="button" data-bs-toggle="collapse"
                    data-bs-target="#sidebarCollapse" aria-controls="sidebarCollapse" aria-expanded="false"
                    aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <!-- Brand -->
                <a class="navbar-brand py-lg-2 mb-lg-5 px-lg-6 me-0 d-flex gap-2" href="#">
                     <img src="{{asset('public/assets/images/qm_logo_black.svg')}}" alt="...">
                </a>
                <!-- User menu (mobile) -->
                <div class="navbar-user d-lg-none">
                    <div class="dropdown">
                        <!-- Toggle -->
                        <a href="#" id="sidebarAvatar" role="button" data-bs-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">
                            <div class="avatar-parent-child">
                               
                                <img alt="Image Placeholder"
                                    src="{{asset('public/assets/images/qm_logo_black.svg')}}"
                                    class="avatar avatar- rounded-circle">
                                <span class="avatar-child avatar-badge bg-success"></span>
                            </div>
                        </a>
                        <!-- Menu -->
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="sidebarAvatar">
                            
                            <a href="{{route('admin.users')}}" class="dropdown-item">Users</a>
                            <hr class="dropdown-divider">
                            <a href="{{route('logout')}}" class="dropdown-item">Logout</a>
                        </div>
                    </div>
                </div>

                <!-- Collapse -->
                <div class="collapse navbar-collapse sidebar " id="sidebarCollapse">
                    <!-- Navigation -->
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link <?php echo $page=="dashboard" ?'active  active2':''?>" href="{{route('admin.dashboard')}}">
                                <i class="bi bi-house"></i> Dashboard
                            </a>
                        </li>
                        
                        <li class="nav-item has-submenu">
                    		<a class="nav-link <?php echo $page=="questions" ?'active  active2':''?> d-flex justify-content-between" href="#">
                    		    <span><i class="bi bi-bar-chart" style=""></i>&nbsp;&nbsp;&nbsp; Manage Question</span>
                    		    <span><i class="bi bi-chevron-down"></i></span>
                    		</a>
                    		<ul class="submenu collapse">
                    			<li class="nav-item">
                                    <a class="nav-link <?php echo $page=="questions" ?'active  active2':''?>" href="{{route('admin.questions')}}">
                                        <i class="bi bi-bar-chart"></i> Questions
                                    </a>
                                </li>
                    			<li class="nav-item">
                                    <a class="nav-link <?php echo $page=="newquestion" ?'active  active2':''?>" href="{{route('new.question')}}">
                                        <i class="bi bi-plus"></i> Questions
                                    </a>
                                </li>
                    		</ul>
                    	</li>

                        
                        <li class="nav-item has-submenu">
                    		<a class="nav-link <?php echo $page=="contests" ?'active  active2':''?> d-flex justify-content-between" href="#">
                    		    <span><i class="bi bi-bar-chart"></i>&nbsp;&nbsp;&nbsp; Manage Contests</span>
                    		    <span><i class="bi bi-chevron-down"></i></span>
                    		</a>
                    		<ul class="submenu collapse">
                    			<li class="nav-item">
                                    <a class="nav-link <?php echo $page=="contests" ?'active  active2':''?>" href="{{route('admin.contests')}}">
                                        <i class="bi bi-bar-chart"></i> Contests
                                    </a>
                                </li>
        
                                <li class="nav-item">
                                    <a class="nav-link <?php echo $page=="newlivecontest" ?'active  active2':''?>" href="{{route('admin.new.live.contest')}}">
                                        <i class="bi bi-plus"></i> Live Contest
                                    </a>
                                </li>
        
                                <li class="nav-item">
                                    <a class="nav-link <?php echo $page=="newtimelimitcontest" ?'active  active2':''?>" href="{{route('admin.new.timelimit.contest')}}">
                                        <i class="bi bi-plus"></i> Time Limit Contest
                                    </a>
                                </li>
        
                                <li class="nav-item">
                                    <a class="nav-link <?php echo $page=="newanytimecontest" ?'active  active2':''?>" href="{{route('admin.new.anytime.contest')}}">
                                        <i class="bi bi-plus"></i> Challenges 
                                    </a>
                                </li>

                    		</ul>
                    	</li>
                    	
                    	<li class="nav-item has-submenu">
                    		<a class="nav-link d-flex justify-content-between" href="#">
                    		    <span><i class="bi bi-bar-chart"></i>&nbsp;&nbsp;&nbsp; Master</span>
                    		    <span><i class="bi bi-chevron-down"></i></span>
                    		</a>
                    		<ul class="submenu collapse">
                    			<li class="nav-item">
                                    <a class="nav-link <?php echo $page=="topics" ?'active active2':''?>" href="{{route('admin.topics')}}">
                                        <i class="bi bi-book"></i> Topics
                                    </a>
                                </li>
                                
                                <li class="nav-item">
                                    <a class="nav-link <?php echo $page=="education" ?'active active2':''?>" href="{{route('admin.education')}}">
                                        <i class="bi bi-house"></i> Education
                                    </a>
                                </li>
                                
                                <li class="nav-item">
                                    <a class="nav-link <?php echo $page=="profession" ?'active active2':''?>" href="{{route('admin.profession')}}">
                                        <i class="bi bi-people"></i> Profession
                                    </a>
                                </li>
                                
                                <li class="nav-item">
                                    <a class="nav-link <?php echo $page=="state" ?'active active2':''?>" href="{{route('admin.state')}}">
                                        <i class="bi bi-globe"></i> State
                                    </a>
                                </li>
                                
                                <li class="nav-item">
                                    <a class="nav-link <?php echo $page=="city" ?'active active2':''?>" href="{{route('admin.city')}}">
                                        <i class="bi bi-globe"></i> City
                                    </a>
                                </li>
                                
                                
                                 <li class="nav-item">
                                    <a class="nav-link <?php echo $page=="faqs" ?'active active2':''?>" href="{{route('admin.faq')}}">
                                        <i class="bi bi-question-diamond"></i> FAQ's
                                    </a>
                                </li>

                    		</ul>
                    	</li>

                        
                        
                        {{-- <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="bi bi-chat"></i> Messages
                                <span
                                    class="badge bg-soft-primary text-primary rounded-pill d-inline-flex align-items-center ms-auto">6</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="bi bi-bookmarks"></i> Collections
                            </a>
                        </li> --}}
                        
                        
                        <li class="nav-item">
                            <a class="nav-link <?php echo $page=="users" ?'active  active2':''?>" href="{{route('admin.users')}}">
                                <i class="bi bi-people"></i> Users
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $page=="users" ?'active  active2':''?>" href="{{route('admin.financial-reports')}}">
                                <i class="bi bi-receipt"></i> Financial Reports
                            </a>
                        </li>
                    </ul>
                    <!-- Divider -->
                    <hr class="navbar-divider my-5 opacity-20">
                    <!-- Navigation -->
                    {{-- <ul class="navbar-nav mb-md-4">
                        <li>
                            <div class="nav-link text-xs font-semibold text-uppercase text-muted ls-wide"
                                href="#">
                                Contacts
                                <span
                                    class="badge bg-soft-primary text-primary rounded-pill d-inline-flex align-items-center ms-4">13</span>
                            </div>
                        </li>
                        <li>
                            <a href="#" class="nav-link d-flex align-items-center">
                                <div class="me-4">
                                    <div class="position-relative d-inline-block text-white">
                                        <img alt="Image Placeholder"
                                            src="https://images.unsplash.com/photo-1548142813-c348350df52b?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=3&w=256&h=256&q=80"
                                            class="avatar rounded-circle">
                                        <span
                                            class="position-absolute bottom-2 end-2 transform translate-x-1/2 translate-y-1/2 border-2 border-solid border-current w-3 h-3 bg-success rounded-circle"></span>
                                    </div>
                                </div>
                                <div>
                                    <span class="d-block text-sm font-semibold">
                                        Marie Claire
                                    </span>
                                    <span class="d-block text-xs text-muted font-regular">
                                        Paris, FR
                                    </span>
                                </div>
                                <div class="ms-auto">
                                    <i class="bi bi-chat"></i>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="nav-link d-flex align-items-center">
                                <div class="me-4">
                                    <div class="position-relative d-inline-block text-white">
                                        <span class="avatar bg-soft-warning text-warning rounded-circle">JW</span>
                                        <span
                                            class="position-absolute bottom-2 end-2 transform translate-x-1/2 translate-y-1/2 border-2 border-solid border-current w-3 h-3 bg-success rounded-circle"></span>
                                    </div>
                                </div>
                                <div>
                                    <span class="d-block text-sm font-semibold">
                                        Michael Jordan
                                    </span>
                                    <span class="d-block text-xs text-muted font-regular">
                                        Bucharest, RO
                                    </span>
                                </div>
                                <div class="ms-auto">
                                    <i class="bi bi-chat"></i>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="nav-link d-flex align-items-center">
                                <div class="me-4">
                                    <div class="position-relative d-inline-block text-white">
                                        <img alt="..."
                                            src="https://images.unsplash.com/photo-1610899922902-c471ae684eff?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=3&w=256&h=256&q=80"
                                            class="avatar rounded-circle">
                                        <span
                                            class="position-absolute bottom-2 end-2 transform translate-x-1/2 translate-y-1/2 border-2 border-solid border-current w-3 h-3 bg-danger rounded-circle"></span>
                                    </div>
                                </div>
                                <div>
                                    <span class="d-block text-sm font-semibold">
                                        Heather Wright
                                    </span>
                                    <span class="d-block text-xs text-muted font-regular">
                                        London, UK
                                    </span>
                                </div>
                                <div class="ms-auto">
                                    <i class="bi bi-chat"></i>
                                </div>
                            </a>
                        </li>
                    </ul> --}}
                    <!-- Push content down -->
                    <div class="mt-auto"></div>
                    <!-- User (md) -->
                    <ul class="navbar-nav">
                        {{--<li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="bi bi-person-square"></i> Account
                            </a>
                        </li>--}}
                        <li class="nav-item">
                            <a class="nav-link" href="{{route('logout')}}">
                                <i class="bi bi-box-arrow-left"></i> Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <!-- Main content -->
        @yield('content')
    </div>
    
    @stack("modal")

    

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>

 <script src="//cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
 <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
 
 <script>
     document.addEventListener("DOMContentLoaded", function(){
  document.querySelectorAll('.sidebar .nav-link').forEach(function(element){
    
    element.addEventListener('click', function (e) {

      let nextEl = element.nextElementSibling;
      let parentEl  = element.parentElement;	

        if(nextEl) {
            e.preventDefault();	
            let mycollapse = new bootstrap.Collapse(nextEl);
            
            if(nextEl.classList.contains('show')){
              mycollapse.hide();
            } else {
                mycollapse.show();
                // find other submenus with class=show
                var opened_submenu = parentEl.parentElement.querySelector('.submenu.show');
                // if it exists, then close all of them
                if(opened_submenu){
                  new bootstrap.Collapse(opened_submenu);
                }
            }
        }
    }); // addEventListener
  }) // forEach
}); 
 </script>
@stack('js')
</body>

</html>
