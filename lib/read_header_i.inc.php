<?php
/**
 * rep2 - �X���b�h�\�� - �w�b�_���� - iPhone�p for read.php
 */

require_once P2_LIB_DIR . '/get_info.inc.php';
require_once P2_LIB_DIR . '/spm_k.inc.php';

// {{{ �ϐ�
// {{{ ��{�ϐ�

$motothre_url = $aThread->getMotoThread(false, '1-10');
$ttitle_en = UrlSafeBase64::encode($aThread->ttitle);
$ttitle_en_q = '&amp;ttitle_en=' . $ttitle_en;
$bbs_q = '&amp;bbs=' . $aThread->bbs;
$key_q = '&amp;key=' . $aThread->key;
$host_bbs_key_q = 'host=' . $aThread->host . $bbs_q . $key_q;
$offline_q = '&amp;offline=1';
$itaj_hd = p2h($aThread->itaj);
$rnum_range = $_conf['mobile.rnum_range'];
$thread_info = get_thread_info($aThread->host, $aThread->bbs, $aThread->key);

// }}}
// {{{ ���X�i�r�ݒ�

if ($do_filtering) {
    include P2_LIB_DIR . '/read_filter_k.inc.php';
} else {
    // {{{ �i�r - �O

    $before_rnum = max(1, $aThread->resrange['start'] - $rnum_range);
    if ($aThread->resrange['start'] == 1) {
        $read_navi_previous_isInvisible = true;
    } else {
        $read_navi_previous_isInvisible = false;
    }

    if ($read_navi_previous_isInvisible) {
        $read_navi_previous_url = '';
    } else {
        $read_navi_previous_url = "{$_conf['read_php']}?{$host_bbs_key_q}&amp;ls={$before_rnum}-{$aThread->resrange['start']}n{$offline_q}{$_conf['k_at_a']}";
    }

    // }}}
    // {{{ �i�r - ��


    if (!empty($_GET['one'])) {
        $read_navi_next_isInvisible = false;
    } elseif ($aThread->resrange['to'] >= $aThread->rescount) {
        $aThread->resrange['to'] = $aThread->rescount;
        $read_navi_next_isInvisible = true;
    } else {
        $read_navi_next_isInvisible = false;
    }
    $after_rnum = $aThread->resrange['to'] + $rnum_range;

    if ($read_navi_next_isInvisible) {
        $read_navi_next_url = '';
    } else {
        $read_navi_next_url = "{$_conf['read_php']}?{$host_bbs_key_q}&amp;ls={$aThread->resrange['to']}-{$after_rnum}n{$offline_q}&amp;nt={$newtime}{$_conf['k_at_a']}";
    }

    // }}}
}

// }}}
// {{{ �w�b�_�v�f

$_conf['extra_headers_ht'] .= <<<EOS
<script type="text/javascript" src="js/respopup_iphone.js?{$_conf['p2_version_id']}"></script>
EOS;
// ImageCache2
if ($_conf['expack.ic2.enabled']) {
    $_conf['extra_headers_ht'] .= <<<EOS
<link rel="stylesheet" type="text/css" href="css/ic2_iphone.css?{$_conf['p2_version_id']}">
<script type="text/javascript" src="js/json2.js?{$_conf['p2_version_id']}"></script>
<script type="text/javascript" src="js/ic2_iphone.js?{$_conf['p2_version_id']}"></script>
EOS;
}
// SPM
if ($_conf['expack.spm.enabled']) {
    $_conf['extra_headers_ht'] .= <<<EOS
<script type="text/javascript" src="js/spm_iphone.js?{$_conf['p2_version_id']}"></script>
EOS;
}
// Limelight
if ($_conf['expack.aas.enabled'] || $_conf['expack.ic2.enabled']) {
    $_conf['extra_headers_ht'] .= <<<EOS
<link rel="stylesheet" type="text/css" href="css/limelight.css?{$_conf['p2_version_id']}">
<script type="text/javascript" src="js/jquery-{$_conf['jquery_version']}.min.js"></script>
<script type="text/javascript" src="js/limelight.js?{$_conf['p2_version_id']}"></script>
<script type="text/javascript">
// <![CDATA[
document.addEventListener('DOMContentLoaded', function(event) {
    var limelight;
    document.removeEventListener(event.type, arguments.callee, false);
    limelight = new Limelight({ 'savable': true, 'title': true });
    limelight.bind();
    window._IRESPOPG.callbacks.push(function(container) {
        limelight.bind(null, container, true);
    });
}, false);
// ]]>
</script>
EOS;
}

// }}}
// }}}
// {{{ HTML�v�����g

