<link href="<?= Yii::getAlias('@metro'); ?>/global/plugins/select2/css/select2.min.css" rel="stylesheet"
      type="text/css"/>
<link href="<?= Yii::getAlias('@metro'); ?>/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet"
      type="text/css"/>

<div class="page-bar"></div>
<h3 class="page-title"> 卡券合并
</h3>
<div class="row">
    <div class="col-md-12">
        <div class="note note-danger">
            <p> 卡券合并注意事项 .</p>
        </div>
        <!-- Begin: life time stats -->
        <div class="portlet light portlet-fit portlet-datatable bordered">

            <form id="merge_form" class="form-horizontal" method="post" enctype="multipart/form-data" method="POST">

                <input type="hidden" name="_csrf-backend" value="<?= Yii::$app->request->csrfToken ?>">
                <?php if($info['id']): ?>
                    <input type="hidden" name="id" value="<?=$info['id']?>">
                <?php endif ?>
                <div class="form-body">
                    <div class="form-group  margin-top-20">
                        <label class="control-label col-md-3">卡券原拥有商家
                            <span class="required"> * </span>
                        </label>
                        <div class="col-md-2">
                            <div class="input-group">
                                <input type="text" class="form-control" id="from_check_val">
                                    <span class="input-group-btn">
                                        <button class="btn blue" id="from_check" type="button">过滤</button>
                                    </span>
                            </div>
                            <div class="input-icon right">
                                <i class="fa"></i>
                                <select class="form-control" name="from_seller_id" id="from_seller_id">
                                    <option value="0">选择保险商家</option>
                                    <?php if($insurance_list){ ?>
                                        <?php foreach($insurance_list as $vo): ?>
                                            <option value="<?= $vo['seller_id']; ?>"><?= $vo['seller_name']; ?></option>
                                        <?php endforeach ?>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group  margin-top-20">
                        <label class="control-label col-md-3">合并到新商家
                            <span class="required"> * </span>
                        </label>
                        <div class="col-md-2">
                            <div class="input-group">
                                <input type="text" class="form-control" id="to_check_val">
                                    <span class="input-group-btn">
                                        <button class="btn blue" id="to_check" type="button">过滤</button>
                                    </span>
                            </div>
                            <div class="input-icon right">
                                <i class="fa"></i>
                                <select class="form-control" name="to_seller_id" id="to_seller_id">
                                    <option value="0">选择保险商家</option>
                                    <?php if($insurance_list){ ?>
                                        <?php foreach($insurance_list as $vo): ?>
                                            <option value="<?= $vo['seller_id']; ?>"><?= $vo['seller_name']; ?></option>
                                        <?php endforeach ?>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group  margin-top-20">
                        <label class="control-label col-md-3">选择合并的套餐
                            <span class="required"> * </span>
                        </label>
                        <div class="col-md-2">
                            <div class="input-icon right">
                                <i class="fa"></i>
                                <select class="form-control" name="d_coverage" id="d_coverage">
                                    <option value="0">全部保险险种</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group  margin-top-20">
                        <label class="control-label col-md-3">填写合并套餐编号

                        </label>

                        <div class="col-md-4">
                            <div class="input-icon right">
                                <textarea class="form-control" rows="5" name="card_number_str"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="form-actions">
                        <div class="row">
                            <div class="col-md-offset-3 col-md-9">
                                <button type="button" id="submitBtn" class="btn green">保存</button>
                                &nbsp;&nbsp;
                                <button type="reset" class="btn">重置</button>
                            </div>
                        </div>
                    </div>
            </form>
            <p></p>
        </div>
    </div>
</div>
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script src="<?= Yii::getAlias('@metro'); ?>/global/plugins/select2/js/select2.full.min.js"
        type="text/javascript"></script>
<script src="<?= Yii::getAlias('@metro'); ?>/global/plugins/jquery-validation/js/jquery.validate.min.js"
        type="text/javascript"></script>
<script src="<?= Yii::getAlias('@metro'); ?>/global/plugins/jquery-validation/js/additional-methods.min.js"
        type="text/javascript"></script>
<!-- END PAGE LEVEL PLUGINS -->

<script>
    $(function () {
        $('#from_seller_id').on('change', function () {
            var seller_id = $(this).val();
            var str = '<option value="0">请选择保险险种</option>';

            if (seller_id == 0) {
                $('#d_coverage').html(str);
                return;
            }

            $.post('/card/getcoveragelist', {
                    'seller_id': seller_id,
                    '_csrf-backend': $('meta[name="csrf-token"]').attr("content")
                },
                function (data) {
                    if (data.code == 'yes') {
                        $.each(data.data, function (index, val) {
                            str += '<option value=' + val.coverage_code + '>' + val.coverage_code + '</option>';
                        })
                    }

                    $('#d_coverage').html(str);
                }, "json");
        });

        //被合并商家过滤
        $('#from_check').on('click', function () {
            var seller_name = $('#from_check_val').val();
            var str = '<option value="0">选择保险商家</option>';

            if (seller_name == '') {
                $('#from_seller_id').html(str);
                return;
            }

            $.post('/card/getsellerlist', {
                    'seller_name': seller_name,
                    'type' : 'insurance',
                    '_csrf-backend': $('meta[name="csrf-token"]').attr("content")
                },
                function (data) {
                    if (data.code == 'yes') {
                        $.each(data.data, function (index, val) {
                            str += '<option value=' + val.seller_id + '>' + val.seller_name + '</option>';
                        })
                    }

                    $('#from_seller_id').html(str);
                }, "json");
        });
        //合并目标商家过滤
        $('#to_check').on('click', function () {
            var seller_name = $('#to_check_val').val();
            var str = '<option value="0">选择保险商家</option>';

            if (seller_name == '') {
                $('#to_seller_id').html(str);
                return;
            }

            $.post('/card/getsellerlist', {
                    'seller_name': seller_name,
                    'type' : 'insurance',
                    '_csrf-backend': $('meta[name="csrf-token"]').attr("content")
                },
                function (data) {
                    if (data.code == 'yes') {
                        $.each(data.data, function (index, val) {
                            str += '<option value=' + val.seller_id + '>' + val.seller_name + '</option>';
                        })
                    }

                    $('#to_seller_id').html(str);
                }, "json");
        });
    });

    jQuery.validator.addMethod("yz", function (value, element, params) {
        var from_seller_id = $('#from_seller_id').val();
        var to_seller_id = $('#to_seller_id').val();
        if (from_seller_id == to_seller_id) {
            return false;
        }

        return true;

    }, "");


    $("#merge_form").validate({
        rules: {
            to_seller_id: {
                required: true,
                yz: true
            }
        },
        messages: {
            to_seller_id: {
                required: '<b style="color: red">请选择商家<b/>',
                yz: '<b style="color: red">待合并商家不能是自己<b/>'
            }
        }
    });

    $("#submitBtn").click(function () {
        if ($("#merge_form").valid()) {
            $("#merge_form").submit();
        }

    });
</script>