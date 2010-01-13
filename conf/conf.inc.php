<?php
/**
 * rep2 - 基本設定ファイル
 * このファイルは、特に理由の無い限り変更しないこと
 */

// バージョン情報
$_conf = array(
    'p2version' => '1.7.29+1.8.x',  // rep2のバージョン
    'p2expack'  => '100113.1300',   // 拡張パックのバージョン
    'p2name'    => 'expack',        // rep2の名前
);

$_conf['p2ua'] = "{$_conf['p2name']}/{$_conf['p2version']}+{$_conf['p2expack']}";

define('P2_VERSION_ID', sprintf('%u', crc32($_conf['p2ua'])));

/*
 * 通常はセッションファイルのロック待ちを極力短くするため
 * ユーザー認証後すぐにセッション変数の変更をコミットする。
 * 認証後もセッション変数を変更するスクリプトでは
 * このファイルを読み込む前に
 *  define('P2_SESSION_CLOSE_AFTER_AUTHENTICATION', 0);
 * とする。
 */
if (!defined('P2_SESSION_CLOSE_AFTER_AUTHENTICATION')) {
    define('P2_SESSION_CLOSE_AFTER_AUTHENTICATION', 1);
}

// {{{ グローバル変数を初期化

$_info_msg_ht = ''; // ユーザ通知用 情報メッセージHTML

$MYSTYLE    = array();
$STYLE      = array();
$debug      = false;
$skin       = null;
$skin_en    = null;
$skin_name  = null;
$skin_uniq  = null;
$_login     = null;
$_p2session = null;

$conf_user_def   = array();
$conf_user_rules = array();
$conf_user_rad   = array();
$conf_user_sel   = array();

// }}}

// 基本設定処理を実行
p2configure();

// クリーンアップ
if (basename($_SERVER['SCRIPT_NAME']) != 'edit_conf_user.php') {
    unset($conf_user_def, $conf_user_rules, $conf_user_rad, $conf_user_sel);
}

// E_NOTICE および暗黙の配列初期化除け
$_conf['filtering'] = false;
$hd = array('word' => null);
$htm = array();
$word = null;

// {{{ p2configure()

/**
 * 一時変数でグローバル変数を汚染しないように設定処理を関数化
 */
