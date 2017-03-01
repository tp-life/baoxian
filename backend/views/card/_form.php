<!-- BEGIN FORM-->
<form action="" id="card_form" method="POST" class="form-horizontal">
    <div class="form-body">
        <div class="form-group">
            <label class="control-label col-md-3">保险配置
                <span class="required"> * </span>
            </label>

            <div class="col-md-2">
                <select class="form-control" name="type_id" id="type_id">
                    <option value="0">请选择保险类型</option>
                    <?php foreach($insurance_coverage_data as $vo): ?>
                        <option value="<?= $vo['type_id']; ?>"><?= $vo['type_name']; ?></option>
                    <?php endforeach ?>
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-control" name="company" id="company">
                    <option value="0">请选择保险公司</option>
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-control" name="coverage" id="coverage">
                    <option value="0">请选择保险险种</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-md-3">卡券数量
                <span class="required"> * </span>
            </label>

            <div class="col-md-2">
                <input type="text" placeholder="请输入[1-1000]张数" id="number" name="number" maxlength="4" data-required="1" class="form-control"/>
                <span>卡券数量生成数量在1千(1-1000)范围 </span>
                <span style="color:red;" id="prompt"></span>
            </div>
        </div>
    </div>
    <input type="hidden" name="_csrf-backend" value="<?= Yii::$app->request->csrfToken ?>">
	<input type="hidden" name="is_export" id="is_export" value="">

    <div class="form-actions">
        <div class="row">
            <div class="col-md-offset-3 col-md-9">
				<button type="reset" class="btn grey-salsa btn-outline">重置按钮</button>&nbsp;
                <button type="button" class="btn green" id="submitBtn" is_export="0">生成卡券</button>&nbsp;
				<button type="button" class="btn red" id="submitBtnDaochu" is_export="1">生成并导出卡券</button>

            </div>
        </div>
    </div>
</form>
<!-- END FORM-->
<script src="<?= Yii::getAlias('@metro'); ?>/global/plugins/jquery-validation/js/jquery.validate.min.js"
        type="text/javascript"></script>
<script src="<?= Yii::getAlias('@metro'); ?>/global/plugins/jquery-validation/js/additional-methods.min.js"
        type="text/javascript"></script>
<script src="<?= Yii::getAlias('@js'); ?>/validation-zh.js" type="text/javascript"></script>
<script type="text/javascript">
    $(function(){
        //保险公司
        $('#type_id').on('change',function(){

            var type_id = $(this).val();
            var str = '<option value="0">请选择保险公司</option>';
            var str1 = '<option value="0">请选择保险险种</option>';
            if(type_id == 0){
                $('#company').html(str);
                $('#coverage').html(str1);
                return ;
            }
            App.startPageLoading();
            $.post('<?= \yii\helpers\Url::to(['card/company']) ?>', { 'type_id': type_id ,'_csrf-backend':$('meta[name="csrf-token"]').attr("content")},
                function(data){
                    if(data.code == 'yes'){
						App.stopPageLoading();
                        $.each(data.data,function(index,val){
                            str+='<option value='+val.company_id+'>'+val.company_name+'</option>';
                        })
                    }

                    $('#company').html(str);
                }, "json");
        });

        //保险险种
        $('#company').on('change',function(){
            var company_id = $(this).val();
            var str = '<option value="0">请选择保险险种</option>';
            if(company_id == 0){
                $('#coverage').html(str);
                return ;
            }
            App.startPageLoading();
            $.post('<?= \yii\helpers\Url::to(['card/coveragelist']) ?>', { 'company_id': company_id ,'_csrf-backend':$('meta[name="csrf-token"]').attr("content")},
                function(data){
                    if(data.code == 'yes'){
                        App.stopPageLoading();
                        $.each(data.data,function(index,val){
                            str+='<option value='+val.id+'#'+val.coverage_code+'>'+val.coverage_code+'</option>';
                        })
                    }

                    $('#coverage').html(str);
                }, "json");
        });

        $('#number').on('input propertychange',function(){
            var v = $(this).val();
            var v_int =  parseInt(v);
			if(v_int>1000){
				$('#prompt').html('输入数目不能大于1000');
				return false;
			}
            $.post('<?= \yii\helpers\Url::to(['card/cknum']) ?>', { 'number': v_int ,'_csrf-backend':$('meta[name="csrf-token"]').attr("content")},
                function(data){
                    if(data.code == 'yes'){
                        console.log(data);
                        $('#prompt').html(data.data);
                    }

                }, "json");
        });

        $("#submitBtn,#submitBtnDaochu").click(function(){

			var kay_value_set = $("#card_form").serializeArray();
			kay_value_set.push({name:'is_export',value:$(this).attr('is_export')})
			$("#is_export").val($(this).attr('is_export'));
			console.log(kay_value_set);

			var ok = true;

			$(kay_value_set).each(function(key,item){
				if(item.name=='type_id' && (item.value=='0'|| item.value=='')){
					showToastr('error', '请选择保险类型');
					ok = false;
				}
				if(item.name=='company' && (item.value=='0'|| item.value=='')){
					showToastr('error', '请选择保险公司');
					ok = false;
				}
				if(item.name=='coverage' && (item.value=='0'|| item.value=='')){
					showToastr('error', '请选择险种');
					ok = false;
				}

				if(item.name=='number' && (item.value=='0'|| item.value=='')){
					showToastr('error', '请输入生成卡券数量');
					ok = false;
				}
			});
			if(!ok){
				return false;
			}
			App.startPageLoading();
			$("#card_form").submit();
        });
    });

</script>