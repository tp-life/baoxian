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
            <span>保险商品</span>
        </li>
    </ul>

</div>
<h3 class="page-title"> 保险商品
    <small>保险商品列表管理</small>
</h3>
<div class="row">
    <div class="col-md-12">
        <div class="note note-danger">

                <p>• 呈现所有乐换新销售的保险险种</p>
                <p>• 添加完成后，保险公司、官方指导价、保险类型和保期不能进行修改</p>
                <p>• 保险险种编码为自动生成 </p>

        </div>
        <!-- Begin: life time stats -->
        <div class="portlet light portlet-fit portlet-datatable bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-user font-dark"></i>
                    <span class="caption-subject font-dark sbold uppercase">保险列表</span>
                </div>
                <div class="actions">
                    <div class="btn-group " >
                        <a href="<?=Yii::$app->urlManager->createUrl(['coverage/create'])?>" class="btn  blue  btn-sm ">
                            <i class="fa fa-plus-square"></i>  新增保险</a>
                    </div>
                </div>
            </div>
            <div class="portlet-body">

                <div class="table-container">
                    <form id="datatable_form">
                        <div class="table-search text-right">
                            <select
                                class="table-group-action-input form-control form-filter input-inline  input-sm" name="company_id">
                                <option value="">保险公司</option>
                                <?php if($model_company):foreach($model_company as $val): ?>
                                    <option value="<?=$val->id?>"  <?=$val->id ==$pramas['c_id']?'selected':''?>><?=$val->name?></option>
                                <?php endforeach;endif; ?>
                            </select>
                            &nbsp;&nbsp;&nbsp;&nbsp;
                            <select
                                class="table-group-action-input form-control form-filter input-inline  input-sm" name="type_id">
                                <option value="">保险类型</option>
                                <?php if($model_type):foreach($model_type as $val): ?>
                                    <option value="<?=$val->id?>" <?=$val->id ==$pramas['t_id']?'selected':''?> ><?=$val->type_name?></option>
                                <?php endforeach;endif; ?>
                            </select>
                            &nbsp;&nbsp;&nbsp;&nbsp;
                            <select
                                class="table-group-action-input form-control form-filter input-inline  input-sm" name="status">
                                <option value="">状态</option>
                                <option value="2">正常</option>
                                <option value="1">冻结中</option>
                            </select>
                            &nbsp;&nbsp;&nbsp;&nbsp;
                            <select
                                class="table-group-action-input form-control form-filter input-inline  input-sm" name="period">
                                <option value="">保期</option>
                                <option value="1"> 1 月</option>
                                <option value="2"> 2 月</option>
                                <option value="3"> 3 月</option>
                                <option value="6"> 6 月</option>
                                <option value="12"> 12 月</option>
                                <option value="18"> 18 月</option>
                                <option value="24"> 24 月</option>
                            </select>
                            &nbsp;&nbsp;&nbsp;&nbsp;
                            <input type="text" size="30"  class="input-md form-filter form-control input-inline" name="coverage_code" placeholder="请输入保险险种进行查询"/>
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
                            <th width="15%"> 保险名称</th>
                            <th width="8%"> 保险险种</th>
                            <th width="8%"> 保险公司</th>
                            <th width="7%"> 成本价</th>
                            <th width="7%"> 批发价</th>
                            <th width="8%"> 保险类型</th>
                            <th width="6%"> 保期</th>
                            <th width="7%"> 最高赔付</th>
                            <th width="9%"> 引入时间</th>
                            <th width="6%"> 状态</th>
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
<!-- BEGIN PAGE LEVEL PLUGINS -->
<!-- END PAGE LEVEL PLUGINS -->
<script  type="text/javascript">
    $(function(){

        EcommerceList.init('/coverage/getdata',{},false,'datatable_ajax');
    });

    function handleStatus(id,status){
        App.blockUI();
        $.post('<?=Yii::$app->urlManager->createUrl(['coverage/change']);?>',{'_csrf-backend':$('meta[name="csrf-token"]').attr("content"),'id':id,'status':status},function(data){
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
</script>
