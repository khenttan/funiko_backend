@extends('admin.admin')


@section('content')   
    
        <div class="box-body table-responsive">
             <div class="mb-3" role="group" aria-label="Page functionality">
                <a class="btn btn-primary" href="{{ route('PushNotification.add') }}">
                    <i class="fa fa-fw fa-plus"></i> Create push notifications
                </a>
            </div>
            <form id="searchForm" name="searchForm" type="post" >
                <div class="row">
                    <div class="col-md-5">
                         <div class="form-group">
                            <label for="name">Title</label>
                            <div class="input-group displayblock">
                                <input type="text" name="name" class="form-control" id="column_filter_1"  onkeyup="searchDatatable(1)" placeholder="Enter title">
                            </div>
                        </div>
                    </div>     
                    <div class="col-md-5">
                         <div class="form-group">
                            <label for="subject">Subject</label>
                            <div class="input-group displayblock">
                                <input type="text" name="subject" class="form-control" id="column_filter_2"  onkeyup="searchDatatable(2)" placeholder="Enter subject">                             
                            </div>
                        </div>
                    </div>   
                    <div class="col-md-2">
                         <div class="form-group">
                            <label for="active_from"><?php echo '&nbsp;' ?></label>
                            <div class="input-group">
                                <button type="button" id="reset" class="btn btn-primary">Reset</button>
                            </div>
                        </div>
                    </div>   
                </div>               
            </form>
            <table id="dataShowTable" class="row-border hover table table-bordered cb-data-table table-r" cellspacing="0" width="100%">
                <thead>
                     <tr>
                        <th width="3%">S.No.</th>
                        <th>Title</th>
                        <th class="desktop">Subject</th>
                        <th>Status</th>
                        <th>Created At</th>
                       <th> Action</th>
                      
                    </tr>
                </thead>
            </table>
        </div>
  


<!-- jQuery Library -->


@endsection  



@section('scripts')
    @parent

    <script>

        var dataTable = "";
        var Sreq      = 1;
        var i           = 1;
        $(document).ready(function() {
     
            dataTable   =    $('#dataShowTable').DataTable({
               
                "bStateSave"     :true,
    
                "fnStateSave"   : function (oSettings, oData) {
                    localStorage.setItem( 'DataTables', JSON.stringify(oData) );
                },
               
                "fnStateLoad"   : function (oSettings) {
                    return JSON.parse( localStorage.getItem('DataTables') );
                },
    
                "processing"    :   true,
                "serverSide"    :   true,
                "order"         :   [[ 0, "desc" ]],
                "sDom"          :   "ltipr",
                "ajax": {
                    async   :   true,
                    "url"   :   "{{route('PushNotification.index')}}",
    
                    "type"  :   "POST",
                    "data"  :   {"sreq":Sreq,'_token':'{{ csrf_token() }}'},
                    beforeSend: function () {
                        Sreq    =   1;
                    },
                    dataSrc :   function(res){
                        if(res){
                           try{
                                Sreq    =   res.sreq;
                                return res.data;
                            } catch(e) {
                                return [];
                            } 
                        }else{
                            return [];
                        }
                    },
                },
                "columns": [
    
    
                   { 
                        name: "id", 
                        data: "id", 
                        "searchable": false, 
                        "orderable": false,
                        render:function(data, type, full, meta){
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                
    
                    { 
                        data: 'name', 
                        name: 'name',
                        "orderable"     : false,
                        "searchable"    : false,
                        render:function(name){
                            if(name != null) return  "<div id='dvNotes' class='wordwrap'>" + name + "</div>";
                        }
                    },
                   
                    {data  : 'subject' },
                    {
                        "data"          :   "is_active",
                        "name"          :   "is_active",
                        "orderable"     :   false,
                        "searchable"    :   false,
                        "render"        :   function(is_active){
                            return is_active;
                        }
                    },
                  
                    { data: 'created' },
    
    
    
    
                    {
                        "data"          : {},
                        "orderable"     : false,
                        "searchable"    : false,
                        "render"        : function(result){
                           
                            var id                  =   (result.id)     ? result.id         : "";
                            var name                =   (result.name)   ? result.name       : "";
                            var adminListUrl        =   "{{$adminListUrl}}";
                            var deleteUrl           =   adminListUrl+'/delete-push-notifications/'+id;
                            var dropDownButton      =   ''+
    
                                '<div class="btn-toolbarr">'+
    
                                    '<a href="'+adminListUrl+'/edit/'+id+'" class="btn btn-info btn-sm mr-1" data-toggle="tooltip" title="" data-original-title="Edit '+name+'">'+
                                         '<i class="">Edit</i>'+
                                    '</a>'+
                                    '<span class="btn btn-sm btn-danger mr-1" onclick=openModal("'+deleteUrl+'") data-toggle="modal" title="" data-original-title="delete '+name+'">'+
                                        '<i class="fa fa-fw fa-trash text-white"></i>'+
                                    '</span>'+
                                
                                    '<div id="delete_modal" class="delete-modal modal fade" role="dialog">'+
                                      '<div class="modal-dialog modal-sm">'+
                                        '<div class="modal-content">'+
                                          '<div class="modal-header">'+
                                            '<button type="button" class="close" data-dismiss="modal">&times;</button>'+
                                            '<div class="delete-icon"></div>'+
                                          '</div>'+
                                          '<div class="modal-body text-center">'+
                                            '<h4 class="modal-heading">Are You Sure ?</h4>'+
                                            '<p>Do you really want to delete these push notification?</p>'+
                                          '</div>'+
                                          '<div class="modal-footer">'+
                                            '<span  class="btn btn-gray"  data-dismiss="modal" title="reset">'+'No</span>'+
                                            '<a href="#" id="deletefaqcat"  '+name+'">'+
                                                '<i class="fa fa-fw fa-trash text-white"></i>Yes'+
                                            '</a>'+
                                          '</div>'+
                                        '</div>'+
                                      '</div>'+
                                    '</div>'+
                                '</div>';
                            return dropDownButton;
                        }
                    },      
    
    
                    
                ]
    
            });
        });
    
        /** Javascript function to search datatable */
        function searchDatatable(columnNumber){
            var query= $('#column_filter_'+columnNumber).val();
            if(Sreq ==  0) {
                dataTable.column(columnNumber).search(query).draw();
                window.location.reload();
            }
        }//end searchDatatable()
    
    </script>
    <script>
          function openModal(url){
            $('#deletefaqcat').attr('href',url);
            $('#delete_modal').modal();
        } 
        </script>

@endsection
