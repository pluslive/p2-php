<?php
/*
�g�p��:
$hissi = new Hissi();
$hissi->host = $host; // �w�肵�Ȃ��ꍇ��2ch�Ƃ݂Ȃ�
$hissi->bbs  = $bbs;
if ($hissi->isEnabled()) {
    // bbs�̎w�肪�K�v
    echo $hissi->getBoardURL();
    $hissi->date = $date;
    // bbs, date�̎w�肪�K�v
    echo $hissi->getBoardDateURL();
    $hissi->id   = $id;
    // bbs, date, id�̎w�肪�K�v
    echo $hissi->getIDURL();
}
*/
class Hissi
{
    public $boards; // array
    public $host;   // �̃z�X�g
    public $bbs;    // �̃f�B���N�g����
    public $id;     // ID
    public $date;   // ���t��yyyymmdd�Ŏw��
    protected $enabled;

    protected $hissiOrg = 'http://hissi.org';
    protected $menuPath = '/menu.php';
    protected $readPath = '/read.php';
    protected $menuUrl;
    protected $readUrl;
    protected $readPattern;

    /**
     * �R���X�g���N�^
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
     * �K���`�F�b�J�[�Ή���ǂݍ���
     * �����œǂݍ��܂��̂Œʏ�͎��s����K�v�͂Ȃ�
     */
    public function load()
    {
        global $_conf;

        $path = P2Util::cacheFileForDL($this->menuUrl);
        // ���j���[�̃L���b�V�����Ԃ�10�{�L���b�V��
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
     * �K���`�F�b�J�[�ɑΉ����Ă��邩���ׂ�
     * $board���Ȃ����load�����s�����
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
     * ID��URL���擾����
     * $all = true�őS�ẴX���b�h��\��
     * isEnabled() == false�ł��擾�ł���̂Œ���
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
     * ��URL��ݒ肷��
     * isEnabled() == false�ł��擾�ł���̂Œ���
     */
    public function getBoardURL()
    {
        return "{$this->readUrl}/{$this->bbs}/";
    }

    /**
     * �̂��̓��t��URL��ݒ肷��
     * isEnabled() == false�ł��擾�ł���̂Œ���
     */
    public function getBoardDateURL()
    {
        $boardUrl = $this->getBoardURL();
        return "{$boardUrl}{$this->date}/";
    }
}
