<?php if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); } 
global $m,$i;

if (isset($_GET['ok'])) {
	echo '<div class="alert alert-success">设置保存成功</div>';
}

function addset($name,$type,$x,$other = '',$text = '') {
	if ($type == 'checkbox') {
		if (option::uget($x) == 1) {
			$other .= ' checked="checked"';
		}
		$value = '1';
	} else {
		$value = option::uget($x);
	}
	echo '<tr><td>'.$name.'</td><td><input type="'.$type.'" name="'.$x.'" value="'.$value.'" '.$other.'>'.$text.'</td>';
}
?><form action="setting.php?mod=set" method="post">
<?php doAction('set_1'); ?>
<div class="table-responsive">
<table class="table table-hover">
	<thead>
		<tr>
			<th style="width:40%">参数</th>
			<th>值</th>
		</tr>
	</thead>
	<tbody>
		<?php doAction('set_3'); ?>
		<tr>
			<td>邮箱设置<br/>更改你在本站设置的邮箱地址</td>
			<td>
				<input type="text" name="mail" value="<?php echo $i['user']['email'] ?>" class="form-control" required>
			</td>
		</tr>
		<?php doAction('set_2'); ?>
	</tbody>
</table></div><input type="submit" class="btn btn-primary" value="提交更改">
