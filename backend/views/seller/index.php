<?php
$this->title = '数据表格';

?>
<!-- BEGIN PAGE LEVEL PLUGINS -->
<!-- END PAGE LEVEL PLUGINS -->

<div class="page-bar">
	<ul class="page-breadcrumb">
		<li><a href="#">商家</a> <i class="fa fa-circle"></i></li>
		<li><span>商家列表</span></li>
	</ul>

</div>
<div class="row">
	<div class="col-md-12">
		<div class="note note-danger">
			<p>保险&理赔商家列表</p>
			<p>所有一级商户为签约商户</p>
            <p>商家导出默认导出当前条件下全部商家,<font color="#ff4500">最好不要超过5000</font></p>
		</div>
		<!-- Begin: life time stats -->
		<div class="portlet light portlet-fit portlet-datatable bordered">
			<div class="portlet-title">

				<div class="actions">
					<div class="btn-group ">
						<a href="<?= \yii\helpers\Url::toRoute(['user/create']) ?>"
							class="btn blue-hoki"> <i class="fa fa-plus-square"></i> 新增商户
						</a>
                        <a attr-url="<?=Yii::$app->urlManager->createUrl('seller/export')?>"  id="seller-download"
                           class="btn  blue  btn-sm ">
                            <i class="fa fa-download"></i> 导出
                        </a>
					</div>
				</div>
			</div>
			<div class="portlet-body">
				<div class="table-actions-wrapper ">
					<span> </span>


				</div>
				<div class="table-container">
					<form id="datatable_form">
						<div class="table-search text-right">

                            <select class="table-group-action-input form-control form-filter input-inline  input-sm" name="province_id" id="province_id">
                                <option value="">请选择省</option>
								<?php foreach ($province as $key => $val): ?>
                                    <option value="<?= $val->area_id . ',' . $val->area_name ?>" ><?= $val->area_name ?></option>
								<?php endforeach ?>
                            </select>

                            <select class="table-group-action-input form-control form-filter input-inline  input-sm" name="city_id" id="city_id">
                                <option value="">请选择市</option>
                            </select>
                            <select class="table-group-action-input form-control form-filter input-inline  input-sm" name="area_id" id="area_id">
                                <option value="">请选择区</option>
                            </select>
                            &nbsp;&nbsp;
							<?= \yii\helpers\Html::dropDownList('seller_type','',[1=>'保险商家',2=>'理赔商家'],['prompt'=>'按商家类型','class'=>'table-group-action-input form-control form-filter input-inline  input-sm'])?>
							&nbsp;&nbsp;
                            <select
								class="table-group-action-input form-control form-filter input-inline  input-sm"
								id="datatable_filter" name="status">
								<option value="">合作状态</option>
								<option value="2">合作中</option>
								<option value="1">已终止</option>
							</select> &nbsp;
                            <select
								class="table-group-action-input form-control form-filter input-inline  input-sm"
								name="p_p" id="p_p_s_1">
								<option value="">商户等级</option>
								<option value="2">签约商户</option>
								<option value="1">子商家</option>
							</select>&nbsp;
                            <select
                                    class="table-group-action-input form-control form-filter input-inline  input-sm"
                                    name="p_s_id" id="p_s_id">
                                <option value="">子商家所属上级</option>
                            </select>
                        &nbsp;
                            <select
                                    class="table-group-action-input form-control form-filter input-inline  input-sm"
                                    name="filter_search_key" onchange="$('#datatable_search').attr('placeholder','请输入'+$(this).children('option:selected').text())" >
                                <option  value="seller_name">商户名称</option>
                                <option value="concat">联系人</option>
                                <option value="concat_tel">联系电话</option>
                            </select>
                            <input type="text" size="30"
								id="datatable_search" name="filter" class="input-md form-filter form-control input-inline"
								placeholder="请输入商户名称" />
							<button class="btn btn-sm green filter-submit " type="button"
								id="datatable_submit">
								<i class="fa fa-search"></i> 搜索
							</button>
						</div>
					</form>
					<table
						class="table table-striped table-bordered table-hover table-checkable"
						id="datatable_ajax">
						<thead>

							<tr role="row" class="heading">
								<th width="10%">商户名称</th>
								<th width="8%">登陆账号</th>
								<th width="12%">所在区域</th>
								<th width="8%">联系人</th>
								<th width="8%">联系电话</th>
								<th width="7%">合作状态</th>
								<th width="8%">商家类型</th>
								<th width="8%">商家等级</th>
                                <th width="8%">所属上级</th>
								<th>操作</th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
			</div>
		</div>
		<!-- End: life time stats -->
	</div>
