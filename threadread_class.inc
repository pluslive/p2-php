<?php
// p2 - �X���b�h ���[�h �N���X

require_once("./filectl_class.inc");
require_once './p2util.class.php';	// p2�p�̃��[�e�B���e�B�N���X

/**
 * �X���b�h���[�h�N���X
 */
class ThreadRead extends Thread{
	var $datlines; // dat����ǂݍ��񂾃��C�����i�[����z��
	
	var $resrange; // array( 'start' => i, 'to' => i, 'nofirst' => bool )
	
	var $onbytes; // �T�[�o����擾����dat�T�C�Y
	var $diedat; // �T�[�o����dat�擾���ł��Ȃ����true
	var $onthefly; // ���[�J����dat�ۑ����Ȃ��I���U�t���C�ǂݍ��݂Ȃ�true

	/**
	 * DAT���_�E�����[�h����
	 */
	function downloadDat()
	{
		global $sid2ch_php, $uaMona, $SID2ch, $dat_response_msg, $relogin2ch;
		
		// �܂�BBS
		if (P2Util::isHostMachiBbs($this->host)) {
			include_once("read_machibbs.inc");
			machiDownload();
		// JBBS@�������
		} elseif (P2Util::isHostJbbsShitaraba($this->host)) {
			include_once("read_machibbs.inc");
			machiDownload();
		} else {
			$this->getDatBytesFromLocalDat(); // $aThread->length ��set

			//2ch bbspink���ǂ�
			if (P2Util::isHost2chs($this->host) and $_GET['maru']) {
				//���O�C�����ĂȂ���� or ���O�C����A24���Ԉȏ�o�߂��Ă����玩���ă��O�C��
				if ((!file_exists($sid2ch_php) or $relogin2ch) or (@filemtime($sid2ch_php) < time() - 60*60*24)) {
					include_once("./login2ch.inc");
					if (!login2ch()) {
						$dat_response_msg = $this->get2chDatError();
						$this->diedat = true;
						return false;
					}
				}

				include($sid2ch_php);
				$this->downloadDat2chMaru();

			// 2ch�̉ߋ����O�q�ɓǂ�
			} elseif ($_GET['kakolog'] && $_GET['kakoget']) {
				if ($_GET['kakoget'] == 1) {
					$ext = ".dat.gz";
				} elseif ($_GET['kakoget'] == 2) {
					$ext = ".dat";
				}
				$this->downloadDat2chKako(urldecode($_GET['kakolog']), $ext);
			// 2ch or 2ch�݊�
			} else {
				$this->downloadDat2ch($this->length); // ����DL����
			}

		}
	
	}
	
