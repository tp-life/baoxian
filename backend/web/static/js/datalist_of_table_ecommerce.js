var EcommerceList={
    grid:null,
    initPickers:function () {
        //init date pickers
        $('.date-picker').datepicker({
            rtl: App.isRTL(),
            autoclose: true,
            language: 'zh-CN',
            format: 'yyyy-mm-dd'
        });
    },
    reload:function(params){
        if(!this.grid){
            return false;
        }
        var obj=this;
        $.each(params,function(key,val){
            obj.ajaxParams[key]=val;
        })
        this.grid.getDataTable().ajax.reload();
    },
    ajaxParams:{'_csrf-backend':$('meta[name="csrf-token"]').attr("content")},
    init:function (url,rqParams,bSort,showTable,length,func) {
        this.initPickers();
        var obj=this;

        rqParams = rqParams || {};
        $.each(rqParams,function(key,val){
            obj.ajaxParams[key]=val;
        })

        this.grid=this.handleList(url,bSort,showTable,length,func);
        return this.grid;
    },

    handleList:function (url,bSort,showTable,length,func) {

        var bSort = bSort || false;
        var rqParams =  this.ajaxParams;
        var length = length || 10;
        var showTableEm = $("#datatable_list");
        if(!(showTable==undefined || showTable=='')){
            showTableEm = $("#"+showTable);
        }
        var grid =  new Datatable();
        //add outside data
        if(rqParams){
            $.each(rqParams,function(y_name,y_value) {
                grid.setAjaxParam(y_name,y_value);
            });
        }

        // get all typeable inputs
        $('textarea.form-filter, select.form-filter, input.form-filter:not([type="radio"],[type="checkbox"])').each(function() {
            grid.setAjaxParam($(this).attr("name"), $(this).val());
        });

        //rewrite
        grid.resetFilter =  function(){
            $('textarea.form-filter, select.form-filter, input.form-filter').each(function() {
                $(this).val("");
            });
            $('input.form-filter[type="checkbox"]').each(function() {
                $(this).attr("checked", false);
            });
            grid.clearAjaxParams();
            if(rqParams){
                $.each(rqParams,function(y_name,y_value) {
                    grid.setAjaxParam(y_name,y_value);
                });
            }
            grid.addAjaxParam("action", 'filter_cancel');
            grid.submitFilter();
        }
        // get all checkboxes
        $('input.form-filter[type="checkbox"]:checked').each(function() {
            grid.addAjaxParam($(this).attr("name"), $(this).val());
        });

        // get all radio buttons
        $('input.form-filter[type="radio"]:checked').each(function() {
            grid.setAjaxParam($(this).attr("name"), $(this).val());
        });

        grid.init({
            src: showTableEm,
            onSuccess: function (grid,data) {
                // execute some code after table records loaded
                if(typeof func === 'function'){
                    func(data);
                }
            },
            onError: function (grid) {
                // execute some code on network or other general error
            },
            //filterApplyAction: "filter-submit",
            //filterCancelAction: "filter-cancel",
            loadingMessage: 'Loading...',
            dataTable: { // here you can define a typical datatable settings from http://datatables.net/usage/options
                // Uncomment below line("dom" parameter) to fix the dropdown overflow issue in the datatable cells. The default datatable layout
                // setup uses scrollable div(table-scrollable) with overflow:auto to enable vertical scroll(see: assets/global/scripts/datatable.js).
                // So when dropdowns used the scrollable div should be removed.
                //"dom": "<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'<'table-group-actions pull-right'>>r>t<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'>>",
                "dom": '<"top">rt<"bottom"lip>',
                "lengthMenu": [
                    [10, 20, 50, 100], // change per page values here
                    [10, 20, 50, 100]
                ],
                "pageLength": length, // default record count per page
                "ajax": {
                    "url": url
                    //'data':function(data){
                    //    $.each(rqParams, function(key, value) {
                    //        data[key] = value;
                    //    });
                    //}

                },
                'orderClasses':true,
                'bSort':bSort,
                //"order": [0,'desc'],// set first column as a default sort by asc
                "language":{
                    "metronicGroupActions": "",
                    "metronicAjaxRequestGeneralError": "请求异常，请联系管理人员.",
                    //"processing": "处理中...",
                    "processing": "",
                    'sProcessing':'Loading',
                    "lengthMenu": " _MENU_ ",
                    "sLengthMenu": " _MENU_ ",
                    "zeroRecords": "对不起，查询不到任何相关数据",
                    //"sInfo": "显示第 _START_ 至 _END_ 项结果，共 _TOTAL_ 项",
                     sInfo:' 第 _PAGE_ 页 / 总 _PAGES_ 页 / 共 _TOTAL_ 条',
                    //'info': '第 _PAGE_ 页 / 总 _PAGES_ 页 ',
                    "infoEmpty": "空记录",
                    "infoFiltered": "(共 _MAX_ 条记录)",
                    "infoPostFix": "",
                    "search": "搜索:",
                    //"sUrl": "",
                    "emptyTable": "空记录",
                    //"loadingRecords": "载入中...",
                    "loadingRecords": "",
                    'sLoadingRecords':'',
                    //"sInfoThousands": ",",
                    "oPaginate": {
                        "first":"第一页",
                        "last":"最后一页",
                        "next":"下一页",
                        "previous":"上一页",
                        "page": "",
                        'pageOf':''
                    },
                    "oAria": {
                        "sSortAscending": ": ASC",
                        "sSortDescending": ": DESC"
                    }
                }
            }
        });

        // handle group actionsubmit button click
        grid.getTableWrapper().on('click', '.table-group-action-submit', function (e) {
            e.preventDefault();
            var action = $(".table-group-action-input", grid.getTableWrapper());
            if (action.val() != "" && grid.getSelectedRowsCount() > 0) {
                grid.setAjaxParam("customActionType", "group_action");
                grid.setAjaxParam("customActionName", action.val());
                grid.setAjaxParam("id", grid.getSelectedRows());
                grid.getDataTable().ajax.reload();
                grid.clearAjaxParams();
            } else if (action.val() == "") {
                alert({
                    type: 'danger',
                    icon: 'warning',
                    message: 'Please select an action',
                    container: grid.getTableWrapper(),
                    place: 'prepend'
                });
            } else if (grid.getSelectedRowsCount() === 0) {
                alert({
                    type: 'danger',
                    icon: 'warning',
                    message: 'No record selected',
                    container: grid.getTableWrapper(),
                    place: 'prepend'
                });
            }
        });

        return grid;

    }

};