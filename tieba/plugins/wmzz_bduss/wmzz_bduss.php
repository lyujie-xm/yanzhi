<?php
if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); } 

if(SYSTEM_PAGE == 'baiduid') {
    function wmzz_bduss_do() {
        echo <<<SCRIPT
<script type="text/javascript">
var wmzz_bduss_in = $("#chrome_bduss").parent().parent();
var wmzz_bduss_data  = '<div class="panel panel-default">';
wmzz_bduss_data += '<div class="panel-heading" onclick="$(\'#wmzz_bduss_area\').fadeToggle();"><h3 class="panel-title"><span class="glyphicon glyphicon-chevron-down"></span> 点击查看 在线 获取BDUSS方法</h3></div>';
wmzz_bduss_data += '<div class="panel-body" id="wmzz_bduss_area" style="display:none">';
wmzz_bduss_data += '1.打开 <a href="https://xn--wvwr9cvyc.xn--6qq986b3xl/tieba/bduss" target="_blank">在线获取BDUSS</a>';
wmzz_bduss_data += '<br/><br/>2.按照要求登录百度，然后将获取到的 BDUSS 填入上面的表单即可';
wmzz_bduss_data += '</div>';
wmzz_bduss_data += '</div>';
wmzz_bduss_in.html(wmzz_bduss_in.html() + wmzz_bduss_data);
</script>
SCRIPT;

    }
    addAction('footer','wmzz_bduss_do');
}