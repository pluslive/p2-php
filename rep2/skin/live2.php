<?php
/**
 * p2 - �f�U�C���p �ݒ�t�@�C��
 *
 *	�R�����g�`����() ���̓f�t�H���g�l
 *	�ݒ�� style/*_css.inc �ƘA��
 */

$STYLE['a_underline_none'] = "1"; // �����N�ɉ������i����:0, ���Ȃ�:1, �X���^�C�g���ꗗ�������Ȃ�:2�j

// {{{ �t�H���g

if (strpos($_SERVER['HTTP_USER_AGENT'], 'Mac') !== false) {
	/* Mac�p�t�H���g�t�@�~���[*/
	if (P2Util::isBrowserSafariGroup()){ /* Safari�n�Ȃ� */
		$STYLE['fontfamily'] = array("Hiragino Maru Gothic ProN", "Arial");
		$STYLE['fontfamily_bold'] = array("Hiragino Kaku Gothic StdN", "Arial Black");
//		$STYLE['fontweight_bold'] = "bold";
	} else {
		$STYLE['fontfamily'] = array("Hiragino Maru Gothic ProN", "�q���M�m�ۃS ProN W4", "Arial"); // ��{�̃t�H���g
		$STYLE['fontfamily_bold'] = array("Hiragino Kaku Gothic StdN", "�q���M�m�p�S StdN W8", "Arial Black"); // ��{�{�[���h�p�t�H���g�i���ʂɑ����ɂ������ꍇ�͎w�肵�Ȃ�("")�j
	}
	/* Mac�p�t�H���g�T�C�Y */
	$STYLE['fontsize'] = "12px"; // ("12px") ��{�t�H���g�̑傫��
	$STYLE['menu_fontsize'] = "10px"; // ("10px") ���j���[�̃t�H���g�̑傫��
	$STYLE['sb_fontsize'] = "10px"; // ("10px") �X���ꗗ�̃t�H���g�̑傫��
	$STYLE['read_fontsize'] = "12px"; // ("12px") �X���b�h���e�\���̃t�H���g�̑傫��
	$STYLE['respop_fontsize'] = "12px"; // ("12px") ���p���X�|�b�v�A�b�v�\���̃t�H���g�̑傫��
	$STYLE['infowin_fontsize'] = "12px"; // ("12px") ���E�B���h�E�̃t�H���g�̑傫��
	$STYLE['form_fontsize'] = "10px"; // ("10px") input, option, select �̃t�H���g�̑傫���iCamino�������j
}else{
	/* Mac�ȊO�̃t�H���g�t�@�~���[*/
	$STYLE['fontfamily'] = array("���C���I", "Meiryo", "�l�r �o�S�V�b�N", "MS P Gothic"); // ("�l�r �o�S�V�b�N") ��{�̃t�H���g
	/* Mac�ȊO�̃t�H���g�T�C�Y */
	$STYLE['fontsize'] = "12px"; // ("12px") ��{�t�H���g�̑傫��
	$STYLE['menu_fontsize'] = "10px"; // ("10px") ���j���[�̃t�H���g�̑傫��
	$STYLE['sb_fontsize'] = "10px"; // ("10px") �X���ꗗ�̃t�H���g�̑傫��
	$STYLE['read_fontsize'] = "12px"; // ("12px") �X���b�h���e�\���̃t�H���g�̑傫��
	$STYLE['respop_fontsize'] = "12px"; // ("12px") ���p���X�|�b�v�A�b�v�\���̃t�H���g�̑傫��
	$STYLE['infowin_fontsize'] = "12px"; // ("12px") ���E�B���h�E�̃t�H���g�̑傫��
	$STYLE['form_fontsize'] = "10px"; // ("10px") input, option, select �̃t�H���g�̑傫��
}

// }}}
/**
 * �F�ʂ̐ݒ�
 *
 * ���w��("")�̓u���E�U�̃f�t�H���g�F�A�܂��͊�{�w��ƂȂ�܂��B
 * �D��x�́A�ʃy�[�W�w�� �� ��{�w�� �� �g�p�u���E�U�̃f�t�H���g�w�� �ł��B
 */