function p2configure()
{
    global $MYSTYLE, $STYLE, $debug;
    global $skin, $skin_en, $skin_name, $skin_uniq;
    global $_conf, $_info_msg_ht, $_login, $_p2session;
    global $conf_user_def, $conf_user_rules, $conf_user_rad, $conf_user_sel;

    // エラー出力設定
    if (defined('E_DEPRECATED')) {
        error_reporting(E_ALL & ~(E_NOTICE | E_STRICT | E_DEPRECATED));
    } else {
        error_reporting(E_ALL & ~(E_NOTICE | E_STRICT));
    }

    // {{{ 基本変数

    $_conf['p2web_url']             = 'http://akid.s17.xrea.com/';
    $_conf['p2ime_url']             = 'http://akid.s17.xrea.com/p2ime.php';
    $_conf['favrank_url']           = 'http://akid.s17.xrea.com/favrank/favrank.php';
    $_conf['expack.web_url']        = 'http://page2.skr.jp/rep2/';
    $_conf['expack.download_url']   = 'http://page2.skr.jp/rep2/downloads.html';
    $_conf['expack.history_url']    = 'http://page2.skr.jp/rep2/history.html';
    $_conf['expack.tgrep_url']      = 'http://page2.xrea.jp/tgrep/search';
    $_conf['expack.ime_url']        = 'http://page2.skr.jp/gate.php';
    $_conf['menu_php']              = 'menu.php';
    $_conf['subject_php']           = 'subject.php';
    $_conf['read_php']              = 'read.php';
    $_conf['read_new_php']          = 'read_new.php';
    $_conf['read_new_k_php']        = 'read_new_k.php';

    // }}}
    // {{{ 環境設定

    // デバッグ
    //$debug = !empty($_GET['debug']);

    putenv('LC_CTYPE=C');

    // タイムゾーンをセット
    date_default_timezone_set('Asia/Tokyo');

    // スクリプト実行制限時間 (秒)
    if (!defined('P2_CLI_RUN')) {
        set_time_limit(60); // (60)
    }

    // 自動フラッシュをオフにする
    ob_implicit_flush(0);

    // file($filename, FILE_IGNORE_NEW_LINES) で CR/LF/CR+LF のいずれも行末として扱う
    ini_set('auto_detect_line_endings', 1);

    // session.trans_sid有効時 や output_add_rewrite_var(),
    // http_build_query() 等で生成・変更される
    // URLのGETパラメータ区切り文字(列)を"&amp;"にする。（デフォルトは"&"）
    ini_set('arg_separator.output', '&amp;');

    // Windows なら
    if (strncasecmp(PHP_OS, 'WIN', 3) == 0) {
        // Windows
        defined('PATH_SEPARATOR') or define('PATH_SEPARATOR', ';');
        defined('DIRECTORY_SEPARATOR') or define('DIRECTORY_SEPARATOR', '\\');
        define('P2_OS_WINDOWS', 1);
    } else {
        defined('PATH_SEPARATOR') or define('PATH_SEPARATOR', ':');
        defined('DIRECTORY_SEPARATOR') or define('DIRECTORY_SEPARATOR', '/');
        define('P2_OS_WINDOWS', 0);
    }

    // ヌルバイト定数
    // mbstring.script_encoding = SJIS-win だと
    // "\0", "\x00" 以降がカットされるので、chr()関数を使う
    define('P2_NULLBYTE', chr(0));

    // }}}
    // {{{ P2Util::header_content_type() を不要にするおまじない

    ini_set('default_mimetype', 'text/html');
    ini_set('default_charset', 'Shift_JIS');

    // }}}
    // {{{ ライブラリ類のパス設定

    define('P2_CONF_DIR', dirname(__FILE__)); // __DIR__ @php-5.3

    define('P2_BASE_DIR', dirname(P2_CONF_DIR));

    // 基本的な機能を提供するするライブラリ
    define('P2_LIB_DIR', P2_BASE_DIR . '/lib');

    // おまけ的な機能を提供するするライブラリ
    define('P2EX_LIB_DIR', P2_BASE_DIR . '/lib/expack');

    // スタイルシート
    define('P2_STYLE_DIR', P2_BASE_DIR . '/style');

    // スキン
    define('P2_SKIN_DIR', P2_BASE_DIR . '/skin');
    define('P2_USER_SKIN_DIR', P2_BASE_DIR . '/user_skin');

    // PEARインストールディレクトリ、検索パスに追加される
    define('P2_PEAR_DIR', P2_BASE_DIR . '/includes');

    // PEARをハックしたファイル用ディレクトリ、通常のPEARより優先的に検索パスに追加される
    // Cache/Container/db.php(PEAR::Cache)がMySQL縛りだったので、汎用的にしたものを置いている
    // include_pathを追加するのはパフォーマンスに影響を及ぼすため、本当に必要な場合のみ定義
    if (defined('P2_USE_PEAR_HACK')) {
        define('P2_PEAR_HACK_DIR', P2_BASE_DIR . '/lib/pear_hack');
    }

    // コマンドラインツール
    define('P2_CLI_DIR', P2_BASE_DIR . '/cli');

    // 検索パスをセット
    $include_path = '';
    if (defined('P2_PEAR_HACK_DIR')) {
        $include_path .= P2_PEAR_HACK_DIR . PATH_SEPARATOR;
    }
    if (is_dir(P2_PEAR_DIR)) {
        $include_path .= P2_PEAR_DIR . PATH_SEPARATOR;
    } else {
        $paths = array();
        foreach (explode(PATH_SEPARATOR, get_include_path()) as $dir) {
            if (is_dir($dir)) {
                $dir = realpath($dir);
                if ($dir != P2_BASE_DIR) {
                    $paths[] = $dir;
                }
            }
        }
        if (count($paths)) {
            $include_path .= implode(PATH_SEPARATOR, array_unique($paths)) . PATH_SEPARATOR;
        }
    }
    $include_path .= P2_BASE_DIR; // fallback
    set_include_path($include_path);
    spl_autoload_register('p2autoload');

    // }}}
    // {{{ 環境チェックとデバッグ

    // ユーティリティを読み込む
    include P2_LIB_DIR . '/p2util.inc.php';

    // 動作環境を確認 (要件を満たしているならコメントアウト可)
    p2checkenv(__LINE__);

    if ($debug) {
        if (!class_exists('Benchmark_Profiler', false)) {
            require 'Benchmark/Profiler.php';
        }
        $profiler = new Benchmark_Profiler(true);
        // p2_print_memory_usage();
        register_shutdown_function('p2_print_memory_usage');
    }

    // }}}
    // {{{ 文字コードの指定

    //mb_detect_order("CP932,CP51932,ASCII");
    mb_internal_encoding('CP932');
    mb_http_output('pass');
    mb_substitute_character(63); // 文字コード変換に失敗した文字が "?" になる
    //mb_substitute_character(0x3013); // 〓
    //ob_start('mb_output_handler');

    if (function_exists('mb_ereg_replace')) {
        define('P2_MBREGEX_AVAILABLE', 1);
        mb_regex_encoding('CP932');
    } else {
        define('P2_MBREGEX_AVAILABLE', 0);
    }

    // }}}
    // {{{ 管理者用設定etc.

    // 管理者用設定を読み込み
    include P2_CONF_DIR . '/conf_admin.inc.php';

    // ディレクトリの絶対パス化
    $_conf['data_dir'] = p2_realpath($_conf['data_dir']);
    $_conf['dat_dir']  = p2_realpath($_conf['dat_dir']);
    $_conf['idx_dir']  = p2_realpath($_conf['idx_dir']);
    $_conf['pref_dir'] = p2_realpath($_conf['pref_dir']);

    // 管理用保存ディレクトリ
    $_conf['admin_dir'] = $_conf['data_dir'] . '/admin';

    // cache 保存ディレクトリ
    // 2005/06/29 $_conf['pref_dir'] . '/p2_cache' より変更
    $_conf['cache_dir'] = $_conf['data_dir'] . '/cache';

    // Cookie 保存ディレクトリ
    // 2008/09/09 $_conf['pref_dir'] . '/p2_cookie' より変更
    $_conf['cookie_dir'] = $_conf['data_dir'] . '/cookie';

    // コンパイルされたテンプレートの保存ディレクトリ
    $_conf['compile_dir'] = $_conf['data_dir'] . '/compile';

    // セッションデータ保存ディレクトリ
    $_conf['session_dir'] = $_conf['data_dir'] . '/session';

    // テンポラリディレクトリ
    $_conf['tmp_dir'] = $_conf['data_dir'] . '/tmp';

    // バージョンIDを二重引用符やヒアドキュメント内に埋め込むための変数
    $_conf['p2_version_id'] = P2_VERSION_ID;

    // 文字コード自動判定用のヒント文字列
    $_conf['detect_hint'] = '◎◇';
    $_conf['detect_hint_input_ht'] = '<input type="hidden" name="_hint" value="◎◇">';
    $_conf['detect_hint_input_xht'] = '<input type="hidden" name="_hint" value="◎◇" />';
    //$_conf['detect_hint_utf8'] = mb_convert_encoding('◎◇', 'UTF-8', 'CP932');
    $_conf['detect_hint_q'] = '_hint=%81%9D%81%9E'; // rawurlencode($_conf['detect_hint'])
    $_conf['detect_hint_q_utf8'] = '_hint=%E2%97%8E%E2%97%87'; // rawurlencode($_conf['detect_hint_utf8'])

    // }}}
    // {{{ 変数設定

    $preferences = array(
        'favita_brd'        => 'p2_favita.brd',         // お気に板 (brd)
        'favlist_idx'       => 'p2_favlist.idx',        // お気にスレ (idx)
        'recent_idx'        => 'p2_recent.idx',         // 最近読んだスレ (idx)
        'palace_idx'        => 'p2_palace.idx',         // スレの殿堂 (idx)
        'res_hist_idx'      => 'p2_res_hist.idx',       // 書き込みログ (idx)
        'res_hist_dat'      => 'p2_res_hist.dat',       // 書き込みログファイル (dat)
        'res_hist_dat_php'  => 'p2_res_hist.dat.php',   // 書き込みログファイル (データPHP)
        'idpw2ch_php'       => 'p2_idpw2ch.php',        // 2ch ID認証設定ファイル (データPHP)
        'sid2ch_php'        => 'p2_sid2ch.php',         // 2ch ID認証セッションID記録ファイル (データPHP)
        'auth_user_file'    => 'p2_auth_user.php',      // 認証ユーザ設定ファイル(データPHP)
        'auth_imodeid_file' => 'p2_auth_imodeid.php',   // docomo iモードID認証ファイル (データPHP)
        'auth_docomo_file'  => 'p2_auth_docomo.php',    // docomo 端末製造番号認証ファイル (データPHP)
        'auth_ez_file'      => 'p2_auth_ez.php',        // EZweb サブスクライバID認証ファイル (データPHP)
        'auth_jp_file'      => 'p2_auth_jp.php',        // SoftBank 端末シリアル番号認証ファイル (データPHP)
        'login_log_file'    => 'p2_login.log.php',      // ログイン履歴 (データPHP)
        'login_failed_log_file' => 'p2_login_failed.dat.php',   // ログイン失敗履歴 (データPHP)
    );
    foreach ($preferences as $k => $v) {
        $_conf[$k] = $_conf['pref_dir'] . '/' . $v;
    }

    $_conf['matome_cache_path'] = $_conf['pref_dir'] . '/matome_cache';
    $_conf['matome_cache_ext']  = '.htm';
    $_conf['matome_cache_max']  = 3; // 予備キャッシュの数

    $_conf['orig_favita_brd']   = $_conf['favita_brd'];
    $_conf['orig_favlist_idx']  = $_conf['favlist_idx'];

    $_conf['cookie_db_path']    = $_conf['db_dir'] . '/p2_cookies.sqlite3';
    $_conf['post_db_path']      = $_conf['db_dir'] . '/p2_post_data.sqlite3';
    $_conf['hostcheck_db_path'] = $_conf['db_dir'] . '/p2_hostcheck_cache.sqlite3';

    // 補正
    if ($_conf['expack.use_pecl_http'] && !extension_loaded('http')) {
        if (!($_conf['expack.use_pecl_http'] == 2 && $_conf['expack.dl_pecl_http'])) {
            $_conf['expack.use_pecl_http'] = 0;
        }
    }

    // コマンドラインモードではここまで
    if (defined('P2_CLI_RUN')) {
        return;
    }

    // }}}

    include P2_LIB_DIR . '/bootstrap.php';
}

