
<div class="row">
    <div class="col-md-12">

        <!-- Begin: life time stats -->
        <div class="portlet box green">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-wallet"></i>卡券申请</div>

            </div>
            <div class="portlet-body">
                <div class="table-scrollable">
                    <form id="card_applf_form">
                        <input type="hidden" name="_csrf-maintainer" value="<?=Yii::$app->request->csrfToken?>">
                    <table class="table table-striped table-hover">
                        <thead>
                        <tr>
                            <th></th>
                            <th>#</th>
                            <th> 险种名称</th>
                            <th> 险种编号</th>
                            <?php if(!$is_two): ?><th> 险种金额 </th><?php endif; ?>
                            <th> 申请数量 </th>
                        </tr>
                        </thead>
                        <tbody>
                            <?php foreach($result as $key=>$val): ?>
                                <tr>
                                    <td width="3%"><input type="checkbox" name="coverage[coverage_<?=$key?>][code]" class="coverage_checkbox" data-num="<?= $key?>" data-count="<?= count($result) ?>" data-price="<?=$val['wholesale_price']?>" value="<?=$val['coverage_code']?>"></td>
                                    <td><?=$key+1?></td>
                                    <td width="22%"><?=join(' ',[$val['company_name'],$val['type_name'],$val['coverage_name']])?></td>
                                    <td><?=$val['coverage_code']?></td>
                                    <?php if(!$is_two): ?> <td class="font-purple-studio">¥ <?=$val['wholesale_price']?></td><?php endif; ?>
                                    <td>
                                        <input type="text" id="price_<?= $key ?>" name="coverage[coverage_<?=$key?>][num]" class="form-control input-inline input-small buyer_num">
                                        <input type="hidden" name="coverage[coverage_<?=$key?>][price]" value="<?=$is_two?0:$val['wholesale_price']?>" >
                                    </td>

                                </tr>
                            <?php endforeach ?>
							<tr class="font-grey-salsa">
								<td colspan="5" align="right">
									当前共选择险种： <span class="font-purple-studio card_coverage">0 </span> 种 ,共计卡券数量：<span class="font-purple-studio card_total_num">0 </span> 张<?php if(!$is_two): ?> ,金额总共：<span class="font-red-thunderbird card_total_price" style="font-size: 16px">¥ 0.00</span><?php endif; ?>
								</td>
								<td>

								</td>
							</tr>

							<tr class="font-grey-salsa"  align="right">
								<td colspan="4">请选择申领支付款方式</td>
								<td>
									<?= \yii\helpers\Html::dropDownList('apply_type','3',\common\models\CardOrderPayback::typeData(),['class'=>'form-control form-filter input-sm']) ?>
								</td>
								<td>

								</td>

							</tr>
							<tr  align="right">
								<td colspan="5">
									<button type="button" class="btn green btn-default submit_card" onclick="check_coverge()">确定申请</button>
								</td>
								<td>

								</td>
							</tr>
                        </tbody>
                        <tfoot>

                        </tfoot>
                    </table>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(function(){
        $('.buyer_num').on('blur',function(){
			$(this).removeClass('border-red-mint');
            var val = $.trim($(this).val());
			if(!val) {
				return false;
			}
            var reg= /^\d+$/;
            if(!reg.test(val)){
                $(this).addClass('border-red-mint');
                showToastr('error','购买数量必须为整数！');
                return false;
            }
			var checkbox=$(this).parent().prevAll().has('input').find('input[type="checkbox"]');
			var checkbox_val=checkbox.get(0).checked;
            var value=parseInt(val);
            if(value >= 0){
                if(!checkbox_val) {
					checkbox.click();
				}else{
					total();
				}
            }
        });

        $('.coverage_checkbox').on('click',function(){
			total();
        });
    })

    function total(){
        var total_coverage=0;
        var total_num=0;
        var total_price=0;
        $('.coverage_checkbox').each(function(){
			var count = $(this).attr('data-count');
			var price = $(this).attr('data-price');
			var key = $(this).attr('data-num');
			//console.log(count,price);return;
            if(this.checked){
                ++ total_coverage;
				var number_value = $('#price_'+key).val();
				var reg= /^\d+$/;
				if(reg.test(number_value)){
					val=parseInt(number_value);
					total_num += val;
					total_price += price * val;
				}
            }
        })
        $('.card_coverage').text(total_coverage);
        $('.card_total_num').text(total_num);
        $('.card_total_price').text('¥ '+total_price);
    }

    function check_coverge(){
        var bstop =true;
		var has_checked = false;
        $('.coverage_checkbox').each(function(){
            if(this.checked){
				has_checked = true;
                var input = $(this).parents('td').nextAll().find('.buyer_num');
                var val=  $.trim(input.val());
                var reg= /^\d+$/;
                if(!reg.test(val)){
                    input.addClass('border-red-mint');
                    bstop = false;
                }
            }
        })
        if(has_checked && bstop){
            var form_data= $('#card_applf_form').serialize();
            $.post('<?= \yii\helpers\Url::to(['card/applf']) ?>',form_data,function(data){
                if(data.code == 'yes'){
                    showToastr('success',data.message);
                    setTimeout(function(){
                        window.location.href='<?= \yii\helpers\Url::to(['card/index']) ?>';
                    },1500);
                }else{
                    showToastr('error',data.message);
                }
            });
        }else{
            showToastr('error','请输入要购买的险种、数量?');
        }
    }
</script>