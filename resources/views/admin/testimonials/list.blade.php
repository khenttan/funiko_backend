@extends('admin.admin')

@section('content')

    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">List All Testimonials</h3>

            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>

        <div class="card-body">

            @include('admin.partials.card.info')

            @include('admin.partials.card.buttons')

            <table id="tbl-list" data-page-length="25" class="dt-table table table-sm table-bordered table-striped table-hover">
                <thead>
                <tr>
                    <th>S.No</th>
                    <th>name</th>
                    <th>image</th>
                    <th>role</th>
                    <th>action</th>
                </tr>
                </thead>
                <tbody>
                    @forelse($list as $key => $data)

                    <tr>

                        <td>{{$list->firstItem() + $key}}</td>

                        <td>{{$data->name ?? ''}}</td>
                        <td>
                            <img src="{{asset('public/storage/uploads/testimonials/'.$data->image ?? '')}}" style="width: 40px; height: 40px">
                        </td>
                        <td>{{$data->role ?? ''}}</td>
                        <td>
                            <a class="btn waves-effect waves-light btn-grd-success" href="{{route('testimonials.edit', ['id'=>base64_encode($data->id), 'page'=>request('page')])}}" style="padding: 4px;">
                                <i class="fa fa-pencil-square-o"></i>Edit
                            </a>
                            <a class="btn waves-effect waves-light btn-grd-danger" onclick="return confirm('Are you sure?')"  href="{{route('testimonials.delete', ['id'=>base64_encode($data->id)])}}" style="padding: 4px;">
                                <i class="fa fa-trash"></i>Delete
                            </a>
                        </td>
                
                    </tr>
                    @empty
                        @endforelse
                </tbody>
                {{ $list->links() }}
            </table>
        </div>
    </div>
@endsection
