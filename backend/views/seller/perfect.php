<link href="<?= Yii::getAlias('@metro'); ?>/global/plugins/select2/css/select2.min.css" rel="stylesheet"
      type="text/css"/>
<link href="<?= Yii::getAlias('@metro'); ?>/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet"
      type="text/css"/>
<link href="<?= Yii::getAlias('@metro'); ?>/global/plugins/autocomplete/jquery.auto-complete.css" rel="stylesheet"
      type="text/css"/>


        <!-- Begin: life time stats -->
        <div class="portlet light portlet-fit portlet-datatable bordered">
            <div class="portlet-title">
                <div class="caption">
                                    <i class="icon-bubble font-green"></i>
                    <span class="caption-subject font-green bold uppercase">商户信息完善</span>
                </div>

            </div>
			<div class="note note-danger">
				<p> 完善商户信息 .</p>
			</div>
            <form id="createSellerNext" class="form-horizontal" method="post">

                <input type="hidden" name="_csrf-backend" value="<?= Yii::$app->request->csrfToken ?>">
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
                                <input type="text" class="form-control" value="<?= $user->phone ?>" disabled="disabled"
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

                    <div class="form-group  margin-top-20">
                        <label class="control-label col-md-3">商家等级
                            <span class="required"> * </span>
                        </label>

                        <div class="col-md-4">
                            <div class="radio-list">
                                <?php $disabled = $seller['seller_id']?'disabled':'' ?>
                                <label class="radio-inline">
                                    <input type="radio" value="1"  name="p_name" <?=!$seller['pid']?'checked="checked" ':$disabled?> > 一级商家 </label>

                                <label class="radio-inline">
                                    <input type="radio" value="2" name="p_name" <?=$seller['pid']?'checked="checked" ':$disabled?> /> 二级商家 </label>
                            </div>

                        </div>
                    </div>
                    <div class="form-group  margin-top-20 <?=$seller['pid']?'':'hide'?>" id="seller_level">
                        <label class="control-label col-md-3">
                            <span class="required">  </span>
                        </label>

                        <div class="col-md-4">
                            <div class="input-icon right">
                                <i class="fa"></i>
                                <input type="text" class="form-control" value="<?=$seller['parent_name']?>" name="parent_name"
                                       id="autocome" placeholder="请选择上级商家" <?=$seller['pid']?'disabled':''?> />
                                <input type="hidden" name="pid" id="seller_pid" value="<?=$seller['pid']?>" >
                                <span class="help-block " style="color: red">&nbsp;&nbsp;&nbsp;请选择提示框中的具体商家</span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <h4 class="control-label col-md-2 " style="">收款信息</h4>

                    </div>
                    <hr>
                    <div class="form-group  margin-top-20">
                        <label class="control-label col-md-3">开户银行
                            <span class="required"></span>
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
                            <span class="required"></span>
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
                                           name="is_type[]" <?= $seller['is_insurance'] == 1 ? 'checked="checked" readonliy' : '' ?> >
                                    保险销售 </label>
                                &nbsp;&nbsp;&nbsp;
                                <label>
                                    <input type="checkbox" value="2"
                                           name="is_type[]" <?= $seller['is_repair'] == 1 ? 'checked="checked" readonliy' : '' ?> />
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

						<div class="col-md-9">
							<div class="input-icon right">
								<i class="fa"></i>
								<input type="checkbox" value="1" name="is_agreement"  <?= $seller['is_agreement'] == 1 ? 'checked="checked"' : '' ?> />
								<a class="font-purple-seance" title="点击查看" data-target="#service-responsive" href="<?= \yii\helpers\Url::to(['marticle/default/agreement']) ?>" data-toggle="modal">&lt;&lt;商家合同&gt;&gt;</a>
							</div>

						</div>
					</div>
					<hr>
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

<!-- BEGIN PAGE LEVEL PLUGINS -->
<script src="<?= Yii::getAlias('@metro'); ?>/global/plugins/select2/js/select2.full.min.js"
        type="text/javascript"></script>
<script src="<?= Yii::getAlias('@metro'); ?>/global/plugins/jquery-validation/js/jquery.validate.min.js"
        type="text/javascript"></script>
<script src="<?= Yii::getAlias('@metro'); ?>/global/plugins/jquery-validation/js/additional-methods.min.js"
        type="text/javascript"></script>
<script src="<?= Yii::getAlias('@metro'); ?>/global/plugins/autocomplete/jquery.auto-complete.min.js"
        type="text/javascript"></script>
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="<?= Yii::getAlias('@js'); ?>/validation-zh.js" type="text/javascript"></script>
<script src="<?= Yii::getAlias('@js'); ?>/form-validation.js" type="text/javascript"></script>
<!-- END PAGE LEVEL SCRIPTS -->
<script>
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
                if (name == 'province_id') {
                    $('#city_id').html(html).css('display', 'inline');
                    $('#area_id').css('display', 'none');
                } else if (name == 'city_id') {
                    $('#area_id').html(html).css('display', 'inline');
                }
            });
        });


        $('#autocome').autoComplete({
            minChars: 2,
            cache:false,
            source: function (term, suggest) {
                $.getJSON('<?=Yii::$app->urlManager->createUrl('seller/level')?>', {q: term}, function (data) {
                    if(data.length == 1 && term == data[0][1]){
                        $('#seller_pid').val(data[0][0]);
                    }else{
                        $('#seller_pid').val('');
                    }

                    data =data.length?data:['@@@@@'];
                    suggest(data);
                });


            },
            renderItem: function (item, search) {
                console.log(item);
                if(item === '@@@@@')
                    return '<div class="autocomplete-suggestion" style="color: red" data-langname="' + search + '" data-lang=" " data-val="' + search + '"> × 没有匹配的商家</div>';
                else
                    return '<div class="autocomplete-suggestion" data-langname="' + item[1] + '" data-lang="' + item[0] + '" data-val="' + search + '">' + item[1] + '</div>';
            },
            onSelect: function (e, term, item) {
                $('#seller_pid').val(item.data('lang'));
                $('#autocome').val(item.data('langname'));
            }
        })

        $('#autocome').on('blur',function(){
            if($('#seller_pid').val() < 1){
                $(this).val('')
            }
        });

        $('input[name="p_name"]').on('click',function(){
            if($(this).val() == 2){
                $('#seller_level').removeClass('hide');
            }else{
                $('#seller_level').addClass('hide');
            }
        });
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