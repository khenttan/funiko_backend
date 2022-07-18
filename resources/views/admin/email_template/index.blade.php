@extends('admin.admin')
@section('content')
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Auto-Emails</h3>
            </div>
            <div class="card-body">
                @include('admin.partials.card.info')
                @include('admin.partials.card.buttons')
                <table id="tbl-list" data-server="false" class="dt-table table table-striped table-bordered table-sm" cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th class="desktop">Subject</th>
                        <th style="min-width: 100px;">Status</th>
                        <th>Created At</th>
                        <th style="min-width: 100px;">Action</th>

                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($items as $key=>$item)
                        <tr>
                            <td>{{ $key+1 }}</td>
                            <td>{{ $item->name }}</td>
                            <td>{!! $item->subject!!}</td>
                            <td>{{ ($item->is_active == '1')? 'Activate':'Deactivate' }}</td>
                            <td>{{ format_date($item->created_at) }}</td>
                            <td>
                                <div class="btn-toolbarr">

                                    <a href="{{ route('EmailTemplate.edit_template', $item->id) }}" class="btn btn-primary btn-xs mr-1" data-toggle="tooltip" title="" data-original-title= "Edit.{{$item->name}}">
                                        <i class="fa fa-fw fa-edit text-white"></i>
                                    </a>

                                    {!! action_row($selectedNavigation->url.'/delete', $item->id, $item->name, ['delete'], false) !!}

                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
@endsection