// {{{ ��{(style)

$STYLE['bgcolor'] = "#f6f6f6"; // ("#fff") ��{ �w�i�F
$STYLE['background'] = ""; // ("./skin/live/bg.gif") ��{ �w�i�摜
$STYLE['textcolor'] = "#333"; // ("#333") ��{ �e�L�X�g�F
$STYLE['acolor'] = "#900"; // ("#900") ��{ �����N�F
$STYLE['acolor_v'] = "#900"; // ("#900") ��{ �K��ς݃����N�F�B
$STYLE['acolor_h'] = "#999"; // ("#999") ��{ �}�E�X�I�[�o�[���̃����N�F

$STYLE['fav_color'] = "#900"; // ("#900") ���C�Ƀ}�[�N�̐F

// }}}
// {{{ ���j���[(menu)

$STYLE['menu_bgcolor'] = "#f6f6f6"; //("#000") ���j���[�̔w�i�F
$STYLE['menu_color'] = "#333"; //("#fff") menu �e�L�X�g�F
//$STYLE['menu_background'] = ""; //("") ���j���[�̔w�i�摜
$STYLE['menu_cate_color'] = "#666"; // ("#fff") ���j���[�J�e�S���[�̐F

$STYLE['menu_acolor_h'] = "#900"; // ("#900") ���j���[ �}�E�X�I�[�o�[���̃����N�F

$STYLE['menu_ita_color'] = "#333"; // ("#fff") ���j���[ �� �����N�F
$STYLE['menu_ita_color_v'] = "#333"; // ("#fff") ���j���[ �� �K��ς݃����N�F
$STYLE['menu_ita_color_h'] = "#900"; // ("#900") ���j���[ �� �}�E�X�I�[�o�[���̃����N�F

$STYLE['menu_newthre_color'] = "#900";	// ("#900") menu �V�K�X���b�h���̐F
$STYLE['menu_newres_color'] = "#900";	// ("#900") menu �V�����X���̐F

// }}}
// {{{ �X���ꗗ(subject)

$STYLE['sb_bgcolor'] = "#f6f6f6"; // ("#fff") subject �w�i�F
//$STYLE['sb_background'] = ""; // ("") subject �w�i�摜
$STYLE['sb_color'] = "#333";  // ("#333") subject �e�L�X�g�F

$STYLE['sb_acolor'] = "#333"; // ("#333") subject �����N�F
$STYLE['sb_acolor_v'] = "#333"; // ("#333") subject �K��ς݃����N�F
$STYLE['sb_acolor_h'] = "#900"; // ("#900") subject �}�E�X�I�[�o�[���̃����N�F

$STYLE['sb_th_bgcolor'] = "#f6f6f6"; // ("#fff") subject �e�[�u���w�b�_�w�i�F
$STYLE['sb_th_background'] = "./skin/live/bg.gif"; // ("./skin/live/bg.gif") subject �e�[�u���w�b�_�w�i�摜
$STYLE['sb_tbgcolor'] = "#f6f6f6"; // ("#fff") subject �e�[�u�����w�i�F0
$STYLE['sb_tbgcolor1'] = "#f6f6f6"; // ("#fff") subject �e�[�u�����w�i�F1
//$STYLE['sb_tbackground'] = ""; // ("") subject �e�[�u�����w�i�摜0
//$STYLE['sb_tbackground1'] = ""; // ("") subject �e�[�u�����w�i�摜1

$STYLE['sb_ttcolor'] = "#333"; // ("#333") subject �e�[�u���� �e�L�X�g�F
$STYLE['sb_tacolor'] = "#333"; // ("#333") subject �e�[�u���� �����N�F
$STYLE['sb_tacolor_h'] = "#900"; // ("#900")subject �e�[�u���� �}�E�X�I�[�o�[���̃����N�F

