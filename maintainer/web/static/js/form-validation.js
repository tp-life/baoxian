var FormValidation = function () {

    // basic validation


    // 创建用户
    var handleValidation2 = function() {
            var form2 = $('#createSeller');
            var error2 = $('.alert-danger', form2);
            var success2 = $('.alert-success', form2);

            form2.validate({
                errorElement: 'span',
                errorClass: 'help-block help-block-error',
                focusInvalid: false,
                ignore: "",
                rules: {
                    name: {
                        required: true,
                        digits: true,
                        rangelength: [11, 11],
                        remote: {
                            url: '/user/checkphone',
                            type: 'get',
                            data: {
                                phone: function () {
                                    return $('#createSeller input[name="name"]').val();
                                }
                            },
                            complete: function (data) {
                                var t = data.responseJSON;
                                if(!t){
                                    $('#nextStep').css('display','inline').attr('href','/seller/perfect?phone='+$('#createSeller input[name="name"]').val());
                                }

                            }
                        }
                    },
                    password: {
                        required: true,
                        rangelength: [6, 12]
                    },
                },
                messages:{
                    name:{
                        required:'请输入手机号码',
                        remote:'该号码已经被使用,请换个号码继续注册'
                    },
                    password:{
                        required:'请输入密码'
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
                    error.insertAfter(element);
                },

                highlight: function (element) {
                    $(element)
                        .closest('.form-group').removeClass("has-success").addClass('has-error');
                },

                unhighlight: function (element) {

                },

                success: function (label, element) {
                    var icon = $(element).parent('.input-icon').children('i');
                    $(element).closest('.form-group').removeClass('has-error').addClass('has-success');
                    icon.removeClass("fa-warning").addClass("fa-check");
                },

                submitHandler: function (form) {
                    //
                    //success2.show();
                    //error2.hide();
                    form[0].submit();
                }
            });


    }


    /**
     * 完善商户信息
     */

    var handleValidation3 = function() {
        var form2 = $('#createSellerNext');
        var error2 = $('.alert-danger', form2);
        var success2 = $('.alert-success', form2);

        jQuery.validator.addMethod("parent_name", function(value, element) {
            var p_type=$('input[name="p_name"]:checked').val()
            if(p_type == 1){
                return true;
            }
            if($('#seller_pid').val() < 1){
                return false;
            }
            return true;
        }, "请选择上级商家");

        form2.validate({
            errorElement: 'span',
            errorClass: 'help-block help-block-error',
            focusInvalid: false,
            ignore: "",
            rules: {
                seller_name: {
                    required: true,
                    rangelength: [2, 25],
                },
                concat: {
                    required: true,
                    rangelength: [2, 25]
                },
                concat_tel:{
                    required:true,
                    rangelength:[11,11],
                    digits: true
                },
                area_id:{
                    required:true
                },
                detail_address:{
                    required:true
                },
                brank_name:{
                    required: true
                },
                account_holder:{
                    required: true
                },
                brank_account:{
                    required: true,
                    rangelength:[15,20],
                    digits: true
                },
                parent_name:{
                    parent_name:true
                }
            },
            messages:{
                seller_name:{
                    required:'请输入商户名称'
                },
                concat:{
                    required:'请输入联系人'
                },
                concat_tel:{
                    required:'请输入联系人手机',
                    rangelength:'请输入11位手机号码',
                    digits:'请输入11位手机号码'
                },
                area_id:{
                    required:'请选择所属地区'
                },
                detail_address:{
                    required:'请输入详细地址'
                },
                brank_name:{
                    required:'请输入开户银行名称'
                },
                account_holder:{
                    required:'请输入开户人'
                },
                brank_account:{
                    required:'请输入银行账号',
                    rangelength:'银行账号在15位到20位之间',
                    digits:'银行必须为整数'
                },
                parent_name:{
                    parent_name:'请选择上级商家'
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
                //
                //success2.show();
                //error2.hide();
                var type = $(form).find('input[type="checkbox"]:checked').val();
                if(!type) {
                    showToastr('error','请选择商家业务类型');
                    return false;
                }
                var form_data=$(form).serializeArray();
                App.blockUI();
                $.post('/seller/create',form_data,function(data){
                    App.unblockUI();
                    data=typeof data =='string'? $.parseJSON(data):data;
                    if(data.code !=='yes'){
                        showToastr('error',data.message);
                        return false;
                    }
                    showToastr('success',data.message);
                    setTimeout(function(){
                        window.location.href='/seller/index';
                    },2000);
                });
                return false;
            }
        });


    }
    return {
        //main function to initiate the module
        init: function () {
            handleValidation2();
            handleValidation3();

        }

    };

}();

jQuery(document).ready(function() {
    FormValidation.init();
});