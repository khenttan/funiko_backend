@extends('admin.admin')

@section('content')

		<div class="card card-primary">
			<div class="card-header">
				<h3 class="card-title">
					<span>List All Product Features</span>
				</h3>
			</div>

			<div class="card-body">

				@include('admin.partials.card.info')

				@include('admin.partials.card.buttons')

				<table id="tbl-list" data-server="false" class="dt-table table table-striped table-bordered table-sm" cellspacing="0" width="100%">
					<thead>
					<tr>
						<th>Feature</th>
                        <th>Created</th>
						<th>Action</th>
					</tr>
					</thead>
					<tbody>
                    @if(!empty($items))    
					@foreach ($items as $item)
						<tr>
							<td>{{ $item->name }}</td>
                            <td>{{ $item->created_at->format('d M Y') }}</td>
							<td style="display:flex;">
                                @php $newURL=$selectedNavigation->url.'/subfeatures/' @endphp
                               {!! action_row($newURL, $item->id, $item->name, [ 'edit','delete']) !!}</td>
                               
						</tr>
					@endforeach
                    @endif
					</tbody>
				</table>
				<a href="javascript:window.history.go(-2);" class="btn btn-secondary ">
					<i class="fa fa-fw fa-chevron-left"></i> Back
				</a>
			</div>
		</div>

@endsection