$STYLE['sb_order_color'] = "#333"; // ("#333") �X���ꗗ�̔ԍ� �����N�F

$STYLE['thre_title_color'] = "#333"; // ("#333") subject �X���^�C�g�� �����N�F
$STYLE['thre_title_color_v'] = "#333"; // ("#333") subject �X���^�C�g�� �K��ς݃����N�F
$STYLE['thre_title_color_h'] = "#900"; // ("#900") subject �X���^�C�g�� �}�E�X�I�[�o�[���̃����N�F

$STYLE['sb_tool_bgcolor'] = "#f6f6f6"; // ("#000") subject �c�[���o�[�̔w�i�F
//$STYLE['sb_tool_background'] = ""; // ("") subject �c�[���o�[�̔w�i�摜
$STYLE['sb_tool_border_color'] = "#ccc"; // ("#900") subject �c�[���o�[�̃{�[�_�[�F
$STYLE['sb_tool_color'] = "#666"; // ("#fff") subject �c�[���o�[�� �����F
$STYLE['sb_tool_acolor'] = "#333"; // ("#fff") subject �c�[���o�[�� �����N�F
$STYLE['sb_tool_acolor_v'] = "#333"; // ("#fff") subject �c�[���o�[�� �K��ς݃����N�F
$STYLE['sb_tool_acolor_h'] = "#900"; // ("#900") subject �c�[���o�[�� �}�E�X�I�[�o�[���̃����N�F
$STYLE['sb_tool_sepa_color'] = "#333"; // ("#fff") subject �c�[���o�[�� �Z�p���[�^�����F

$STYLE['sb_now_sort_color'] = "#900";	// ("#900") subject ���݂̃\�[�g�F

$STYLE['sb_thre_title_new_color'] = "#900";	// ("#900") subject �V�K�X���^�C�g���̐F

$STYLE['sb_tool_newres_color'] = "#900"; // ("#900") subject �c�[���o�[�� �V�K���X���̐F
$STYLE['sb_newres_color'] = "#900"; // ("#900") subject �V�����X���̐F

// }}}
// {{{ �X�����e(read)

$STYLE['read_bgcolor'] = "#f6f6f6"; // ("#fff") �X���b�h�\���̔w�i�F
//$STYLE['read_background'] = ""; // ("") �X���b�h�\���̔w�i�摜
$STYLE['read_color'] = "#333"; // ("#333") �X���b�h�\���̃e�L�X�g�F

$STYLE['read_acolor'] = "#900"; // ("#900") �X���b�h�\�� �����N�F
$STYLE['read_acolor_v'] = "#900"; // ("#900") �X���b�h�\�� �K��ς݃����N�F
$STYLE['read_acolor_h'] = "#999"; // ("#999") �X���b�h�\�� �}�E�X�I�[�o�[���̃����N�F

$STYLE['read_newres_color'] = "#c60"; // ("#c60")  �V�����X�Ԃ̐F

$STYLE['read_thread_title_color'] = "#900"; // ("#900") �X���b�h�^�C�g���F
$STYLE['read_name_color'] = "#999"; // ("#777") ���e�҂̖��O�̐F
$STYLE['read_mail_color'] = "#06c"; // ("#06c") ���e�҂�mail�̐F ex)
$STYLE['read_mail_sage_color'] = "#c60"; // ("#c60") sage�̎��̓��e�҂�mail�̐F ex)
$STYLE['read_ngword'] = "#ccc"; // ("#ccc") NG���[�h�̐F

// }}}
// {{{ �������[�h

//$SYTLE['live_b_width'] = "1px"; // ("1px") �������[�h�A�{�[�_�[��
//$SYTLE['live_b_color'] = "#ccc"; // ("#ccc") �������[�h�A�{�[�_�[�F
//$SYTLE['live_b_style'] = "dotted"; // ("dotted") �������[�h�A�{�[�_�[�`��

// }}}
// {{{ ���X�������݃t�H�[��

