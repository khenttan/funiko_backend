@extends('admin.admin')

@section('content')
    <div class="card  card-primary">
        <div class="card-header">
            <h3 class="card-title">List All Group</h3>

            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>

        <div class="card-body">
            @include('admin.partials.card.info')

            {{-- @include('admin.partials.card.buttons') --}}

            <table id="tbl-list" data-page-length="10" class="dt-table table table-sm table-bordered table-striped table-hover">
                <thead> 
                <tr>
                    <th>S. No.</th>
                    <th><i class="fa fa-fw fa-user text-muted"></i>Name/Title</th>
                    <th><i class="fa fa-fw fa-user text-muted"></i>Admin Username</th>
                    <th><i class="fa fa-fw fa-user text-muted"></i>Creation date</th>
                    <th><i class="fa fa-fw fa-mobile text-muted"></i>Group Icon</th>
                    <th><i class="fa fa-fw fa-mobile text-muted"></i>Status</th>

                    <th><i class="fa fa-fw fa-mobile text-muted"></i>Number of Members</th>

                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($items as $key=>$item)

                    <tr>
                        <td>{{ $key+1 }}</td>
                        <td>{{ $item['group_name'] ??'' }}</td>
                        <td>{{ $item['admin']['username']??'' }}</td>
                        <td> {{ Carbon\Carbon::parse($item['created_at'])->format('Y-m-d')}}</td>
                        @if($item['group_icon'] != null)
                        <td><img src="{{ $item['group_icon'] }}" width="100" height="60"></td>

                        @else
                        <td>No Image Uploaded</td>
                        @endif
                
                        <td>
                            <span class="badge badge-{{ $item['is_active']==config('globalConstant.activate') ? 'success':'danger' }}">{{ $item['is_active']==config('globalConstant.activate') ? 'Active':'Inactive' }}</span>
                        </td>   
                        <td>
                            {{$item['group_member_count'] }}
                        </td> 
                        <td>
                            {!! action_row($selectedNavigation->url, $item['id'], 'Members', ['show'], false) !!}
                            {{--
                            <!-- Code for impersonate-->        
                            @if($item->email_verified_at)
                                <div class="btn-group">
                                    <form id="impersonate-login-form-{{ $item->id }}" action="{{ route('impersonate.login', $item->id) }}" method="post">
                                        <input name="_token" type="hidden" value="{{ csrf_token() }}">
                                        <a data-form="impersonate-login-form-{{ $item->id }}" class="btn btn-warning btn-xs btn-confirm-modal-row" data-toggle="tooltip" title="Impersonate {{ $item->fullname }}">
                                            <i class="fa fa-unlock"></i>
                                        </a>
                                    </form>
                                </div>
                            @endif
                            --}}    
                           
                            <div class="dropdown">
                                <button class="btn btn-{{  $item['is_active']  == 1 ? 'success' : 'dark'  }} btn-sm  dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                {{  $item['is_active']  == 1 ? __('Defualt') : __('Set Defualt')  }}
                                </button>
                                <div class="dropdown-menu animated--fade-in" aria-labelledby="dropdownMenuButton">
                                <a class="dropdown-item" href="{{ route('back.group.status',[$item['id'],1]) }}">{{ __('Active') }}</a>
                                <a class="dropdown-item" href="{{ route('back.group.status',[$item['id'],0]) }}">{{ __('Deactive') }}</a>
                                </div>
                            </div>                        
                        </td>
                     
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="modal-status" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header alert-info">
                    <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close"> -->
                    <!--  <span aria-hidden="true">&times;</span></button> -->
                    <h4 class="modal-title">Update Status</h4>
                </div>
                <div class="modal-body">
                    <form id="form-modal-status">
                        <input type="hidden" name="id"/>
                        <p>Are you sure you want to <b id="statusText"> </b> the status for this account?</p>
                        
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel
                    </button>
                    <button id="modal-status-submit" type="button" class="btn btn-primary btn-ajax-submit" data-dismiss="modal">
                        Update Status
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    @parent
    <script type="text/javascript" charset="utf-8">
        $(function () {
            $('.dt-table').on('click', '.btn-modal-status-row', onUpdateStatusClick);
            function onUpdateStatusClick(e)
            {
                e.preventDefault();
                $('#modal-status').modal('show');
                $('#form-modal-status input[name="id"]').val($(this).attr('data-id'));
                if($(this).attr('data-status-id')=="{{config('globalConstant.activate')}}"){
                    $('#statusText').text('Deactivate');    
                }
                else{
                    $('#statusText').text('Activate');
                }
                return false;
            }

            // on submit click
            $('#modal-status-submit').on('click', function () {
                var transactionId = $('#form-modal-status input[name="id"]').val();
                $.ajax({
                  url: "/admin/accounts/clients/" + transactionId + "/status",  
                  method: "POST",
                  success: function(data) {
                       window.location.reload(true);
                  }
                });
            })
        })
    </script> 
@endsection
