<?php
/*
	+live - �����p�X���b�h�\�� ../ShowThreadPc.php ���ǂݍ��܂��
*/

// �I�[�g�����[�h�̔ŐV�����X�擪�ɖڈ󃉃C����}��
$live_newline = "<table id=\"{$res_id}\" class=\"res\" cellspacing=\"2\" cellpadding=\"0\" style=\"border-top: {$STYLE['live_b_n']};\" width=\"100%\"><tr class=\"res-header\">";
$live_oldline = "<table id=\"{$res_id}\" class=\"res\" cellspacing=\"2\" cellpadding=\"0\" style=\"border-top: {$STYLE['live_b_l']};\" width=\"100%\"><tr class=\"res-header\">";
// 
$live_td = "<td class=\"live_res\" style=\"color:{$STYLE['read_color']}; font-size:{$STYLE['live_font-size']}; word-wrap: break-word;\" width=\"250px\" valign=\"top\">";
// �V�����X�̔ԍ��F
$live_newnum = "<span class=\"spmSW\"{$spmeh}><b style=\"color:{$STYLE['read_newres_color']};\">{$i}</b></span>";
$live_oldnum = "<span class=\"spmSW\"{$spmeh}>{$i}</span>";

if ($this->thread->onthefly) {
	$GLOBALS['newres_to_show_flag'] = true;
	// �ԍ� (�I���U�t���C)
	$tores .= "{$live_oldline}{$live_td}<span class=\"ontheflyresorder spmSW\"{$spmeh}>{$i}</span>";
} elseif ($i == 1) {
	// �ԍ� (1)
	if ($this->thread->readnum > 1) {
		$tores .= "{$live_oldline}{$live_td}{$live_oldnum}";
	} else {
		$tores .= "{$live_oldline}{$live_td}{$live_newnum}";
	}
} elseif ($i == $this->thread->readnum +1) {
	$GLOBALS['newres_to_show_flag'] = true;
	// �ԍ� (�V�����X�� �擪)
	if ($_GET['live']) {
		$tores .= "{$live_newline}{$live_td}{$live_newnum}";
	} else {
		$tores .= "{$live_oldline}{$live_td}{$live_newnum}";
	}
} elseif ($i > $this->thread->readnum) {
	// �ԍ� (�V�����X�� �㑱)
	$tores .= "{$live_oldline}{$live_td}{$live_newnum}";
} elseif ($_conf['expack.spm.enabled']) {
	// �ԍ� (SPM)
	$tores .= "{$live_oldline}{$live_td}{$live_oldnum}";
} else {
	// �ԍ�
	$tores .= "{$live_oldline}{$live_td}{$i}";
}

// ���O
$tores .= preg_replace('{<b>[ ]*</b>}i', '', "&nbsp;<span class=\"name\"><b>{$name}</b></span> : ");

// ID
$tores .= "{$date_id}";

if ($this->am_side_of_id) {
	$tores .= ' ' . $this->activeMona->getMona($res_id);
}

// ���[��
$tores .= "&nbsp;{$mail}";

// �탌�X���X�g(�c�`��)
if ($_conf['backlink_list'] == 1 || $_conf['backlink_list'] > 2) {
    $tores .= $this->_quotebackListHtml($i, 1);
}

$tores .= "</td>";

// �d�� & ���X�{�^��
$stall_05 = "<td width=\"5px\"  style=\"border-left: {$STYLE['live_b_s']};\">&nbsp;</td>";
$stall_30 = "<td width=\"32px\" style=\"border-left: {$STYLE['live_b_s']};\">&nbsp;{$res_button}</td>";
if ($_GET['live']) {
	$tores .= "$stall_30";
} else {
	$tores .= "$stall_05";
}

// ���e
$tores .= "<td class=\"live_res\" id=\"{$msg_id}\" class=\"{$msg_class}\"{$res_dblclc} width=\"\" style=\"color:{$STYLE['read_color']}; font-size: {$STYLE['read_fontsize']}; word-wrap: break-word;\">{$msg}�@";

if ($_conf['backlink_block'] > 0) {
    // ��Q�ƃu���b�N�\���p��onclick��ݒ�
    $tores .= "<div onclick=\"toggleResBlk(event, this, " . $_conf['backlink_block_readmark'] . ")\">\n";
} else {
    $tores .= "<div>\n";
}

// �탌�X�W�J�p�u���b�N
if ($_conf['backlink_block'] > 0) {
    $backlinks = $this->_getBacklinkComment($i);
    if (strlen($backlinks)) {
        $tores .= '<div class="resblock"><img src="img/btn_plus.gif" width="15" height="15" align="left"></div>';
        $tores .= $backlinks;
    }
}
// �탌�X���X�g(���`��)
if ($_conf['backlink_list'] == 2 || $_conf['backlink_list'] > 2) {
    $tores .= $this->_quotebackListHtml($i, 2, false);
}

$tores .= "</div>";
$tores .= "</td>";

// �e�[�u���I��
$tores .= "</tr></table>\n";

// ���X�|�b�v�A�b�v�p���p
//$tores .= $rpop;

?>