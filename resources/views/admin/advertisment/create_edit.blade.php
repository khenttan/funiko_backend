@extends('admin.admin')

@section('content')
    <form id="banner" class="card card-primary" method="POST" action="{{$selectedNavigation->url . (isset($item)? "/{$item->id}" : '')}}" accept-charset="UTF-8" enctype="multipart/form-data">
        <input name="_token" type="hidden" value="{{ csrf_token() }}">
        <input name="_method" type="hidden" value="{{isset($item)? 'PUT':'POST'}}">

        <div class="card-header">
            <h3 class="card-title">
                <span><i class="fa fa-edit"></i></span>
                <span>{{ isset($item)? 'Edit the ' . $item->title . ' entry': 'Create a new Advertisment' }}</span>
            </h3>

            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>

        <div class="card-body">
            @include('admin.partials.card.info')

            <fieldset>
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label for="name">Title</label>
                            <input type="title" class="form-control {{ form_error_class('title', $errors) }}" id="title" name="title" placeholder="Enter title" value="{{ ($errors && $errors->any()? old('title') : (isset($item)? $item->title : '')) }}">
                            {!! form_error_message('title', $errors) !!}
                        </div>
                    </div>

                    {{-- <div class="col-md-2">
                        <div class="form-group">
                            <label for="hide_name">Set Visibility</label>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" name="hide_name" class="custom-control-input" id="hide_name" {!! ($errors && $errors->any()? (old('hide_name') == 'on'? 'checked':'') : (isset($item)&& $item->hide_name == 1? 'checked' : '' )) !!}>
                                <label class="custom-control-label" for="hide_name">Hide Name</label>
                                {!! form_error_message('hide_name', $errors) !!}
                            </div>
                        </div>
                    </div> --}}
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="description">Description
                                <span class="small">(Optional)</span></label>
                            <input type="text" class="form-control {{ form_error_class('description', $errors) }}" id="description" name="description" placeholder="Enter Description" value="{{ ($errors && $errors->any()? old('description') : (isset($item)? $item->description : '')) }}">
                            {!! form_error_message('description', $errors) !!}
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col col-6">
                        <div class="form-group">
                            <label for="action_name">Amount charged to Advertiser
                                <span class="small">(Optional)</span></label>
                            <input type="number" class="form-control {{ form_error_class('amount', $errors) }}" id="amount" name="amount" placeholder="Enter amount" value="{{ ($errors && $errors->any()? old('action_name') : (isset($item)? $item->amount : '')) }}">
                            {!! form_error_message('amount', $errors) !!}
                        </div>
                    </div>

                    <div class="col col-6">
                        <div class="form-group">
                            <label for="action_url">Action Url <span class="small">(Optional)</span></label>
                            <div class="input-group">
                                <input type="text" class="form-control {{ form_error_class('action_url', $errors) }}" id="action_url" name="action_url" placeholder="Enter Action Url" value="{{ ($errors && $errors->any()? old('action_url') : (isset($item)? $item->action_url : '')) }}">
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="fas fa-link"></i></span>
                                </div>
                                {!! form_error_message('action_url', $errors) !!}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col col-6">
                        <div class="form-group">
                            <label for="active_from">Active From
                                <span class="small">(Optional)</span></label>
                            <div class="input-group">
                                <input type="text" class="form-control {{ form_error_class('active_from', $errors) }}" id="active_from" name="active_from" data-date-format="YYYY-MM-DD HH:mm:ss" placeholder="Enter Active From" value="{{ ($errors && $errors->any()? old('active_from') : (isset($item)? $item->active_from : '')) }}">
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                </div>
                                {!! form_error_message('active_from', $errors) !!}
                            </div>
                        </div>
                    </div>

                    <div class="col col-6">
                        <div class="form-group">
                            <label for="active_to">Active To
                                <span class="small">(Optional)</span></label>
                            <div class="input-group">
                                <input type="text" class="form-control {{ form_error_class('active_to', $errors) }}" id="active_to" name="active_to" data-date-format="YYYY-MM-DD HH:mm:ss" placeholder="Enter Active From" value="{{ ($errors && $errors->any()? old('active_to') : (isset($item)? $item->active_to : '')) }}">
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                </div>
                                {!! form_error_message('active_to', $errors) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </fieldset>

        </div>
        @include('admin.partials.form.form_footer')
    </form>

@endsection

@section('scripts')
    @parent
    <script type="text/javascript" charset="utf-8">
        $(function () {
            setDateTimePickerRange('#active_from', '#active_to');
        })
    </script>

    
@endsection
