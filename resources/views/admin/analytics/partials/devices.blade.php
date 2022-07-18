<div class="card card-outline card-primary" id="box-devices" style="min-height: 400px;">
    <div class="card-header">
        <h4 class="float-left m-0">
            <span><i class="fa fa-mobile-alt"></i></span>
            <span>Top performing Users</span>
        </h4>

        {{-- @include('admin.partials.boxes.toolbar') --}}
    </div>

    <div class="card-body">
   
        <table id="tbl-devices" data-order-by="1|desc" class="table nowrap table-striped table-sm table-bordered" cellspacing="0" width="100%">
            <thead>
            <tr>
                <th>User Name</th>
                <th>Followers</th>
            </tr>
            </thead>
            <tbody>
                @foreach ($users_data as $user )
                <tr>
                    <td>{{ $user['username'] }}</td>

                    <td>{{ $user['myfollwer_count'] }}</td>
                </tr>
                @endforeach

            </tbody>
        </table>
    </div>
</div>

