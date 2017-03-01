/**
 * Created by tp on 16/8/16.
 */


var handleValidation3 = function() {
    var form2 = $('#createInsuranceCoverage');
    var error2 = $('.alert-danger', form2);
    var success2 = $('.alert-success', form2);

    form2.validate({
        errorElement: 'span',
        errorClass: 'help-block help-block-error',
        focusInvalid: false,
        ignore: "",
        rules: {
            coverage_name: {
                required: true,
                rangelength: [2, 25]
            },
            company_name:{
                required:true
            },
            type_name:{
                required:true
            },
            period:{
                required: true
            },
            cost_price: {
                required: true,
                number: true,
                range: [0.01, 99999]

            },
            wholesale_price:{
                required: true,
                number:true,
                range:[0.01,99999]
            },
            official_price: {
                required: true,
                number: true,
                range: [0.01, 99999]
            },
            max_payment: {
                required: true,
                number: true,
                range: [0.01, 999999]
            }
        },
        messages:{
            company_name: {
                required: '<i class="fa fa-exclamation-circle"></i>请选择保险公司'
            },
            type_name: {
                required: '<i class="fa fa-exclamation-circle"></i>请选择保险类型'
            },
            coverage_name:{
                required: '<i class="fa fa-exclamation-circle"></i>请输入险种名称'
            },
            period: {
                required: '<i class="fa fa-exclamation-circle"></i>请输入保险质保期'
            },
            cost_price: {
                required: '<i class="fa fa-exclamation-circle"></i>请输入保险成本价',
                number: '<i class="fa fa-exclamation-circle"></i>请输入数字',
                range: '<i class="fa fa-exclamation-circle"></i>不超过5位数字整数'
            },
            wholesale_price: {
                required: '<i class="fa fa-exclamation-circle"></i>请输入保险批发价',
                number: '<i class="fa fa-exclamation-circle"></i>请输入数字',
                range: '<i class="fa fa-exclamation-circle"></i>请输入在0.01到99999之间的数字'
            },
            official_price: {
                required: '<i class="fa fa-exclamation-circle"></i>请输入保险官方价',
                number: '<i class="fa fa-exclamation-circle"></i>请输入数字',
                range: '<i class="fa fa-exclamation-circle"></i>不超过5位数字整数'
            },
            max_payment: {
                required: '<i class="fa fa-exclamation-circle"></i>请输入保险最高赔付价',
                number: '<i class="fa fa-exclamation-circle"></i>请输入数字',
                range: '<i class="fa fa-exclamation-circle"></i>不超过6位数字整数'
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
            var form_data=$(form).serializeArray();
            App.blockUI();
            $.post('/coverage/update',form_data,function(data){
                App.unblockUI();
                data=typeof data =='string'? $.parseJSON(data):data;
                if(data.code !=='yes'){
                    showToastr('error',data.message);
                    return false;
                }
                showToastr('success',data.message);
                setTimeout(function(){
                    window.location.href='/coverage/index';
                },2000);
            });

            return false;
        }
    });


}

jQuery(document).ready(function () {



    $('#official_price').bind('keyup change blur mouseup focus keypress', function () {
        checkCoverage();
    });


    handleValidation3();
});

function checkCoverage() {

    var company_val = $("#company_id").val();
    var company=company_val.split(',');
    var sp = company[2] || '';

    var period = $("#period").val();
    if (isNaN(period)) {
        period='00'
    }
    if (period.length < 2) {
        period = '0' + period;
    }

    var official_price = $("#official_price").val();
    if (official_price.length == 0) {
        official_price = '000';
    } else if (official_price.length == 1) {
        official_price = '00' + official_price;
    }
    else if (official_price.length == 2) {
        official_price = '0' + official_price;
    }else {
        official_price = '' + parseInt(official_price);

    }

    var type_val = $("#type_id").val();
    var type = type_val.split(',');
    var type_code=type[2] || '00';
    //险种生成规则：保险公司英文代码+保期（两位数字）+官方指导价+保险类型识别代码
    var coverage_code = sp + period + official_price + type_code;
    $('#coverage_code').val(coverage_code);

}


