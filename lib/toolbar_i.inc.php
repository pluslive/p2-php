<?php
/**
 * rep2 - �c�[���o�[�p���[�e�B���e�B�iiPhone�j
 */

// {{{ _toolbar_i_button()

/**
 * �c�[���o�[�A�C�R��
 *
 * @param string $icon
 * @return string
 */
function _toolbar_i_icon($icon)
{
    static $hd = null;

    if ($hd === null) {
        $hd = false;
        if (isset($_SESSION['device_pixel_ratio'])) {
            if ($_SESSION['device_pixel_ratio'] > 1.0) {
                $hd = $GLOBALS['_conf']['have_glyphish_2x'];
            }
        }
    }

    if ($hd && preg_match('@img/glyphish/icons2/\\d+-[\\-\\w]+\\.png$@', $icon)) {
        $icon = substr($icon, 0, -4) . '@2x.png';
    }

    return $icon;
}

/**
 * �c�[���o�[�{�^�� (�����N)
 *
 * @param string $icon
 * @param string $label
 * @param string $uri
 * @param string $attrs
 * @return string
 */
function _toolbar_i_button($icon, $label, $uri, $attrs = '')
{
    global $_conf;

    if (strlen($attrs) && strncmp($attrs, ' ', 1) !== 0) {
        $attrs = ' ' . $attrs;
    }

    if (strpos($attrs, 'class="') === false) {
        $attrs .= ' class="hoverable"';
    } else {
        $attrs = str_replace('class="', 'class="hoverable ', $attrs);
    }

    if (empty($_conf['expack.iphone.toolbars.no_label'])) {
        $label = '<br>' . $label;
    } else {
        $label = '';
    }

    $icon = _toolbar_i_icon($icon);

    return <<<EOS
<a href="{$uri}"{$attrs}><img src="{$icon}" width="48" height="32" alt="">{$label}</a>
EOS;
}

// }}}
// {{{ toolbar_i_standard_button()

/**
 * �W���̃c�[���o�[�{�^�� (�����N)
 *
 * @param string $icon
 * @param string $label
 * @param string $uri
 * @return string
 */
function toolbar_i_standard_button($icon, $label, $uri)
{
    if (strlen($uri) > 1 && strncmp($uri, '#', 1) === 0) {
        $attrs = ' onclick="return iutil.toolbarScrollTo(this, event);"';
    } else {
        $attrs = '';
    }
    return _toolbar_i_button($icon, $label, $uri, $attrs);
}

// }}}
// {{{ toolbar_i_badged_button()

/**
 * �o�b�W�t���̃c�[���o�[�{�^�� (�����N)
 *
 * @param string $icon
 * @param string $label
 * @param string $uri
 * @param string $badge
 * @return string
 */
function toolbar_i_badged_button($icon, $label, $uri, $badge)
{
    $label .= sprintf('<span class="badge l%d">%s</span>', min(strlen($badge), 4), $badge);
    return _toolbar_i_button($icon, $label, $uri);
}

// }}}
// {{{ toolbar_i_opentab_button()

/**
 * �����N��V�����^�u�ŊJ���c�[���o�[�{�^��
 *
 * @param string $icon
 * @param string $label
 * @param string $uri
 * @return string
 */
function toolbar_i_opentab_button($icon, $label, $uri)
{
    return _toolbar_i_button($icon, $label, $uri, ' target="_blank"');
}

// }}}
// {{{ toolbar_i_disabled_button()

/**
 * �����ȃc�[���o�[�{�^��
 *
 * @param string $icon
 * @param string $label
 * @param string $uri
 * @return string
 */
function toolbar_i_disabled_button($icon, $label)
{
    global $_conf;

    if (empty($_conf['expack.iphone.toolbars.no_label'])) {
        $label = '<br>' . $label;
    } else {
        $label = '';
    }

    $icon = _toolbar_i_icon($icon);

    return <<<EOS
<span class="unavailable"><img src="{$icon}" width="48" height="32" alt="">{$label}</span>
EOS;
}