	/**
	 * 2ch�p DAT���_�E�����[�h����
	 */
	function downloadDat2ch($from_bytes)
	{
		global $_conf, $dat_response_msg, $ls, $fsockopen_time_limit, $ext_win_target, $_info_msg_ht, $dat_perm, $proxy;
		global $debug, $prof;
	
		if(!($this->host && $this->bbs && $this->key)){return false;}

		if($from_bytes==""){$from_bytes=0;}
		if($from_bytes==0){
			$mode="w";
			$zero_read=true;
		}else{
			$mode="a";
			$from_bytes=$from_bytes-1;
		}
		
		$method = "GET";
		if (!$uaMona) {$uaMona = "Monazilla/1.00";}
		$p2ua = $uaMona." (".$_conf['p2name']."/".$_conf['p2version'].")";
		
		$url = "http://" . $this->host . "/{$this->bbs}/dat/{$this->key}.dat";
		//$url="http://news2.2ch.net/test/read.cgi?bbs=newsplus&key=1038486598";

		$URL = parse_url($url); //URL����
		if (isset($URL['query'])) { //�N�G���[
		    $URL['query'] = "?".$URL['query'];
		} else {
		    $URL['query'] = "";
		}

		//�v���L�V
		if($proxy['use']){
			$send_host=$proxy['host'];
			$send_port=$proxy['port'];
			$send_path=$url;
		}else{
			$send_host=$URL['host'];
			$send_port=$URL['port'];
			$send_path=$URL['path'].$URL['query'];
		}
		
		if (!$send_port){$send_port = 80;}//�f�t�H���g��80
			
		$request = $method." ".$send_path." HTTP/1.0\r\n";
		$request .= "Host: ".$URL['host']."\r\n";
		$request .= "Accept: */*\r\n";
		//$request .= "Accept-Charset: Shift_JIS\r\n";
		//$request .= "Accept-Encoding: gzip, deflate\r\n";
		$request .= "Accept-Language: ja, en\r\n";
		$request .= "User-Agent: ".$p2ua."\r\n";
		if(!$zero_read){$request .= "Range: bytes={$from_bytes}-\r\n";}
		$request .= "Referer: http://{$URL['host']}/{$this->bbs}/\r\n";
		$request .= "Connection: Close\r\n";

		if($this->modified){
			$request .= "If-Modified-Since: ".$this->modified."\r\n";
		}
	
		$request .= "\r\n";
		
		/* WEB�T�[�o�֐ڑ� */
		$fp = fsockopen($send_host, $send_port, $errno, $errstr, $fsockopen_time_limit);
		if (!$fp) {
			$url_t = P2Util::throughIme($url);
			$_info_msg_ht .= "<p>�T�[�o�ڑ��G���[: {$errstr} ({$errno})<br>p2 info: <a href=\"{$url_t}\"{$ext_win_target}>{$url}</a> �ɐڑ��ł��܂���ł����B</p>";
			$this->diedat=true; return false;
		}
		$wr = "";
		fputs($fp, $request);

		while (!feof($fp)){

			if($start_here){

				if($code=="200" || $code=="206"){
					
					while( !feof($fp) ){
						$wr .= fread($fp, 4096);
					}
					
					if(!$zero_read){ //�����̉��s�ł��ځ[��`�F�b�N
						if(substr($wr, 0, 1)!="\n"){
							//echo "���ځ[�񌟏o";
							fclose ($fp);
							unset($this->onbytes);
							unset($this->modified);
							return $this->downloadDat2ch(0); //���ځ[�񌟏o�B�S����蒼���B
						}
						$wr=substr($wr, 1);
					}
					FileCtl::make_datafile($this->keydat, $dat_perm);
					$fdat = fopen($this->keydat, $mode);
					fwrite($fdat, $wr);
					fclose ($fdat);
					//echo $wr."<br>";// for debug
					
					$debug && $prof->startTimer( "dat_size_check" );
					//�擾��T�C�Y�`�F�b�N
					if( $zero_read==false && $this->onbytes){
						$this->getDatBytesFromLocalDat(); //$aThread->length ��set
						if($this->onbytes != $this->length ){
							fclose ($fp);
							unset($this->onbytes);
							unset($this->modified);
							$_info_msg_ht .= "p2 info: $this->onbytes/$this->length �t�@�C���T�C�Y���ςȂ̂ŁAdat���Ď擾<br>";
							return $this->downloadDat2ch(0); //dat�T�C�Y�͕s���B�S����蒼���B
						}elseif($this->onbytes == $this->length){ //�T�C�Y�������Ȃ炻�̂܂�
							fclose ($fp);
							$this->isonline=true;
							return;
						}
					}
					$debug && $prof->stopTimer( "dat_size_check" );
					
				}else{  //�X���b�h���Ȃ��Ɣ��f
					fclose ($fp);
					return $this->downloadDat2chNotFound();
				}
				
			}else{
				$l = fgets($fp,32800);
				if( preg_match("/^HTTP\/1\.\d (\d+) (.+)\r\n/", $l, $matches) ){ // ex) HTTP/1.1 304 Not Modified
					$code = $matches[1];
					
					if($code=="200" || $code=="206"){ //Partial Content
						;
					}elseif( $code=="304" ){ //Not Modified
						fclose ($fp);
						$this->isonline=true;
						return "304 Not Modified";
					}elseif( $code=="416" ){ // Requested Range Not Satisfiable				
						//echo "���ځ[�񌟏o";
						fclose ($fp);
						unset($this->onbytes);
						unset($this->modified);
						return $this->downloadDat2ch(0); // ���ځ[�񌟏o�B�S����蒼���B
					}else{
						fclose ($fp);
						return $this->downloadDat2chNotFound();							
					}
				}
				
				if($zero_read){
					if( preg_match("/^Content-Length: ([0-9]+)/", $l, $matches) ){
						$this->onbytes = $matches[1];
					}
				}else{

					if( preg_match("/^Content-Range: bytes ([^\/]+)\/([0-9]+)/", $l, $matches) ){
						$this->onbytes = $matches[2];
					}
					
				}
					
				if( preg_match("/^Last-Modified: (.+)\r\n/", $l, $matches) ){
					//echo $matches[1]."<br>"; //debug
					$this->modified = $matches[1];
			
				}elseif($l=="\r\n"){
					$start_here=true;
				}
			}	
		}
		fclose($fp);
		$this->isonline=true;
		return true;
	}
	
	function downloadDat2chNotFound(){
		global $dat_response_msg;
		if( P2Util::isHost2chs($this->host) ){ //2ch, bbspink �Ȃ�read.cgi�Ŋm�F
			$dat_response_msg = $this->get2chDatError();
		}
		$this->diedat=true;
		return false;		
	}
	
