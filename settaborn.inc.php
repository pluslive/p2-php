<?php
// p2 - スレッドあぼーんの関数

require_once './p2util.class.php';
require_once './filectl.class.php';

/**
 * スレッドあぼーんをオンオフする
 *
 * $set は、0(解除), 1(追加), 2(トグル)
 */
function settaborn($host, $bbs, $key, $set)
{
    global $_conf, $title_msg, $info_msg;
    
    //==================================================================
    // key.idx 読み込む
    //==================================================================
    
    // idxfileのパスを求めて
    $datdir_host = P2Util::datdirOfHost($host);
    $idxfile = "{$datdir_host}/{$bbs}/{$key}.idx";
    
    // データがあるなら読み込む
    if (is_readable($idxfile)) {
        $lines = @file($idxfile);
        $l = rtrim($lines[0]);
        $data = explode('<>', $l);
    }
    
    //==================================================================
    // p2_threads_aborn.idxに書き込む
    //==================================================================
    
    // p2_threads_aborn.idx のパス取得
    $datdir_host = P2Util::datdirOfHost($host);
    $taborn_idx = "{$datdir_host}/{$bbs}/p2_threads_aborn.idx";
    
    // p2_threads_aborn.idx がなければ生成
    FileCtl::make_datafile($taborn_idx, $_conf['p2_perm']);
    
    // p2_threads_aborn.idx 読み込み;
    $taborn_lines= @file($taborn_idx);
    
    $neolines = array();
    
    if ($taborn_lines) {
        foreach ($taborn_lines as $line) {
            $line = rtrim($line);
            $lar = explode('<>', $line);
            if ($lar[1] == $key) {
                $aborn_attayo = true; // 既にあぼーん中である
                if ($set == 0 or $set == 2) {
                    $title_msg_pre = "+ あぼーん 解除しますた";
                    $info_msg_pre = "+ あぼーん 解除しますた";
                }
                continue;
            }
            if (!$lar[1]) { continue; } // keyのないものは不正データ
            $neolines[] = $line;
        }
    }
    
    // 新規データ追加
    if ($set == 1 or !$aborn_attayo && $set == 2) {
        $newdata = "$data[0]<>{$key}<><><><><><><><>";
        $neolines ? array_unshift($neolines, $newdata) : $neolines = array($newdata);
        $title_msg_pre = "○ あぼーん しますた";
        $info_msg_pre = "○ あぼーん しますた";
    }
    
    // 書き込む
    $cont = '';
    if (!empty($neolines)) {
        foreach ($neolines as $l) {
            $cont .= $l."\n";
        }
    }
    if (FileCtl::file_write_contents($taborn_idx, $cont) === false) {
        die('Error: cannot write file.');
    }
    
    $GLOBALS['title_msg'] = $title_msg_pre;
    $GLOBALS['info_msg'] = $info_msg_pre;
    
    return true;
}

?>
