@extends('admin.admin')

@section('content')

    <form class="card card-primary" method="POST" action="/admin/pages/{{ $page->id . '/sections/content' . (isset($item)? "/{$item->id}" : '')}}" accept-charset="UTF-8" enctype="multipart/form-data">
        {!! csrf_field() !!}
        {!! method_field(isset($item)? 'put':'post') !!}
        <input name="page_id" type="hidden" value="{{ $page->id }}">

        <div class="card-header">
            <span>{{ isset($item)? 'Edit the "' . $item->heading . '" entry': 'Create a new Page Content Section' }}</span>

            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>

        <div class="card-body">
            <div class="callout callout-info callout-help">
                <h4 class="title">How it works?</h4>
                <ul>
                    <li>Enter heading & choose heading size (optional)</li>
                    <li>Browse for a featured photo, choose the alignment for the image & enter caption (optional)</li>
                    <li>Enter content (optional)</li>
                    <li>Click on submit to save changes, you will be redirected back in order to upload videos, photos & documents</li>
                   <!--  <li>Scroll below to upload videos to the page content section (optional)</li>
                    <li>Scroll below to upload photos to the page content section (optional)</li>
                    <li>Scroll below to upload documents to the page content section (optional)</li> -->
                </ul>
            </div>

            <fieldset>
                @include('admin.pages.components.form_heading')
                @php
                $path=url('/').'/admin/pages/1/sections/content/9/edit';
                @endphp
             
                <div class="row">
                    <div class="@if(isset($item) && $item->media) col-md-6 @else col-md-8 @endif">
                        <div class="form-group {{ form_error_class('media', $errors) }}">
                            <label>Upload your Photo - Maximum 2MB <span class="small">(Optional)</span> </label>
                            <div class="input-group">
                                <input id="media-label" type="text" class="form-control" readonly placeholder="Browse for a photo">
                                <input id="media" style="display: none" accept="{{ get_file_extensions('image') }}" type="file" name="media" onchange="document.getElementById('media-label').value = this.value">

                                <div class="input-group-append">
                                    <button type="button" class="btn btn-secondary" onclick="document.getElementById('media').click();">Browse</button>
                                </div>
                            </div>
                            {!! form_error_message('media', $errors) !!}
                        </div>
                    </div>

                    
                    <div class="col-md-4">
                        <div class="form-group {{ form_error_class('media_align', $errors) }}">
                            <label for="media_align">Media Alignment</label>
                            {!! form_select('media_align', ['left'  => 'Left', 'right' => 'Right', 'top'   => 'Top / Center'], ($errors && $errors->any()? old('media_align') : (isset($item)? $item->media_align : 'left')), ['class' => 'select2 form-control']) !!}
                            {!! form_error_message('media_align', $errors) !!}
                        </div>
                    </div>

                    @if(isset($item) && $item->media)
                        <div class="col-md-2 text-center" id="media-box">
                            <a data-lightbox="Feature Image" href="{{ $item->imageUrl }}">
                                <img class="img-fluid mt-2" src="{{ $item->thumb_url }}" style="height:75px;"/>
                            </a>
                            <button title="Remove media" class="btn btn-danger btn-xs btn-delete-row pull-right btn-delete-media" id="form-delete-row{{ $item->id }}" data-id="{{ $item->id }}" data-page-id="{{ $item->page_id }}">
                                <i class="fa fa-fw fa-times"></i></button>
                            <a href="/admin/resources/page-content/{{ $item->id }}/crop-resource" title="Crop media" class="btn btn-info btn-xs pull-right">
                                <i class="fa fa-fw fa-crop-alt"></i></a>
                        </div>
                    @endif
                </div>

                <div class="row">
                   
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="caption">Media Caption <span class="small">(Optional, good for SEO)</span></label>
                            <input type="text" class="form-control {{ form_error_class('caption', $errors) }}" id="caption" name="caption" placeholder="Enter Caption" value="{{ ($errors && $errors->any()? old('caption') : (isset($item)? $item->caption : '')) }}">
                            {!! form_error_message('caption', $errors) !!}
                        </div>
                    </div>
                </div>

                @include('admin.pages.components.form_content')

            </fieldset>

        </div>
        @include('admin.partials.form.form_footer')
    </form>

    <!-- @  if(isset($item))
        @ include('admin.resources.resourceable', ['resource' => $item])
    @ endif -->
@endsection


@section('scripts')
    @parent
    <script type="text/javascript" charset="utf-8">
        $(function () {

            $('.btn-delete-media').on('click', function (e) {
                e.preventDefault();

                $id = $(this).attr('data-id');
                $page_id = $(this).attr('data-page-id');
            
                $.ajax({

                    type: 'POST',
                    url: "/admin/pages/" + $page_id + "/sections/content/" + $id + "/removeMedia",
                    dataType: "json",
                    success: function (data) {

                        if (data.error) {
                            notifyError(data.error.title, data.error.content);
                        } else {
                            notify('Successfully', 'The media was successfully removed.', null, null, 5000);
                        }

                        $('body').find('#media-box').html('');
                    }
                });
            });
        });
        $('#video_title').on('change',function(){
                //get the file name
                var fileName = $(this).val();
                //replace the "Choose a file" label
                $(this).next('.custom-file-label').html(fileName);
            })
    </script>
     
@endsection
