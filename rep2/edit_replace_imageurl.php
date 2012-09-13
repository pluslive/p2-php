<?php
/*
    p2 - �u���摜URL�ҏW�C���^�t�F�[�X
*/

require_once __DIR__ . '/../init.php';
require_once P2_LIB_DIR . '/wiki/ReplaceImageUrlCtl.php';
 
$_login->authorize(); // ���[�U�F��

$csrfid = P2Util::getCsrfId(__FILE__);

if (!empty($_POST['submit_save']) || !empty($_POST['submit_default'])) {
    if (!isset($_POST['csrfid']) || $_POST['csrfid'] != $csrfid) {
        die('p2 error: �s���ȃ|�X�g�ł�');
    }
}


//=====================================================================
// �O����
//=====================================================================

$replaceImageUrlCtl = new ReplaceImageUrlCtl();

// [�ۑ�]�{�^��
if (!empty($_POST['submit_save'])) {
    if ($replaceImageUrlCtl->save($_POST['dat']) !== false) {
        $_info_msg_ht .= '<p>���ݒ���X�V�ۑ����܂���</p>';
    } else {
        $_info_msg_ht .= '<p>�~�ݒ���X�V�ۑ��ł��܂���ł���</p>';
    }
// [���X�g����ɂ���]�{�^��
} elseif (!empty($_POST['submit_default'])) {
    if ($this->clear()) {
        $_info_msg_ht .= '<p>�����X�g����ɂ��܂���</p>';
    } else {
        $_info_msg_ht .= '<p>�~���X�g����ɂł��܂���ł���</p>';
    }
}
// ���X�g�ǂݍ���
$formdata = $replaceImageUrlCtl->load();

//=====================================================================
// �v�����g�ݒ�
//=====================================================================
$ptitle_top = '�u���摜URL�ҏW';
$ptitle = strip_tags($ptitle_top);


//=====================================================================
// �v�����g
//=====================================================================
// �w�b�_HTML���v�����g
P2Util::header_nocache();
echo $_conf['doctype'];
echo <<<EOP
<html lang="ja">
<head>
    {$_conf['meta_charset_ht']}
    <meta name="ROBOTS" content="NOINDEX, NOFOLLOW">
    <meta http-equiv="Content-Style-Type" content="text/css">
    <meta http-equiv="Content-Script-Type" content="text/javascript">
    <title>{$ptitle}</title>\n
EOP;

if (!$_conf['ktai']) {
    echo <<<EOP
    <script type="text/javascript" src="js/basic.js?{$_conf['p2_version_id']}"></script>
    <link rel="stylesheet" href="css.php?css=style&amp;skin={$skin_en}" type="text/css">
    <link rel="stylesheet" href="css.php?css=edit_conf_user&amp;skin={$skin_en}" type="text/css">
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">\n
EOP;
}

$body_at = ($_conf['ktai']) ? $_conf['k_colors'] : ' onLoad="top.document.title=self.document.title;"';
echo <<<EOP
</head>
<body{$body_at}>\n
EOP;

// PC�p�\��
if (!$_conf['ktai']) {
    echo <<<EOP
<p id="pan_menu"><a href="editpref.php">�ݒ�Ǘ�</a> &gt; {$ptitle_top}</p>\n
EOP;
}

// PC�p�\��
if (!$_conf['ktai']) {
    $htm['form_submit'] = <<<EOP
        <tr class="group">
            <td colspan="8" align="center">
                <input type="submit" name="submit_save" value="�ύX��ۑ�����">
                <input type="submit" name="submit_default" value="���X�g����ɂ���" onClick="if (!window.confirm('���X�g����ɂ��Ă���낵���ł����H�i��蒼���͂ł��܂���j')) {return false;}"><br>
            </td>
        </tr>\n
EOP;
// �g�їp�\��
} else {
    $htm['form_submit'] = <<<EOP
<input type="submit" name="submit_save" value="�ύX��ۑ�����"><br>\n
EOP;
}

// ��񃁃b�Z�[�W�\��
if (!empty($_info_msg_ht)) {
    echo $_info_msg_ht;
    $_info_msg_ht = "";
}