// }}}
// {{{ p2checkenv()

/**
 * 動作環境を確認する
 *
 * @return bool
 */
function p2checkenv($check_recommended)
{
    global $_info_msg_ht;

    $php_version = phpversion();
    $required_version = '5.2.8';
    $recommended_version52 = '5.2.12';
    $recommended_version53 = '5.3.1';
    $required_extensions = array(
        'dom',
        'json',
        'libxml',
        'mbstring',
        'pcre',
        'pdo',
        'pdo_sqlite',
        'session',
        'spl',
        //'xsl',
        'zlib',
    );

    // PHPのバージョン
    if (version_compare($php_version, $required_version, '<')) {
        p2die("PHP {$required_version} 未満では使えません。");
    }

    // 必須拡張モジュール
    foreach ($required_extensions as $ext) {
        if (!extension_loaded($ext)) {
            p2die("{$ext} 拡張モジュールがロードされていません。");
        }
    }

    // 有効だと動作しないphp.iniディレクティブ
    $directives = array(
        'safe_mode',
        'register_globals',
        'magic_quotes_gpc',
        'mbstring.encoding_translation',
        'session.cookie_httponly',
    );
    foreach ($directives as $directive) {
        if (ini_get($directive)) {
            p2die("{$directive} が On です。",
                  "php.ini で {$directive} を Off にしてください。");
        }
    }

    // eAccelerator
    if (extension_loaded('eaccelerator') &&
        version_compare(EACCELERATOR_VERSION, '0.9.5.2', '<'))
    {
        $err = 'eAcceleratorを更新してください。';
        $ev = EACCELERATOR_VERSION;
        $msg = <<<EOP
<p>PHP 5.2で例外を捕捉できない問題のあるeAccelerator ({$ev})がインストールされています。<br>
eAcceleratorを無効にするか、この問題が修正されたeAccelerator 0.9.5.2以降を使用してください。<br>
<a href="http://eaccelerator.net/">http://eaccelerator.net/</a></p>
EOP;
        p2die($err, $msg, true);
    }

    // 推奨バージョン
    if ($check_recommended) {
        if (version_compare($php_version, '5.3.0-dev', '>=')) {
            $recommended_version = $recommended_version53;
        } else {
            $recommended_version = $recommended_version52;
        }
        if (version_compare($php_version, $recommended_version, '<')) {
            // title.php のみメッセージを表示
            if (basename($_SERVER['PHP_SELF'], '.php') == 'title') {
                $_info_msg_ht .= <<<EOP
<p><strong>推奨バージョンより古いPHPで動作しています。</strong><em>(PHP {$php_version})</em><br>
PHP {$recommended_version} 以降にアップデートすることをおすすめします。</p>
<p style="font-size:smaller">このメッセージを表示しないようにするには <em>{\$rep2_directory}</em>/conf/conf.inc.php の {$check_recommended} 行目、<br>
<samp>p2checkenv(__LINE__);</samp> を <samp>p2checkenv(false);</samp> に書き換えてください。</p>
EOP;
            }
            return false;
        }
    }

    return true;
}

