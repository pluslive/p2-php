<?php
/*
	+live - �X���b�h�\���Ɋւ��鋤�ʏ��� ../ShowThreadPc.php ���ǂݍ��܂��
*/

// ���O
// �f�t�H���g�̖������̕\��
// showthreadpc.class.php�ɂ�

// ���t��ID
// ID�t�B���^
if ($_conf['flex_idpopup'] == 1 && $id && $this->thread->idcount[$id] > 1) {
	$date_id = str_replace($idstr, $this->idFilter($idstr, $id), $date_id);
}
// ID���� (O,P,Q,I ��) �̋���
if ($_conf['live.id_b']) {
	if (!preg_match("(ID:)", $date_id)) { // ID�����Ŗ����\���̂ݗL��̔� (�T��)
		$date_id = preg_replace('((\s([a-zA-Z])$)(?![^<]*>))', '<b class="mail">$1</b>', $date_id);
	} else {
		$date_id = preg_replace('((ID: ?)([0-9A-Za-z/.+]{10}|[0-9A-Za-z/.+]{8}|\\?\\?\\?)?([a-zA-Z])(?=[^0-9A-Za-z/.+]|$)(?![^<]*>))', '$1$2<b class="mail">&nbsp;$3</b>', $date_id);
	}
}
// ���t�̒Z�k
if (preg_match("([0-2][0-9]{3}/[0-1][0-9]/[0-3][0-9])", $date_id)) {
	if ($_GET['live']) { // �������͓��t��S�폜
		$date_id = preg_replace("([0-2][0-9]{3}/[0-1][0-9]/[0-3][0-9]\(..\))", "", $date_id);
	} else { // ��L�ȊO�͔N����2����
		if (preg_match("(class=\"ngword)", $date_id)) { // NGID�̎�
			$date_id = preg_replace("(([0-2][0-9])([0-9]{2}/[0-1][0-9]/[0-3][0-9]\(..\)))", "$2", $date_id);
		} else {
			$date_id = preg_replace("(([0-2][0-9])([0-9]{2}/[0-1][0-9]/[0-3][0-9]\(..\)))", "$2", $date_id);
		}
	}
}

// ���[��
if ($mail) {
	// �������̏ꍇ
	if ($_conf['live.mail_sage'] 
	&& ($_GET['live'])) {
		// sage �� �� ��
		if (preg_match("(^[\\s�@]*sage[\\s�@]*$)", $mail)) {
			if ($STYLE['read_mail_sage_color']) {
				$mail = "<span class=\"sage\" title=\"{$mail}\">��</span>";
			} elseif ($STYLE['read_mail_color']) {
				$mail = "<span class=\"mail\" title=\"{$mail}\">��</span>";
			} else {
				$mail = "<span title=\"{$mail}\">��</span>";
			}
		// sage �ȊO�� �� ��
		} else {
			$mail = "<span class=\"mail\" title=\"{$mail}\">��</span>";
		}
	// �m�[�}������
	} elseif (preg_match("(^[\\s�@]*sage[\\s�@]*$)", $mail)
	&& $STYLE['read_mail_sage_color']) {
		$mail = "<span class=\"sage\">{$mail}</span>";
	} elseif ($STYLE['read_mail_color']) {
		$mail = "<span class=\"mail\">{$mail}</span>";
	} else {
		$mail = "{$mail}";
	}
}

// [����Ƀ��X] �̕��@
if ($_GET['live']) {
	$ttitle_en_q ="&amp;ttitle_en=".UrlSafeBase64::encode($this->thread->ttitle);
	// ���e���_�u���N���b�N
	if ($_conf['live.res_button'] >= 1) {
		$res_dblclc = "ondblclick=\"window.parent.livepost.location.href='live_post_form.php?host={$this->thread->host}&amp;bbs={$this->thread->bbs}&amp;key={$this->thread->key}&amp;resnum={$i}{$ttitle_en_q}&amp;inyou=1'\" title=\"{$i} �Ƀ��X (double click)\"";
	}
	// ���X�{�^��
	if ($_conf['live.res_button'] <= 1) {
		if ($_conf['iframe_popup'] == 3) {
			$res_button = "<a href=\"live_post_form.php?host={$this->thread->host}&amp;bbs={$this->thread->bbs}&amp;key={$this->thread->key}&amp;resnum={$i}{$ttitle_en_q}&amp;inyou=1\" target=\"livepost\" title=\"{$i} �Ƀ��X\"><img src =\"./img/re.png\" alt=\"Re:\" width=\"22\" height=\"12\"></a>";
		} else {
			$res_button = "(<a href=\"live_post_form.php?host={$this->thread->host}&amp;bbs={$this->thread->bbs}&amp;key={$this->thread->key}&amp;resnum={$i}{$ttitle_en_q}&amp;inyou=1\" target=\"livepost\" title=\"{$i} �Ƀ��X\">re:</a>)";
		}
	} 
}

// ���e
// �\���؋l�ߏ���
if (mb_ereg("(([\\s�@]*[^\x01-\x7E][\\s�@]*<br>){2,})", $msg)) {
	$msg = mb_ereg_replace("([\\s�@]*<br>[\\s�@]*)", "", $msg);			// 3�s�ȏ��1�����u���̉��s��������s���폜
}
// �������̕\���؋l�ߏ���
if ($_conf['live.msg']
&& ($_GET['live'])) {
	$msg = mb_convert_kana($msg, 'rnas');								// �S�p�̉p���A�L���A�X�y�[�X�𔼊p��
	if (!preg_match ("(tp:/|ps:/|res/)", $msg)) {
		$msg = mb_ereg_replace("([\\s�@]*<br>[\\s�@]*)", " ", $msg);	// �S���s�����������p�X�y�[�X�ɁB���e�ɊO�������N��ʐ����ꗗ���܂ޏꍇ�͑ΏۊO
	}
	$msg = mb_ereg_replace("(\s{2,})", " ", $msg);						// �A���X�y�[�X��1��
}

// +live �X���b�h���e�\���ؑ�
if ($_GET['live']) {
	if ($_conf['live.view_type'] == 0 ) {
		include P2_LIB_DIR . '/live/default_view.inc.php';
	} else {
		include P2_LIB_DIR . '/live/live_view.inc.php';
	}
} else {
	if ($_conf['live.view_type'] > 1 ) {
		include P2_LIB_DIR . '/live/live_view.inc.php';
	} else {
		include P2_LIB_DIR . '/live/default_view.inc.php';
	}
}

?>