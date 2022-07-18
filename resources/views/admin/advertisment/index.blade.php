@extends('admin.admin')

@section('content')


    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">List All Advertisment</h3>

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
                    <th>Banner</th>
                    <th>Description</th>
                    <th>Active From</th>
                    <th>Active To</th>
                    {{-- <th>Image</th> --}}
                    <th>Website</th>
                    <th style="min-width: 100px;">Action</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($items as $item)
                    <tr>
                        <td>{{ $item->title }}</td>
                        <td>{{ $item->description }}</td>
                     
                        <td>{{ format_date($item->active_from) }}</td>
                        <td>{{ isset($item->active_to)? format_date($item->active_to):'-' }}</td>
                        {{-- <td>{!! image_row_link($item->image_thumb, $item->image) !!}</td> --}}
                        <td>{{ $item->action_url  }}</td>
                        
                        <td>
                            <div class="btn-group">
                                <a href="/admin/advertisement/{{ $item->id }}/videoUpload" class="btn btn-info btn-xs" data-toggle="tooltip" title="Add Resources">
                                    <i class="fas fa-fw fa-photo-video"></i>
                                </a>
                            </div>
                            {!! action_row($selectedNavigation->url, $item->id, $item->title, ['edit', 'delete'], false) !!}
                           
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
 
