<?php
// p2 -  �T�u�W�F�N�g -  �c�[���o�[�\���i�g�сj
// for subject.php

$matome_accesskey_at="";
$matome_accesskey_navi="";

//�V���܂Ƃߓǂ� =========================================
if($upper_toolbar_done){
	$matome_accesskey_at=" {$accesskey}=\"{$k_accesskey['matome']}\"";
	$matome_accesskey_navi="{$k_accesskey['matome']}.";
}

if($aThreadList->spmode!="soko"){ //�q�ɂłȂ����
	if($shinchaku_attayo){
		$shinchaku_matome_ht =<<<EOP
		<a href="{$_conf['read_new_k_php']}?host={$aThreadList->host}&amp;bbs={$aThreadList->bbs}&amp;spmode={$aThreadList->spmode}{$norefresh_q}&amp;nt={$newtime}{$k_at_a}"{$matome_accesskey_at}>{$matome_accesskey_navi}�V�܂Ƃ�({$shinchaku_num})</a>
EOP;
	}else{
		$shinchaku_matome_ht =<<<EOP
		<a href="{$_conf['read_new_k_php']}?host={$aThreadList->host}&amp;bbs={$aThreadList->bbs}&amp;spmode={$aThreadList->spmode}&amp;nt={$newtime}{$k_at_a}"{$matome_accesskey_at}>{$matome_accesskey_navi}�V�܂Ƃ�</a>
EOP;
	}
}

//�v�����g==============================================
echo "<p>{$ptitle_ht} {$shinchaku_matome_ht}</p>\n";

//��ϐ�==============================================
$upper_toolbar_done=true;

?>