<?php
/*
使用例:
$hissi = new Hissi();
$hissi->host = $host; // 指定しない場合は2chとみなす
$hissi->bbs  = $bbs;
if ($hissi->isEnabled()) {
    // bbsの指定が必要
    echo $hissi->getBoardURL();
    $hissi->date = $date;
    // bbs, dateの指定が必要
    echo $hissi->getBoardDateURL();
    $hissi->id   = $id;
    // bbs, date, idの指定が必要
    echo $hissi->getIDURL();
}
*/
class Hissi
{
    public $boards; // array
    public $host;   // 板のホスト
    public $bbs;    // 板のディレクトリ名
    public $id;     // ID
    public $date;   // 日付をyyyymmddで指定
    protected $enabled;

    protected $hissiOrg = 'http://hissi.org';
    protected $menuPath = '/menu.php';
    protected $readPath = '/read.php';
    protected $menuUrl;
    protected $readUrl;
    protected $readPattern;

    /**
     * コンストラクタ
     */
    public function __construct(array $options = null)
    {
        if ($options) {
            $validOptions = array(
                'host', 'bbs', 'id', 'date',
                'hissiOrg', 'menuPath', 'readPath',
            );
            foreach ($validOptions as $option) {
                if (isset($options[$option])) {
                    $this->$option = $options[$option];
                }
            }
        }

        $this->menuUrl = $this->hissiOrg . $this->menuPath;
        $this->readUrl = $this->hissiOrg . $this->readPath;
        $this->readPattern = '@<a href='
            . preg_quote($this->readUrl, '@')
            . '/(\\w+)/>.+?</a><br>@';
    }

    /**
     * 必死チェッカー対応板を読み込む
     * 自動で読み込まれるので通常は実行する必要はない
     */
    public function load()
    {
        global $_conf;

        $path = P2Util::cacheFileForDL($this->menuUrl);
        // メニューのキャッシュ時間の10倍キャッシュ
        P2UtilWiki::cacheDownload($this->menuUrl, $path, $_conf['menu_dl_interval'] * 36000);

        $this->boards = array();
        $file = @file_get_contents($path);
        if ($file) {
            if (preg_match_all($this->readPattern, $file, $boards)) {
                $this->boards = $boards[1];
            }
        }
    }

    /**
     * 必死チェッカーに対応しているか調べる
     * $boardがなければloadも実行される
     */
    public function isEnabled()
    {
        if ($this->host) {
            if (!P2Util::isHost2chs($this->host)) {
                return false;
            }
        }

        if (!is_array($this->boards)) {
            $this->load();
        }
        $this->enabled = in_array($this->bbs, $this->boards) ? true : false;

        return $this->enabled;
    }

    /**
     * IDのURLを取得する
     * $all = trueで全てのスレッドを表示
     * isEnabled() == falseでも取得できるので注意
     */
    public function getIDURL($all = false, $page = 0)
    {
        $boardDateUrl = $this->getBoardDateURL();
        $id_en = rtrim(base64_encode($this->id), '=');
        $query = $all ? '?thread=all' : '';
        if ($page) {
            $query = $query ? "{$query}&p={$page}" : "?p={page}";
        }
        return "{$boardDateUrl}{$id_en}.html{$query}";
    }

    /**
     * 板のURLを設定する
     * isEnabled() == falseでも取得できるので注意
     */
    public function getBoardURL()
    {
        return "{$this->readUrl}/{$this->bbs}/";
    }

    /**
     * 板のその日付のURLを設定する
     * isEnabled() == falseでも取得できるので注意
     */
    public function getBoardDateURL()
    {
        $boardUrl = $this->getBoardURL();
        return "{$boardUrl}{$this->date}/";
    }
}
