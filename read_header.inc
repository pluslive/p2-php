<?php
// p2 -  �X���b�h�\�� -  �w�b�_���� -  for read.php

//�ϐ�
$diedat_msg="";

$info_st="���";
$delete_st="�폜";
$all_st="�S��";
$prev_st="�O";
$next_st="��";
$shinchaku_st="�V�����X�̕\��";
$tuduki_st="������ǂ�";
$moto_thre_st="���X��";
$latest_st="�ŐV";
$dores_st="���X";
$aborn_st="���ڂ�";

$motothre_url = $aThread->getMotoThread($GLOBAL['ls']);
$ttitle_en = base64_encode($aThread->ttitle);
$ttitle_en_q = "&amp;ttitle_en=".$ttitle_en;
$bbs_q = "&amp;bbs=".$aThread->bbs;
$key_q = "&amp;key=".$aThread->key;
$popup_q = "&amp;popup=1";
$offline_q = "&amp;offline=1";

//=================================================================
// �w�b�_
//=================================================================

//���C�Ƀ}�[�N�ݒ�==================================================

if($aThread->fav){$favmark="<span class=\"fav\">��</span>";}else{$favmark="<span class=\"fav\">+</span>";}
if($aThread->fav){$favdo=0;}else{$favdo=1;}

//���X�i�r�ݒ�=====================================================

$rnum_range=100;
$latest_show_res_num=50; //�ŐVXX

$read_navi_range="";

//----------------------------------------------
// $read_navi_range -- 1- 101- 201-
for($i=1; $i <= $aThread->rescount; $i=$i+$rnum_range){
	$offline_range_q="";
	$ito=$i+$rnum_range-1;
	if($ito <= $aThread->rnum){
		$offline_range_q=$offline_q;
	}
	$read_navi_range = $read_navi_range . "<a href=\"{$_conf['read_php']}?host={$aThread->host}{$bbs_q}{$key_q}&amp;ls={$i}-{$ito}{$offline_range_q}\">{$i}-</a>\n";

}

//----------------------------------------------
// $read_navi_previous -- �O100
$before_rnum=$aThread->resrange['start']-$rnum_range;
if($before_rnum<1){$before_rnum=1;}
if($aThread->resrange['start']==1){
	$read_navi_previous_isInvisible=true;
}
//if($before_rnum!=1){
//	$read_navi_previous_anchor="#r{$before_rnum}";
//}

if(!$read_navi_previous_isInvisible){
	$read_navi_previous="<a href=\"{$_conf['read_php']}?host={$aThread->host}{$bbs_q}{$key_q}&amp;ls={$before_rnum}-{$aThread->resrange['start']}{$offline_q}{$read_navi_previous_anchor}\">{$prev_st}{$rnum_range}</a>";
	$read_navi_previous_header="<a href=\"{$_conf['read_php']}?host={$aThread->host}{$bbs_q}{$key_q}&amp;ls={$before_rnum}-{$aThread->resrange['start']}{$offline_q}#r{$aThread->resrange['start']}\">{$prev_st}{$rnum_range}</a>";
}

//----------------------------------------------
//$read_navi_next -- ��100
if($aThread->resrange['to'] > $aThread->rescount){
	$aThread->resrange['to'] = $aThread->rescount;
	//$read_navi_next_anchor="#r{$aThread->rescount}";
	//$read_navi_next_isInvisible=true;
}else{
	//$read_navi_next_anchor="#r{$aThread->resrange['to']}";
}
if($aThread->resrange['to'] == $aThread->rescount){
	$read_navi_next_anchor="#r{$aThread->rescount}";
}
$after_rnum=$aThread->resrange['to']+$rnum_range;

$offline_range_q="";
if($after_rnum <= $aThread->rnum){
	$offline_range_q=$offline_q;
}

//if(!$read_navi_next_isInvisible){
$read_navi_next="<a href=\"{$_conf['read_php']}?host={$aThread->host}{$bbs_q}{$key_q}&amp;ls={$aThread->resrange['to']}-{$after_rnum}{$offline_range_q}&amp;nt={$newtime}{$read_navi_next_anchor}\">{$next_st}{$rnum_range}</a>";
//}

//----------------------------------------------
// $read_footer_navi_new  ������ǂ� �V�����X�̕\��

