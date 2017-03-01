jQuery(document).ready(function () {
    jQuery('#admin_form').yiiActiveForm([{
        "id": "admin-username",
        "name": "username",
        "container": ".field-admin-username",
        "input": "#admin-username",
        "enableAjaxValidation": true,
        "validate": function (attribute, value, messages, deferred, $form) {
            yii.validation.required(value, messages, {"message": "用户名不能为空。"});
            yii.validation.string(value, messages, {
                "message": "用户名必须是一条字符串。",
                "max": 15,
                "tooLong": "用户名只能包含至多15个字符。",
                "skipOnEmpty": 1
            });
        }
    }, {
        "id": "admin-password",
        "name": "password",
        "container": ".field-admin-password",
        "input": "#admin-password",
        "validate": function (attribute, value, messages, deferred, $form) {
            yii.validation.required(value, messages, {"message": "密码不能为空。"});
            yii.validation.string(value, messages, {
                "message": "密码必须是一条字符串。",
                "max": 32,
                "tooLong": "密码只能包含至多32个字符。",
                "skipOnEmpty": 1
            });
        }
    }, {
        "id": "admin-phone",
        "name": "phone",
        "container": ".field-admin-phone",
        "input": "#admin-phone",
        "validate": function (attribute, value, messages, deferred, $form) {
            yii.validation.required(value, messages, {"message": "电话不能为空。"});
            yii.validation.string(value, messages, {
                "message": "电话必须是一条字符串。",
                "max": 11,
                "tooLong": "电话只能包含至多11个字符。",
                "skipOnEmpty": 1
            });
            yii.validation.regularExpression(value, messages, {
                "pattern": /^1[34578]{1}\d{9}$/,
                "not": false,
                "message": "请填写11位有效电话号码",
                "skipOnEmpty": 1
            });
        }
    }, {
        "id": "admin-email",
        "name": "email",
        "container": ".field-admin-email",
        "input": "#admin-email",
        "validate": function (attribute, value, messages, deferred, $form) {
            yii.validation.string(value, messages, {
                "message": "邮箱必须是一条字符串。",
                "max": 150,
                "tooLong": "邮箱只能包含至多150个字符。",
                "skipOnEmpty": 1
            });
        }
    }, {
        "id": "admin-is_system",
        "name": "is_system",
        "container": ".field-admin-is_system",
        "input": "#admin-is_system",
        "validate": function (attribute, value, messages, deferred, $form) {
            yii.validation.number(value, messages, {
                "pattern": /^\s*[+-]?\d+\s*$/,
                "message": "是否系统管理必须是整数。",
                "skipOnEmpty": 1
            });
        }
    }, {
        "id": "admin-role_id",
        "name": "role_id",
        "container": ".field-admin-role_id",
        "input": "#admin-role_id",
        "validate": function (attribute, value, messages, deferred, $form) {
            yii.validation.required(value, messages, {"message": "角色不能为空。"});
            yii.validation.number(value, messages, {
                "pattern": /^\s*[+-]?\d+\s*$/,
                "message": "角色必须是整数。",
                "skipOnEmpty": 1
            });
        }
    }, {
        "id": "admin-login_at",
        "name": "login_at",
        "container": ".field-admin-login_at",
        "input": "#admin-login_at",
        "validate": function (attribute, value, messages, deferred, $form) {
            yii.validation.number(value, messages, {
                "pattern": /^\s*[+-]?\d+\s*$/,
                "message": "Login At必须是整数。",
                "skipOnEmpty": 1
            });
        }
    }, {
        "id": "admin-login_ip",
        "name": "login_ip",
        "container": ".field-admin-login_ip",
        "input": "#admin-login_ip",
        "validate": function (attribute, value, messages, deferred, $form) {
            yii.validation.string(value, messages, {
                "message": "Login Ip必须是一条字符串。",
                "max": 15,
                "tooLong": "Login Ip只能包含至多15个字符。",
                "skipOnEmpty": 1
            });
        }
    }], {"validationUrl": "\/admin\/ckform"});
});