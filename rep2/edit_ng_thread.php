<?php
/*
    p2 - NG�X���b�h�C���^�t�F�[�X
*/

require_once __DIR__ . '/../init.php';
require_once P2_LIB_DIR . '/wiki/NgThreadCtl.php';

$_login->authorize(); // ���[�U�F��

if (!empty($_POST['submit_save']) || !empty($_POST['submit_default'])) {
    if (!isset($_POST['csrfid']) || $_POST['csrfid'] != P2Util::getCsrfId()) {
        die('p2 error: �s���ȃ|�X�g�ł�');
    }
}

$ngThreadCtl = new NgThreadCtl();

//=====================================================================
// �O����
//=====================================================================

// {{{ ���ۑ��{�^����������Ă�����A�ݒ��ۑ�

if (!empty($_POST['submit_save'])) {

    if ($ngThreadCtl->save($POST['nga']) != false) {
        $_info_msg_ht .= '<p>���ݒ���X�V�ۑ����܂���</p>';
    } else {
        $_info_msg_ht .= '<p>�~�ݒ���X�V�ۑ��ł��܂���ł���</p>';
    }

// }}}
// {{{ ���f�t�H���g�ɖ߂��{�^����������Ă�����

} elseif (!empty($_POST['submit_default'])) {
    if ($ngThreadCtl->clear()) {
        $_info_msg_ht .= '<p>�����X�g����ɂ��܂���</p>';
    } else {
        $_info_msg_ht .= '<p>�~���X�g����ɂł��܂���ł���</p>';
    }
}

// }}}
// {{{ ���X�g�ǂݍ���

$formdata = $ngThreadCtl->load();

// }}}

//=====================================================================
// �v�����g�ݒ�
//=====================================================================
$ptitle_top = 'NG�X���b�h�ҏW';
$ptitle = strip_tags($ptitle_top);

$csrfid = P2Util::getCsrfId();

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
} else {
    echo basename($path) . "<br>";
}

// PC�p�\��
if (!$_conf['ktai']) {
    $htm['form_submit'] = <<<EOP
        <tr class="group">
            <td colspan="7" align="center">
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
<li>���[�h: NG/���ځ[�񃏁[�h (��ɂ���Ɠo�^����)</li>
<li>i: �啶���������𖳎�</li>
<li>re: ���K�\��</li>
<li>��: newsplus,software �� (���S��v, �J���}��؂�)</li>
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
    <input type="hidden" name="path" value="{$path_ht}">
    <input type="hidden" name="csrfid" value="{$csrfid}">\n
EOP;

// PC�p�\���itable�j
if (!$_conf['ktai']) {
    echo <<<EOP
    <table class="edit_conf_user" cellspacing="0">
        <tr>
            <td align="center">�~</td>
            <td align="center">���[�h</td>
            <td align="center">i</td>
            <td align="center">re</td>
            <td align="center">��</td>
            <td align="center">�ŏI�q�b�g�����Ɖ�</td>
        </tr>
        <tr class="group">
            <td colspan="6">�V�K�o�^</td>
        </tr>\n
EOP;
    $row_format = <<<EOP
        <tr>
            <td><input type="checkbox" name="nga[%1\$d][del]" value="1"></td>
            <td><input type="text" size="35" name="nga[%1\$d][word]" value="%2\$s"></td>
            <td><input type="checkbox" name="nga[%1\$d][ignorecase]" value="1"%3\$s></td>
            <td><input type="checkbox" name="nga[%1\$d][regex]" value="1"%4\$s></td>
            <td><input type="text" size="10" name="nga[%1\$d][bbs]" value="%5\$s"></td>
            <td align="right">
                <input type="hidden" name="nga[%1\$d][hits]" value="%6\$s">%6\$s
                <input type="hidden" name="nga[%1\$d][lasttime]" value="%7\$d">(%7\$d)
            </td>
        </tr>\n
EOP;
// �g�їp�\��
} else {
    echo "�V�K�o�^<br>\n";
    $row_format = <<<EOP
<input type="text" name="nga[%1\$d][word]" value="%2\$s"><br>
��:<input type="text" size="5" name="nga[%1\$d][bbs]" value="%5\$s">
<input type="checkbox" name="nga[%1\$d][ignorecase]" value="1"%3\$s>i
<input type="checkbox" name="nga[%1\$d][regex]" value="1"%4\$s>re
<input type="text" name="nga[%1\$d][del]" value="1">�~<br>
<input type="hidden" name="nga[%1\$d][lasttime]" value="%6\$s">
<input type="hidden" name="nga[%1\$d][hits]" value="%7\$d">(%6\$d)<br>\n
EOP;
}

printf($row_format, -1, '', '', '', '', '--', 0);

echo $htm['form_submit'];
if (!empty($formdata)) {
    foreach ($formdata as $k => $v) {
        printf($row_format,
            $k,
            p2h($v['word']),
            $v['ignorecase'],
            $v['regex'],
            p2h($v['bbs']),
            p2h($v['lasttime']),
            p2h($v['hits'])
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