$usage = <<<EOP
<ul>
<li>�~: �폜</li>
<li>Match: �}�b�`���� (��ɂ���Ɠo�^����)</li>
<li>Replace: �u��URL (��ɂ���Ɠo�^����)</li>
<li>Referer: Referer</li>
<li>Extract: �\�[�X����URL��W�J</li>
<li>Source: �\�[�X�}�b�`����</li>
<li>Recheck: Extract�����y�[�W��������`�F�b�N���邩</li>
<li>Ident: �摜URL�̈�Ӑ�����</li>
</ul>
EOP;
if ($_conf['ktai']) {
    $usage = mb_convert_kana($usage, 'k');
}
echo <<<EOP
{$usage}
<form method="POST" action="{$_SERVER['SCRIPT_NAME']}" target="_self" accept-charset="{$_conf['accept_charset']}">
    {$_conf['k_input_ht']}
    <input type="hidden" name="detect_hint" value="�����@����">
    <input type="hidden" name="csrfid" value="{$csrfid}">\n
EOP;

// PC�p�\���itable�j
if (!$_conf['ktai']) {
    echo <<<EOP
    <table class="edit_conf_user" cellspacing="0">
        <tr>
            <td align="center">�~</td>
            <td align="center">Match</td>
            <td align="center">Replace</td>
            <td align="center">Referer</td>
            <td align="center">Extract</td>
            <td align="center">source</td>
            <td align="center">Recheck</td>
            <td align="center">Ident</td>
        </tr>
        <tr class="group">
            <td colspan="8">�V�K�o�^</td>
        </tr>\n
EOP;
    $row_format = <<<EOP
        <tr>
            <td><input type="checkbox" name="dat[%1\$d][del]" value="1"></td>
            <td><input type="text" size="30" name="dat[%1\$d][match]" value="%2\$s"></td>
            <td><input type="text" size="30" name="dat[%1\$d][replace]" value="%3\$s"></td>
            <td><input type="text" size="30" name="dat[%1\$d][referer]" value="%4\$s"></td>
            <td><input type="text" size="30" name="dat[%1\$d][extract]" value="%5\$s"></td>
            <td><input type="text" size="30" name="dat[%1\$d][source]" value="%6\$s"></td>
            <td><input type="checkbox" name="dat[%1\$d][recheck]" value="1"%7\$s></td>
            <td><input type="text" size="30" name="dat[%1\$d][ident]" value="%8\$s"></td>
        </tr>\n
EOP;
// �g�їp�\��
} else {
    echo "�V�K�o�^<br>\n";
    $row_format = <<<EOP
Mat:<input type="text" name="dat[%1\$d][match]" value="%2\$s"><br>
Rep:<input type="text" name="dat[%1\$d][replace]" value="%3\$s"><br>
Ref:<input type="text" name="dat[%1\$d][referer]" value="%4\$s"><br>
<input type="text" name="dat[%1\$d][extract]" value="$EXTRACT"%5\$s>Exp<br>
Src:<input type="text" name="dat[%1\$d][source]" value="%6\$s"><br>
<input type="text" name="dat[%1\$d][del]" value="1">�~<br>
\n
EOP;
}

printf($row_format, -1, '', '', '', '', '', '', '');

echo $htm['form_submit'];

if (!empty($formdata)) {
    foreach ($formdata as $k => $v) {
        printf($row_format,
            $k,
            p2h($v['match']),
            p2h($v['replace']),
            p2h($v['referer']),
            p2h($v['extract']),
            p2h($v['source']),
            $v['recheck'] ? ' checked' : '',
            p2h($v['ident'])
        );
    }
    echo $htm['form_submit'];
}

// PC�Ȃ�
if (!$_conf['ktai']) {
    echo '</table>'."\n";
}

echo '</form>'."\n";


// �g�тȂ�
if ($_conf['ktai']) {
    echo <<<EOP
<hr>
<a {$_conf['accesskey']}="{$_conf['k_accesskey']['up']}" href="editpref.php{$_conf['k_at_q']}">{$_conf['k_accesskey']['up']}.�ݒ�ҏW</a>
{$_conf['k_to_index_ht']}
EOP;
}

echo '</body></html>';

// �����܂�
exit;
