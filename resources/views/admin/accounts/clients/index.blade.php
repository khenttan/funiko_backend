@extends('admin.admin')

@section('content')
    <div class="card  card-primary">
        <div class="card-header">
            <h3 class="card-title">List All Clients</h3>

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
                    <th><i class="fa fa-fw fa-user text-muted"></i>Full Name</th>
                    <th><i class="fa fa-fw fa-user text-muted"></i>User Name</th>
                    <th><i class="fa fa-fw fa-user text-muted"></i>Profile Icon</th>

                    <th><i class="fa fa-fw fa-mobile text-muted"></i>Registration Date</th>
                    <th><i class="fa fa-fw fa-mobile text-muted"></i>Location</th>
                    <th><i class="fa fa-fw fa-mobile text-muted"></i>Gender</th>
                    <th><i class=""></i>Status</th>

                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($items as $key=>$item)
                    @if(!$item->isAdmin())
                    <tr>
                        <td>{{ $key+1 }}</td>
                        <td>{{ $item->fullname??'' }}</td>
                        <td>{{ $item->username??'' }}</td>
                        @if($item->image != null)
                        <td><img src="{{ $item->image }}" width="100" height="60"></td>
                        @else
                        <td>  No Image Uploaded </td>
                        @endif
                        <td>{{$item->created_at }}</td>
                        @php
                        $item_array = $item->toArray();
                         $citynamed    = $item_array['city']['name'] ??  '';
                        @endphp
                        <td>{{$citynamed }}</td>
                        <td>{{$item->gender }}</td>
                        <td>
                            <span class="badge badge-{{ $item->status==config('globalConstant.activate') ? 'success':'danger' }}">{{ $item->status==config('globalConstant.activate') ? 'Active':'Inactive' }}</span>
                        </td>   
                        <td>
                            {!! action_row($selectedNavigation->url, $item->id, $item->fullname, ['show'], false) !!}
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
                           
                            <a data-id="{{ $item->id }}"  data-status-id="{{ $item->status }}"   class="btn btn-info btn-xs btn-modal-status-row" data-toggle="tooltip" title="Update Status"><i class="fa fa-{{ $item->status==config('globalConstant.activate') ? 'unlock':'lock' }}"></i></a>
                        
                        </td>
                     
                    </tr>
                    @endif
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
