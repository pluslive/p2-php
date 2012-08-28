<?php
/*
	+live - ユーザ設定 デフォルト このファイルはデフォルト値の設定なので、特に変更する必要はありません
*/

// {{{ ■表示設定

// レス表示の種類
$conf_user_def['live.view_type'] = "1"; // ("1")
$conf_user_sel['live.view_type'] = array('0' => '常にデフォルト表示', '1' => '実況時のみ実況用表示', '2' => '常に実況用表示');

// ID末尾の O (携帯) P (公式p2) Q (フルブラウザ) I (iPhone) を太字に
$conf_user_def['live.id_b'] = 0; // (0)
$conf_user_rad['live.id_b'] = array('1' => 'する', '0' => 'しない');

// 連鎖ハイライト (表示範囲のレスのみに連鎖)
$conf_user_def['live.highlight_chain'] = 0; // (0)
$conf_user_rad['live.highlight_chain'] = array('1' => 'する', '0' => 'しない');

// YouTubeプレビュー表示のサイズ
$conf_user_def['live.youtube_winsize'] = 0; // (0)
$conf_user_sel['live.youtube_winsize'] = array('0' => 'ノーマルサイズ', '1' => 'ハーフサイズ');

// }}}
// {{{ ■実況中設定

// 表示するレス数 (100以下推奨)
$conf_user_def['live.before_respointer'] = "50"; // ("50")

// 下部書込フレームの高さ (px)
$conf_user_def['live.post_width'] = "85"; // ("85")

// デフォルトの名無しの表示
$conf_user_def['live.bbs_noname'] = 0; // (0)
$conf_user_rad['live.bbs_noname'] = array('1' => 'する', '0' => 'しない');

// sage を ▼ に
$conf_user_def['live.mail_sage'] = 1; // (1)
$conf_user_rad['live.mail_sage'] = array('1' => 'する', '0' => 'しない');

// 全ての改行とスペースの削除
$conf_user_def['live.msg'] = 1; // (1)
$conf_user_rad['live.msg'] = array('1' => 'する', '0' => 'しない');

// [これにレス] の方法
$conf_user_def['live.res_button'] = 0; // (0)
$conf_user_sel['live.res_button'] = array('0' => '[ re: ] ボタン', '1' => '両方', '2' => '内容をダブルクリック');

// 書込規制用タイマー
$conf_user_def['live.write_regulation'] = 0; // (0)
$conf_user_sel['live.write_regulation'] = array('0' => 'タイマー無し', '1' => '15秒', '2' => '20秒', '3' => '30秒', '4' => '40秒', '5' => '50秒', '6' => '1分');

// ImageCache2のサムネイル作成
$conf_user_def['live.ic2_onoff'] = "1"; // ("1")
$conf_user_rad['live.ic2_onoff'] = array('1' => 'on', '0' => 'off');

// }}}
// {{{ ■リロード/スクロール

// オートリロードの間隔
$conf_user_def['live.reload_time'] = 2; // (2)
$conf_user_sel['live.reload_time'] = array('0' => 'リロード無し', '1' => '5秒', '2' => '10秒', '3' => '15秒', '4' => '20秒');

// オートスクロールの滑らかさ (最も滑らか 1 、スクロール無し 0)
$conf_user_def['live.scroll_move'] = 3; // (3)

// オートスクロールの速度 (最速 1 、スクロール無しの場合は上の滑らかさの値を 0 に)
$conf_user_def['live.scroll_speed'] = 10; // (10)

// }}}
?>