$STYLE['post_pop_size'] = "610,350"; // ("610,350") ���X�������݃|�b�v�A�b�v�E�B���h�E�̑傫���i��,�c�j
$STYLE['post_msg_rows'] = 10; // (10) ���X�������݃t�H�[���A���b�Z�[�W�t�B�[���h�̍s��
$STYLE['post_msg_cols'] = 70; // (70) ���X�������݃t�H�[���A���b�Z�[�W�t�B�[���h�̌���

// }}}
// {{{ ���X�|�b�v�A�b�v

$STYLE['respop_color'] = "#333"; // ("#333") ���X�|�b�v�A�b�v�̃e�L�X�g�F
$STYLE['respop_bgcolor'] = "#fff"; // ("#fff") ���X�|�b�v�A�b�v�̔w�i�F
//$STYLE['respop_background'] = ""; // ("") ���X�|�b�v�A�b�v�̔w�i�摜
$STYLE['respop_b_width'] = "1px"; // ("4px") ���X�|�b�v�A�b�v�̃{�[�_�[��
$STYLE['respop_b_color'] = "#ccc"; // ("#999") ���X�|�b�v�A�b�v�̃{�[�_�[�F
$STYLE['respop_b_style'] = "dotted"; // ("double") ���X�|�b�v�A�b�v�̃{�[�_�[�`��

$STYLE['info_pop_size'] = "600,380"; // ("600,380") ���|�b�v�A�b�v�E�B���h�E�̑傫���i��,�c�j

$STYLE['conf_btn_bgcolor'] = '#efefef';

// }}}
// {{{ style/*_css.inc �Œ�`����Ă��Ȃ��ݒ�

$MYSTYLE['read']['body']['margin'] = "0px"; // ("0px") �X���b�h�\�� �}�[�W��
$MYSTYLE['read']['body']['padding'] = "10px"; // ("10px") �X���b�h�\�� �p�f�B���O
$MYSTYLE['read']['form#header']['margin'] = "-10px -10px 5px -10px"; // ("-10px -10px 5px -10px") �X���b�h�\���w�b�_�̃}�[�W��
$MYSTYLE['read']['form#header']['padding'] = "3px 10px 3px 10px"; // ("3px 10px 3px 10px") �X���b�h�\���w�b�_�̃p�f�B���O
$MYSTYLE['read']['form#header']['line-height'] = "100%"; // ("100%") �X���b�h�\���w�b�_�̍s��
$MYSTYLE['read']['form#header']['vertical-align'] = "middle"; // ("middle") �X���b�h�\���w�b�_�̕����ʒu
$MYSTYLE['read']['form#header']['background'] = "#f6f6f6"; // ("#000") �X���b�h�\���w�b�_�̔w�i
$MYSTYLE['read']['form#header']['border'] = "1px #ccc solid"; // ("1px #900 solid") �X���b�h�\���w�b�_�̉��{�[�_�[
$MYSTYLE['read']['form#header']['color'] = "#333"; // ("#fff") �X���b�h�\���w�b�_�̃e�L�X�g�F
$MYSTYLE['read']['div#kakiko']['border-top'] = "1px #999 solid"; // ("1px #CACACA solid") �X���b�h�\�� �������X�t�H�[���� ��{�[�_�[�F
$MYSTYLE['read']['div#kakiko']['margin'] = "5px -10px -10px -10px"; // ("5px -10px -5px -10px") �X���b�h�\�� �������X�t�H�[���� �}�[�W��
$MYSTYLE['read']['div#kakiko']['padding'] = "5px 10px"; // ("5px 10px") �X���b�h�\�� �������X�t�H�[���� �p�f�B���O
$MYSTYLE['read']['div#kakiko']['background'] = "#f6f6f6"; // ("#fff url(./skin/live/bg.gif)") �X���b�h�\�� �������X�t�H�[���� �w�i

$MYSTYLE['prvw']['#dpreview']['background'] = "#fff";
$MYSTYLE['post']['#original_msg']['background'] = "#fff";
$MYSTYLE['post']['body']['background'] = "#f6f6f6";

