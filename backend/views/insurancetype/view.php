<!--<div class="portlet light bordered">-->
<!--    <div class="portlet-title">-->
<!--        <div class="caption">-->
<!--            <i class="icon-social-dribbble font-purple-soft"></i>-->
<!--            <span class="caption-subject font-purple-soft bold uppercase">保险类型详情</span>-->
<!--        </div>-->
<!--    </div>-->
<!--    <div class="portlet-body">-->
<!---->
<!--    </div>-->
<!--</div>-->
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <h4 class="modal-title">保险类型详情</h4>
</div>
<div class="modal-body">
    <div class="col-sm-12" style="margin-bottom: 30px">
        <h4><?=$info->type_name?><span class="font-purple-seance sbold">（code:#<?=$info->type_code?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;保险接入量：<?=$info->insurance_number?>）</span></h4>
        <p class="help-block" style="margin-top: 25px; color: #96c0d8">备注：<?=$info->note?></p>
    </div>
    <ul class="nav nav-tabs">
        <li class="active">
            <a href="#tab_1_1" data-toggle="tab"> 保险详情 </a>
        </li>
        <li>
            <a href="#tab_1_2" data-toggle="tab"> 投保须知 </a>
        </li>
        <li>
            <a href="#tab_1_3" data-toggle="tab"> 理赔须知 </a>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade active in" id="tab_1_1">
            <?=htmlspecialchars_decode($view['info'])?>
        </div>
        <div class="tab-pane fade" id="tab_1_2">
            <?=htmlspecialchars_decode($view['insure'])?>
        </div>
        <div class="tab-pane fade" id="tab_1_3">
            <?=htmlspecialchars_decode($view['claims'])?>
        </div>

    </div>
    <div class="clearfix margin-bottom-20"> </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn default" data-dismiss="modal">关闭</button>
    <!--<button type="button" class="btn blue">Save changes</button>-->
</div>