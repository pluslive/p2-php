<?php
/*
	+live - �n�C���C�g���[�h�̕ϊ� ../ShowThreadPc.php ���ǂݍ��܂��
*/

$live_highlight_style = "background-color: {$STYLE['live_highlight']}; font-weight: {$STYLE['live_highlight_word_weight']}; border-bottom: {$STYLE['live_highlight_word_border']};";
$live_highlight_chain_style = "background-color: {$STYLE['live_highlight_chain']}; font-weight: {$STYLE['live_highlight_word_weight']}; border-bottom: {$STYLE['live_highlight_word_border']};";

// �A���n�C���C�g�ϊ�
if ($ng_type & self::HIGHLIGHT_CHAIN) {
	$highlight_chain_nums = implode('|', array_intersect($highlight_chain_nums, $this->_highlight_nums));
	$highlight_chain_nums = "(" . $highlight_chain_nums . ")(?!\d)(?![^<]*>)"; // �������ꕔ����Ă��܂��A���J�[��HTML�^�O���Ƀ}�b�`�����Ȃ�
	$msg = preg_replace("(((?:&gt;|��|-)+)($highlight_chain_nums))", "<span style=\"{$live_highlight_chain_style}\">$1$2</span>", $msg);
}

// �n�C���C�g���b�Z�[�W�ϊ�
if ($ng_type & self::HIGHLIGHT_MSG) {
	$highlight_msgs = quotemeta(implode('|', $highlight_msgs));
	$highlight_msgs = "(" . $highlight_msgs . ")(?![^<]*>)"; // HTML�^�O���Ƀ}�b�`�����Ȃ�
	if (preg_match("(<(regex:i|i)>)", $highlight_msgs)) {
		$highlight_msgs = preg_replace("(<(regex|regex:i|i)>)", "", $highlight_msgs);
		$msg = mb_eregi_replace("($highlight_msgs)", "<span style=\"{$live_highlight_style}\">\\1</span>", $msg);
	} else {
		$highlight_msgs = preg_replace("(<(regex|regex:i|i)>)", "", $highlight_msgs);
		$msg = mb_ereg_replace("($highlight_msgs)", "<span style=\"{$live_highlight_style}\">\\1</span>", $msg);
	}
}

// �n�C���C�g�l�[���ϊ�
if ($ng_type & self::HIGHLIGHT_NAME) {
	$name = preg_replace("(<b>|</b>)", "", $name);
	$name = "</b><span style=\"{$live_highlight_style}\">$name</span><b>";
}

// �n�C���C�g���[���ϊ�
if ($ng_type & self::HIGHLIGHT_MAIL) {
	$mail = "<span style=\"{$live_highlight_style}\">$mail</span>";
}

// �n�C���C�gID�ϊ�
if ($ng_type & self::HIGHLIGHT_ID) {
	$date_id = preg_replace("((ID:))", "<span style=\"{$live_highlight_style}\">$1", $date_id ."</span>");
}

?>