//!empty($_GET['nocache']) and P2Util::header_nocache();
echo $_conf['doctype'];
echo <<<EOP
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=Shift_JIS">
<meta name="ROBOTS" content="NOINDEX, NOFOLLOW">
{$_conf['extra_headers_ht']}
<title>{$ptitle_ht}</title>
</head>
<body class="nopad">
<div class="ntoolbar" id="header">
<h1 class="ptitle hoverable">{$aThread->ttitle_hd}</h1>
EOP;

// {{{ �e��{�^����

echo '<table><tbody><tr>';

// �ɖ߂�
echo '<td>';
$escaped_url = "{$_conf['subject_php']}?{$host_bbs_key_q}{$_conf['k_at_a']}";
echo toolbar_i_standard_button('img/glyphish/icons2/104-index-cards.png', $itaj_hd, $escaped_url);
echo '</td>';

// ���X����
echo '<td>';
echo toolbar_i_showhide_button('img/glyphish/icons2/06-magnifying-glass.png', '����', 'read_toolbar_filter');
echo '</td>';

// ���C�ɃX��
echo '<td>';
if ($thread_info) {
    echo toolbar_i_fav_button('img/glyphish/icons2/28-star.png', '���C�ɃX��', $thread_info);
} else {
    echo toolbar_i_disabled_button('img/glyphish/icons2/28-star.png', '���C�ɃX��');
}
echo '</td>';

// ���̑�
echo '<td>';
echo toolbar_i_showhide_button('img/gp0-more.png', '���̑�', 'read_toolbar_extra');
echo '</td>';

// ����
echo '<td>';
echo toolbar_i_standard_button('img/gp2-down.png', '��', '#footer');
echo '</td>';

echo '</tr></tbody></table>';

// }}}
// {{{ ���̑��̃c�[��

echo '<div id="read_toolbar_extra" class="extra">';
echo '<table><tbody>';

// {{{ ���̑� - ���C�ɓ���Z�b�g

if ($thread_info && $_conf['expack.misc.multi_favs']) {
    echo '<tr>';
    for ($i = 1; $i <= $_conf['expack.misc.favset_num']; $i++) {
        echo '<td>';
        echo toolbar_i_fav_button('img/glyphish/icons2/28-star.png', '-', $thread_info, $i);
        echo '</td>';
        if ($i % 5 === 0 && $i != $_conf['expack.misc.favset_num']) {
            echo '</tr><tr>';
        }
    }
    $mod_cells = $_conf['expack.misc.favset_num'] % 5;
    if ($mod_cells) {
        $mod_cells = 5 - $mod_cells;
        for ($i = 0; $i < $mod_cells; $i++) {
            echo '<td>&nbsp;</td>';
        }
    }
    echo '</tr>';
}

// }}}
// {{{ ���̑� - �{�^����

echo '<tr>';

// >>1
echo '<td>';
$escaped_url = "{$_conf['read_php']}?{$host_bbs_key_q}&amp;ls=1-{$rnum_range}{$offline_q}{$_conf['k_at_a']}";
echo toolbar_i_standard_button('img/glyphish/icons2/63-runner.png', '&gt;&gt;1', $escaped_url);
echo '</td>';

// �ގ��X������
echo '<td>';
$escaped_url = "{$_conf['subject_php']}?{$host_bbs_key_q}&amp;itaj_en="
             . UrlSafeBase64::encode($aThread->itaj)
             . '&amp;method=similar&amp;word='
             . rawurlencode($aThread->ttitle_hc)
             . "&amp;refresh=1{$_conf['k_at_a']}";
echo toolbar_i_standard_button('img/glyphish/icons2/06-magnifying-glass.png', '�ގ��X��', $escaped_url);
echo '</td>';

// �a������
echo '<td>';
echo toolbar_i_palace_button('img/glyphish/icons2/108-badge.png', '�a������', $thread_info);
echo '</td>';

// �X���b�h���ځ[��
echo '<td>';
echo toolbar_i_aborn_button('img/glyphish/icons2/128-bone.png', '���ځ[��', $thread_info);
echo '</td>';

// ���O�폜
echo '<td>';
if (file_exists($aThread->keydat)) {
    $escaped_url = "info.php?{$host_bbs_key_q}{$ttitle_en_q}&amp;dele=1{$_conf['k_at_a']}";
    echo toolbar_i_standard_button('img/glyphish/icons2/64-zap.png', '���O�폜', $escaped_url);
} else {
    echo toolbar_i_disabled_button('img/glyphish/icons2/64-zap.png', '���O�폜');
}
echo '</td>';

echo '</tr>';

// }}}

echo '</tbody></table>';

// {{{ ���̑� - SPM�t�H�[��

