@extends('admin.admin')

@section('content')

 <div class="box-header">
       
 {!! Form::open(['method' => 'POST', 'action' => 'Admin\notification\AdminPushNotificationController@saveTemplate', 'files' => true]) !!}
        
        {!! csrf_field() !!}

       

        <input name="_token" type="hidden" value="{{ csrf_token() }}">

            <div class="card-body">
              
                <fieldset>
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Enter Name" value="{{ ($errors && $errors->any()? old('name') : (isset($item)? $item->name : '')) }}">
                                <small class="text-danger">{{ $errors->first('name') }}</small>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">

                            <div class="form-group{{ $errors->has('action') ? ' has-error' : '' }}">
                            {!! Form::label('action', 'Action') !!}
                               {!! Form::select('action', [""=>"Please select a action"]+$actionOptions, null, ['class' => 'form-control', 'id'=>'action', ]) !!}

                            <small class="text-danger">{{ $errors->first('action') }}</small>

                            </div>
                        </div>

                        <div class="col-12 col-md-8">

                            <div class="form-group">
                            {!! Form::label('constants', 'Constants') !!}
                           {!! Form::select('constants', $actionOptions, null, ['class' => 'form-control', 'id'=>'constants', ]) !!}
                            <small class="text-danger">{{ $errors->first('constants') }}</small>

                            </div>
                          
                        </div>
                        <div class="col-12 col-md-2">
                            <div class="form-group">
                            <label for="subject">&nbsp;</label>
                            <button type="button" class="btn bg-indigo form-control btnvar" onclick="insertHTML()">
                            Insert variable
                            </button>
                            </div>
                        </div>
                        <div class="col-12 col-md-12">
                            <div class="form-group">
                                <label for="subject">Subject</label>
                                <input type="text" class="form-control" id="subject" name="subject" placeholder="Enter Subject" value="{{ ($errors && $errors->any()? old('subject') : (isset($item)? $item->subject : '')) }}">
                              <small class="text-danger">{{ $errors->first('subject') }}</small>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="body">Body</label>
                        <textarea class="form-control summernote" id="content" name="body" rows="10">{{ ($errors && $errors->any()? old('body') : (isset($item)? $item->body : '')) }}</textarea>
                        <small class="text-danger">{{ $errors->first('body') }}</small>
                    </div>
                </fieldset>
            </div>

        
        <div class="box-footer">
      <div class="btn-group pull-left">
        {!! Form::reset("Reset", ['class' => 'btn btn-yellow btn-default']) !!}
        {!! Form::submit("Create", ['class' => 'btn btn-add btn-default']) !!}
      </div>
    </div>


    <!-- include libraries(jQuery, bootstrap) -->

{{-- <script type="text/javascript" src="{{asset('public/js/summernote.min.js')}}"></script>
<!-- summernote css/js -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet"> --}}
<!-- <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script> -->
  
    

@endsection


@section('scripts')
    @parent
    {{-- <script type="text/javascript" charset="utf-8">
        $(document).ready(function() {
         $('.summernote').summernote();
       });
   </script> --}}
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
                   url     :   "{{route('PushNotification.getConstant')}}",
                   data: {
                       "action": value,
                       "_token": "{{ csrf_token() }}",
                   },
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
                       $('#content').append(newStr);
                   }
               });
           }
       }// end insertHTML()
   </script>

    
@endsection