// }}}
// {{{ toolbar_i_showhide_button()

/**
 * �^�[�Q�b�g�v�f�̕\���E��\�����g�O������c�[���o�[�{�^��
 *
 * @param string $icon
 * @param string $label
 * @param string $id
 * @return string
 */
function toolbar_i_showhide_button($icon, $label, $id)
{
    $attrs = ' onclick="return iutil.toolbarShowHide(this, event);"';
    return _toolbar_i_button($icon, $label, "#{$id}", $attrs);
}

// }}}
// {{{ toolbar_i_favita_button()

/**
 * ���C�ɔ̓o�^�E�������g�O������c�[���o�[�{�^��
 *
 * @param string $icon
 * @param string $label (fallback)
 * @param object $info @see lib/get_info.inc.php: get_board_info()
 * @param int $setnum
 * @return string
 */
function toolbar_i_favita_button($icon, $label, $info, $setnum = 0)
{
    if (!array_key_exists($setnum, $info->favs)) {
        return toolbar_i_disabled_button($icon, $label);
    }

    $fav = $info->favs[$setnum];
    $attrs = ' onclick="return iutil.toolbarRunHttpCommand(this, event);"';
    if (!$fav['set']) {
        $attrs .= ' class="inactive"';
    }
    $uri = 'httpcmd.php?' . http_build_query(array(
        'cmd'       => 'setfavita',
        'host'      => $info->host,
        'bbs'       => $info->bbs,
        'itaj_en'   => UrlSafeBase64::encode($info->itaj),
        'setnum'    => $setnum,
        'setfavita' => 2,
    ), '', '&amp;');

    return _toolbar_i_button($icon, $fav['title'], $uri, $attrs);
}

// }}}
// {{{ toolbar_i_fav_button()

/**
 * ���C�ɃX���̓o�^�E�������g�O������c�[���o�[�{�^��
 *
 * @param string $icon
 * @param string $label (fallback)
 * @param object $info @see lib/get_info.inc.php: get_thread_info()
 * @param int $setnum
 * @return string
 */
function toolbar_i_fav_button($icon, $label, $info, $setnum = 0)
{
    if (!array_key_exists($setnum, $info->favs)) {
        return toolbar_i_disabled_button($icon, $label);
    }

    $fav = $info->favs[$setnum];
    $attrs = ' onclick="return iutil.toolbarRunHttpCommand(this, event);"';
    if (!$fav['set']) {
        $attrs .= ' class="inactive"';
    }
    $uri = 'httpcmd.php?' . http_build_query(array(
        'cmd'       => 'setfav',
        'host'      => $info->host,
        'bbs'       => $info->bbs,
        'key'       => $info->key,
        'ttitle_en' => UrlSafeBase64::encode($info->ttitle),
        'setnum'    => $setnum,
        'setfav'    => 2,
    ), '', '&amp;');

    return _toolbar_i_button($icon, $fav['title'], $uri, $attrs);
}

// }}}
// {{{ toolbar_i_palace_button()

/**
 * �a������̓o�^�E�������g�O������c�[���o�[�{�^��
 *
 * @param string $icon
 * @param string $label
 * @param object $info @see lib/get_info.inc.php: get_thread_info()
 * @return string
 */
function toolbar_i_palace_button($icon, $label, $info)
{
    $attrs = ' onclick="return iutil.toolbarRunHttpCommand(this, event);"';
    if (!$info->palace) {
        $attrs .= ' class="inactive"';
    }
    $uri = 'httpcmd.php?' . http_build_query(array(
        'cmd'       => 'setpal',
        'host'      => $info->host,
        'bbs'       => $info->bbs,
        'key'       => $info->key,
        'ttitle_en' => UrlSafeBase64::encode($info->ttitle),
        'setpal'    => 2,
    ), '', '&amp;');

    return _toolbar_i_button($icon, $label, $uri, $attrs);
}