//$MYSTYLE['subject']['table.toolbar']['height'] = "30px"; // ("30px") subject �c�[���o�[�̍���
//$MYSTYLE['subject']['table.toolbar']['background-position'] = "top"; // ("top") subject �c�[���o�[�̔w�i�摜�ʒu
//$MYSTYLE['subject']['table.toolbar']['background-repeat'] = "repeat-x"; // ("repeat-x") subject �c�[���o�[�̔w�i�摜�J�Ԃ�
//$MYSTYLE['subject']['table.toolbar']['border-left'] = "none"; // ("none") subject �c�[���o�[�̍��{�[�_�[
//$MYSTYLE['subject']['table.toolbar']['border-right'] = "none"; // ("none") subject �c�[���o�[�̉E�{�[�_�[
//$MYSTYLE['subject']['table.toolbar *']['padding'] = "0"; // ("0") subject �c�[���o�[�̃p�f�B���O
$MYSTYLE['subject']['table.toolbar *']['line-height'] = "100%"; // ("100%") subject �c�[���o�[�̍s��
//$MYSTYLE['subject']['table.toolbar td']['padding'] = "1px"; // ("1px") subject �c�[���o�[�����̃p�f�B���O
$MYSTYLE['subject']['tr.tableheader td']['color'] = "#333"; // ("#333") subject �w�b�_ �e�L�X�g�F
$MYSTYLE['subject']['tr.tableheader a']['color'] = "#333"; // ("#333") subject �w�b�_ �����N�F
$MYSTYLE['subject']['tr.tableheader a:hover']['color'] = "#900"; // ("#900") subject �w�b�_ �}�E�X�I�[�o�[���̃����N�F
//$MYSTYLE['subject']['tr#pager td']['color'] = "#F9F9F9"; // ("#F9F9F9") subject 
//$MYSTYLE['subject']['tr#pager a']['color'] = "#F9F9F9"; // ("#F9F9F9") subject 
//$MYSTYLE['subject']['tr#pager a:hover']['color'] = "#E3E3E3"; // ("#E3E3E3") subject 

$MYSTYLE['iv2']['div#toolbar']['background'] = "#f6f6f6"; // ("#000") �C���[�W�r���[���[�c�[���o�[�̔w�i
$MYSTYLE['iv2']['div#toolbar td']['color'] = "#333"; // ("#fff") �C���[�W�r���[���[�c�[���o�[�̃e�L�X�g�F

// �X���^�C�̉e�i���Ԃ�Safari�Ƃ�Opera���炢�����Ή����ĂȂ��E�E�EKonqueror�ł�OK�H�j
$MYSTYLE['read']['.thread_title']['text-shadow'] = "5px 5px 20px #666"; // ("5px 5px 20px #666")

//�V�����X
$MYSTYLE['subject']['sb_td']['border-top'] = "1px dotted #ccc"; // ("1px dotted #999") subject �e�[�u���̃{�[�_�[0
$MYSTYLE['subject']['sb_td1']['border-top'] = "1px dotted #ccc"; // ("1px dotted #999") subject �e�[�u���̃{�[�_�[1
$MYSTYLE['subject']['.itatitle']['font-size'] = "10px"; // ("10px") subject ��

//�t�B���^�����O����
$MYSTYLE['base']['#filterstart, .filtering']['background-color'] = "transparent"; // ("transparent") �t�B���^�����O�w�i�F
$MYSTYLE['base']['#filterstart, .filtering']['font-family'] = $STYLE['fontfamily']; // ($STYLE['fontfamily']) �t�B���^�����O �t�H���g�t�@�~���[
$MYSTYLE['base']['#filterstart, .filtering']['font-weight'] = 'normal'; // ('normal') �t�B���^�����O �t�H���g�̑���
//$MYSTYLE['base']['#filterstart, .filtering']['border-top'] = ""; // ("") �t�B���^�����O �{�[�_�[��
//$MYSTYLE['base']['#filterstart, .filtering']['border-right'] = ""; // ("") �t�B���^�����O �{�[�_�[�E
$MYSTYLE['base']['#filterstart, .filtering']['border-bottom'] = "3px #900 double"; // ("3px #900 double") �t�B���^�����O �{�[�_�[��
//$MYSTYLE['base']['#filterstart, .filtering']['border-left'] = ""; // ("") �t�B���^�����O �{�[�_�[��

