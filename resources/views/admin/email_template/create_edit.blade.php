@extends('admin.admin')

@section('content')

    <form  id="email"  method="POST" class="card card-primary"  action="{{ route('EmailTemplate.save_template',isset($item)? $item->id:'')  }}" accept-charset="UTF-8">
        {!! csrf_field() !!}

        <div class="card-header">
            <h3 class="card-title">
                <span><i class="fa fa-edit"></i></span>
                <span>{{ isset($item)? 'Edit the ' . $item->name . ' entry': 'Create a new Page' }}</span>
            </h3>


            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>

        <input name="_token" type="hidden" value="{{ csrf_token() }}">

            <div class="card-body">
                @include('admin.partials.card.info')
                <fieldset>
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" class="form-control {{ form_error_class('name', $errors) }}" id="name" name="name" placeholder="Enter Name" value="{{ ($errors && $errors->any()? old('name') : (isset($item)? $item->name : '')) }}">
                                {!! form_error_message('name', $errors) !!}
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="action">Action</label>


                                {!! form_select('action', (['' => 'Please select a action'] + $actionOptions), ($errors && $errors->any()? old('action') : (isset($item)? $item->action : '')), ['id'=>'action', 'class' => 'select2 form-control ' . form_error_class('action', $errors)]) !!}
                                {!! form_error_message('action', $errors) !!}
                            </div>
                        </div>
                        <div class="col-12 col-md-8">
                            <div class="form-group">
                                <label for="constants">Contants</label>
                                {!! form_select('constants', (['' => 'Please select a constants'] + $actionOptions), ($errors && $errors->any()? old('constants') : (isset($item)? $item->constants : '')), ['id'=>'constants', 'class' => 'select2 form-control ' . form_error_class('constants', $errors)]) !!}
                                {!! form_error_message('constants', $errors) !!}
                            </div>
                        </div>
                        <div class="col-12 col-md-2">
                            <div class="form-group">
                            <label for="subject">&nbsp;</label>
                            <button type="button" class="btn bg-indigo form-control" onclick="insertHTML()">
                            Insert variable
                            </button>
                            </div>
                        </div>
                        <div class="col-12 col-md-12">
                            <div class="form-group">
                                <label for="subject">Subject</label>
                                <input type="text" class="form-control {{ form_error_class('subject', $errors) }}" id="subject" name="subject" placeholder="Enter Subject" value="{{ ($errors && $errors->any()? old('subject') : (isset($item)? $item->subject : '')) }}">
                                {!! form_error_message('subject', $errors) !!}
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="body">Body</label>
                        <textarea class="form-control summernote {{ form_error_class('body', $errors) }}" id="content" name="body" rows="10">{{ ($errors && $errors->any()? old('body') : (isset($item)? $item->body : '')) }}</textarea>
                        {!! form_error_message('body', $errors) !!}
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
            initSummerNote('.summernote');
        })
    </script>
    
    <script type="text/javascript" >

        /**
         * Function to get email action options on change
         */
        $("#action").change(function(e){
            appendConstants();
        });

        /**
         * Function to get email action options on page load
         */
        $(function(){
            appendConstants();
        });

         /**
         * Function to append email constants
         */
        function appendConstants(){
            var value   =   $("#action").val();
            var options =   '<option value="">Please select a constants</option>';
            $("#constants").html(options);
            if(value){
                $.ajax({
                    type    :   "POST",
                    url     :   "{{route('EmailTemplate.getConstant')}}",
                    data    :   {"action" : value},
                    success :   function(response){
                        if(response){
                            var result = JSON.parse(response);
                            //var result = (response.result)   ? response.result :[];
                            result.map(function(records){
                                if(records){
                                    //var res = records.replace('"','');
                                    //options  += "<option value='"+res+"'>"+res+"</option>";
                                    options  += "<option value='"+records+"'>"+records+"</option>";
                                }
                            });


                            $("#constants").html(options);
                            //$('#constants').selectpicker('refresh');
                        }else if(response && response.message){
                            notice(response.status,response.message);
                        }
                    },
                });
            }
        }// end appendConstants()

        /**
        * Insert constant in ckeditor
        */
        function insertHTML(){
            var constant = $("#constants").val();
            if(constant){
                $(".summernote").each(function(index){
                    var id = $(this).attr("id");
                    if(id){
                        var newStr = '{'+constant+'}';
                        $("#"+id).summernote('insertText', newStr);
                    }
                });
            }
        }// end insertHTML()
    </script>
@endsection
