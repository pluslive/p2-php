<?php
/**
 * rep2expack - セットアップ用関数群
 */

// {{{ p2_load_class()

/**
 * クラスローダー
 *
 * @param string $name
 * @return void
 */
function p2_load_class($name)
{
    if (preg_match('/^(?:
            BbsMap |
            BrdCtl |
            BrdMenu(?:Cate|Ita)? |
            DataPhp |
            DownloadDat[0-9A-Z][0-9A-Za-z]* |
            FavSetManager |
            FileCtl |
            HostCheck |
            JStyle |
            Login |
            MD5Crypt |
            MatomeCache(?:List)? |
            NgAbornCtl |
            P2[A-Z][A-Za-z]* |
            PresetManager |
            Res(?:Article|Filter(?:Element)?|Hist) |
            Session |
            SettingTxt |
            ShowBrdMenu(?:K|Pc) |
            ShowThread(?:K|Pc)? |
            StrCtl |
            StrSjis |
            SubjectTxt |
            Thread(?:List|Read)? |
            UA |
            UrlSafeBase64 |
            Wap(?:UserAgent|Request|Response)
        )$/x', $name))
    {
        if (strncmp($name, 'Wap', 3) === 0) {
            include P2_LIB_DIR . '/Wap.php';
        } else {
            include P2_LIB_DIR . '/' . $name . '.php';
        }
    } elseif (preg_match('/^[A-Z][A-Za-z]*DataStore$/', $name)) {
        include P2_LIB_DIR . '/P2DataStore/' . $name . '.php';
    }
}

// }}}
// {{{ p2_rewrite_vars_for_proxy()

/**
 * リバースプロキシ経由で動作するように$_SERVER変数を書き換える
 *
 * @param void
 * @return void
 */
function p2_rewrite_vars_for_proxy()
{
    global $_conf;

    foreach (array('HTTP_HOST', 'HTTP_PORT', 'REQUEST_URI', 'SCRIPT_NAME', 'PHP_SELF') as $key) {
        if (array_key_exists($key, $_SERVER)) {
            $_SERVER["X_REP2_ORIG_{$key}"] = $_SERVER[$key];
        }
    }

    if ($_conf['reverse_proxy_host']) {
        if ($_conf['reverse_proxy_host'] === 'auto') {
            if (isset($_SERVER['HTTP_X_FORWARDED_HOST'])) {
                $_SERVER['HTTP_HOST'] = $_SERVER['HTTP_X_FORWARDED_HOST'];
            } else {
                return;
            }
        } else {
            $_SERVER['HTTP_HOST'] = $_conf['reverse_proxy_host'];
        }
    } else {
        return;
    }

    if ($_conf['reverse_proxy_port']) {
        if ($_conf['reverse_proxy_port'] === 'auto') {
            if (isset($_SERVER['HTTP_X_FORWARDED_PORT'])) {
                $_SERVER['HTTP_PORT'] = $_SERVER['HTTP_X_FORWARDED_PORT'];
            }
        } else {
             $_SERVER['HTTP_PORT'] = $_conf['reverse_proxy_port'];
        }
    }

    if ($_conf['reverse_proxy_path']) {
        $path = '/' . trim($_conf['reverse_proxy_path'], '/');
        foreach (array('REQUEST_URI', 'SCRIPT_NAME', 'PHP_SELF') as $key) {
            if (!isset($_SERVER[$key]) || $_SERVER[$key] === '') {
                $_SERVER[$key] = $path . '/';
            } else {
                $_SERVER[$key] = $path . $_SERVER[$key];
            }
        }
    }
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