//HTML�|�b�v�A�b�v
$MYSTYLE['read']['#iframespace']['border'] = "1px #999 solid"; // ("4px #999 double") HTML�|�b�v�A�b�v�̃{�[�_�[
$MYSTYLE['read']['#closebox']['border'] = "1px #999 solid"; // ("4px #999 double") HTML�|�b�v�A�b�v �N���[�Y�{�b�N�X�̃{�[�_�[
$MYSTYLE['read']['#closebox']['color'] = "#fff"; // ("#fff") HTML�|�b�v�A�b�v �N���[�Y�{�b�N�X�̃e�L�X�g�F
$MYSTYLE['read']['#closebox']['background-color'] = "#900"; // ("#900") HTML�|�b�v�A�b�v �N���[�Y�{�b�N�X�̔w�i�F
$MYSTYLE['subject']['#iframespace'] = &$MYSTYLE['read']['#iframespace']; // (&$MYSTYLE['read']['#iframespace']) subject �t���[��
$MYSTYLE['subject']['#closebox'] = &$MYSTYLE['read']['#closebox']; // (&$MYSTYLE['read']['#closebox']) subject �N���[�Y�{�b�N�X

//���E�C���h�E
$MYSTYLE['info']['td.tdleft']['color'] = "#333"; // ("#333") ���E�C���h�E�̃e�L�X�g�F
$MYSTYLE['kanban']['td.tdleft']['color'] = "#333"; // ("#333") ���E�C���h�E�̃e�L�X�g�F

// }}}
// {{{ +live �������[�h

$STYLE['live_b_l'] = "1px #ccc dotted"; // ("1px #999 dotted") +live ���X�Ԃ̎d�ؐ�
//$STYLE['live_b_s'] = ""; // ("0px #999 dotted; background:url(./skin/live/bg.gif)") +live �ԍ� �ڗ� ���O ���t ID �\�����ƃ��X�\�����̎d�ؐ�
$STYLE['live_b_n'] = "2px #900 dotted"; // ("2px #900 dotted") +live �����\��&�I�[�g�����[�h���̊��ǁ`�V���̎d�ؐ�
$STYLE['live_highlight'] = "#cff"; // ("#cff") +live �n�C���C�g���[�h�\�����̔w�i�F
$STYLE['live_highlight_chain'] = "#ffc"; // ("#ffc") +live �A���n�C���C�g�\�����̔w�i�F
$STYLE['live_highlight_word_weight'] = "bold"; // ("bold") +live �A���n�C���C�g�\�����̃t�H���g�̑���
$STYLE['live_highlight_word_border'] = "3px #900 double"; // ("3px #900 double") +live �A���n�C���C�g�\�����̃A���_�[���C��
$STYLE['live_font-size'] = "10px"; // ("10px") +live �ԍ� �ڗ� ���O ���t ID ���̃t�H���g�T�C�Y
$STYLE['live2_color'] = "#eee"; // ("#eee") +live Type-B�� �ԍ� �ڗ� ���O ���t ID �\�����̔w�i�F

$MYSTYLE['read']['a']['font-size'] = "10px"; // ("10px") �X���b�h�\�� �����N�̃t�H���g�T�C�Y
$MYSTYLE['read']['.thread_title']['font-size'] = "12px"; // ("12px") �X���b�h�\�� �X���^�C�̃t�H���g�T�C�Y
$MYSTYLE['editpref']['fieldset']['background'] = "#fff"; // �ݒ�Ǘ��� fieldset �^�O���w�i�F

// }}}

?>