@extends('admin.admin')

@section('content')
    <div class="card  card-primary">
        <div class="card-header">
            <h3 class="card-title">List All Members</h3>

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
                    <th><i class="fa fa-fw fa-user text-muted"></i>Username</th>
                    <th><i class="fa fa-fw fa-mobile text-muted"></i>Image</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($items['group_member'] as $key=>$item)

                    <tr>
                        <td>{{ $key+1 }}</td>
                        <td>{{ $item['member']['username']??'' }}</td>
                        <td><img src="{{ $item['member']['image'] }}" width="100" height="60"></td>
        
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

