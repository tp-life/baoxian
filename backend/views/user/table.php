<?php
$this->title='数据表格';
?>
<!-- BEGIN PAGE LEVEL PLUGINS -->
<link href="<?= Yii::getAlias('@metro'); ?>/global/plugins/datatables/datatables.min.css" rel="stylesheet" type="text/css"/>
<link href="<?= Yii::getAlias('@metro'); ?>/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css" rel="stylesheet"
      type="text/css"/>
<link href="<?= Yii::getAlias('@metro'); ?>/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet"
      type="text/css"/>
<!-- END PAGE LEVEL PLUGINS -->

<div class="page-bar">
    <ul class="page-breadcrumb">
        <li>
            <a href="#">商家</a>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <span>普通会员</span>
        </li>
    </ul>

</div>
<h3 class="page-title"> 普通会员
    <small>会员列表管理</small>
</h3>
<div class="row">
    <div class="col-md-12">
        <div class="note note-danger">
            <p> 普通会员列表 </p>
        </div>
        <!-- Begin: life time stats -->
        <div class="portlet light portlet-fit portlet-datatable bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-user font-dark"></i>
                    <span class="caption-subject font-dark sbold uppercase">会员列表</span>
                </div>
<!--                <div class="actions">-->
<!--                    <div class="btn-group btn-group-devided" data-toggle="buttons">-->
<!--                        <label class="btn btn-transparent grey-salsa btn-outline btn-circle btn-sm active">-->
<!--                            <input type="radio" name="options" class="toggle" id="option1">Actions</label>-->
<!--                        <label class="btn btn-transparent grey-salsa btn-outline btn-circle btn-sm">-->
<!--                            <input type="radio" name="options" class="toggle" id="option2">Settings</label>-->
<!--                    </div>-->
<!--                    <div class="btn-group">-->
<!--                        <a class="btn red btn-outline btn-circle" href="javascript:;" data-toggle="dropdown">-->
<!--                            <i class="fa fa-share"></i>-->
<!--                            <span class="hidden-xs"> 工具 </span>-->
<!--                            <i class="fa fa-angle-down"></i>-->
<!--                        </a>-->
<!--                        <ul class="dropdown-menu pull-right">-->
<!--                            <li>-->
<!--                                <a href="javascript:;"> </a>-->
<!--                            </li>-->
<!--                            <li class="divider"></li>-->
<!--                            <li>-->
<!--                                <a href="javascript:;"> Print Invoices </a>-->
<!--                            </li>-->
<!--                        </ul>-->
<!--                    </div>-->
<!--                </div>-->
            </div>
            <div class="portlet-body">
                <div class="table-actions-wrapper ">
                    <span> </span>

                </div>
                <div class="table-container">
                    <form id="datatable_form">
                        <div class="table-search text-right">
                            <select class="table-group-action-input form-control form-filter input-inline input-small input-sm" id="datatable_filter" name="field">
                                <option value="">筛选项</option>
                                <option value="name">用户名</option>
                                <option value="phone">电话</option>
                            </select>
                            <input type="text" id="datatable_search" class="form-filter" name="field_value" />
                            <button class="btn btn-sm green filter-submit " type="button" id="datatable_submit">
                                <i class="fa fa-check"></i> Submit
                            </button>
                        </div>
                    </form>
                    <table class="table table-striped table-bordered table-hover table-checkable" id="datatable_ajax">
                        <thead>
                        <tr role="row" class="heading">
<!--                            <th width="2%">-->
<!--                                <input type="checkbox" class="group-checkable"></th>-->
                            <th width="5%"> ID&nbsp;#</th>
                            <th width="10%"> 头像</th>
                            <th width="10%"> 帐号</th>
                            <th width="10%"> 电话</th>
                            <th width="10%"> 状态</th>
<!--                            <th width="15%"> 操作</th>-->
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
<script src="<?= Yii::getAlias('@metro'); ?>/global/scripts/datatable.js" type="text/javascript"></script>
<script src="<?= Yii::getAlias('@metro'); ?>/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
<script src="<?= Yii::getAlias('@metro'); ?>/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
<script src="<?= Yii::getAlias('@metro'); ?>/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script  type="text/javascript">
    $(function(){
        EcommerceList.init('/user/getdata',{},false,'datatable_ajax');
    });
</script>
<!-- END PAGE LEVEL SCRIPTS -->