</div>
<!-- BEGIN PAGE LEVEL PLUGINS -->
<!-- END PAGE LEVEL PLUGINS -->
<script type="text/javascript">
    $(function(){

        var URL = '<?= \yii\helpers\Url::to(['seller/index']) ?>';
        EcommerceList.init(URL,{},false,'datatable_ajax');


        //条件导出下载
        $('#seller-download').on('click',function(){
            var parm=$('#datatable_form').serialize();
            var href = $(this).attr('attr-url');
            href +='?'+parm;
            console.log(href);
            bootbox.confirm("确认导出当前条件下所有商家?&nbsp;<font color='#ff4500'>建议导出总量不要超过5000</font>", function(result) {
                if(result){
                    window.open(href, "_blank")
                }
            });
        });


        //省市区加载
        $('#province_id , #city_id').on('change', function () {
            var html = '<option value="">请选择地区</option>';
            var province = $(this).val();
            if(!province){
                $('#city_id').html(html);
                $('#area_id').html(html);
                return ;
            }
            var pval = province.split(',');
            var name = this.name;
            App.startPageLoading();
            $.post('<?= \yii\helpers\Url::to(['seller/getarea']) ?>', {
                'id': pval[0],
                '_csrf-backend': $('meta[name="csrf-token"]').attr("content")
            }, function (data) {
                data = typeof data == 'string' ? $.parseJSON(data) : data;
                if (data.code !== 'yes') {
                    showToastr('warning', data.message);
                    return false;
                }

                $.each(data.data, function (index, ele) {
                    html += '<option value="' + ele.area_id + ',' + ele.area_name + '">' + ele.area_name + '</option>';
                })
                if (name == 'province_id') {
                    $('#city_id').html(html);
                    $('#area_id').html(html);

                } else if (name == 'city_id') {
                    $('#area_id').html(html);
                }
                App.stopPageLoading();
            });
        });

        //子商家所属上级
        var flag_is_req = false;
        $('#p_p_s_1').on('change', function () {
            var value = $(this).val();
            if (flag_is_req === false && value === '1') {
                App.startPageLoading();
                $.post(URL, {
                    '_csrf-backend': $('meta[name="csrf-token"]').attr("content"),
                    'leader': 'yes'
                }, function (data) {
                    if (data.code == 'yes') {

                        var str = '<option value="">子商家所属上级</option>';
                        $.each(data.data, function (index, val) {
                            str += '<option value=' + val.seller_id + '>' + val.seller_name + '</option>';
                        })
                        $('#p_s_id').html(str);
                        App.stopPageLoading();
                        flag_is_req = true;
                    }

                }, 'json');
            } else {
                $('#p_s_id')[0].selectedIndex = 0;
            }
        });


    });

    function handleStatus(seller_id, status) {
        var s_text = (status == 0) ? '确定终止合作并冻结其所以未激活卡券？' : '确定重启合作并保持相关冻结卡券状态不变？';
        bootbox.confirm(s_text, function (result) {
            if (result) {
                App.blockUI();
                $.post('<?=Yii::$app->urlManager->createUrl(['seller/change']);?>', {
                    '_csrf-backend': $('meta[name="csrf-token"]').attr("content"),
                    'seller_id': seller_id,
                    'status': status
                }, function (data) {
                    App.unblockUI();
                    if (data.code != 'yes') {
                        showToastr('error', data.message);
                        return false;
                    }
                    showToastr('success', data.message);
                    setTimeout(function () {
                        window.location.reload();
                    }, 1000);
                });
            }
        });
    }

    function restPwd(seller_id){
    	bootbox.confirm("确要重置该商家的密码吗？", function(result) {
			if(result){
				App.blockUI();
				$.post('<?=Yii::$app->urlManager->createUrl(['seller/rest']);?>',{
					'_csrf-backend':$('meta[name="csrf-token"]').attr("content"),
					'seller_id':seller_id
				},function(data){
					App.unblockUI();
					var msg = 'success'
					if(data.code !='yes'){
		                msg ='error';
		            }
					showToastr(msg,data.message);
	                return false;
				})
			}
		});
    }
</script>