// IC2�����N�A����
if ($_conf['expack.ic2.enabled'] && $_conf['expack.ic2.thread_imagelink']) {
    echo '<table><tbody><tr>';
    echo '<td colspan="4">';
    echo kspform($aThread);
    echo '</td>';
    $escaped_url = 'iv2.php?field=memo&amp;keyword='
        . rawurlencode($aThread->ttitle)
        . "&amp;session_no_close=1{$_conf['k_at_a']}";
    echo '<td>';
    if ($_conf['expack.ic2.thread_imagecount']) {
        require_once P2EX_LIB_DIR . '/ic2_getcount.inc.php';
        $cnt = 0;
        try {
            $cnt = getIC2ImageCount($aThread->ttitle);
        }
        catch (Exception $e) {
            $cnt = -1;
        }
        if ($cnt == 0) {
            echo toolbar_i_disabled_button('img/glyphish/icons2/42-photos.png',
                '�摜');
        } else {
            echo toolbar_i_opentab_button('img/glyphish/icons2/42-photos.png',
                '�摜' . ($cnt < 0 ? '(?)' : "({$cnt})"), $escaped_url);
        }
    } else {
        echo toolbar_i_opentab_button('img/glyphish/icons2/42-photos.png',
            '�摜', $escaped_url);
    }
    echo '</td></tr></tbody></table>';
} else {
    echo kspform($aThread);
}


// }}}

echo '</div>';

// }}}
// {{{ ���X�����t�H�[��

$htm['rf_hidden_fields'] = ResFilterElement::getHiddenFields($aThread->host, $aThread->bbs, $aThread->key);
$htm['rf_word_field'] = ResFilterElement::getWordField(array(
    'autocorrect' => 'off',
    'autocapitalize' => 'off',
));
$htm['rf_field_field'] = ResFilterElement::getFieldField();
$htm['rf_method_field'] = ResFilterElement::getMethodField();
$htm['rf_match_field'] = ResFilterElement::getMatchField();
$htm['rf_include_field'] = ResFilterElement::getIncludeField();

echo <<<EOP
<div id="read_toolbar_filter" class="extra">
<form id="read_filter" method="get" action="{$_conf['read_php']}" accept-charset="{$_conf['accept_charset']}">
{$htm['rf_hidden_fields']}{$htm['rf_word_field']}
<input type="submit" id="submit1" name="submit_filter" value="����"><br>
{$htm['rf_field_field']}��{$htm['rf_method_field']}��{$htm['rf_match_field']}
{$htm['rf_include_field']}
{$_conf['detect_hint_input_ht']}{$_conf['k_input_ht']}
</form>
</div>
EOP;

// }}}
// {{{ �e��ʒm

$info_ht = P2Util::getInfoHtml();
if (strlen($info_ht)) {
    echo "<div class=\"info\">{$info_ht}</div>";
}

// �X�����T�[�o�ɂȂ����
if ($aThread->diedat) {
    echo '<div class="info">';
    if ($aThread->getdat_error_msg_ht) {
        echo $aThread->getdat_error_msg_ht;
    } else {
        echo $aThread->getDefaultGetDatErrorMessageHTML();
    }
    echo "<p><a href=\"{$motothre_url}\" target=\"_blank\">{$motothre_url}</a></p>";
    echo '</div>';
}

// �t�B���^�q�b�g��
if ($do_filtering) {
    $htm['rf_field_names'] = array(
        ResFilter::FIELD_NUMBER => '���X�ԍ�',
        ResFilter::FIELD_HOLE => '�S��', 
        ResFilter::FIELD_MESSAGE => '�{��',
        ResFilter::FIELD_NAME => '���O',
        ResFilter::FIELD_MAIL => '���[��',
        ResFilter::FIELD_DATE => '���t',
        ResFilter::FIELD_ID => 'ID',
    );
    $hd['word'] = ResFilter::getWord('p2h');
    echo "<div class=\"hits\">{$htm['rf_field_names'][$resFilter->field]}��&quot;{$hd['word']}&quot;��";
    echo ($resFilter->match == ResFilter::MATCH_ON) ? '�܂�' : '�܂܂Ȃ�';
    if ($resFilter->include & ResFilter::INCLUDE_REFERENCES) {
        echo '+�Q��';
    }
    if ($resFilter->include & ResFilter::INCLUDE_REFERENCED) {
        echo '+�t�Q��';
    }
    echo ': <span id="searching">n</span>hit!</div>';
}

// }}}

if ($_GET['showbl']) {
    echo  '<div class="hits">' . p2h($aThread->resrange['start']) . '�ւ�ڽ</div>';
}

echo '</div>'; // end toolbar

// }}}

/*
 * Local Variables:
 * mode: php
 * coding: cp932
 * tab-width: 4
 * c-basic-offset: 4
 * indent-tabs-mode: nil
 * End:
 */
// vim: set syn=php fenc=cp932 ai et ts=4 sw=4 sts=4 fdm=marker:
