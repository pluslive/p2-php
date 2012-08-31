<?php
/**
 * rep2 - txt を 表示
 */

require_once __DIR__ . '/../init.php';

$_login->authorize(); // ユーザ認証

// 引数エラー
if (!isset($_GET['file'])) {
    p2die('file が指定されていません');
}

//=========================================================
// 変数
//=========================================================
$file = (isset($_GET['file'])) ? $_GET['file'] : null;
$encode = 'Shift_JIS';

//=========================================================
// 前処理
//=========================================================
// 読み込めるファイルを限定する
$readable_files = array('doc/README.txt', 'doc/README-EX.txt', 'doc/ChangeLog.txt');

if ($readable_files && $file && (!in_array($file, $readable_files))) {
    $files_st = '「' . implode('」と「', $readable_files) . '」';
    p2die(basename($_SERVER['SCRIPT_NAME'])." 先生の読めるファイルは、{$files_st}だけ！");
}

//=========================================================
// HTMLプリント
//=========================================================
// 読み込むファイルは拡張子.txtだけ
if (preg_match('/\\.txt$/i', $file)) {
    viewTxtFile($file, $encode);
} else {
    p2die("error: cannot view '{$file}'");
}

// {{{ viewTxtFile()

/**
 * ファイル内容を読み込んで表示する関数
 */
function viewTxtFile($file, $encode)
{
    if (!$file) {
        p2die('file が指定されていません');
    }

    $filename = basename($file);
    $ptitle = $filename;

    //ファイル内容読み込み
    $cont = FileCtl::file_read_contents(P2_BASE_DIR . DIRECTORY_SEPARATOR . $file);
    if ($cont === false) {
        $cont_area = '';
    } else {
        if (strcasecmp($encode, 'EUC-JP') === 0) {
            $cont = mb_convert_encoding($cont, 'CP932', 'CP51932');
        } elseif (strcasecmp($encode, 'UTF-8') === 0) {
            $cont = mb_convert_encoding($cont, 'CP932', 'UTF-8');
        }
        $cont_area = p2h($cont);
    }

    // プリント
    echo <<<EOHEADER
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=Shift_JIS">
    <meta name="ROBOTS" content="NOINDEX, NOFOLLOW">
    {$_conf['extra_headers_ht']}
    <title>{$ptitle}</title>
    <link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
</head>
<body onload="top.document.title=self.document.title;">\n
EOHEADER;

    P2Util::printInfoHtml();
    echo "<pre>";
    echo $cont_area;
    echo "</pre>";
    echo '</body></html>';

    return true;
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
