<table class=" table table-bordered  table-hover">
	<thead>
	<tr>
		<th>变动日志</th>
		<th width="20%">时间/处理</th>
	</tr>
	</thead>
	<tbody>

	<?php foreach($data as $row): ?>
		<tr>
			<td><?= $row['content'] ?></td>
			<td class="font-grey-salsa">
				<?= $row['update_time'] ?>
				<a class="btn green btn-xs btn-default" onclick="handleStatus(<?= $row['id'] ?>,1)" href="javascript:;"> 重启 </a>

			</td>
		</tr>
	<?php endforeach; ?>

	</tbody>
</table>