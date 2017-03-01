<?php
$this->title = '数据表格';
?>
<!-- BEGIN PAGE LEVEL PLUGINS -->
<!-- END PAGE LEVEL PLUGINS -->

<div class="page-bar">
    <ul class="page-breadcrumb">
        <li>
            <a href="#">商品</a>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <span>保险类型</span>
        </li>
    </ul>

</div>
<h3 class="page-title"> 保险类型
    <small>保险类型列表管理</small>
</h3>
<div class="row">
    <div class="col-md-12">
        <div class="note note-danger">
            <p> 呈现所有乐换新销售的保险类型，识别代码为两位数字，作为保险险种代码所用 </p>
        </div>
        <!-- Begin: life time stats -->
        <div class="portlet light portlet-fit portlet-datatable bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-settings font-dark"></i>
                    <span class="caption-subject font-dark sbold uppercase">保险类型</span>
                </div>
                <div class="actions">
                    <div class="btn-group ">
                        <a href="<?= Yii::$app->urlManager->createUrl(['insurancetype/create']); ?>"
                           class="btn  blue   btn-sm ">
                            <i class="fa fa-plus-square"></i> 新增保险类型</a>
                    </div>
                </div>
            </div>
            <div class="portlet-body">
                <div class="table-actions-wrapper ">
                </div>
                <div class="table-container">

                    <table class="table table-striped table-bordered table-hover table-checkable"
                           id="datatable_ajax">
                        <thead>
                        <tr role="row" class="heading">
<!--                            <th width="2%">-->
<!--                                <input type="checkbox" class="group-checkable"></th>-->
                            <th width="5%"> ID&nbsp;#</th>
                            <th width="10%"> 保险类型</th>
                            <th width="10%"> 识别代码</th>
                            <th width="8%"> 已接入险种数</th>
                            <th width="8%"> 状态</th>
                            <th width="18%"> 操作</th>
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
<!-- begin 协议 -->
<div class="modal fade bs-modal-lg" tabindex="-1" id="my-card-type" aria-hidden="true">
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
<!-- BEGIN PAGE LEVEL PLUGINS -->
<!-- END PAGE LEVEL PLUGINS -->
<script type="text/javascript">
    $(function () {
        EcommerceList.init('<?=Yii::$app->urlManager->createUrl(['insurancetype/getdata']);?>', {}, false, 'datatable_ajax');
    });

    function handleStatus(id, status) {
        App.blockUI();
        $.post('<?=Yii::$app->urlManager->createUrl(['insurancetype/change']);?>', {
            '_csrf-backend': $('meta[name="csrf-token"]').attr("content"),
            'id': id,
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
</script>
