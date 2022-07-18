@extends('admin.admin')

@section('content')

    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">List All Banners</h3>

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
                    <th>Button</th>
                    <th>Active From</th>
                    <th>Active To</th>
                    <th>Image</th>
                    <th>Website</th>
                    <th style="min-width: 100px;">Action</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($items as $item)
                    <tr>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->description }}</td>
                        <td>
                            <a target="_blank" href="{{ $item->action_url }}">{{ $item->action_name }}</a>
                        </td>
                        <td>{{ format_date($item->active_from) }}</td>
                        <td>{{ isset($item->active_to)? format_date($item->active_to):'-' }}</td>
                        <td>{!! image_row_link($item->image_thumb, $item->image) !!}</td>
                        <td>{{ $item->is_website ? 'Yes':'No' }}</td>
                        <td>
                          
                            {!! action_row($selectedNavigation->url, $item->id, $item->title, ['show', 'edit', 'delete'], false) !!}

                           
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