	/**
	 * 2ch���p DAT���_�E�����[�h����
	 */
	function downloadDat2chMaru()
	{
		global $_conf, $dat_response_msg, $uaMona, $SID2ch, $relogin2ch, $fsockopen_time_limit, $ext_win_target, $_info_msg_ht, $dat_perm, $proxy;

		if (!($this->host && $this->bbs && $this->key)) {return false;}
		
		unset($datgz_attayo, $start_here, $isGzip, $done_gunzip, $marudatlines, $code);
		
		$method = "GET";
		$p2ua = $uaMona." (".$_conf['p2name']."/".$_conf['p2version'].")";
		
		//  GET /test/offlaw.cgi?bbs=��&key=�X���b�h�ԍ�&sid=�Z�b�V����ID HTTP/1.1
		$SID2ch = urlencode($SID2ch);
		$url = "http://" . $this->host . "/test/offlaw.cgi/{$this->bbs}/{$this->key}/?raw=0.0&sid={$SID2ch}";

		$URL = parse_url($url); // URL����
		if (isset($URL['query'])) { // �N�G���[
		    $URL['query'] = "?".$URL['query'];
		} else {
		    $URL['query'] = "";
		}
	
		//�v���L�V
		if($proxy['use']){
			$send_host=$proxy['host'];
			$send_port=$proxy['port'];
			$send_path=$url;
		}else{
			$send_host=$URL['host'];
			$send_port=$URL['port'];
			$send_path=$URL['path'].$URL['query'];
		}
		
		if (!$send_port){$send_port = 80;}//�f�t�H���g��80

		$request = $method." ".$send_path." HTTP/1.0\r\n";
		$request .= "Host: ".$URL['host']."\r\n";
		$request .= "Accept-Encoding: gzip, deflate\r\n";
		//$request .= "Accept-Language: ja, en\r\n";
		$request .= "User-Agent: ".$p2ua."\r\n";
		//$request .= "X-2ch-UA: ".$_conf['p2name']."/".$_conf['p2version']."\r\n";
		//$request .= "Range: bytes={$from_bytes}-\r\n";
		$request .= "Connection: Close\r\n";
		if($modified){$request .= "If-Modified-Since: $modified\r\n";}
		$request .= "\r\n";
		
		/* WEB�T�[�o�֐ڑ� */
		$fp = fsockopen($send_host, $send_port, $errno, $errstr, $fsockopen_time_limit);
		if (!$fp) {
			$url_t = P2Util::throughIme($url);
			$_info_msg_ht .= "<p>�T�[�o�ڑ��G���[: {$errstr} ({$errno})<br>p2 info - <a href=\"{$url_t}\"{$ext_win_target}>{$url}</a> �ɐڑ��ł��܂���ł����B</p>";
			$this->diedat = true;
			return false;
		}
		
		fputs($fp, $request);
		$body = "";
		while (!feof($fp)) {

			if($start_here){

				if($code=="200"){

					while (!feof($fp)){
						$body .= fread($fp, 4096);
					}

					
					if($isGzip){
						$gztempfile=$this->keydat.".gz";
						FileCtl::mkdir_for($gztempfile);
						$ftemp = fopen($gztempfile, "wb") or die("Error: {$gztempfile} ���X�V�ł��܂���ł���");
						fwrite($ftemp, $body);
						fclose ($ftemp);
						if(extension_loaded('zlib') ){
							$body = FileCtl::get_gzfile_contents($gztempfile);
						}else{
							if(file_exists($this->keydat)){ //���ɑ��݂���Ȃ�ꎞ�o�b�N�A�b�v�ޔ�
								if(file_exists($this->keydat.".bak")){unlink($this->keydat.".bak");}
								rename($this->keydat, $this->keydat.".bak");
							}
							$rcode=1;
							system("gzip -d $gztempfile", $rcode); //��
							if($rcode!=0){
								if(file_exists($this->keydat.".bak")){
									if(file_exists($this->keydat)){unlink($this->keydat);}
									rename($this->keydat.".bak", $this->keydat); //���s�Ȃ�o�b�N�A�b�v�߂�
								}
								$dat_response_msg="<p>p2 info - 2�����˂�ߋ����O�q�ɂ���̃X���b�h��荞�݂́APHP��<a href=\"http://www.php.net/manual/ja/ref.zlib.php\">zlib�g�����W���[��</a>���Ȃ����Asystem��gzip�R�}���h���g�p�\�łȂ���΂ł��܂���B</p>";
								if( file_exists($gztempfile) ){ unlink($gztempfile); } //gztemp�t�@�C�����̂Ă�
								$this->diedat=true;
								return false;
							}else{
								if(file_exists($this->keydat.".bak")){unlink($this->keydat.".bak");}
								$done_gunzip=true;
							}

						}
						if( file_exists($gztempfile) ){ unlink($gztempfile); } //temp�t�@�C�����̂Ă�
					}
					
					if(!$done_gunzip){
						FileCtl::make_datafile($this->keydat, $dat_perm);
						$fdat = fopen($this->keydat, "w");
						fwrite($fdat, $body);
						fclose ($fdat);
					}
					
					//�N���[�j���O=====
					$marudatlines=@file($this->keydat);
					if($marudatlines){
						$firstline=array_shift($marudatlines);
						if(! strstr($firstline, "+OK") ){ // �`�����N�Ƃ�
							$secondline = array_shift($marudatlines);
						}
						FileCtl::make_datafile($this->keydat, $dat_perm);
						$fdat = fopen($this->keydat, "w");
						foreach($marudatlines as $aline){
							if($chunked){ //�`�����N�G���R�[�f�B���O���~�����Ƃ���(HTTP 1.0�ł��̂�)
								fwrite($fdat, $aline);
							}else{
								fwrite($fdat, $aline);
							}
						}
						fclose ($fdat);
					}
					
					
				}else{ // dat.gz�͂Ȃ������Ɣ��f
					fclose ($fp);
					return $this->downloadDat2chMaruNotFound();
				}
				
			}else{
				$l = fgets($fp,128000);
				//echo $l."<br>";// for debug
				if( preg_match("/^HTTP\/1\.\d (\d+) (.+)\r\n/", $l, $matches) ){ // ex) HTTP/1.1 304 Not Modified
					$code = $matches[1];
					
					if($code=="200"){
						;
					}elseif( $code=="304" ){
						fclose ($fp);
						$this->isonline=true;
						return "304 Not Modified";
					}else{
						fclose ($fp);
						return $this->downloadDat2chMaruNotFound();							
					}
				
				}elseif( preg_match("/^Content-Encoding: (x-)?gzip/", $l, $matches) ){
					$isGzip = true;
				}elseif( preg_match("/^Last-Modified: (.+)\r\n/", $l, $matches) ){
					$lastmodified = $matches[1];
				}elseif( preg_match("/^Content-Length: ([0-9]+)/", $l, $matches) ){
					$onbytes = $matches[1];
				}elseif( preg_match("/^Transfer-Encoding: (.+)\r\n/", $l, $matches) ){ //Transfer-Encoding: chunked
					$t_enco = $matches[1];
					if($t_enco == "chunked"){
						$chunked = true;
					}
				}elseif($l=="\r\n"){
					$start_here = true;
				}
			}
			
		}
		fclose ($fp);
		$this->isonline=true;
		return true;
	}