// }}}
// {{{ toolbar_i_aborn_button()

/**
 * �X���b�h���ځ[���Ԃ��g�O������c�[���o�[�{�^��
 *
 * @param string $icon
 * @param string $label
 * @param object $info @see lib/get_info.inc.php: get_thread_info()
 * @return string
 */
function toolbar_i_aborn_button($icon, $label, $info)
{
    $attrs = ' onclick="return iutil.toolbarRunHttpCommand(this, event);"';
    if (!$info->taborn) {
        $attrs .= ' class="inactive"';
    }
    $uri = 'httpcmd.php?' . http_build_query(array(
        'cmd'       => 'taborn',
        'host'      => $info->host,
        'bbs'       => $info->bbs,
        'key'       => $info->key,
        'ttitle_en' => UrlSafeBase64::encode($info->ttitle),
        'taborn'    => 2,
    ), '', '&amp;');

    return _toolbar_i_button($icon, $label, $uri, $attrs);
}

// }}}
// {{{ toolbar_i_action_board_button()

/**
 * �O���A�v�����Ŕ��J���{�^��
 *
 * @param string $icon
 * @param string $label (fallback)
 * @param ThreadList $aThreadList
 * @return string
 */
function toolbar_i_action_board_button($icon, $label, ThreadList $aThreadList)
{
    global $_conf;

    $type = _toolbar_i_client_type();
    $pattern = $_conf["expack.tba.{$type}.board_uri"];
    if (!$pattern) {
        return toolbar_i_disabled_button($icon, $label);
    }

    $host = $aThreadList->host;
    $bbs = $aThreadList->bbs;
    $url = "http://{$host}/{$bbs}/";
    $uri = p2h(strtr($pattern, array(
        '$time' => time(),
        '$host' => rawurlencode($host),
        '$bbs'  => rawurlencode($bbs),
        '$url'  => $url,
        '$eurl' => rawurlencode($url),
    )), false);

    $title = $_conf["expack.tba.{$type}.board_title"];
    if ($title !== '') {
        $label = p2h($title, false);
    }

    return _toolbar_i_button($icon, $label, $uri);
}

// }}}
// {{{ toolbar_i_action_thread_button()

/**
 * �O���A�v�����ŃX���b�h���J���{�^��
 *
 * @param string $icon
 * @param string $label (fallback)
 * @param Thread $aThread
 * @return string
 */
function toolbar_i_action_thread_button($icon, $label, Thread $aThread)
{
    global $_conf;

    $type = _toolbar_i_client_type();
    $pattern = $_conf["expack.tba.{$type}.thread_uri"];
    if (!$pattern) {
        return toolbar_i_disabled_button($icon, $label);
    }

    $url = $aThread->getMotoThread(true, '');
    $uri = p2h(strtr($pattern, array(
        '$time' => time(),
        '$host' => rawurlencode($aThread->host),
        '$bbs'  => rawurlencode($aThread->bbs),
        '$key'  => rawurlencode($aThread->key),
        '$ls'   => rawurlencode($aThread->ls),
        '$url'  => $url,
        '$eurl' => rawurlencode($url),
        '$path' => preg_replace('!^https?://!', '', $url),
    )), false);

    $title = $_conf["expack.tba.{$type}.thread_title"];
    if ($title !== '') {
        $label = p2h($title, false);
    }

    return _toolbar_i_button($icon, $label, $uri);
}

// }}}
// {{{ _toolbar_i_client_type()

/**
 * �N���C�A���g�̎�ނ�Ԃ�
 *
 * @param void
 * @return string
 */
function _toolbar_i_client_type()
{
    global $_conf;

    switch ($_conf['client_type']) {
        case 'i':
            $type = UA::isAndroidWebKit() ? 'android' : 'iphone';
            break;
        case 'i':
            $type = 'mobile';
            break;
        case 'pc':
        default:
            $type = 'other';
    }

    return $type;
}

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
