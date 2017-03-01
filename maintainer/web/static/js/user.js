var TableDatatablesAjax = function () {

    var handleRecords = function () {
        var grid = new Datatable();
        var ajaxParams={'_csrf-backend':$('meta[name="csrf-token"]').attr("content")};
        grid.init({
            src: $("#datatable_ajax"),
            onSuccess: function (grid, response) {
            },
            onError: function (grid) {

            },
            onDataLoad: function(grid) {

            },
            loadingMessage: 'Loading...',
            dataTable: {
                "bSort": false,
                "bStateSave": true,
                "lengthMenu": [
                    [10, 20, 50, 100, 150, -1],
                    [10, 20, 50, 100, 150, "All"]
                ],
                "dom": '<"table-search">rt<"bottom"pli><"clear">',
                "pageLength": 20,
                "ajax": {
                    "url": "/user/getdata",
                    'data':function(data){
                        $.each(ajaxParams, function(key, value) {
                            data[key] = value;
                        });
                    },

                },
            }
        });
        $('#datatable_submit').on('click',function(e){

            var filed=$('#datatable_filter').val();
            var content=$('#datatable_search').val();

            if(filed !='' && content !=''){
                ajaxParams['field']=filed;
                ajaxParams['field_value']=content;
                grid.getDataTable().ajax.reload();
            }

        });
    }

    return {
        init: function () {
            handleRecords();
        }

    };

}();

jQuery(document).ready(function() {
    TableDatatablesAjax.init();
});