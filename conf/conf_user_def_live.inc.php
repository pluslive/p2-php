<?php
/*
	+live - ���[�U�ݒ� �f�t�H���g ���̃t�@�C���̓f�t�H���g�l�̐ݒ�Ȃ̂ŁA���ɕύX����K�v�͂���܂���
*/

// {{{ ���\���ݒ�

// ���X�\���̎��
$conf_user_def['live.view_type'] = "1"; // ("1")
$conf_user_sel['live.view_type'] = array('0' => '��Ƀf�t�H���g�\��', '1' => '�������̂ݎ����p�\��', '2' => '��Ɏ����p�\��');

// ID������ O (�g��) P (����p2) Q (�t���u���E�U) I (iPhone) �𑾎���
$conf_user_def['live.id_b'] = 0; // (0)
$conf_user_rad['live.id_b'] = array('1' => '����', '0' => '���Ȃ�');

// �A���n�C���C�g (�\���͈͂̃��X�݂̂ɘA��)
$conf_user_def['live.highlight_chain'] = 0; // (0)
$conf_user_rad['live.highlight_chain'] = array('1' => '����', '0' => '���Ȃ�');

// YouTube�v���r���[�\���̃T�C�Y
$conf_user_def['live.youtube_winsize'] = 0; // (0)
$conf_user_sel['live.youtube_winsize'] = array('0' => '�m�[�}���T�C�Y', '1' => '�n�[�t�T�C�Y');

// }}}
// {{{ ���������ݒ�

// �\�����郌�X�� (100�ȉ�����)
$conf_user_def['live.before_respointer'] = "50"; // ("50")

// ���������t���[���̍��� (px)
$conf_user_def['live.post_width'] = "85"; // ("85")

// �f�t�H���g�̖������̕\��
$conf_user_def['live.bbs_noname'] = 0; // (0)
$conf_user_rad['live.bbs_noname'] = array('1' => '����', '0' => '���Ȃ�');

// sage �� �� ��
$conf_user_def['live.mail_sage'] = 1; // (1)
$conf_user_rad['live.mail_sage'] = array('1' => '����', '0' => '���Ȃ�');

// �S�Ẳ��s�ƃX�y�[�X�̍폜
$conf_user_def['live.msg'] = 1; // (1)
$conf_user_rad['live.msg'] = array('1' => '����', '0' => '���Ȃ�');

// [����Ƀ��X] �̕��@
$conf_user_def['live.res_button'] = 0; // (0)
$conf_user_sel['live.res_button'] = array('0' => '[ re: ] �{�^��', '1' => '����', '2' => '���e���_�u���N���b�N');

// �����K���p�^�C�}�[
$conf_user_def['live.write_regulation'] = 0; // (0)
$conf_user_sel['live.write_regulation'] = array('0' => '�^�C�}�[����', '1' => '15�b', '2' => '20�b', '3' => '30�b', '4' => '40�b', '5' => '50�b', '6' => '1��');

// ImageCache2�̃T���l�C���쐬
$conf_user_def['live.ic2_onoff'] = "1"; // ("1")
$conf_user_rad['live.ic2_onoff'] = array('1' => 'on', '0' => 'off');

// }}}
// {{{ �������[�h/�X�N���[��

// �I�[�g�����[�h�̊Ԋu
$conf_user_def['live.reload_time'] = 2; // (2)
$conf_user_sel['live.reload_time'] = array('0' => '�����[�h����', '1' => '5�b', '2' => '10�b', '3' => '15�b', '4' => '20�b');

// �I�[�g�X�N���[���̊��炩�� (�ł����炩 1 �A�X�N���[������ 0)
$conf_user_def['live.scroll_move'] = 3; // (3)

// �I�[�g�X�N���[���̑��x (�ő� 1 �A�X�N���[�������̏ꍇ�͏�̊��炩���̒l�� 0 ��)
$conf_user_def['live.scroll_speed'] = 10; // (10)

// }}}
?>
