/**
 * Created by tp on 16/8/16.
 */


var handleValidation3 = function() {
    var form2 = $('#createInsuranceCompany');
    var error2 = $('.alert-danger', form2);
    var success2 = $('.alert-success', form2);
    jQuery.validator.addMethod("en", function(value, element) {
        var tel = /^[a-zA-Z]+$/;
        return this.optional(element) || (tel.test(value));
    }, "请正确填写英文");
    form2.validate({
        errorElement: 'span',
        errorClass: 'help-block help-block-error',
        focusInvalid: false,
        ignore: "",
        rules: {
            company_name: {
                required: true,
                rangelength: [2, 25],
            },
            sp:{
                required: true,
                en:true,
                rangelength: [2, 10],
            },
            concat_name: {
                required: true,
                rangelength: [2, 25]
            },
            concat_tel:{
                required:true,
                rangelength:[11,11],
                digits: true
            },
            a_id:{
                required:true
            },
            address_detail:{
                required:true
            }
        },
        messages:{
            company_name:{
                required:'请输入公司名称'
            },
            sp:{
                required:'请输入英文简称',
            },
            concat_name:{
                required:'请输入联系人'
            },
            concat_tel:{
                required:'请输入联系人手机',
                rangelength:'请输入11位手机号码',
                digits:'请输入11位手机号码'
            },
            a_id:{
                required:'请选择所属地区'
            },
            address_detail:{
                required:'请输入详细地址'
            }
        },

        invalidHandler: function (event, validator) {
            //success2.hide();
            //error2.show();
            showToastr('error','您有一些错误,请修正您的输入');
            App.scrollTo(error2, -200);
        },

        errorPlacement: function (error, element) {
            var icon = $(element).parent('.input-icon').children('i');
            icon.removeClass('fa-check').addClass("fa-warning");
            icon.attr("data-original-title", error.text()).tooltip({'container': 'body'});
            if (element.parents('.check_li').size() > 0) {
                error.appendTo(element.parents('.check_li').attr("data-error-container"));
            }else{
                error.insertAfter(element);
            }

        },

        highlight: function (element) {
            $(element)
                .closest('.form-group').removeClass("has-success").addClass('has-error');
        },
        success: function (label, element) {
            var icon = $(element).parent('.input-icon').children('i');
            $(element).closest('.form-group').removeClass('has-error').addClass('has-success'); // set success class to the control group
            icon.removeClass("fa-warning").addClass("fa-check");
        },

        submitHandler: function (form) {

            //success2.show();
            //error2.hide();
            var form_data=$(form).serializeArray();
            App.blockUI();
            $.post('/company/create',form_data,function(data){
                App.unblockUI();
                data=typeof data =='string'? $.parseJSON(data):data;
                if(data.code !=='yes'){
                    showToastr('error',data.message);
                    return false;
                }
                showToastr('success',data.message);
                setTimeout(function(){
                    window.location.href='/company/index';
                },2000);
            });

            return false;
        }
    });


}

jQuery(document).ready(function () {
    $('#province_id , #city_id').on('change', function () {
        var province = $(this).val();
        var pval = province.split(',');
        var name = this.name;
        $.post('/seller/getarea', {
            'id': pval[0],
            '_csrf-backend': $('meta[name="csrf-token"]').attr("content")
        }, function (data) {
            data = typeof data == 'string' ? $.parseJSON(data) : data;
            if (data.code !== 'yes') {
                showToastr('warning', data.message);
                return false;
            }
            var html = '<option value="">请选择地区</option>';
            $.each(data.data, function (index, ele) {
                html += '<option value="' + ele.area_id + ',' + ele.area_name + '">' + ele.area_name + '</option>';
            })
            if (name == 'p_id') {
                $('#city_id').html(html).css('display', 'inline');
                $('#area_id').css('display', 'none');
            } else if (name == 'c_id') {
                $('#area_id').html(html).css('display', 'inline');
            }
        });
    });


    $('#uploadPic').on('click',function(){
        $('#fileToUpload').click();

    });
    //$('#fileToUpload').change(function(){
    //    alert(456);
    //});

    $('#fileToUpload').on('change',function(){

        var src = getFullPath($(this)[0]);
        $('#uploadPic').attr('src',src);

        $.ajaxFileUpload({
            url:'/company/upload',
            secureuri :false,
            fileElementId :'fileToUpload',
            data:{'_csrf-backend':$('meta[name="csrf-token"]').attr("content")},
            dataType : 'json',
            success : function (data, status){

                if(typeof(data.status) != 'undefined'){
                    if(data.status == 1){
                        $('#logo').val(data.url);
                    }else{
                        showToastr('error',data.msg);
                    }
                }
            },
            error: function(data, status, e){
                showToastr('error',e);
            }
        })

    });

    handleValidation3();
});



