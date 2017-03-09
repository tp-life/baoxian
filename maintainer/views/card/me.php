
<div class="row">
    <div class="col-md-12">
        <div class="portlet light portlet-fit portlet-datatable bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-settings font-green"></i>
                    <span class="caption-subject font-green sbold uppercase">我的卡券</span>
                </div>
                <div class="actions">
                    <div class="btn-group ">
                        <a class="btn     btn-sm " onclick="downloadCard()">
                            <i class="fa fa-download"></i> 导出卡券
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="note note-danger">
                    <p> 卡券导出默认导出当前条件下全部卡券,<font color="#ff4500">最好不要超过5000张</font></p>
                    <p> <font color="#ff4500">通过加强筛选条件导出，导出数据限制5000张</font></p>
                    <p> 复选框选择只对发放有效</p>
                    <p> <font color="#ff4500">复选框卡券发放必须为单一险种</font></p>
                    <p> <font color="#ff4500">复选框卡券发放必须为未激活卡券</font></p>
                    <p> <font color="#ff4500">单次卡券单险种发放不超过1000张</font></p>
                </div>
            </div>
            <div class="portlet-body">
                <div class="table-actions-wrapper ">
                </div>
                <div class="table-container">
                    <form id="datatable_form">
                        <div class="table-search">
                            <div class="row">
                                <div class="col-sm-3 text-left">
                                   <!--<a class="btn green  sbold" id="service-responsive_mm" data-target="#service-responsive" href="<?/*= \yii\helpers\Url::to(['card/selsendcard']) */?>" data-toggle="modal">-->
                                       <a class="btn green  sbold" id="service-responsive_mm">
                                    <i class="fa fa-share"></i>发放给子商户
                                    </a>
                                </div>
                                <div class="col-sm-9">
                                    <div class="table-search text-right">
										<?= \yii\helpers\Html::dropDownList('coverage_code','',\common\models\InsuranceCoverage::getCoverageDataCodeAll(),['prompt'=>'选择险种','class'=>'table-group-action-input form-control form-filter input-inline  input-sm']) ?>
                                        <select
                                            class="table-group-action-input form-control form-filter input-inline  input-sm"
                                            id="datatable_filter" name="status">
                                            <option value="">卡券状态</option>
                                            <option value="0">未激活</option>
                                            <option value="1">已激活</option>
                                            <option value="2">已失效</option>
                                            <option value="3">冻结中</option>
                                        </select>
                                        <input type="text" size="30" id="datatable_search" name="keyword" class="input-md form-filter form-control input-inline" placeholder="请输入 卡号 进行查询"/>
                                        <button class="btn btn-sm green filter-submit " type="button" id="datatable_submit">
                                            <i class="fa fa-search"></i> 搜索
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <table class="table table-striped table-bordered table-hover table-checkable"
                           id="datatable_ajax">
                        <thead>
                        <tr role="row" class="heading">
                            <th width="2%"><input type="checkbox"  name="id_all" class="group-checkable">全选</th>
                            <th width="5%"> ID&nbsp;#</th>
                            <th width="10%"> 卡券号</th>
                            <th width="8%">  险种</th>
                            <th width="8%">  领用ID号</th>
                            <th width="10%"> 卡券状态</th>
                            <th width="18%"> 卡券流转</th>
                            <th width="10%"> 时间</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                    </form>
                </div>
            </div>

            <div style="display: block">

            </div>
        </div>
        <!-- End: life time stats -->
    </div>
</div>
<!-- BEGIN PAGE LEVEL PLUGINS -->
<!-- END PAGE LEVEL PLUGINS -->
<script type="text/javascript">
    $(function () {
        EcommerceList.init('<?=Yii::$app->urlManager->createUrl('card/getme')?>', {}, false, 'datatable_ajax');

        $("#service-responsive_mm").on('click',function(){
            $('#service-responsive').modal('show');

            $('#service-responsive').on('show.bs.modal', function () {
                //初始化
            });

            setTimeout(function(){
                var url ='<?= \yii\helpers\Url::to(['card/selsendcard']) ?>';
                $('.modal-content').load(url, $('#datatable_form').serialize(), function(){
                    $('#service-responsive').modal('show');
                });

            },200);

        });


    });

    function downloadCard() {
        var url ='<?=Yii::$app->urlManager->createUrl('card/download')?>';
        var data = $('#datatable_form').serialize();
        window.location.href =url+'?'+data;
    }
</script>
<!-- begin 子商家发放 modal -->
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
<!-- end 商家理赔状态流程更新 modal -->
