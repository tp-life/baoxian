

<div class="box">
	<div class="box-content text-center" style='min-height: 400px;'>
		<div class="row">
			<div class="col-md-4"></div>
			<div class="col-md-4">
				<div class="alert alert-<?= $type; ?>">
					<p><h3><?php print_r($message)?></h3></p>
						<p class="jump">
							页面自动 <a id="href" href="<?=$href; ?>">跳转</a> 等待时间： <b
								id="wait"><?=$wait ?></b>
						</p>
				</div>
			</div>
			<div class="col-md-4"></div>
		</div>
	</div>
</div>
<script type="text/javascript">
	(function () {
		var wait = document.getElementById('wait'), href = document.getElementById('href').href;
		totaltime = parseInt(wait.innerHTML);
		var interval = setInterval(function () {
			var time = --totaltime;
			wait.innerHTML = "" + time;
			if (time === 0) {
				window.location.href = href;
				clearInterval(interval);
			}
			;
		}, 1000);

	})();

</script>                            

