<?php if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); }
global $i;

switch ($i['mode'][0]) {
    case 'baiduid':
        loadhead('百度账号管理');
		template('baiduid');
		break;
	case 'showtb':
        loadhead('云签到设置和日志');
		template('showtb');
		break;
	case 'log':
		//兼容老版本插件，重定向到showtb
		Clean();
		ReDirect('index.php?mod=showtb');
		break;
	case 'set':
        loadhead('个人设置');
		template('set');
		break;
	case 'admin':
		if (ROLE != 'admin') msg('权限不足！');
		switch($i['mode'][1]) {
			case 'set':
                loadhead('全局设置');
				template('admin-set');
				break;
			case 'tools':
                loadhead('工具箱');
				template('admin-tools');
				break;
			case 'users':
                loadhead('用户管理');
				template('admin-users');
				break;
			case 'plugins':
                loadhead('插件管理');
				template('admin-plugins');
				break;
			case 'cron':
				loadhead('计划任务');
				template('admin-cron');
				break;
			case 'editcron':
				loadhead('编辑计划任务');
				template('admin-editcron');
				break;
			case 'update':
                loadhead('检查更新');
				template('admin-update');
				break;
			case 'setplug':
				$plug = strip_tags($_GET['plug']);
				$pluginfo = getPluginInfo($plug);

				if (file_exists(SYSTEM_ROOT.'/plugins/'.$plug.'/'.$plug.'_setting.php') && in_array($_GET['plug'], $i['plugins']['actived'])) {
                    loadhead($pluginfo['plugin']['name'] . ' - 插件管理');
					require_once SYSTEM_ROOT.'/plugins/'.$plug.'/'.$plug.'_setting.php';
                    echo '<br/><br/><br/>';
                    if(!empty($pluginfo['plugin']['url']))
                        echo '<a href="'.$pluginfo['plugin']['url'].'" target="_blank">';
                    echo $pluginfo['plugin']['name'];
                    if(!empty($pluginfo['plugin']['url']))
                        echo '</a>';
				} else {
					echo '<b>插件设置页面不存在</b>';
				}
				break;
			case 'stat':
                loadhead('统计信息');
				template('admin-stat');
				break;
		}
		break;
	default:
        loadhead();
		template('index');
		break;
}
?>
