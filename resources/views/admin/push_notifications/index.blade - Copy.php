
@extends('admin.admin')
@section('content')
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Auto-Emails</h3>
                <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
            </div>
             <div class="card-body">
            <div class="mb-3" role="group" aria-label="Page functionality">
                <a class="btn btn-primary" href="{{ route('EmailTemplate.add') }}">
                    <i class="fa fa-fw fa-plus"></i> Create ad email templates
                </a>
            </div>
            @include('admin.partials.card.info')
           <!--  <div class="card-body">
                @include('admin.partials.card.info')
                @include('admin.partials.card.buttons') -->
              <table id="tbl-list" data-page-length="25" class="dt-table table table-sm table-bordered table-striped table-hover">
                    <thead>
                    <tr>
                        <th>Title</th>
                        <th class="desktop">Subject</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th style="min-width: 100px;">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($items as $item)
                        <tr>
                            <td>{{ $item->name }}</td>
                            <td>{!! $item->subject!!}</td>
                            <td>{{ ($item->is_active == '1')? 'Activate':'Deactivate' }}</td>
                            <td>{{ format_date($item->created_at) }}</td>
                            
                              <?php $urlName =    WEBSITE_ADMIN_NAV_URL.$selectedNavigation->url.'/email-template/'.$item->id?>
                            <td>
                                <div class="btn-toolbarr">

                                     <a href="{{route('EmailTemplate.edit',[$item->id])}}" class="btn btn-primary btn-xs mr-1" data-toggle="tooltip" title="" data-original-title="Edit {{$item->name}}">
                                        <i class="fa fa-fw fa-edit text-white"></i>
                                    </a>
                                       <a href="{{route('EmailTemplate.delete',[$item->id])}}" class="btn btn-primary btn-xs mr-1" data-toggle="tooltip" title="" data-original-title="Delete {{$item->name}}">
                                        <i class="fa fa-fw fa-trash text-white"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
@endsection
@section('scripts')
    @parent
    <script type="text/javascript" charset="utf-8">
      

    </script>
@endsection
