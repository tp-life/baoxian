<a class="parsefile btn"   title="点击转化处理">导入转化</a>
&nbsp;
<a href="javascript:;" class="btn" onclick="sayParseDemo()"   title="查看demo">查看demo</a>
<div style="display: none;">
	<input style="display: none;" type="file" name="UploadForm[file]"  id="file_parse">
</div>
<script src="<?= Yii::getAlias('@js'); ?>/ajaxfileupload.js" type="text/javascript"></script>
<script type="text/javascript">

	$('.parsefile').on('click', function () {
		$('#file_parse').click();
	});

	$('#file_parse').on('change', function () {

		$.ajaxFileUpload({
			url: '<?= \yii\helpers\Url::to(['mimport/default/upload']) ?>',
			secureuri: false,
			fileElementId: 'file_parse',
			data: {
				'_csrf-maintainer':'<?= Yii::$app->request->csrfToken ?>',
				'_csrf-backend':'<?= Yii::$app->request->csrfToken ?>'
			},
			dataType: 'json',
			success: function (data,status) {
				//console.log(data);
				//console.log(status);
				//console.log(data.code);
				if(data.code=='yes'){
					$('#<?= $this->context->id ?>').val(data.data.cards);
					showToastr('success',data.message);
					bootbox.alert('本次卡券共计：'+data.data.count, function() {
					});
				}else{
					showToastr('error',data.message);
				}

			},
			error: function (data, status, e) {
				showToastr('error', e);
			}
		})

	});

	function sayParseDemo()
	{
		var demo ="<div class=\"bg-grey-salsa bg-font-grey-salsa\">序列号1<br/>" +
			"序列号2<br/>" +
			"序列号3<br/>" +
			"序列号4<br/>" +
			"序列号5<br/>" +
			"---</div>";

		bootbox.dialog({
			message:demo,
			size:'large',
			title:'文件转化Demo'
		});
	}

</script>