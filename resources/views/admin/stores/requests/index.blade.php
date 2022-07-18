@extends('admin.admin')

@section('content')

    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">
                <span>List All Seller Requests For Stores</span>
            </h3>
        </div>

        <div class="card-body">

            @include('admin.partials.card.info')

           
            <table id="tbl-list-new" data-server="false" class="dt-table table table-striped table-bordered table-sm" cellspacing="0" width="100%">
                <thead>
                <tr>
                    <th>S No.</th>
                    <th>Shop Name</th>
                    <th>Bio</th>
                    <th>Seller Name</th>
                    <th>Created At</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($items as $key=>$item)
                    <tr>
                        <td>{{$key+1}}</td>
                        <td>{{$item->shop_name??''}}</td> 
                        <td>{{$item->shop_description??''}}</td> 
                        <td> {{ userDetailsById($item->user_id)??''}}</td> 
                        <td>{{ format_date($item->created_at) }}</td>
                        <td>
                            <span class="badge badge-{{$item->is_approved == config('globalConstant.shop_accepted') ? 'success' : ( $item->is_approved== config('globalConstant.shop_rejected') ? 'danger' : 'warning')}}">
                            {{$item->is_approved == config('globalConstant.shop_accepted') ? 'Accepted' : ( $item->is_approved== config('globalConstant.shop_rejected') ? 'Rejected' : 'Pending')}}
                            </span>
                        </td>
                        <td style="display:flex;">  
                            {!! action_row($selectedNavigation->url, $item->id, $item->shop_name, ['show', 'edit', 'delete']) !!}
                            <button style="margin-left:4px; {{ $item->is_approved == config('globalConstant.shop_rejected') ? 'display: none;' : ''}}" title="Reject" data-id="{{ $item->id }}"  data-status-id="{{config('globalConstant.shop_rejected')}}"   class="btn btn-info btn-xs btn-modal-status-row"><i class="fas fa-times-circle"></i></button>
                            <button style="margin-left:4px; {{ ($item->is_approved == config('globalConstant.shop_rejected'))||($item->is_approved == config('globalConstant.shop_accepted')) ? 'display: none;' : ''}} " title="Accept" data-id="{{ $item->id }}"  data-status-id="{{config('globalConstant.shop_accepted')}}"   class="btn btn-info btn-xs btn-modal-status-row"><i class="fa fa-check"></i></button>
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
                    <form id="form-modal-status-store">
                        <input type="hidden" name="id"/>
                        <input type="hidden" name="status"/>
                        <p>Are you sure you want to <b id="statusText"> </b>  the shop request for this account?</p>
                        <textarea class="form-control" name="reply" id="reply" rows="3" placeholder="Please provide a reason here!" style="display: none;"></textarea>
                        <span id="reply-error" style="display: none; color:red;">Please enter a reason</span>
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
                $('#form-modal-status-store input[name="id"]').val($(this).attr('data-id'));
                if($(this).attr('data-status-id')=="{{config('globalConstant.shop_accepted')}}" ){
                    $('#statusText').text('Accept');    
                    $('#reply').hide();
                    $('#reply-error').hide();
                    $('#form-modal-status-store input[name="status"]').val("{{config('globalConstant.shop_accepted')}}");
                }
                else{
                    $('#statusText').text('Reject');
                    $('#reply').show();
                    $('#reply-error').hide();
                    $('#form-modal-status-store input[name="status"]').val("{{config('globalConstant.shop_rejected')}}");
                }
                return false;
            }

            // on submit click
            $('#modal-status-submit').on('click', function () {
                var transactionId   =   $('#form-modal-status-store input[name="id"]').val();
                var status          =   $('#form-modal-status-store input[name="status"]').val();
                var reply           =   $.trim($('#reply').val());
                if ( status=="{{config('globalConstant.shop_rejected')}}"  && !reply.replace(/\s/g, '').length) {
                    $('#reply-error').show();
                    return false;
                }
                
                if(status=="{{config('globalConstant.shop_rejected')}}" && !reply ){
                    $('#reply-error').show();
                    return false;
                }

                $.ajax({
                  url: "/admin/stores/sellers/status",  
                  data:{
                    id:transactionId, 
                    status:status, 
                    reply:reply,
                    },
                  method: "POST",
                  success: function(data) {
                     window.location.reload(true);
                  }
                });
            })
        })
    </script> 
@endsection
