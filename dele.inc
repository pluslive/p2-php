<?php
// p2 - �X���b�h�f�[�^�ADAT���폜����

require_once("datactl.inc");

// ==================================================
// �w�肵���z��keys�̃��O���폜����֐�
// ==================================================
function deleteLogs($host, $bbs, $keys){
	
	$datdir_host=datdirOfHost($host);
	
	if(sizeof($keys)==1){ //�w��key�̃��O���폜
		offRecent($host, $bbs, $keys[0]);
		offResHist($host, $bbs, $keys[0]);
		$r = deleteThisKey($datdir_host, $bbs, $keys[0]);
		
	}elseif($keys){ //�w��key�z��̃��O���폜�itaborn�j
		foreach($keys as $keyval){
			offRecent($host, $bbs, $keyval);
			offResHist($host, $bbs, $keyval);
			deleteThisKey($datdir_host, $bbs, $keyval);
		}
		$r = true;
	}

	return $r;
}//�œK���̗]�n����

// ==================================================
// �w�肵���L�[�̃X���b�h���O���폜����֐�
// ==================================================
function deleteThisKey($datdir_host, $bbs, $key){

	//$datdir_host=datdirOfHost($host);
	$anidx = "$datdir_host/{$bbs}/{$key}.idx";
	$adat = "$datdir_host/{$bbs}/{$key}.dat";
	
	//File����
	if( file_exists($anidx) ){
		$done=true;
		unlink($anidx);
	}
	if( file_exists($adat) ){
		$done=true;
		unlink($adat);
	}
	
	if($done){
		return 1;
	}else{
		return 2;
	}
}


// ==================================================
// �w�肵���L�[���ŋߓǂ񂾃X���ɓ����Ă邩�ǂ������`�F�b�N����֐�
// ==================================================
function checkRecent($host, $bbs, $key){
	global $rctfile;

	$lines = @file($rctfile); //�ǂݍ���
	if($lines){ //�����true
		foreach($lines as $l){
			$l=trim($l);
			$lar = explode("<>", $l);
			if( $lar[1] == $key && $lar[10] == $host && $lar[11] == $bbs){ // ��������
				return true;
			}
		}
	}
	return false;
}

// ==================================================
// �w�肵���L�[���������ݗ����ɓ����Ă邩�ǂ������`�F�b�N����֐�
// ==================================================
function checkResHist($host, $bbs, $key){
	global $prefdir;
	
	$rh_idx=$prefdir."/p2_res_hist.idx";
	$lines = @file($rh_idx); //�ǂݍ���
	if($lines){//�����true
		foreach($lines as $l){
			$l=trim($l);
			$lar = explode("<>", $l);
			if( $lar[1] == $key && $lar[10] == $host && $lar[11] == $bbs){ // ��������
				return true;
			}
		}
	}
	return false;
}

// ==================================================
// �w�肵���L�[�̗������폜����֐�
// ==================================================
function offRecent($host, $bbs, $key){
	global $rctfile;

	$lines = @file($rctfile); //�ǂݍ���
	if($lines){ //����΍폜
		foreach($lines as $l){
			$l=trim($l);
			$lar = explode("<>", $l);
			if( $lar[1] == $key && $lar[10] == $host && $lar[11] == $bbs){ // �폜
				$done = true;
				continue;
			}
			$neolines[]=$l;
		}
	}

	//��������
	$fp = @fopen($rctfile,"w") or die("Error: cannot write. ({$rctfile})");
	if($neolines){
		foreach($neolines as $l){ fputs($fp, $l."\n"); }
	}
	fclose($fp);
	
	if($done){
		return 1;
	}else{
		return 2;
	}
}

// ==================================================
// �w�肵���L�[�̏������ݗ������폜����֐�
// ==================================================
function offResHist($host, $bbs, $key){
	global $prefdir;
	
	$rh_idx=$prefdir."/p2_res_hist.idx";
	$lines = @file($rh_idx); //�ǂݍ���
	if($lines){//����΍폜
		foreach($lines as $l){
			$l=trim($l);
			$lar = explode("<>", $l);
			if( $lar[1] == $key && $lar[10] == $host && $lar[11] == $bbs){ // �폜
				$done = true;
				continue;
			}
			$neolines[]=$l;
		}
	}

	//��������
	$fp = @fopen($rh_idx,"w") or die("Error: cannot write. ({$rh_idx})");
	if($neolines){
		foreach($neolines as $l){ fputs($fp, $l."\n"); }
	}
	fclose($fp);
	
	if($done){
		return 1;
	}else{
		return 2;
	}
}

?>