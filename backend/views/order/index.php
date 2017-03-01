<?php
$this->title = '数据表格';
?>
<!-- BEGIN PAGE LEVEL PLUGINS -->
<!-- END PAGE LEVEL PLUGINS -->

<div class="page-bar">
    <ul class="page-breadcrumb">
        <li>
            <a href="<?= \yii\helpers\Url::to(['order/index']) ?>">订单</a>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <span>保险订单</span>
        </li>
    </ul>

</div>
<div class="row">
    <div class="col-md-12">
        <div class="note note-danger">
			<p>保险卡券激活与保险购买处理</p>
			<p>保险审核与保单号更新</p>
            <p>导出最多导出<span style="color: red">5000</span>条记录，超出记录将不进行导出，请根据需要按条件进行导出</p>
            <p style="color:red">保险单号导入为匹配IMEI号，格式为csv。<a class="btn btn-sm btn-outline" target="_blank" href="<?=Yii::$app->urlManager->createUrl('order/example')?>">下载导入demo</a></p>
        </div>
        <!-- Begin: life time stats -->
        <div class="portlet light portlet-fit portlet-datatable bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-user font-green"></i>
                    <span class="caption-subject font-green sbold uppercase">保险订单列表</span>
                </div>
                <div class="actions">
                    <div class="btn-group ">
                        <a href="<?=Yii::$app->urlManager->createUrl('order/export')?>" target="_blank" id="order-download"
                           class="btn  blue  btn-sm ">
                            <i class="fa fa-download"></i> 导出</a>
                        <a href="javascript:void(0)"  id="upload_baoxian"
                           class="btn  green   btn-sm ">
                            <i class="fa fa-upload"></i> 批量导入保险号</a>
                    </div>
                    <input type="file" name="baoxian" style="display: none" id="upload_baoxian_input">
                </div>
            </div>
            <div class="portlet-body">

                <div class="table-container">
                    <form id="datatable_form">
                        <div class="table-search text-right">
                            <?= \yii\helpers\Html::dropDownList('status','',\common\models\Order::getBackendStatusData(),['prompt'=>'状态查询','class'=>'table-group-action-input form-control form-filter input-inline  input-sm']) ?>
                            &nbsp;&nbsp;&nbsp;&nbsp;
                            <div class="input-icon right" style="display: inline;">
                                <i class="fa fa-calendar font-blue"></i>
                                <input class="input-md form-filter date date-picker input-inline form-control" size="20" type="text" name="date" placeholder="起始时间">
                            </div>

                            至

                            <div class="input-icon right" style="display: inline;">
                                <i class="fa fa-calendar font-blue"></i>
                                <input class="input-md form-filter date date-picker input-inline form-control" size="20" type="text" name="e_date" placeholder="结束时间">
                            </div>
                            &nbsp;&nbsp;&nbsp;&nbsp;


                            <select
                                class="table-group-action-input form-control form-filter input-inline  input-sm"
                                name="fg">
                                <option value="buyer">投保人</option>
                                <option value="buyer_phone"> 投保手机号</option>
                                <option value="imei_code"> 手机IEMI号</option>
                                <option value="policy_number"> 保单号</option>
                                <option value="seller_name">销售商家</option>
                                <option value="coverage_code">保险险种</option>
                                <option value="card_number">卡券序列号</option>
                                <option value="order_sn">订单编号</option>
                            </select>

                            <input type="text"  class="input-md form-filter form-control input-inline" size="30" name="search"
                                   placeholder="请输入对应的关键字进行查询"/>
                            <button class="btn btn-sm green filter-submit " type="button" id="datatable_submit">
                                <i class="fa fa-search"></i> 搜索
                            </button>
                        </div>
                    </form>
                    <table class="table table-striped table-bordered table-hover table-checkable"
                           id="datatable_ajax">
                        <thead>
                        <tr role="row" class="heading">
<!--                            <th width="2%">-->
<!--                                <input type="checkbox" class="group-checkable"></th>-->
                            <th width="5%"> ID&nbsp;#</th>
                            <th width="10%"> 订单编号</th>
                            <th width="10%"> 投保人</th>
                            <th width="8%"> 投保手机号</th>
                            <th width="10%"> 手机IEMI号</th>
                            <th width="10%"> 品牌型号</th>
                            <th width="10%"> 保险险种</th>
                            <th width="13%"> 下单时间</th>
                            <th width="8%"> 订单状态</th>
                            <th width="10%"> 保单号</th>
                            <th width="8%"> 保期</th>
                            <th width="10%"> 操作</th>
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
<script src="<?= Yii::getAlias('@js'); ?>/ajaxfileupload.js" type="text/javascript"></script>
<script type="text/javascript">
    $(function () {
        EcommerceList.init('<?=Yii::$app->urlManager->createUrl('order/getdata')?>', {}, false, 'datatable_ajax');

        $('#order-download').on('click',function(){
            var parm=$('#datatable_form').serialize();
            this.href +='?'+parm;
        });

        $('#upload_baoxian').on('click',function(){
            $('#upload_baoxian_input').click();
        });
        $('#upload_baoxian_input').on('change',function () {
            App.blockUI();
            $.ajaxFileUpload({
                url: '<?= Yii::$app->urlManager->createUrl('order/import')?>',
                secureuri: false,
                fileElementId: 'upload_baoxian_input',
                data: {
                    '_csrf-backend':'<?= Yii::$app->request->csrfToken ?>'
                },
                dataType : 'json',
                success: function (data) {
                    App.unblockUI();
                    if(data.status == 1){
                        showToastr('success',data.msg);
                        setTimeout(function(){
                            window.location.reload();
                        },1500)
                    }else{
                        showToastr('error',data.msg);
                    }
                },
                error: function (data, status, e) {
                    App.unblockUI();
                    showToastr('error', e);
                },
            })
        })

    });
</script>
