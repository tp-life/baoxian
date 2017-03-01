<?php if($data): ?>
	<?php foreach($data as $menu): ?>
		<li class="nav-item<?php if($menu['active']): ?> active open<?php endif;?>">
			<a href="javascript:;" class="nav-link nav-toggle">
				<i class="<?= $menu['icon'] ?>"></i>
				<span class="title"><?= $menu['name'] ?></span>
				<span class="arrow <?php if($menu['active']): ?> open<?php endif;?>"></span>
			</a>
			<ul class="sub-menu" <?php if($menu['active']): ?> style="display: block;" <?php else:?> style="display: none;"<?php endif;?>>
				<!--group begin-->
				<?php if($menu['group']): ?>
					<?php foreach($menu['group'] as $group): ?>
						<?php if(count($group['nodes'])>1):?>
							<li class="nav-item<?php if($group['active']): ?> open<?php endif;?>">
								<a href="javascript:;" class="nav-link nav-toggle">
									<i class="<?= $group['icon'] ?>"></i>
									<span class="title"><?= $group['name'] ?></span>
									<span class="arrow <?php if($group['active']): ?> open<?php endif;?>"></span>
								</a>
								<ul class="sub-menu" <?php if($group['active']): ?> style="display: block;" <?php else:?> style="display: none;"<?php endif;?>>
									<?php foreach($group['nodes'] as $_node): ?>
									<li class="nav-item <?php if($_node['active']): ?> acitve<?php endif;?>">
										<a href="<?= $_node['url']?>" class="nav-link "> <?= $_node['name'] ?> </a>
									</li>
									<?php endforeach; ?>
								</ul>
							</li>
						<?php elseif(count($group['nodes'])==1): ?>
							<?php $node = current($group['nodes']); ?>
							<li class="nav-item<?php if($node['active']): ?> active<?php endif;?>">
								<a href="<?= $node['url']?>" class="nav-link ">
									<i class="<?= $group['icon'] ?>"></i>
									<span class="title"><?= $group['name'] ?></span>

								</a>
							</li>
						<?php endif; ?>
					<?php endforeach; ?>
				<?php endif;?>
				<!--group end-->
			</ul>
		</li>
		<?php endforeach; ?>
<?php else:?>
	<li class="nav-item  active open">
		<a href="javascript:;" class="nav-link nav-toggle">
			<i class="icon-bar-chart"></i>
			<span class="title">No permissions,裸奔状态</span>
			<span class="arrow"></span>
		</a>
	</li>
<?php endif;?>
