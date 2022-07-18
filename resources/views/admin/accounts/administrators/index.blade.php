@extends('admin.admin')

@section('content')
    <div class="card  card-primary">
        <div class="card-header">
            <h3 class="card-title">List All Administrators</h3>

            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>

        <div class="card-body">

            <table id="tbl-list" data-page-length="25" class="dt-table table table-sm table-bordered table-striped table-hover">
                <thead>
                <tr>
                    <th><i class="fa fa-fw fa-user text-muted"></i> Name</th>
                    <th><i class="fa fa-fw fa-envelope text-muted"></i> Email</th>
                    <th><i class="fa fa-fw fa-mobile-phone text-muted"></i> Cellphone</th>
                    <th>Roles</th>
                    <th><i class="fa fa-fw fa-calendar text-muted"></i> Last Login</th>
                    
                </tr>
                </thead>
                <tbody>
                @foreach ($items as $item)
                    <tr>
                        <td>{{ $item->fullname }}</td>
                        <td>{{ $item->email }}</td>
                        <td>{{ $item->cellphone }}</td>
                        <td>{{ $item->roles_string }}</td>
                        <td>{{ ($item->logged_in_at)? $item->logged_in_at->diffForHumans():'-' }}</td>
                       
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
