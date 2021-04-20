<?php
class qq_login{
	public function login_sig(){
		$url="http://xui.ptlogin2.qq.com/cgi-bin/xlogin?proxy_url=http%3A//qzs.qq.com/qzone/v6/portal/proxy.html&daid=5&pt_qzone_sig=1&hide_title_bar=1&low_login=0&qlogin_auto_login=1&no_verifyimg=1&link_target=blank&appid=549000912&style=22&target=self&s_url=http%3A//qzs.qq.com/qzone/v5/loginsucc.html?para=izone&pt_qr_app=手机QQ空间&pt_qr_link=http%3A//z.qzone.com/download.html&self_regurl=http%3A//qzs.qq.com/qzone/v6/reg/index.html&pt_qr_help_link=http%3A//z.qzone.com/download.html";
		$ret = $this->get_curl($url,0,0,0,1);
		preg_match('/pt_login_sig=(.*?);/',$ret,$match);
		return $match[1];
	}
	public function dovc($uin,$sig,$ans,$cap_cd,$sess,$collectname,$websig,$cdata,$collect=null){
		if(empty($uin))return array('saveOK'=>-1,'msg'=>'QQ不能为空');
		if(empty($sig))return array('saveOK'=>-1,'msg'=>'sig不能为空');
		if(empty($ans))return array('saveOK'=>-1,'msg'=>'验证码不能为空');
		if(empty($cap_cd))return array('saveOK'=>-1,'msg'=>'cap_cd不能为空');
		if(empty($sess))return array('saveOK'=>-1,'msg'=>'sess不能为空');
		if(strpos($ans,',')){
			$subcapclass=9;
		}else{
			$subcapclass=0;
		}
		$collectname=!empty($collectname)?$collectname:'collect';
		$collect=$collect?$collect:$this->get_curl('http://getcollect.duapp.com/');
		$url='http://captcha.qq.com/cap_union_new_verify';
		$post='aid=716027609&captype=&protocol=http&clientype=1&disturblevel=&apptype=2&noheader=0&uid='.$uin.'&color=&showtype=&fb=1&lang=2052&cap_cd='.$cap_cd.'&rnd='.rand(100000,999999).'&rand=0.744936'.time().'&sess='.$sess.'&subcapclass='.$subcapclass.'&vsig='.$sig.'&ans='.$ans.'&'.$collectname.'='.$collect.'&websig='.$websig.'&cdata='.$cdata;
		$data=$this->get_curl($url,$post);
		$arr=json_decode($data,true);
		if(array_key_exists('errorCode',$arr) && $arr['errorCode']==0){
			return array('rcode'=>0,'randstr'=>$arr['randstr'],'sig'=>$arr['ticket']);
		}elseif($arr['errorCode']==50){
			return array('rcode'=>50,'errmsg'=>'验证码输入错误！');
		}elseif($arr['errorCode']==12 && $subcapclass==9){
			return array('rcode'=>12,'errmsg'=>$arr['errMessage']);
		}else{
			return array('rcode'=>9,'errmsg'=>$arr['errMessage']);
		}
	}
	public function getvcpic($uin,$sig,$cap_cd,$sess){
		if(empty($uin))return array('saveOK'=>-1,'msg'=>'QQ不能为空');
		if(empty($sig))return array('saveOK'=>-1,'msg'=>'sig不能为空');
		$url='http://captcha.qq.com/cap_union_new_getcapbysig?aid=716027609&captype=&protocol=http&clientype=1&disturblevel=&apptype=2&noheader=0&uid='.$uin.'&color=&showtype=&fb=1&lang=2052&cap_cd='.$cap_cd.'&rnd='.rand(100000,999999).'&rand=0.02398118'.time().'&sess='.$sess.'&vsig='.$sig.'&ischartype=1';
		return $this->get_curl($url);
	}
	public function getvcpic2($uin,$sig,$cap_cd,$sess,$img_index=0){
		if(empty($uin))return array('saveOK'=>-1,'msg'=>'QQ不能为空');
		if(empty($sig))return array('saveOK'=>-1,'msg'=>'sig不能为空');
		$url='http://captcha.qq.com/cap_union_new_getcapbysig?aid=716027609&captype=&protocol=http&clientype=1&disturblevel=&apptype=2&noheader=0&uid='.$uin.'&color=&showtype=&fb=1&lang=2052&cap_cd='.$cap_cd.'&rnd='.rand(100000,999999).'&rand=0.02398118'.time().'&sess='.$sess.'&vsig='.$sig.'&img_index='.$img_index;
		return $url;
	}
	public function qqlogin($uin,$pwd,$p,$vcode,$pt_verifysession){
		if(empty($uin))return array('saveOK'=>-1,'msg'=>'QQ不能为空');
		if(empty($pwd))return array('saveOK'=>-1,'msg'=>'pwd不能为空');
		if(empty($p))return array('saveOK'=>-1,'msg'=>'密码不能为空');
		if(empty($vcode))return array('saveOK'=>-1,'msg'=>'验证码不能为空');
		if(empty($pt_verifysession))return array('saveOK'=>-1,'msg'=>'pt_verifysession不能为空');
		if(strpos('s'.$vcode,'!')){
			$v1=0;
		}else{
			$v1=1;
		}
		$url='http://ptlogin2.qq.com/login?u='.$uin.'&verifycode='.strtoupper($vcode).'&pt_vcode_v1='.$v1.'&pt_verifysession_v1='.$pt_verifysession.'&p='.$p.'&pt_randsalt=2&u1=https%3A%2F%2Fgraph.qq.com%2Foauth%2Flogin_jump&ptredirect=0&h=1&t=1&g=1&from_ui=1&ptlang=2052&action=1-3-'.time().'728&js_ver=10222&js_type=1&pt_uistyle=40&aid=716027609&daid=383&pt_3rd_aid=100312028&';
		$ret = $this->get_curl($url,0,0,0,1);
		if(preg_match("/ptuiCB\('(.*?)'\);/", $ret, $arr)){
			$r=explode("','",str_replace("', '","','",$arr[1]));
			if($r[0]==0){
				if(strpos($r[2],'mibao_vry'))
					return array('saveOK'=>-3,'msg'=>'请先到QQ安全中心关闭网页登录保护！');
				$data=$this->get_curl($r[2],0,0,0,1);
				if($data) {
					$cookie='';
					preg_match_all('/Set-Cookie: (.*);/iU',$data,$matchs);
					foreach ($matchs[1] as $val) {
						if(substr($val,-1)=='=')continue;
						$cookie.=$val.'; ';
					}
					preg_match('/skey=(.*?);/',$cookie,$skey);
					$data=$this->get_curl('https://passport.baidu.com/phoenix/account/startlogin?type=15&tpl=mn&u=https%3A%2F%2Fwww.baidu.com%2F&display=popup&act=implicit&xd=https%3A%2F%2Fwww.baidu.com%2Fcache%2Fuser%2Fhtml%2Fxd.html%23display%3Dpopup&fire_failure=1',0,0,0,1);
					preg_match('/mkey=(.{32});/',$data,$mkey);
					if($mkey = $mkey[1]){
						$url = 'https://graph.qq.com/oauth2.0/authorize';
						$post = 'response_type=code&client_id=100312028&redirect_uri=https%3A%2F%2Fpassport.baidu.com%2Fphoenix%2Faccount%2Fafterauth%3Fmkey%3D'.$mkey.'&scope=get_user_info%2Cget_other_info%2Cadd_t%2Cadd_share&state='.time().'&switch=&from_ptlogin=1&src=1&update_auth=1&openapi=80901010_2010&g_tk='.$this->getGTK($skey[1]).'&auth_time='.time().'510&ui=9E3FD6BC-3792-4834-A1B1-53B646BD4048';
						echo $data=$this->get_curl($url,$post,0,$cookie,1);exit;
						preg_match("/Location: (.*?)\r\n/", $data, $match);
						if($match[1]){
							$data=$this->get_curl($match[1],0,0,'mkey='.$mkey.';',1);
							preg_match('/BDUSS=(.*?);/',$data,$BDUSS);
							preg_match('/STOKEN=(.*?);/',$data,$STOKEN);
							preg_match('/PTOKEN=(.*?);/',$data,$PTOKEN);
							preg_match('/passport_uname: \'(.*?)\'/',$data,$uname);
							preg_match('/displayname: \'(.*?)\'/',$data,$displayname);
						}else{
							return array('saveOK'=>-3,'msg'=>'登录成功，回调百度失败！');
						}
					}else{
						return array('saveOK'=>-3,'msg'=>'登录成功，获取mkey失败！');
					}
				}
				if($BDUSS[1] && $STOKEN[1] && $PTOKEN[1]){
					return array('saveOK'=>0,'uid'=>$this->getUserid($uname[1]),'user'=>$uname[1],'displayname'=>$displayname[1],'bduss'=>$BDUSS[1],'stoken'=>$STOKEN[1],'ptoken'=>$PTOKEN[1]);
				}else{
					return array('saveOK'=>-3,'msg'=>'登录成功，获取相关信息失败！');
				}
			}elseif($r[0]==4){
				return array('saveOK'=>4,'msg'=>'验证码错误');
			}elseif($r[0]==3){
				return array('saveOK'=>3,'msg'=>'密码错误');
			}elseif($r[0]==19){
				return array('saveOK'=>19,'uin'=>$uin,'msg'=>'您的帐号暂时无法登录，请到 http://aq.qq.com/007 恢复正常使用');
			}else{
				return array('saveOK'=>-6,'msg'=>str_replace('"','\'',$r[4]));
			}
		}else{
			return array('saveOK'=>-2,'msg'=>$ret);
		}
	}
	public function getvc($uin,$sig,$sess){
		if(empty($uin))return array('saveOK'=>-1,'msg'=>'请先输入QQ号码');
		if(empty($sig))return array('saveOK'=>-1,'msg'=>'SIG不能为空');
		if(!preg_match("/^[1-9][0-9]{4,11}$/",$uin)) exit('{"saveOK":-2,"msg":"QQ号码不正确"}');
		if($sess=='0'){
			$url='http://captcha.qq.com/cap_union_new_show?aid=549000912&captype=&protocol=http&clientype=1&disturblevel=&apptype=2&noheader=0&uid='.$uin.'&color=&showtype=&fb=1&lang=2052&cap_cd='.$sig.'&rnd='.rand(100000,999999);
			$data=$this->get_curl($url);
			if(strpos($data,'img_index=')){
				if(preg_match("/=\"([0-9a-zA-Z\*\_\-]{187})\"/", $data, $match1) && preg_match("/=\"([0-9a-zA-Z\*\_\-]{204})\"/", $data, $match2)){
					preg_match('/\Number\(\"(\d+)\"\)/', $data, $Number);
					preg_match('/websig=([0-9a-f]{128})/', $data, $websig);
					preg_match('/ans=.*?&([a-z]{6})=/', $data, $collectname);
					preg_match('/{&quot;randstr&quot;:&quot;(.{4})&quot;,&quot;M&quot;:&quot;(\d+)&quot;,&quot;ans&quot;:&quot;([0-9a-f]{32})&quot;}/', $data, $cdata_arr);
					$cdata=$this->getcdata($cdata_arr[3],$cdata_arr[2],$cdata_arr[1]);
					$height = $Number[1];
					$imgA = $this->getvcpic2($uin,$match1[1],$sig,$match2[1],1);
					$imgB = $this->getvcpic2($uin,$match1[1],$sig,$match2[1],0);
					$width = $this->captcha($imgA, $imgB);
					$ans = $width.','.$height.';';
					return array('saveOK'=>2,'vc'=>$match1[1],'sess'=>$match2[1],'collectname'=>$collectname[1],'websig'=>$websig[1],'ans'=>$ans,'cdata'=>$cdata);
				}else{
					return array('saveOK'=>-3,'msg'=>'获取验证码失败');
				}
			}else{
				if(preg_match("/=\"([0-9a-zA-Z\*\_\-]{129})\"/", $data, $match1) && preg_match("/=\"([0-9a-zA-Z\*\_\-]{204})\"/", $data, $match2)){
					preg_match('/websig=([0-9a-f]{128})/', $data, $websig);
					preg_match('/ans\+\"&([a-z]{6})=/', $data, $collectname);
					preg_match('/{&quot;randstr&quot;:&quot;(.{4})&quot;,&quot;M&quot;:&quot;(\d+)&quot;,&quot;ans&quot;:&quot;([0-9a-f]{32})&quot;}/', $data, $cdata_arr);
					$cdata=$this->getcdata($cdata_arr[3],$cdata_arr[2],$cdata_arr[1]);
					return array('saveOK'=>0,'vc'=>$match1[1],'sess'=>$match2[1],'collectname'=>$collectname[1],'websig'=>$websig[1],'cdata'=>$cdata);
				}else{
					return array('saveOK'=>-3,'msg'=>'获取验证码失败');
				}
			}
		}else{
			$url='http://captcha.qq.com/cap_union_new_getsig';
			$post='aid=549000912&captype=&protocol=http&clientype=1&disturblevel=&apptype=2&noheader=0&uid='.$uin.'&color=&showtype=&fb=1&cap_cd='.$sig.'&rnd=634076&rand=0.37335288'.time().'&sess='.$sess;
			$data=$this->get_curl($url,$post);
			$arr=json_decode($data,true);
			$cdata=$this->getcdata($arr['chlg']['ans'],$arr['chlg']['M'],$arr['chlg']['randstr']);
			if($arr['initx'] && $arr['inity']){
				$height = $arr['inity'];
				$imgA = $this->getvcpic2($uin,$arr['vsig'],$sig,$sess,1);
				$imgB = $this->getvcpic2($uin,$arr['vsig'],$sig,$sess,0);
				$width = $this->captcha($imgA, $imgB);
				$ans = $width.','.$height.';';
				return array('saveOK'=>2,'vc'=>$arr['vsig'],'sess'=>$sess,'ans'=>$ans,'cdata'=>$cdata);
			}elseif($arr['vsig']){
				return array('saveOK'=>0,'vc'=>$arr['vsig'],'sess'=>$sess,'cdata'=>$cdata);
			}else{
				return array('saveOK'=>-3,'msg'=>'获取验证码失败');
			}
		}
	}
	public function checkvc($uin){
		if(empty($uin))return array('saveOK'=>-1,'msg'=>'请先输入QQ号码');
		if(!preg_match("/^[1-9][0-9]{4,13}$/",$uin)) exit('{"saveOK":-2,"msg":"QQ号码不正确"}');
		$url='http://check.ptlogin2.qq.com/check?pt_tea=2&uin='.$uin.'&appid=716027609&ptlang=2052&regmaster=&pt_uistyle=9&r=0.397176'.time();
		$data=$this->get_curl($url);
		if(preg_match("/ptui_checkVC\('(.*?)'\);/", $data, $arr)){
			$r=explode("','",$arr[1]);
			if($r[0]==0){
				return array('saveOK'=>0,'uin'=>$uin,'vcode'=>$r[1],'pt_verifysession'=>$r[3]);
			}else{
				return array('saveOK'=>1,'uin'=>$uin,'sig'=>$r[1]);
			}
		}else{
			return array('saveOK'=>-3,'msg'=>'获取验证码失败');
		}
	}
	public function getqrpic(){
		$url='http://ptlogin2.qq.com/ptqrshow?appid=716027609&e=2&l=M&s=4&d=72&v=4&t=0.5409099'.time().'&daid=383';
		$arr=$this->get_curl($url,0,0,0,1,0,0,1);
		preg_match('/qrsig=(.*?);/',$arr['header'],$match);
		if($qrsig=$match[1])
			exit('{"saveOK":0,"qrsig":"'.$qrsig.'","data":"'.base64_encode($arr['body']).'"}');
		else
			exit('{"saveOK":1,"msg":"二维码获取失败"}');
	}
	public function qrlogin(){
		$qrsig=empty($_GET['qrsig'])?exit('{"saveOK":-1,"msg":"qrsig不能为空"}'):$_GET['qrsig'];
		$sig=$this->login_sig();
		$url='http://ptlogin2.qq.com/ptqrlogin?u1=https%3A%2F%2Fgraph.qq.com%2Foauth%2Flogin_jump&ptqrtoken='.$this->getqrtoken($qrsig).'&ptredirect=0&h=1&t=1&g=1&from_ui=1&ptlang=2052&action=1-0-'.time().'000&js_ver=10222&js_type=1&login_sig='.$sig.'&pt_uistyle=40&aid=716027609&daid=383&pt_3rd_aid=100312028&';
		$ret = $this->get_curl($url,0,$url,'qrsig='.$qrsig.'; ',1);
		if(preg_match("/ptuiCB\('(.*?)'\);/", $ret, $arr)){
			$r=explode("','",str_replace("', '","','",$arr[1]));
			if($r[0]==0){
				$uin=$this->getuin($uin[1]);
				$data=$this->get_curl($r[2],0,0,0,1);
				if($data) {
					$cookie='';
					preg_match_all('/Set-Cookie: (.*);/iU',$data,$matchs);
					foreach ($matchs[1] as $val) {
						if(substr($val,-1)=='=')continue;
						$cookie.=$val.'; ';
					}
					preg_match('/skey=(.*?);/',$cookie,$skey);
					$data=$this->get_curl('https://passport.baidu.com/phoenix/account/startlogin?type=15&tpl=mn&u=https%3A%2F%2Fwww.baidu.com%2F&display=popup&act=implicit&xd=https%3A%2F%2Fwww.baidu.com%2Fcache%2Fuser%2Fhtml%2Fxd.html%23display%3Dpopup&fire_failure=1',0,0,0,1);
					preg_match('/mkey=(.{32});/',$data,$mkey);
					if($mkey = $mkey[1]){
						$url = 'https://graph.qq.com/oauth2.0/authorize';
						$post = 'response_type=code&client_id=100312028&redirect_uri=https%3A%2F%2Fpassport.baidu.com%2Fphoenix%2Faccount%2Fafterauth%3Fmkey%3D'.$mkey.'&scope=get_user_info%2Cget_other_info%2Cadd_t%2Cadd_share&state='.time().'&switch=&from_ptlogin=1&src=1&update_auth=1&openapi=80901010_2010&g_tk='.$this->getGTK($skey[1]).'&auth_time='.time().'510&ui=9E3FD6BC-3792-4834-A1B1-53B646BD4048';
						$data=$this->get_curl($url,$post,0,$cookie,1);
						preg_match("/Location: (.*?)\r\n/", $data, $match);
						if($match[1]){
							$data=$this->get_curl($match[1],0,0,'mkey='.$mkey.';',1);
							preg_match('/BDUSS=(.*?);/',$data,$BDUSS);
							preg_match('/STOKEN=(.*?);/',$data,$STOKEN);
							preg_match('/PTOKEN=(.*?);/',$data,$PTOKEN);
							preg_match('/passport_uname: \'(.*?)\'/',$data,$uname);
							preg_match('/displayname: \'(.*?)\'/',$data,$displayname);
						}else{
							return array('saveOK'=>-3,'msg'=>'登录成功，回调百度失败！');
						}
					}else{
						return array('saveOK'=>-3,'msg'=>'登录成功，获取mkey失败！');
					}
				}
				if($BDUSS[1] && $STOKEN[1] && $PTOKEN[1]){
					exit('ptuiCB("0","'.$this->getUserid($uname[1]).'","'.$uname[1].'","'.$displayname[1].'","'.$BDUSS[1].'","'.$STOKEN[1].'","'.$PTOKEN[1].'");');
				}else{
					exit('ptuiCB("6","'.$uin.'","登录成功，获取相关信息失败！");');
				}
			}elseif($r[0]==65){
				exit('ptuiCB("1","'.$uin.'","二维码已失效。");');
			}elseif($r[0]==66){
				exit('ptuiCB("2","'.$uin.'","二维码未失效。");');
			}elseif($r[0]==67){
				exit('ptuiCB("3","'.$uin.'","正在验证二维码。");');
			}else{
				exit('ptuiCB("6","'.$uin.'","'.str_replace('"','\'',$r[4]).'");');
			}
		}else{
			exit('{"saveOK":6,"msg":"'.$ret.'"}');
		}
	}
	private function getuin($uin){
        for($i = 0; $i < strlen($uin); $i++){
			if($uin[$i]=='o'||$uin[$i]=='0')continue;
			else break;
        }
        return substr($uin,$i);
    }
	private function getqrtoken($qrsig){
        $len = strlen($qrsig);
        $hash = 0;
        for($i = 0; $i < $len; $i++){
            $hash += (($hash << 5) & 2147483647) + ord($qrsig[$i]) & 2147483647;
			$hash &= 2147483647;
        }
        return $hash & 2147483647;
    }
	private function getUserid($uname){
		$ua = 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36';
		$data = $this->get_curl('http://tieba.baidu.com/home/get/panel?ie=utf-8&un='.urlencode($uname),0,0,0,0,$ua);
		$arr = json_decode($data,true);
		$userid = $arr['data']['id'];
		return $userid;
	}
	private function captcha($imgAurl,$imgBurl){
		$imgA = imagecreatefromjpeg($imgAurl);
		$imgB = imagecreatefromjpeg($imgBurl);
		$imgWidth = imagesx($imgA);
		$imgHeight = imagesy($imgA);
		
		$t=0;$r=0;
		for ($y=20; $y<$imgHeight-20; $y++){
		   for ($x=20; $x<$imgWidth-20; $x++){
			   $rgbA = imagecolorat($imgA,$x,$y);
			   $rgbB = imagecolorat($imgB,$x,$y);
			   if(abs($rgbA-$rgbB)>1800000){
				   $t++;
				   $r+=$x;
			   }
		   }
		}
		return round($r/$t)-55;
	}
	private function getcdata($ans,$M,$randstr){
		for ($r = 0; $r < $M && $r < 1000; $r++) {
			$c = $randstr . $r;
			$d = md5 ($c);
			if ($ans == $d) {
				$a = $r;
				break;
			}
		}
		return $a;
	}
	private function getGTK($skey){
        $len = strlen($skey);
        $hash = 5381;
        for ($i = 0; $i < $len; $i++) {
            $hash += ($hash << 5 & 2147483647) + ord($skey[$i]) & 2147483647;
            $hash &= 2147483647;
        }
        return $hash & 2147483647;
    }
	private function get_curl($url,$post=0,$referer=0,$cookie=0,$header=0,$ua=0,$nobaody=0,$split=0){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		$httpheader[] = "Accept:application/json";
		$httpheader[] = "Accept-Encoding:gzip,deflate,sdch";
		$httpheader[] = "Accept-Language:zh-CN,zh;q=0.8";
		$httpheader[] = "Connection:close";
		curl_setopt($ch, CURLOPT_HTTPHEADER, $httpheader);
		if($post){
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		}
		if($header){
			curl_setopt($ch, CURLOPT_HEADER, TRUE);
		}
		if($cookie){
			curl_setopt($ch, CURLOPT_COOKIE, $cookie);
		}
		if($referer){
			curl_setopt($ch, CURLOPT_REFERER, "http://ptlogin2.qq.com/");
		}
		if($ua){
			curl_setopt($ch, CURLOPT_USERAGENT,$ua);
		}else{
			curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.152 Safari/537.36');
		}
		if($nobaody){
			curl_setopt($ch, CURLOPT_NOBODY,1);

		}
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_ENCODING, "gzip");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		$ret = curl_exec($ch);
		if ($split) {
			$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
			$header = substr($ret, 0, $headerSize);
			$body = substr($ret, $headerSize);
			$ret=array();
			$ret['header']=$header;
			$ret['body']=$body;
		}
		curl_close($ch);
		return $ret;
	}
}