// }}}
// {{{ p2checkmigration()

/**
 * マイグレーションの必要があるかどうかをチェック
 *
 * @param   string  $config_version
 * @return  array
 */
function p2checkmigration($config_version)
{
    // "yymmdd.hhmm" 形式のマイグレーションが必要な変更のあったバージョン番号の配列
    // 値はユニークかつ昇順にソートされていなければならない
    $versions = array(
        '100113.1300',
    );

    $migrators = array();
    $found = false;

    foreach ($versions as $version) {
        if ($found || version_compare($config_version, $version, '<')) {
            $found = true;
            $migrator_name = str_replace('.', '_', $version);
            $migrator_func = 'p2_migrate_' . $migrator_name;
            $migrator_file = '/migrators/' . $migrator_name . '.php';
            $migrators[$migrator_func] = $migrator_file;
        }
    }

    if ($found) {
        return $migrators;
    } else {
        return null;
    }
}

// }}}
// {{{ p2migrate()

/**
 * マイグレーションを実行
 *
 * @param array $user_conf 古いユーザー設定
 * @param array $migrators マイグレーション関数のリスト
 * @return array 新しいユーザー設定
 */
function p2migrate(array $user_conf, array $migrators)
{
    global $_conf;

    foreach ($migrators as $migrator_func => $migrator_file) {
        include P2_LIB_DIR . $migrator_file;
        $user_conf = $migrator_func($_conf, $user_conf);
    }

    return $user_conf;
}

// }}}
// {{{ p2autoload()

/**
 * クラスローダー
 *
 * @string $name
 * @return void
 */
function p2autoload($name)
{
    if (strncmp($name, 'Wap', 3) === 0) {
        include P2_LIB_DIR . '/Wap.php';
    }
    if (strncmp($name, 'P2Http', 6) === 0) {
        include P2_LIB_DIR . '/P2HttpExt.php';
    }
    if (preg_match('/^(?:
            BbsMap |
            BrdCtl |
            BrdMenu |
            DataPhp |
            FavSetManager |
            FileCtl |
            HostCheck |
            Login |
            NgAbornCtl |
            P2\\w+ |
            PresetManager |
            ResHist |
            Session |
            SettingTxt |
            ShowBrdMenu\\w* |
            ShowThread\\w* |
            StrCtl |
            StrSjis |
            SubjectTxt |
            Thread\\w*
        )$/x', $name))
    {
        include P2_LIB_DIR . '/' . $name . '.php';
    }
    if (preg_match('/^\\w+DataStore$/', $name)) {
        include P2_LIB_DIR . '/P2DataStore/' . $name . '.php';
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