	function downloadDat2chMaruNotFound()
	{
		global $_conf, $dat_response_msg, $relogin2ch;
		if (!$relogin2ch) {
			$relogin2ch=true;
			return $this->downloadDat();
		} else {
			$remarutori_ht="<a href=\"{$_conf['read_php']}?host={$this->host}&amp;bbs={$this->bbs}&amp;key={$this->key}&amp;ls={$ls}&amp;maru=true&amp;relogin2ch=true\">�Ď擾�����݂�</a>";
			$dat_response_msg="<p>p2 info - ��ID�ł̃X���b�h�擾�Ɏ��s���܂����B[{$remarutori_ht}]</p>";
			$this->diedat=true;
			return false;
		}
	}
	
	/**
	 * 2ch�̉ߋ����O�q�ɂ���dat.gz���_�E�����[�h���𓀂���
	 */
	function downloadDat2chKako($uri, $ext)
	{
		global $_conf, $dat_response_msg, $bbs_win_target, $fsockopen_time_limit, $ext_win_target, $_info_msg_ht, $dat_perm, $proxy;

		$url = $uri.$ext;
	
		$method = "GET";
		if (!$httpua) {
			$httpua = "Monazilla/1.00 (".$_conf['p2name']."/".$_conf['p2version'].")";
		}
		
		$URL = parse_url($url); //URL����
		if (isset($URL['query'])) { //�N�G���[
		    $URL['query'] = "?".$URL['query'];
		} else {
		    $URL['query'] = "";
		}
	
		//�v���L�V
		if($proxy['use']){
			$send_host=$proxy['host'];
			$send_port=$proxy['port'];
			$send_path=$url;
		}else{
			$send_host=$URL['host'];
			$send_port=$URL['port'];
			$send_path=$URL['path'].$URL['query'];
		}
		if (!$send_port){$send_port = 80;}//�f�t�H���g��80
	
		$request = $method." ".$send_path." HTTP/1.0\r\n";
		$request .= "Host: ".$URL['host']."\r\n";
		$request .= "User-Agent: ".$httpua."\r\n";
		$request .= "Connection: Close\r\n";
		//$request .= "Accept-Encoding: gzip\r\n";
		if($modified){
			$request .= "If-Modified-Since: $modified\r\n";
		}
		$request .= "\r\n";
	
		/* WEB�T�[�o�֐ڑ� */
		$fp = fsockopen($send_host, $send_port, $errno, $errstr, $fsockopen_time_limit);
		if (!$fp) {
			$url_t = P2Util::throughIme($url);
			echo "<p>�T�[�o�ڑ��G���[: $errstr ($errno)<br>p2 info - <a href=\"{$url_t}\"{$ext_win_target}>$url</a> �ɐڑ��ł��܂���ł����B</p>";
			$this->diedat = true;
			return false;
		}

		fputs($fp, $request);
		$body = "";
		while (!feof($fp)) {
		
			if($start_here){
			
				if($code=="200"){
					
					while (!feof($fp)){
						$body .= fread($fp, 4096);
					}
					
					if($isGzip){
						$gztempfile=$this->keydat.".gz";
						FileCtl::mkdir_for($gztempfile);
						$ftemp = fopen($gztempfile, "wb") or die("Error: $gztempfile ���X�V�ł��܂���ł���");
						fwrite($ftemp, $body);
						fclose ($ftemp);
						if(extension_loaded('zlib') ){
							$body = FileCtl::get_gzfile_contents($gztempfile);
						}else{
							if(file_exists($this->keydat)){ //���ɑ��݂���Ȃ�ꎞ�o�b�N�A�b�v�ޔ�
								if(file_exists($this->keydat.".bak")){unlink($this->keydat.".bak");}
								rename($this->keydat, $this->keydat.".bak");
							}
							$rcode=1;
							system("gzip -d $gztempfile", $rcode); //��
							if($rcode!=0){
								if(file_exists($this->keydat.".bak")){
									if(file_exists($this->keydat)){unlink($this->keydat);}
									rename($this->keydat.".bak", $this->keydat); //���s�Ȃ�o�b�N�A�b�v�߂�
								}
								$dat_response_msg="<p>p2 info - 2�����˂�ߋ����O�q�ɂ���̃X���b�h��荞�݂́APHP��<a href=\"http://www.php.net/manual/ja/ref.zlib.php\">zlib�g�����W���[��</a>���Ȃ����Asystem��gzip�R�}���h���g�p�\�łȂ���΂ł��܂���B</p>";
								if( file_exists($gztempfile) ){ unlink($gztempfile); } //gztemp�t�@�C�����̂Ă�
								$this->diedat=true;
								return false;
							}else{
								if(file_exists($this->keydat.".bak")){unlink($this->keydat.".bak");}
								$done_gunzip=true;
							}

						}
						if( file_exists($gztempfile) ){ unlink($gztempfile); } //temp�t�@�C�����̂Ă�
					}

					if(!$done_gunzip){
						FileCtl::make_datafile($this->keydat, $dat_perm);
						$fdat = fopen($this->keydat, "w");
						fwrite($fdat, $body);
						fclose ($fdat);
					}
					
				}else{ // �Ȃ������Ɣ��f
					fclose ($fp);
					return $this->downloadDat2chKakoNotFound($uri, $ext);
				
				}

			}else{
				$l = fgets($fp,128000);
				if( preg_match("/^HTTP\/1\.\d (\d+) (.+)\r\n/", $l, $matches) ){ // ex) HTTP/1.1 304 Not Modified
					$code = $matches[1];
					
					if($code=="200"){
						;
					}elseif( $code=="304" ){
						fclose ($fp);
						$this->isonline=true;
						return "304 Not Modified";
					}else{
						fclose ($fp);
						return $this->downloadDat2chKakoNotFound($uri, $ext);
					}

				}elseif( preg_match("/^Content-Encoding: (x-)?gzip/", $l, $matches) ){
					$isGzip=true;
				}elseif( preg_match("/^Last-Modified: (.+)\r\n/", $l, $matches) ){
					$lastmodified = $matches[1];
				}elseif( preg_match("/^Content-Length: ([0-9]+)/", $l, $matches) ){
					$onbytes = $matches[1];
				}elseif($l=="\r\n"){
					$start_here=true;
				}
			}
			
		}
		fclose ($fp);
		$this->isonline=true;
		return true;
	}
	
