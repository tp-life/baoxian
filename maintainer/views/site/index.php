<div class="row">
    <div class="col-md-12">
        <!-- BEGIN VALIDATION STATES-->
        <div class="portlet light portlet-fit portlet-form bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-settings font-red"></i>
                    <span class="caption-subject font-red sbold uppercase">系统消息</span>
                </div>
                <div class="actions">
                    <div class="btn-group btn-group-devided" data-toggle="buttons">

                    </div>
                </div>
            </div>
            <div class="portlet-body">
                <div class="row">
                    <div class="col-md-12">
                        <!-- BEGIN PORTLET-->

                        <div class="col-md-12">


                                <div class="scroller" style="height: 580px;" data-always-visible="1"
                                     data-rail-visible="0">
                                    <ul class="feeds">
                                        <?php foreach ($result as $val):
                                            $color = ' font-green-sharp';
                                            $href = 'javascript:void(0)';

                                            switch ($val['type']) {
                                                case 'withdrawal' :
                                                    $msg = '提现申请';
                                                    $msg_color = 'font-green-jungle';
                                                    $href = Yii::$app->urlManager->createUrl(['withdraw/index', 'order_id' => $val['m_order_id']]);
                                                    break;
                                                case 'payment' :
                                                    $msg = '提现到账';
                                                    $msg_color = 'font-red-thunderbird';
                                                    $href = Yii::$app->urlManager->createUrl(['withdraw/index', 'order_id' => $val['m_order_id']]);
                                                    break;
                                                case 'assigned':
                                                    $msg = '维修指派';
                                                    $msg_color = 'font-purple-seance';
                                                    $href = Yii::$app->urlManager->createUrl(['order/view', 'id' => $val['m_order_id']]);
                                                    break;
                                                default :
                                                    $msg = '';
                                                    $msg_color = '';
                                            }
                                            if ($val['status']) {
                                                $href = 'javascript:void(0)';
                                                $color = 'font-grey-mint';
                                                $msg_color = 'font-grey-mint';
                                            }
                                            ?>
                                            <li class="row">
                                                <div class="col-sm-10">
                                                    <?php if (!$val['status']): ?> <a href="javascript:void(0)"
                                                                                      onclick="handleMsg(<?= $val['id'] ?>,'<?= $href ?>')"><?php endif; ?>
                                                        <div class="col1">
                                                            <div class="cont">
                                                                <div class="cont-col1">
                                                                    <?php if (!$val['status']): ?>
                                                                        <div class="label label-sm label-danger">
                                                                            <i class="fa fa-bell-o"></i>
                                                                        </div>
                                                                    <?php endif; ?>
                                                                </div>

                                                                <div class="cont-col2">

                                                                    <div class="desc ">
                                                                        &nbsp;&nbsp;
                                                                    <span class="<?= $msg_color ?>">
                                                                        [ <?= $msg ?> ]
                                                                    </span>
                                                                        &nbsp;&nbsp;&nbsp;&nbsp;
                                                                    <span class="<?= $color ?>">
                                                                        <?= $val['content'] ?>
                                                                    </span>
                                                                    </div>

                                                                </div>

                                                            </div>
                                                        </div>
                                                        <?php if (!$val['status']): ?></a><?php endif; ?>
                                                </div>
                                                <div class="col-sm-2 text-right">

                                                    <div
                                                        class="date <?= $color ?>"> <?= date('Y-m-d H:i', $val['add_time']) ?></div>

                                                </div>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                                <!--END TABS-->


                            <div class="row">
                                <div class="col-md-12 text-right">

                                    <ul class="pager pull-right">
                                        <?php if($total_page > 1 && $page): ?>
                                        <li><a href="<?=Yii::$app->urlManager->createUrl(['site/index','page'=>$page-1])?>">上一页</a></li>
                                        <?php endif; ?>
                                        <?php if($total_page > $page  && $total_page > 1): ?>
                                        <li><a href="<?=Yii::$app->urlManager->createUrl(['site/index','page'=>$page+1])?>">下一页</a></li>
                                        <?php endif; ?>
                                    </ul>
                                </div>

                            </div>
                        </div>

                        <!-- END PORTLET-->

                    </div>
                </div>
            </div>
        </div>
        <!-- END VALIDATION STATES-->
    </div>
</div>
<script type="text/javascript">

    function handleMsg(id,url){
        $.post('<?=Yii::$app->urlManager->createUrl('site/handlemsg')?>',{id:id,'_csrf-maintainer': $('meta[name="csrf-token"]').attr("content")},function(data){
            if (data.code == 'yes') {
                window.location.href = url;
            }
        })
    }

</script>