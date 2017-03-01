<link href="<?= Yii::getAlias('@metro'); ?>/global/plugins/select2/css/select2.min.css" rel="stylesheet"
      type="text/css"/>
<link href="<?= Yii::getAlias('@metro'); ?>/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet"
      type="text/css"/>
<link href="<?= Yii::getAlias('@metro'); ?>/global/plugins/autocomplete/jquery.auto-complete.css" rel="stylesheet"
      type="text/css"/>
<div class="row">
    <div class="col-md-12">

        <!-- Begin: life time stats -->
        <div class="portlet light portlet-fit portlet-datatable bordered">
            <div class="portlet-title">
                <div class="caption">
                                        <i class="icon-bubble font-green"></i>
                    <span class="caption-subject font-green bold uppercase">商户信息完善</span>
                </div>
				<div class="actions">
					<div class="btn-group btn-group-devided" data-toggle="buttons">
						<button class="btn  red btn-outline  btn-sm active" name="back" type="button"><i class="fa fa-angle-left"></i> 返回</button>
					</div>
				</div>
            </div>
            <form id="createSellerNext" class="form-horizontal" method="post">

                <input type="hidden" name="_csrf-maintainer" value="<?= Yii::$app->request->csrfToken ?>">
                <input type="hidden" name="member_id" value="<?= $member_id ?>">

                <div class="form-body">
                    <div class="alert alert-danger display-hide">
                        <button class="close" data-close="alert"></button>
                        您有一些错误,请检查您的输入
                    </div>
                    <div class="alert alert-success display-hide">
                        <button class="close" data-close="alert"></button>
                        验证成功!
                    </div>
                    <div class="form-group">
                        <h4 class="control-label col-md-2 " style="">基本资料</h4>

                    </div>
                    <hr>
                    <div class="form-group  margin-top-20">
                        <label class="control-label col-md-3">商户名称
                            <span class="required"> * </span>
                        </label>

                        <div class="col-md-4">
                            <div class="input-icon right">
                                <i class="fa"></i>
                                <input type="text" class="form-control" value="<?= $seller['seller_name'] ?>"
                                       name="seller_name" placeholder=""/></div>
                        </div>
                    </div>

                    <div class="form-group  margin-top-20">
                        <label class="control-label col-md-3">登录账号

                        </label>

                        <div class="col-md-4">
                            <div class="input-icon right">
                                <i class="fa"></i>
                                <input type="text" class="form-control" value="<?= $user->name ?>" disabled="disabled"
                                       placeholder=""/></div>
                        </div>
                    </div>

                    <div class="form-group  margin-top-20">
                        <label class="control-label col-md-3">联系人
                            <span class="required"> * </span>
                        </label>

                        <div class="col-md-4">
                            <div class="input-icon right">
                                <i class="fa"></i>
                                <input type="text" class="form-control" name="concat" value="<?= $seller['concat'] ?>"
                                       placeholder=""/></div>
                        </div>
                    </div>
                    <div class="form-group  margin-top-20">
                        <label class="control-label col-md-3">联系电话
                            <span class="required"> * </span>
                        </label>

                        <div class="col-md-4">
                            <div class="input-icon right">
                                <i class="fa"></i>
                                <input type="text" class="form-control" name="concat_tel"
                                       value="<?= $seller['concat_tel'] ?>" placeholder=""/></div>
                        </div>
                    </div>

                    <div class="form-group  margin-top-20">
                        <label class="control-label col-md-3">商户地址
                            <span class="required"> * </span>
                        </label>

                        <div class="col-md-5">
                            <div class="input-icon right">
                                <select class="form-control input-small input-inline" name="province_id"
                                        id="province_id">
                                    <option value="">请选择省</option>
                                    <?php foreach ($province as $key => $val): ?>
                                        <option
                                            value="<?= $val->area_id . ',' . $val->area_name ?>" <?= $val->area_id == $seller['province_id'] ? 'selected' : '' ?>><?= $val->area_name ?></option>
                                    <?php endforeach ?>
                                </select>

                                <select class="form-control input-small input-inline" name="city_id" id="city_id"
                                        style="display: <?= $city_html ? 'inline' : 'none' ?>">
                                    <?= $city_html ?>
                                </select>
                                <select class="form-control input-small input-inline" name="area_id" id="area_id"
                                        style="display: <?= $area_html ? 'inline' : 'none' ?>">
                                    <?= $area_html ?>
                                </select>

                            </div>
                        </div>
                    </div>

                    <div class="form-group  margin-top-20">
                        <label class="control-label col-md-3">
                            <span class="required"></span>
                        </label>

                        <div class="col-md-5">
                            <div class="input-icon right">
                                <input type="text" class="form-control input-xlarge"
                                       value="<?= $seller['detail_address'] ?>" name="detail_address" placeholder=""/>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <h4 class="control-label col-md-2 " style="">收款信息</h4>

                    </div>
                    <hr>
                    <div class="form-group  margin-top-20">
                        <label class="control-label col-md-3">开户银行
                            <span class="required">  </span>
                        </label>

                        <div class="col-md-4">
                            <div class="input-icon right">
                                <i class="fa"></i>
								<?= \yii\helpers\Html::dropDownList('brank_name',$seller['brank_name'],\common\models\BankHouse::getBankNameList(),['class'=>'form-control form-filter input-sm','prompt'=>'请选择银行']) ?>

							</div>
                        </div>
                    </div>
                    <div class="form-group  margin-top-20">
                        <label class="control-label col-md-3">开户人
                            <span class="required"> </span>
                        </label>

                        <div class="col-md-4">
                            <div class="input-icon right">
                                <i class="fa"></i>
                                <input type="text" class="form-control" value="<?= $seller['account_holder'] ?>"
                                       name="account_holder" placeholder=""/></div>
                        </div>
                    </div>
                    <div class="form-group  margin-top-20">
                        <label class="control-label col-md-3">开户帐号
                            <span class="required">  </span>
                        </label>

                        <div class="col-md-4">
                            <div class="input-icon right">
                                <i class="fa"></i>
                                <input type="text" class="form-control" value="<?= $seller['brank_account'] ?>"
                                       name="brank_account" placeholder=""/></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <h4 class="control-label col-md-2 " style="">业务类型</h4>

                    </div>
                    <hr>
                    <div class="form-group">
                        <label class="control-label col-md-3">
                        </label>

                        <div class="col-md-4">
                            <div class="checkbox check_li" data-error-container="#form_2_services_error">

                                <label>
                                    <input type="checkbox" value="1"
                                           name="is_type[]" disabled <?= $seller['is_insurance'] == 1 ? 'checked="checked"' : '' ?> >
                                    保险销售 </label>
                                &nbsp;&nbsp;&nbsp;
                                <label>
                                    <input type="checkbox" value="2"
                                           name="is_type[]" disabled <?= $seller['is_repair'] == 1 ? 'checked="checked"' : '' ?> />
                                    手机维修 </label>
                            </div>

                            <div id="form_2_services_error"></div>
                        </div>
                    </div>
					<div class="form-group">
						<h4 class="control-label col-md-2 " style="">协议条款</h4>
					</div>
					<hr>
					<div class="form-group">
						<label class="control-label col-md-3">
							商家合同
							<span class="required"> * </span>
						</label>

						<div class="col-md-4">
							<div class="checkbox check_li" data-error-container="#form_2_services_error">
								<input type="checkbox" value="1" name="is_agreement" disabled <?= $seller['is_agreement'] == 1 ? 'checked="checked"' : '' ?> />
								<a class="font-purple-seance" title="点击查看" data-target="#service-responsive" href="<?= \yii\helpers\Url::to(['marticle/default/agreement']) ?>" data-toggle="modal">&lt;&lt;商家合同&gt;&gt;</a>
							</div>

							<div id="form_2_services_error"></div>
						</div>
					</div>
                    <div class="form-actions">
                        <div class="row">
                            <div class="col-md-offset-3 col-md-9">
                                <button type="submit" class="btn green">保存</button>
                                &nbsp;&nbsp;
                                <button type="reset" class="btn">重置</button>
                            </div>
                        </div>
                    </div>
            </form>
            <p></p>
        </div>
    </div>