	function downloadDat2chKakoNotFound($uri, $ext){
		global $dat_response_msg, $bbs_win_target;
					
		if($ext==".dat.gz"){
			return $this->downloadDat2chKako($uri, ".dat"); //.dat.gz���Ȃ�������.dat�ł�����x
		}
		if($_GET['kakolog']){
			$kakolog_ht = "<p><a href=\"{$_GET['kakolog']}.html\"{$bbs_win_target}>{$_GET['kakolog']}.html</a></p>";
		}
		$dat_response_msg = "<p>p2 info - 2�����˂�ߋ����O�q�ɂ���̃X���b�h��荞�݂Ɏ��s���܂����B</p>";
		$dat_response_msg .= $kakolog_ht;
		$this->diedat=true;
		return false;

	}
	
	/**
	 * 2ch��dat���擾�ł��Ȃ�����������Ԃ�
	 */
	function get2chDatError()
	{
		global $_conf, $ls, $idpw2ch_php, $sid2ch_php, $bbs_win_target;
		global $fsockopen_time_limit, $_info_msg_ht, $ext_win_target, $proxy;
	
		$read_url = "http://{$this->host}/test/read.cgi/{$this->bbs}/{$this->key}";
		
		// read.cgi �擾 ====================
		include_once("./wap.inc");
		$wap_ua = new UserAgent;
		$wap_ua->setAgent($_conf['p2name']."/".$_conf['p2version']); // �����́A"Monazilla/" �������NG
		$wap_ua->setTimeout($fsockopen_time_limit);
		$wap_req = new Request;
		$wap_req->setUrl($read_url);
		if ($proxy['use']) {
			$wap_req->setProxy($proxy['host'], $proxy['port']);
		}
		$wap_res = $wap_ua->request($wap_req);
		
		if ($wap_res->is_error()) {
			$url_t = P2Util::throughIme($wap_req->url);
			$_info_msg_ht .= "<div>Error: {$wap_res->code} {$wap_res->message}<br>";
			$_info_msg_ht .= "p2 info: <a href=\"{$url_t}\"{$ext_win_target}>{$wap_req->url}</a> �ɐڑ��ł��܂���ł����B</div>";
			$read_response = false;
		} else {
			$read_response = $wap_res->content;
		}
		unset($wap_ua, $wap_req, $wap_res);		
		//=============================
		
		$kakosoko_match="/<title><hr>���@���̃X���b�h�͉ߋ����O�q�ɂɊi..����Ă��܂��B<hr><\/title>/";
		$naidesu_match="/<title>����Ȕ�or�X���b�h�Ȃ��ł��B<\/title>/";
		
		//<a href="../../../../mac/kako/1004/10046/1004680972.html">
		$kakohtml_match="/<a href=\"\.\.\/\.\.\/\.\.\/\.\.\/([^\/]+\/kako\/\d+(\/\d+)?\/(\d+)).html\">/";
		$waithtml_match="/html�������̂�҂��Ă���悤�ł��B/";
		
		if(preg_match($kakosoko_match, $read_response, $matches)){ //<title>�����̃X���b�h�͉ߋ����O�q�ɂ�
			$dat_response_status="���̃X���b�h�͉ߋ����O�q�ɂɊi�[����Ă��܂��B";
			//if(file_exists($idpw2ch_php) || file_exists($sid2ch_php)){
				$marutori_ht="<a href=\"{$_conf['read_php']}?host={$this->host}&amp;bbs={$this->bbs}&amp;key={$this->key}&amp;ls={$ls}&amp;maru=true\">��ID��p2�Ɏ�荞��</a>";
			//}else{
			//	$marutori_ht="<a href=\"login2ch.php\" target=\"subject\">��ID���O�C��</a>";
			//}
			$dat_response_msg="<p>2ch info - ���̃X���b�h�͉ߋ����O�q�ɂɊi�[����Ă��܂��B [{$marutori_ht}]</p>";
			
		}elseif( preg_match($naidesu_match, $read_response, $matches) ){ //<title>������Ȕ�or�X���b�h�Ȃ��ł��B
		
			if( preg_match($kakohtml_match, $read_response, $matches) ){
				$dat_response_status = "����! �ߋ����O�q�ɂŁAhtml�����ꂽ�X���b�h�𔭌����܂����B";
				$kakolog_uri = "http://{$this->host}/{$matches[1]}";
				$kakolog_url_en = urlencode($kakolog_uri);
				$read_kako_url="{$_conf['read_php']}?host={$this->host}&amp;bbs={$this->bbs}&amp;key={$this->key}&amp;ls={$ls}&amp;kakolog={$kakolog_url_en}&amp;kakoget=1";
				$dat_response_msg="<p>2ch info - ����! �ߋ����O�q�ɂŁA<a href=\"{$kakolog_uri}.html\"{$bbs_win_target}>�X���b�h {$matches[3]}.html</a> �𔭌����܂����B [<a href=\"{$read_kako_url}\">p2�Ɏ�荞��œǂ�</a>]</p>";
				
			}elseif( preg_match($waithtml_match, $read_response, $matches) ){
				$dat_response_status="����! �X���b�h��html�������̂�҂��Ă���悤�ł��B";
				$marutori_ht="<a href=\"{$_conf['read_php']}?host={$this->host}&amp;bbs={$this->bbs}&amp;key={$this->key}&amp;ls={$ls}&amp;maru=true\">��ID��p2�Ɏ�荞��</a>";
				$dat_response_msg="<p>2ch info - ����! �X���b�h��html�������̂�҂��Ă���悤�ł��B [{$marutori_ht}]</p>";
				
			}else{
				if($_GET['kakolog']){
					$dat_response_status="����Ȕ�or�X���b�h�Ȃ��ł��B";
					$kako_html_url=urldecode($_GET['kakolog']).".html";
					$read_kako_url="{$_conf['read_php']}?host={$this->host}&amp;bbs={$this->bbs}&amp;key={$this->key}&amp;ls={$ls}&amp;kakolog={$_GET['kakolog']}&amp;kakoget=1";
					$dat_response_msg = "<p>2ch info - ����Ȕ�or�X���b�h�Ȃ��ł��B</p>";
					$dat_response_msg .= "<p><a href=\"{$kako_html_url}\"{$bbs_win_target}>{$kako_html_url}</a> [<a href=\"{$read_kako_url}\">p2�Ƀ��O����荞��œǂ�</a>]</p>";
				}else{				
					$dat_response_status="����Ȕ�or�X���b�h�Ȃ��ł��B";
					$dat_response_msg="<p>2ch info - ����Ȕ�or�X���b�h�Ȃ��ł��B</p>";
				}
			}
			
		} elseif ($_GET['kakolog']) {
			$dat_response_status="";
			$kako_html_url=urldecode($_GET['kakolog']).".html";
			$read_kako_url="{$_conf['read_php']}?host={$this->host}&amp;bbs={$this->bbs}&amp;key={$this->key}&amp;ls={$ls}&amp;kakolog={$_GET['kakolog']}&amp;kakoget=1";
			$dat_response_msg="<p><a href=\"{$kako_html_url}\"{$bbs_win_target}>{$kako_html_url}</a> [<a href=\"{$read_kako_url}\">p2�Ƀ��O����荞��œǂ�</a>]</p>";
		
		}
		
		return $dat_response_msg;
	}
	