if($aThread->resrange['to'] == $aThread->rescount){
	$read_footer_navi_new="<a href=\"{$_conf['read_php']}?host={$aThread->host}{$bbs_q}{$key_q}&amp;ls={$aThread->rescount}-&amp;nt={$newtime}#r{$aThread->rescount}\" accesskey=\"r\">{$shinchaku_st}</a>";
}else{
	$read_footer_navi_new="<a href=\"{$_conf['read_php']}?host={$aThread->host}{$bbs_q}{$key_q}&amp;ls={$aThread->resrange['to']}-{$offline_q}\" accesskey=\"r\">{$tuduki_st}</a>";
}

//====================================================================
// HTML�v�����g
//====================================================================

//�c�[���o�[����HTML=======
$toolbar_right_ht=<<<EOTOOLBAR
			<a href="{$subject_php}?host={$aThread->host}{$bbs_q}{$key_q}" target="subject">{$aThread->itaj}</a>
			<!-- <a href="{$subject_php}?host={$aThread->host}{$bbs_q}{$key_q}&amp;setfav={$favdo}" target="subject" title="���C�ɃX���}�[�N">{$favmark}</a> -->
			<a href="info.php?host={$aThread->host}{$bbs_q}{$key_q}{$ttitle_en_q}" target="info" onClick="return OpenSubWin('info.php?host={$aThread->host}{$bbs_q}&amp;key={$aThread->key}{$ttitle_en_q}{$popup_q}',{$STYLE['info_pop_size']},0,0)">{$info_st}</a> 
			<a href="info.php?host={$aThread->host}{$bbs_q}{$key_q}{$ttitle_en_q}&amp;dele=true" target="info" onClick="return OpenSubWin('info.php?host={$aThread->host}{$bbs_q}{$key_q}{$ttitle_en_q}&amp;popup=2&amp;dele=true',{$STYLE['info_pop_size']},0,0)" title="���O���폜����">{$delete_st}</a> 
<!--			<a href="info.php?host={$aThread->host}{$bbs_q}{$key_q}{$ttitle_en_q}&amp;taborn=2" target="info" onClick="return OpenSubWin('info.php?host={$aThread->host}{$bbs_q}&amp;key={$aThread->key}{$ttitle_en_q}&amp;popup=2&amp;taborn=2',{$STYLE['info_pop_size']},0,0)" title="�X���b�h�̂��ځ[���Ԃ��g�O������">{$aborn_st}</a> -->
			<a href="{$motothre_url}" title="�T�[�o��̃I���W�i���X����\��">{$moto_thre_st}</a>
EOTOOLBAR;

//=====================================
header_content_type();
if($doctype){ echo $doctype;}
echo <<<EOHEADER
<html lang="ja">
<head>
	<meta name="ROBOTS" content="NOINDEX, NOFOLLOW">
	<meta http-equiv="Content-Style-Type" content="text/css">
	<meta http-equiv="Content-Script-Type" content="text/javascript">
	<title>{$ptitle_ht}</title>\n
EOHEADER;

@include("style/style_css.inc"); //�X�^�C���V�[�g
@include("style/read_css.inc"); //�X�^�C���V�[�g

echo <<<EOP
	<script type="text/javascript" src="{$basic_js}"></script>
	<script type="text/javascript" src="{$respopup_js}"></script>
	<script type="text/javascript" src="js/htmlpopup.js"></script>
	<script type="text/javascript" src="js/showhide.js"></script>\n
EOP;
if($bottom_res_form){
	echo <<<EOP
	<script type="text/javascript" src="js/post_form.js"></script>\n
EOP;
	$onLoad_script.="checkSage( document.getElementById('mail') );";
}

if(! $_GET['one']){
	$onLoad_script.="setWinTitle();";
}

if($onLoad_script){
	$onLoad_ht=" onLoad=\"{$onLoad_script}\"";
}

echo <<<EOP
</head>
<body{$onLoad_ht}>\n
EOP;

echo $_info_msg_ht;
$_info_msg_ht="";

