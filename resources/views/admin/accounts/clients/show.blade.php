@extends('admin.admin')

@section('content')

<div class="container">
    <div class="main-body">
    
          <!-- Breadcrumb -->
          <nav aria-label="breadcrumb" class="main-breadcrumb">
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="index.html">Home</a></li>
              <li class="breadcrumb-item"><a href="javascript:void(0)">User</a></li>
              <li class="breadcrumb-item active" aria-current="page">User Profile</li>
            </ol>
          </nav>
          <!-- /Breadcrumb -->
    
          <div class="row gutters-sm">
            <div class="col-md-4 mb-3">
              <div class="card">
                <div class="card-body">
                  <div class="d-flex flex-column align-items-center text-center">
                    <img src="{{ $item['image'] }}" alt="Admin" class="rounded-circle" width="150">
                    <div class="mt-3">
                      <h4>{{ $item['username'] }}</h4>
                      <p class="text-muted font-size-sm">Address:- {{ $item['city']['name'] ?? '' }}, {{ $item['state']['name'] ?? '' }}, {{ $item['country']['name'] ?? '' }}</p>
                  
                    </div>
                  </div>
                </div>
              </div>
              <div class="card mt-3">
                <ul class="list-group list-group-flush">
                  <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                    <h6 class="mb-0">Full Name</h6>
                    <span class="text-secondary"> {{ $item['firstname']." " }}  {{ $item['lastname'] }}</span>
                  </li>
                  <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                    <h6 class="mb-0">Email</h6>
                    <span class="text-secondary"> {{ $item['email'] != null ? $item['email'] : "Not provided" }}</span>
                  </li>
                {{-- {{ dd($item['cellphone']) }} --}}
                  <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                    <h6 class="mb-0">Mobile</h6>
                    <span class="text-secondary">{{ $item['dial_code'] }}{{ $item['cellphone'] }} </span>
                  </li>
                  <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                    <h6 class="mb-0">Bio</h6>
                    <span class="text-secondary">  {{ $item['bio'] ?? '' }}
                    </span>
                  </li>
                  <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                    <h6 class="mb-0">Interest</h6>
                    @php
                    $interest_category = array_column($item['interest'], 'interest_category');  
                    $interest_name =  array_column($interest_category, 'name');  
                    $interest_name         = implode(',',$interest_name)
                  @endphp
                    <span class="text-secondary">  {{  $interest_name  }}</span>
                  </li>
                  <li class="list-group-item myfollwer d-flex justify-content-between align-items-center flex-wrap">
                    <h6 class="mb-0">Followers Number</h6>
                    <span class="text-secondary"> {{ count($item['myfollwer']) }}
                    </span>
                  </li>
                  <ul class="list-group list_follower" style="display: none">
                    @foreach ($item['myfollwer'] as  $my_followoing)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                       <a href="/admin/accounts/clients/{{ $my_followoing['follower']['id']}}">
                        <img src=" {{ $my_followoing['follower']['image'] }}" height="50" width="50"  class="user-image img-circle elevation-2" alt="User Image">

                        {{ $my_followoing['follower']['username'] }} </a>
                       {{-- <span class="badge badge-primary badge-pill">14</span> --}}
                     </li>
                   @endforeach
                 </ul>

                 <li class="list-group-item myfollwing d-flex justify-content-between align-items-center flex-wrap">
                  <h6 class="mb-0">Followings Number</h6>
                  <span class="text-secondary"> {{ count($item['my_following']) }}
                  </span>
                </li>
                <ul class="list-group list_following" style="display: none">
                  @foreach ($item['my_following'] as  $my_follow)
                  <li class="list-group-item d-flex  justify-content-between align-items-center">
                    
                    <a href="/admin/accounts/clients/{{ $my_follow['following']['id']}}">
                      
                      <img src=" {{ $my_follow['following']['image'] }}" height="50" width="50"  class="user-image img-circle elevation-2" alt="User Image">

                      {{ $my_follow['following']['username'] }} </a>
                    
                    {{-- <span class="badge badge-primary badge-pill">14</span> --}}
                   </li>
                  @endforeach
                </ul>
              </div>
            </div>
            <div class="col-md-8">
              
              <div class="row gutters-sm">
                @foreach($item['total_post'] as $key => $video_data)
                <div class="col-sm-4 mb-2">
                  <div class="card h-100">
                    <div class="card-body">
                      <h6 class="d-flex align-items-center mb-3"><i class="material-icons text-info mr-2"></i>{{$video_data['video_title']}}</h6>
                      <small></small>
                      <figure id="video-viewport" style="max-width: 315px; max-height: 177px;">
                        <video id="video-{{$video_data['id']}}" width="200px" height="100px" preload="auto" controls muted="" src="{{$video_data['video']}}">
                            <source src="{{$video_data['video']}}" type="video/mp4">
                        </video>
                    </figure>
                    <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                      <h6 class="mb-0">Total Views</h6>
                      <span class="text-secondary"> {{is_array($video_data['post_view']) && count($video_data['post_view']) > 0 ? $video_data['post_view'][0]['post_count'] : "0" }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                      <h6 class="mb-0">Total Comments</h6>
                      <span class="text-secondary">{{ $video_data['comments_count'] }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                      <h6 class="mb-0">Total Likes</h6>
                      <span class="text-secondary">{{ $video_data['post_like_count'] }}  </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                      <h6 class="mb-0">Total Share</h6>
                      <span class="text-secondary">{{ $video_data['post_share_count'] }}  </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                      @php
                        if(count($video_data['tags']) > 0){
                         $tags_name =  array_column($video_data['tags'],'tags');
                         $tags_name =  implode(",",$tags_name);
                        }else{
                          $tags_name = "";
                        }
                      @endphp
                      <p><b>{{ $tags_name }}</b></p>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                      <div class="row-fluid">
                        @php
                        if($video_data['is_visible'] == "0" ){
                          $visibility = 1;
                        }else{
                          $visibility = 0;
                        } 
                        @endphp
                        <a href="{{ route('back.client_video.status',[$video_data['id'],$visibility]) }}" class="btn {{ $video_data['is_visible'] == 1 ? "btn-secondary" : "btn-primary" }} " role="button" aria-disabled="true"> {{ $video_data['is_visible'] == 1 ? "Deactive" : "Active" }}</a>
                        <a href="{{ route('back.client_video.delete',$video_data['id']) }}" class="btn btn-danger" role="button" aria-disabled="true">Delete</a>
                        </div>
                    </li>
                    </div>
                  </div>
                </div>
                @endforeach
                
              </div>
            </div>
            
          </div>

        </div>
    </div>
    @endsection

    {{ $apps->links() }}

@section('scripts')
@parent

<script type="text/javascript" charset="utf-8">
    $(document).ready(function(){
            $(".myfollwing").click(function(){
                    $(".list_following").toggle();
            });
             $(".myfollwer").click(function(){
                $(".list_follower").toggle();
            });
    });
</script>


@endsection