<link rel="stylesheet" href="<?=Yii::getAlias('@css');?>/webuploader.css"/>
<style>
    .webuploader-pick{display: inline}
</style>
<?php
    $dom = 'dom_'.uniqid();
?>
<div id="<?=$dom?>">
    <button class="btn green btn-sm"  ><?=$button?></button>
</div>
<script type="text/javascript" src="<?=Yii::getAlias('@js');?>/webuploader.min.js"></script>
<script type="text/javascript">
    $(function(){
        var uploader = WebUploader.create({

            // 选完文件后，是否自动上传。
            auto: true,
            method: 'POST',
            // swf文件路径
            swf: '<?=Yii::getAlias('@js');?>/Uploader.swf',
            fileSingleSizeLimit: 10 * 1024 * 1024,
            // 文件接收服务端。
//            server: 'http://webuploader.duapp.com/server/fileupload.php',
            server:'<?= \yii\helpers\Url::to(['mimport/upload/img']) ?>',
//            sendAsBinary: true,
            formData:{
                '_csrf-maintainer':'<?= Yii::$app->request->csrfToken ?>',
                '_csrf-backend':'<?= Yii::$app->request->csrfToken ?>',
                'dir':'<?=$dir?>',
                'url':'<?=$url?>',
                'parms':'<?=$parms?>'
            },
            // 选择文件的按钮。可选。
            // 内部根据当前运行是创建，可能是input元素，也可能是flash.
            pick: '#<?=$dom?>',
            fileVal:'upimg',
            // 只允许选择图片文件。
            accept: {
                title: 'Images',
                extensions: 'gif,jpg,jpeg,bmp,png'
//                mimeTypes: 'image/*'
            },
            compress:{
                width: 960,
                height: 1280,
                quality: 100,
                allowMagnify: false,
                crop: false,
                preserveHeaders: true,
                compressSize: 1 * 1024 * 1024 //小于1 M 不压缩
            }
        });
        // 当有文件添加进来的时候
        uploader.on( 'fileQueued', function( file ) {
            App.blockUI();
        });
        uploader.on("uploadComplete", function (file) {
            App.unblockUI();
            uploader.removeFile( file );
        });
        uploader.on('uploadSuccess', function (file, response) {
            App.unblockUI();
            console.log(response);
            if(response.code == 200){
                var imgId ='<?=$img?>';
                var input ='<?=$id?>';
                if($.trim(input).length > 0){
                    $('#'+input).val(response.data.path);
                }
                if($.trim(imgId).length > 0){
                    $('#'+imgId).attr('src',response.data.path);
                }
            }else{
                showToastr('error',response.msg,'图片上传失败','toast-top-right')
            }
        });
        uploader.on("error", function (type ) {
            App.unblockUI();
            console.log(type );
            showToastr('error','格式错误','图片上传失败','toast-top-right')
        });

    })


</script>
