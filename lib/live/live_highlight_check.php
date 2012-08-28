<?php
/*
	+live - ハイライトワードのチェック ../ShowThread.php より読み込まれる
*/

// 連鎖ハイライト
if ($_conf['live.highlight_chain'] && $this->_has_ngaborns &&
	preg_match_all('/(?:&gt;|＞)([1-9][0-9\\-,]*)/', $msg, $highlight_matches)
) {
	$highlight_chain_nums = array_unique(preg_split('/[-,]+/',
										trim(implode(',', $highlight_matches[1]), '-,'),
										-1,
										PREG_SPLIT_NO_EMPTY));

	if (array_intersect($highlight_chain_nums, $this->_highlight_nums)) {
		$ngaborns_hits['highlight_chain']++;
		$type |= $this->_markHighlight($i, self::HIGHLIGHT_CHAIN, true);
	}
}

// ハイライトネームチェック
if ($this->ngAbornCheck('highlight_name', $name) !== false) {
	$ngaborns_hits['highlight_name']++;
	$type |= $this->_markHighlight($i, self::HIGHLIGHT_NAME, false);
}

// ハイライトメールチェック
if ($this->ngAbornCheck('highlight_mail', $mail) !== false) {
	$ngaborns_hits['highlight_mail']++;
	$type |= $this->_markHighlight($i, self::HIGHLIGHT_MAIL, false);
}

// ハイライトIDチェック
if ($this->ngAbornCheck('highlight_id', $date_id) !== false) {
	$ngaborns_hits['highlight_id']++;
	$type |= $this->_markHighlight($i, self::HIGHLIGHT_ID, false);
}

// ハイライトメッセージチェック
$a_highlight_msg = $this->ngAbornCheck('highlight_msg', $msg);
if ($a_highlight_msg !== false) {
	$ngaborns_hits['highlight_msg']++;
	$type |= $this->_markHighlight($i, self::HIGHLIGHT_MSG, true);
	$this->_highlight_msgs[] = $a_highlight_msg;
	$highlight_msgs = array_unique($this->_highlight_msgs);
}

?>