// �X�����T�[�o�ɂȂ���� ============================
if ($aThread->diedat) { 

	if ($dat_response_msg) {
		$diedat_msg = $dat_response_msg;
	} else {
		$diedat_msg = "<p><b>p2 info - �T�[�o����ŐV�̃X���b�h�����擾�ł��܂���ł����B</b></p>";
	}
	
	if ($_conf['iframe_popup'] == 1) {
		$motothre_popup=" onMouseover=\"showHtmlPopUp('{$motothre_url}',event,{$_conf['iframe_popup_delay']})\" onMouseout=\"offHtmlPopUp()\"";
	} elseif ($_conf['iframe_popup'] == 2) {
		$motothre_popup=" onMouseover=\"showHtmlPopUp('{$motothre_url}',event,{$_conf['iframe_popup_delay']})\" onMouseout=\"offHtmlPopUp()\"";
	}
	
	$motothre_popup = " onMouseover=\"showHtmlPopUp('{$motothre_url}',event,{$_conf['iframe_popup_delay']})\" onMouseout=\"offHtmlPopUp()\"";
	if ($_conf['iframe_popup'] == 1) {
		$motothre_ht = "<a href=\"{$motothre_url}\"{$bbs_win_target}{$motothre_popup}>{$motothre_url}</a>";
	} elseif ($_conf['iframe_popup'] == 2) {
		$motothre_ht = "(<a href=\"{$motothre_url}\"{$bbs_win_target}{$motothre_popup}>p</a>)<a href=\"{$motothre_url}\"{$bbs_win_target}>{$motothre_url}</a>";
	} else {
		$motothre_ht = "<a href=\"{$motothre_url}\"{$bbs_win_target}>{$motothre_url}</a>";
	}
	
	echo $diedat_msg;
	echo "<p>";
	echo  $motothre_ht;
	echo "</p>";
	echo "<hr>";
	
	if(!$aThread->rescount){ //�������X���Ȃ���΃c�[���o�[�\��
		echo <<<EOP
<table width="100%" style="padding:0px 0px 10px 0px;">
	<tr>
		<td align="left">
			&nbsp;
		</td>
		<td align="right">
			{$toolbar_right_ht}
		</td>
	</tr>
</table>
EOP;
	}
}


if($aThread->rescount and (!$_GET['renzokupop']) ){
//���X�t�B���^===============================
	if($res_filter['field']=="name"){$selected_name=" selected";}
	elseif($res_filter['field']=="mail"){$selected_mail=" selected";}
	elseif($res_filter['field']=="id"){$selected_id=" selected";}
	elseif($res_filter['field']=="msg"){$selected_msg=" selected";}
	if($res_filter['match']=="off"){$selected_off=" selected";}

	echo <<<EOP
<form id="header" method="GET" action="{$_conf['read_php']}">
	<input type="hidden" name="bbs" value="{$aThread->bbs}">
	<input type="hidden" name="key" value="{$aThread->key}">
	<input type="hidden" name="host" value="{$aThread->host}">
	<input type="hidden" name="ls" value="all">
	<select id="field" name="field">
		<option value="name"{$selected_name}>���O
		<option value="mail"{$selected_mail}>���[��
		<option value="id"{$selected_id}>ID
		<option value="msg"{$selected_msg}>���b�Z�[�W
	</select>
	��
	<input id="word" name="word" value="{$word}" size="24">
	��
	<select id="match" name="match">
		<option value="on">�܂�
		<option value="off"{$selected_off}>�܂܂Ȃ�
	</select>
	���X��
	<input type="submit" name="submit" value="�t�B���^�\��">

</form>\n
EOP;
}

if( ($aThread->rescount or $_GET['one'] && !$aThread->diedat) and (!$_GET['renzokupop'])){

	if($_GET['one']){$id_header=" id=\"header\"";}
	echo <<<EOP
<table{$id_header} width="100%" style="padding:0px 0px 10px 0px;">
	<tr>
		<td align="left">
			<a href="{$_conf['read_php']}?host={$aThread->host}{$bbs_q}{$key_q}&amp;ls=all">{$all_st}</a>
			{$read_navi_range}
			{$read_navi_previous_header}
			<a href="{$_conf['read_php']}?host={$aThread->host}{$bbs_q}{$key_q}&amp;ls=l{$latest_show_res_num}">{$latest_st}{$latest_show_res_num}</a>
		</td>
		<td align="right">
			{$toolbar_right_ht}
		</td>
		<td align="right">
			<a href="#footer">��</a>
		</td>
	</tr>
</table>\n
EOP;

}


//if(!$_GET['renzokupop'] ){
	echo "<h3 class=\"thread_title\">{$aThread->ttitle}</h3>\n";
//}


 ?>