	/**
	 * >>1�݂̂��v���r���[����
	 */
	function previewOne()
	{
		global $_conf, $dat_response_msg, $ls, $ptitle_ht, $fsockopen_time_limit, $ext_win_target, $_info_msg_ht, $proxy;

		if (!($this->host && $this->bbs && $this->key)) {return false;}
		
		// ���[�J��dat����擾
		if (is_readable($this->keydat)) {
			$fd = fopen($this->keydat, "r");
			$first_line = fgets($fd, 32800);
			fclose ($fd);
			
			// be.2ch.net �Ȃ�EUC��SJIS�ϊ�
			if (P2Util::isHostBe2chNet($this->host)) {
				include_once './strctl_class.inc';
				$first_line = StrCtl::p2EUCtoSJIS($first_line);
			}
	
			$first_datline = rtrim($first_line);
			if (strstr($first_datline, "<>")) {
				$datline_sepa = "<>";
			} else {
				$datline_sepa = ",";
				$this->dat_type = "2ch_old";
			}
			$d = explode($datline_sepa, $first_datline);
			$this->ttitle = $d[4];
		}
		
		// ���[�J��dat�Ȃ���΃I�����C������
		if (!$first_line) {
		
			$method = "GET";
			$url = "http://" . $this->host . "/{$this->bbs}/dat/{$this->key}.dat";
			
			$URL = parse_url($url); // URL����
			if (isset($URL['query'])) { // �N�G���[
			    $URL['query'] = "?".$URL['query'];
			} else {
			    $URL['query'] = "";
			}
		
			//�v���L�V
			if ($proxy['use']) {
				$send_host = $proxy['host'];
				$send_port = $proxy['port'];
				$send_path = $url;
			} else {
				$send_host = $URL['host'];
				$send_port = $URL['port'];
				$send_path = $URL['path'].$URL['query'];
			}
			
			if (!$send_port) {$send_port = 80;} // �f�t�H���g��80
	
			$request = $method." ".$send_path." HTTP/1.0\r\n";
			$request .= "Host: ".$URL['host']."\r\n";
			$request .= "User-Agent: Monazilla/1.00 (".$_conf['p2name']."/".$_conf['p2version'].")"."\r\n";
			//$request .= "Range: bytes={$from_bytes}-\r\n";
			$request .= "Connection: Close\r\n";
			$request .= "\r\n";
			
			/* WEB�T�[�o�֐ڑ� */
			$fp = fsockopen($send_host, $send_port, $errno, $errstr, $fsockopen_time_limit);
			if (!$fp) {
				$url_t = P2Util::throughIme($url);
				$_info_msg_ht .= "<p>�T�[�o�ڑ��G���[: $errstr ($errno)<br>p2 info - <a href=\"{$url_t}\"{$ext_win_target}>{$url}</a> �ɐڑ��ł��܂���ł����B</p>";
				$this->diedat = true;
				return false;
			}
			
			fputs($fp, $request);
		
			while (!feof($fp)) {

				if ($start_here) {
				
					if ($code == "200") {
						$first_line = fgets($fp, 32800);
						break;
					} else {
						fclose($fp);
						return $this->previewOneNotFound();
					}
				} else {
					$l = fgets($fp,32800);
					//echo $l."<br>";// for debug
					if (preg_match("/^HTTP\/1\.\d (\d+) (.+)\r\n/", $l, $matches)) { // ex) HTTP/1.1 304 Not Modified
						$code = $matches[1];
						
						if ($code == "200") {
							;
						} else {
							fclose($fp);
							return $this->previewOneNotFound();							
						}

					} elseif (preg_match("/^Content-Length: ([0-9]+)/", $l, $matches)) {
						$onbytes = $matches[1];
					} elseif ($l == "\r\n") {
						$start_here = true;
					}
				}
				
			}
			fclose($fp);
			
			// be.2ch.net �Ȃ�EUC��SJIS�ϊ�
			if (P2Util::isHostBe2chNet($this->host)) {
				include_once './strctl_class.inc';
				$first_line = StrCtl::p2EUCtoSJIS($first_line);
			}

			$first_datline = rtrim($first_line);
			
			if (strstr($first_datline, "<>")) {
				$datline_sepa = "<>";
			} else {
				$datline_sepa = ",";
				$this->dat_type = "2ch_old";
			}
			$d = explode($datline_sepa, $first_datline);
			$this->ttitle = $d[4];
			
			$this->onthefly = true;
			
		} else {
			if (!$this->newline) { $this->newline = 2; } // �֋X��
		}

		$this->onthefly && $body .= "<div><span class=\"onthefly\">on the fly</span></div>";
		$body .= "<dl>";
		
		include_once("./showthread_class.inc"); // HTML�\���N���X
		include_once("./showthreadpc_class.inc"); // HTML�\���N���X
		$aShowThread = new ShowThreadPc($this);
		$body .= $aShowThread->transRes($first_line, 1); // 1��\��
		unset($aShowThread);
		
		$body .= "</dl>\n";
		return $body;
	}
	
