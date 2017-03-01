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
							<?= \yii\helpers\Html::dropDownList('seller_type','',[1=>'保险商家',2=>'理赔商家'],['prompt'=>'按商家类型','class'=>'table-group-action-input form-control form-filter input-inline  input-sm'])?>
							&nbsp;&nbsp;
                            <select
								class="table-group-action-input form-control form-filter input-inline  input-sm"
								id="datatable_filter" name="status">
								<option value="">合作状态</option>
								<option value="2">合作中</option>
								<option value="1">已终止</option>
							</select> &nbsp;&nbsp; <select
								class="table-group-action-input form-control form-filter input-inline  input-sm"
								name="p_p">
								<option value="">商户等级</option>
								<option value="2">签约商户</option>
								<option value="1">子商家</option>
							</select> &nbsp;&nbsp; <input type="text" size="30"
								id="datatable_search" name="filter" class="input-md form-filter form-control input-inline"
								placeholder="请输入商户名称/商户ID进行查询" />
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
								<th width="10%">登陆账号</th>
								<th width="15%">所在区域</th>
								<th width="10%">联系人</th>
								<th width="8%">联系电话</th>
								<!-- <th width="20%"> 收款信息</th>-->
								<th width="7%">合作状态</th>
								<th width="8%">商家类型</th>
								<th width="8%">商家等级</th>
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

        EcommerceList.init('/seller/getdata',{},false,'datatable_ajax');


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


    });

    function handleStatus(seller_id,status){
        App.blockUI();
        $.post('<?=Yii::$app->urlManager->createUrl(['seller/change']);?>',{'_csrf-backend':$('meta[name="csrf-token"]').attr("content"),'seller_id':seller_id,'status':status},function(data){
            App.unblockUI();
            if(data.code !='yes'){
                showToastr('error',data.message);
                return false;
            }
            showToastr('success',data.message);
            setTimeout(function(){
                window.location.reload();
            },1000);
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