</div>
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script src="<?= Yii::getAlias('@metro'); ?>/global/plugins/select2/js/select2.full.min.js"
        type="text/javascript"></script>
<script src="<?= Yii::getAlias('@metro'); ?>/global/plugins/jquery-validation/js/jquery.validate.min.js"
        type="text/javascript"></script>
<script src="<?= Yii::getAlias('@metro'); ?>/global/plugins/jquery-validation/js/additional-methods.min.js"
        type="text/javascript"></script>
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="<?= Yii::getAlias('@js'); ?>/validation-zh.js" type="text/javascript"></script>
<!-- END PAGE LEVEL SCRIPTS -->
<script>
    jQuery(document).ready(function () {
        $('#province_id , #city_id').on('change', function () {
            var province = $(this).val();
            var pval = province.split(',');
            var name = this.name;
            $.post('<?=Yii::$app->urlManager->createUrl("account/getarea")?>', {
                'id': pval[0],
                '_csrf-maintainer': $('meta[name="csrf-token"]').attr("content")
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
                if (name == 'province_id') {
                    $('#city_id').html(html).css('display', 'inline');
                    $('#area_id').css('display', 'none');
                } else if (name == 'city_id') {
                    $('#area_id').html(html).css('display', 'inline');
                }
            });
        });


        var form2 = $('#createSellerNext');

        form2.validate({
            errorElement: 'span',
            errorClass: 'help-block help-block-error',
            focusInvalid: false,
            ignore: "",
            rules: {
                seller_name: {
                    required: true,
                    rangelength: [2, 25]
                },
                concat: {
                    required: true,
                    rangelength: [2, 25]
                },
                concat_tel: {
                    required: true,
                    rangelength: [11, 11],
                    digits: true
                },
                area_id: {
                    required: true
                },
                detail_address: {
                    required: true
                }

            },
            messages: {
                seller_name: {
                    required: '请输入商户名称'
                },
                concat: {
                    required: '请输入联系人'
                },
                concat_tel: {
                    required: '请输入联系人手机',
                    rangelength: '请输入11位手机号码',
                    digits: '请输入11位手机号码'
                },
                area_id: {
                    required: '请选择所属地区'
                },
                detail_address: {
                    required: '请输入详细地址'
                }

            },

            invalidHandler: function (event, validator) {
                showToastr('error', '您有一些错误,请修正您的输入');
            },

            errorPlacement: function (error, element) {
                var icon = $(element).parent('.input-icon').children('i');
                icon.removeClass('fa-check').addClass("fa-warning");
                icon.attr("data-original-title", error.text()).tooltip({'container': 'body'});
                if (element.parents('.check_li').size() > 0) {
                    error.appendTo(element.parents('.check_li').attr("data-error-container"));
                } else {
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
                var form_data = $(form).serializeArray();
                App.blockUI();
                $.post('<?=Yii::$app->urlManager->createUrl('account/create')?>', form_data, function (data) {
                    App.unblockUI();
                    data = typeof data == 'string' ? $.parseJSON(data) : data;
                    if (data.code !== 'yes') {
                        showToastr('error', data.message);
                        return false;
                    }
                    showToastr('success', data.message);
                    setTimeout(function () {
                        window.location.href = '<?=Yii::$app->urlManager->createUrl('account/index')?>';
                    }, 2000);
                });
                return false;
            }
        })

    });

</script>

<!-- begin 协议 -->
<div class="modal fade bs-modal-lg" tabindex="-1" id="service-responsive" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-body">
				<img src="<?= Yii::getAlias('@metro') ?>/global/img/loading-spinner-grey.gif" alt="" class="loading">
				<span> &nbsp;&nbsp;Loading... </span>
			</div>
		</div>
	</div>
</div>
<!-- end 协议 modal -->