	function previewOneNotFound()
	{
		global $dat_response_msg;
		if (P2Util::isHost2chs($this->host)) { // 2ch, bbspink �Ȃ�read.cgi�Ŋm�F
			$dat_response_msg = $this->get2chDatError();
		}
		$this->diedat = true;
		return false;
	}
	
	//============================================================================
	// lsToPoint -- $ls�𕪉�����start��to��nofirst�����߂郁�\�b�h
	//============================================================================
	function lsToPoint($ls, $data_size){
		global $ktai, $k_rnum_range, $k_rnum_all_range;

		$to=false;
		
		if( strstr($ls, "n") ){
			$nofirst=true;
			$ls=preg_replace("/n/", "", $ls);
		}

		$n=explode("-", $ls);
		if(sizeof($n)==1){
			if(substr($n[0],0,1)=="l"){
				$nn=substr($n[0],1);
				if($ktai){
					if($nn > $k_rnum_range){
						$nn=$k_rnum_range;
					}
				}
				$start = $data_size-$nn;
				if($start<1){ $start=1; }
				
				$to=$data_size;
			}elseif($ls=="all"){
				$start=1; $to=$data_size;
			}else{
				$start=$ls; $to=$ls; $nofirst=true;
			}
		}else{
			$start=$n[0];
			if(!$start){$start=1;}
			$to=$n[1];
			if(!$to){$to=$data_size;}
		}
		
		if($ktai){
			if($k_rnum_all_range > 0){
				if($start + $k_rnum_all_range <= $to){
					$to = $start + $k_rnum_all_range;
				}
				$k_rnum_all_range = $k_rnum_all_range - ($to - $start);
				$all_end=true;
			}
			if(!$all_end){
				if($start + $k_rnum_range <= $to){
					$to = $start + $k_rnum_range;
				}
			}
		}

		$this->resrange = array('start'=>$start,'to'=>$to,'nofirst'=>$nofirst);
		return $this->resrange;
	}
	
	/**
	 * Dat��ǂݍ��ރ��\�b�h
	 * $this->datlines �� set ����
	 */
	function readDat($keydat)
	{
		if (file_exists($keydat)) {
			if ($this->datlines = @file($keydat)) {
			
				// be.2ch.net �Ȃ�EUC��SJIS�ϊ�
				if (P2Util::isHostBe2chNet($this->host)) {
					include_once './strctl_class.inc';
					$this->datlines = array_map(create_function('$str', 'return StrCtl::p2EUCtoSJIS($str);'), $this->datlines);
				}
			
				if (!strstr($this->datlines[0], "<>")) {
					$this->dat_type = "2ch_old";
				}
			}
		} else {
			return false;
		}
		$this->rescount = sizeof($this->datlines);

		return $this->datlines;
	}

	//=========================================
	// datline��explode���郁�\�b�h
	//=========================================
	function explodeDatLine($aLine){
		$aLine = rtrim($aLine);

		if($this->dat_type == "2ch_old"){
			$parts = explode(",", $aLine);
		}else{
			$parts = explode("<>", $aLine);
		}
		
		return $parts;
	}

}
	
?>