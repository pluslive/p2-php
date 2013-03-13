<?php
/*
ReplaceImageURL(url)        ���C���֐�
save(array)                 �f�[�^��ۑ�
load()                      �f�[�^��ǂݍ���ŕԂ�(�����I�Ɏ��s�����)
clear()                     �f�[�^���폜
*/

require_once __DIR__ . '/WikiPluginCtlBase.php';

class ReplaceImageUrlCtl extends WikiPluginCtlBase
{
    protected $filename = 'p2_replace_imageurl.txt';
    protected $data = array();

    // replace�̌��ʂ��O���t�@�C���ɃL���b�V������
    // �Ƃ肠�����O�����N�G�X�g�̔�������$EXTRACT����̏ꍇ�̂ݑΏ�
    // 500�n�̃G���[�������ꍇ�́A�L���b�V�����Ȃ�
    protected $cacheFilename = 'p2_replace_imageurl_cache.txt';
    protected $cacheData = array();
    protected $cacheIsLoaded = false;

    // �S�G���[���L���b�V�����Ė�������(�i�����͂��Ȃ��̂ō��񃊃N�G�X�g�̂݁j
    protected $extractErrors = array();
    // �SreplaceImageUrl���L���b�V������(�i�����͂��Ȃ��̂ō��񃊃N�G�X�g�̂݁j
    protected $onlineCache = array();

    public function clear()
    {
        global $_conf;

        $path = $_conf['pref_dir'] . '/' . $this->filename;

        return @unlink($path);
    }

    public function load()
    {
        global $_conf;

        $lines = array();
        $path = $_conf['pref_dir'].'/'.$this->filename;
        if ($lines = @file($path)) {
            foreach ($lines as $l) {
                if (substr($l, 0, 1) === ';' || substr($l, 0, 1) === "'" ||
                    substr($l, 0, 1) === '#' || substr($l, 0, 2) === '//') {
                    //"#" ";" "'" "//"����n�܂�s�̓R�����g
                    continue;
                }
                $lar = explode("\t", trim($l));
                if (strlen($lar[0]) == 0 || count($lar) < 2) {
                    continue;
                }
                $ar = array(
                    'match'   => $lar[0], // �Ώە�����
                    'replace' => $lar[1], // �u��������
                    'referer' => $lar[2], // ���t�@��
                    'extract' => $lar[3], // EXTRACT
                    'source'  => $lar[4], // EXTRACT���K�\��
                    'recheck'  => $lar[5], // EXTRACT�����y�[�W��������`�F�b�N��������
                    'ident'  => $lar[6],    // �u�����ʂ̉摜URL�ɑ΂��鐳�K
                                            // �\���B�w�肳��Ă���ꍇ�͂���
                                            // �Ń}�b�`����������őO��L���b
                                            // �V���Ɣ�r���A����ł���Γ���
                                            // �摜�ƌ���
                );

                $this->data[] = $ar;
            }
        }

        return $this->data;
    }

    /**
     * saveReplaceImageURL
     * $data[$i]['match']       Match
     * $data[$i]['replace']     Replace
     * $data[$i]['del']         �폜
     */
    public function save($data)
    {
        global $_conf;

        $path = $_conf['pref_dir'] . '/' . $this->filename;

        $newdata = '';

        foreach ($data as $na_info) {
            $a[0] = strtr(trim($na_info['match']  , "\t\r\n"), "\t\r\n", "   ");
            $a[1] = strtr(trim($na_info['replace'], "\t\r\n"), "\t\r\n", "   ");
            $a[2] = strtr(trim($na_info['referer'], "\t\r\n"), "\t\r\n", "   ");
            $a[3] = strtr(trim($na_info['extract'], "\t\r\n"), "\t\r\n", "   ");
            $a[4] = strtr(trim($na_info['source'] , "\t\r\n"), "\t\r\n", "   ");
            $a[5] = strtr(trim($na_info['recheck'] ,"\t\r\n"), "\t\r\n", "   ");
            $a[6] = strtr(trim($na_info['ident'] ,"\t\r\n"), "\t\r\n", "   ");
            if ($na_info['del'] || ($a[0] === '' || $a[1] === '')) {
                continue;
            }
            $newdata .= implode("\t", $a) . "\n";
        }
        return FileCtl::file_write_contents($path, $newdata);
    }


    public function load_cache()
    {
        global $_conf;
        $lines = array();
        $path = $_conf['pref_dir'].'/'.$this->cacheFilename;
        FileCtl::make_datafile($path);
        if ($lines = @file($path)) {
            foreach ($lines as $l) {
                list($key, $data) = explode("\t", trim($l));
                if (strlen($key) == 0 || strlen($data) == 0) continue;
                $this->cacheData[$key] = unserialize($data);
            }
        }
        $this->cacheIsLoaded = true;
        return $this->cacheData;
    }

    public function storeCache($key, $data)
    {
        global $_conf;

        if ($this->cacheData[$key]) {
            // overwrite the cache file
            $this->cacheData[$key] = $data;
            $body = '';
            foreach ($this->cacheData as $_k => $_v) {
                $body .= implode("\t", array($_k,
                    serialize(self::sanitizeForCache($_v)))
                ) . "\n";
            }
            return FileCtl::file_write_contents($_conf['pref_dir'] . '/'
                . $this->cacheFilename, $body);
        } else {
            // append to the cache file
            $this->cacheData[$key] = $data;
            return FileCtl::file_write_contents(
                $_conf['pref_dir'] . '/' . $this->cacheFilename,
                implode("\t", array($key,
                    serialize(self::sanitizeForCache($data)))
                ) . "\n",
                FILE_APPEND
            );
        }
    }

    public static function sanitizeForCache($data)
    {
        if (is_array($data)) {
            foreach (array_keys($data) as $k) {
                $data[$k] = self::sanitizeForCache($data[$k]);
            }
            return $data;
        } else {
            return str_replace(array("\t", "\r", "\n"), '', $data);
        }
    }

    /**
     * replaceImageUrl
     * �����N�v���O�C�������s
     * return array
     *      $ret[$i]['url']     $i�Ԗڂ�URL
     *      $ret[$i]['referer'] $i�Ԗڂ̃��t�@��
     */
    public function replaceImageUrl($url)
    {
        global $_conf;
        // http://janestyle.s11.xrea.com/help/first/ImageViewURLReplace.html

        $this->setup();
        $src = false;

        if (array_key_exists($url, $this->onlineCache)) {
            return $this->onlineCache[$url];
        }

        if ($this->cacheData[$url]) {
            if ($_conf['wiki.replaceimageurl.extract_cache'] == 1) {
                // �L���b�V��������΂����Ԃ�
                return $this->_reply($url, $this->cacheData[$url]['data']);
            }
            if ($this->cacheData[$url]['lost']) {
                // �y�[�W�������Ă���ꍇ�L���b�V����Ԃ�
                return $this->_reply($url, $this->cacheData[$url]['data']);
            }
            if ($_conf['wiki.replaceimageurl.extract_cache'] == 3) {
                // �O��L���b�V���Ō��ʂ������ĂȂ���΂��
                if (array_key_exists('data', $this->cacheData[$url]) && is_array($this->cacheData[$url]['data'])
                    && count($this->cacheData[$url]['data']) == 0) {
                    return $this->_reply($url, $this->cacheData[$url]['data']);
                }
            }
        }
        foreach ($this->data as $v) {
            if (preg_match('{^'.$v['match'].'$}', $url)) {
                $match = $v['match'];
                $replace = str_replace('$&', '$0', $v['replace']);
                $referer = str_replace('$&', '$0', $v['referer']);
                // ���u��(Match��Replace, Match��Referer)
                $replace = @preg_replace ('{'.$match.'}', $replace, $url);
                $referer = @preg_replace ('{'.$match.'}', $referer, $url);
                // $EXTRACT������ꍇ
                // ��:$COOKIE, $COOKIE={URL}, $EXTRACT={URL}�ɂ͖��Ή�
                // $EXTRACT={URL}�̎����͗e��
                if (strstr($v['extract'], '$EXTRACT')){
                    if ($_conf['wiki.replaceimageurl.extract_cache'] == 2) {
                        if ($this->cacheData[$url] && !$v['recheck']) {
                            return $this->_reply($url, $this->cacheData[$url]['data']);
                        }
                    }
                    $source = $v['source'];
                    $return = $this->extractPage($url, $match, $replace, $referer, $source, $v['ident']);
                } else {
                    $return[0]['url']     = $replace;
                    $return[0]['referer'] = $referer;
                }
                break;
            }
        }
        /* plugin_imageCache2�ŏ��������邽�߃R�����g�A�E�g
        // �q�b�g���Ȃ������ꍇ
        if (!$return[0]) {
            // �摜���ۂ�URL�̏ꍇ
            if (preg_match('{^https?://.+?\\.(jpe?g|gif|png)$}i', $url)) {
                $return[0]['url']     = $url;
                $return[0]['referer'] = '';
            }
        }
        */
        return $this->_reply($url, $return);
    }

    protected function _reply($url, $data)
    {
        $this->onlineCache[$url] = $data;
        return $data;
    }

    public function extractPage($url, $match, $replace, $referer, $source, $ident=null)
    {
        global $_conf;

        $ret = array();

        $source =  @preg_replace ('{'.$match.'}', $source, $url);
        $get_url = $referer;
        if ($this->extractErrors[$get_url]) {
            // ���񃊃N�G�X�g�ŃG���[�������ꍇ
            return ($this->cacheData[$url] && $this->cacheData[$url]['data'])
                ? $this->cacheData[$url]['data'] : $ret;
        }

        $params = array();
        $params['timeout'] = $_conf['http_conn_timeout'];
        $params['readTimeout'] = array($_conf['http_read_timeout'], 0);
        if ($_conf['proxy_use']) {
            $params['proxy_host'] = $_conf['proxy_host'];
            $params['proxy_port'] = $_conf['proxy_port'];
        }
        $req = new HTTP_Request($get_url, $params);
        if ($this->cacheData[$url] && $this->cacheData[$url]['responseHeaders']
                && $this->cacheData[$url]['responseHeaders']['last-modified']
                && strlen($this->cacheData[$url]['responseHeaders']['last-modified'])) {
            $req->addHeader("If-Modified-Since",
                $this->cacheData[$url]['responseHeaders']['last-modified']);
        }
        $req->addHeader('User-Agent',
            (!empty($_conf['expack.user_agent'])) ? $_conf['expack.user_agent']
            : $_SERVER['HTTP_USER_AGENT']);
        $response = $req->sendRequest();
        $code = $req->getResponseCode();
        if (PEAR::isError($response) || ($code != 200 && $code != 206 && $code != 304)) {
            $errmsg = PEAR::isError($response) ? $response->getMessage() : $code;
            // ���񃊃N�G�X�g�ł̃G���[���I�����C���L���b�V��
            $this->extractErrors[$get_url] = $errmsg;
            // �T�[�o�G���[�ȊO�Ȃ�i���L���b�V���ɕۑ�
            if ($code && $code < 500) {
                // �y�[�W�������Ă���ꍇ
                if ($this->_checkLost($url, $ret)) {
                    return $this->cacheData[$url]['data'];
                }
                $this->storeCache($url, array('code' => $code,
                    'errmsg' => $errmsg,
                    'responseHeaders' => $req->getResponseHeader(),
                    'data' => $ret));
            }
            return ($this->cacheData[$url] && $this->cacheData[$url]['data'])
                ? $this->cacheData[$url]['data'] : $ret;
        }
        if ($code == 304 && $this->cacheData[$url]) {
            return $this->cacheData[$url]['data'];
        }

        $body = $req->getResponseBody();
        preg_match_all('{' . $source . '}i', $body, $extracted, PREG_SET_ORDER);
        foreach ($extracted as $i => $extract) {
            $_url = $replace; $_referer = $referer;
            foreach ($extract as $j => $part) {
                if ($j < 1) continue;
                $_url       = str_replace('$EXTRACT'.$j, $part, $_url);
                $_referer   = str_replace('$EXTRACT'.$j, $part, $_referer);
            }
            if ($extract[1]) {
                $_url       = str_replace('$EXTRACT', $part, $_url);
                $_referer   = str_replace('$EXTRACT', $part, $_referer);
            }
            $ret[$i]['url']     = $_url;
            $ret[$i]['referer'] = $_referer;
        }

        // �y�[�W�������Ă���ꍇ
        if ($this->_checkLost($url, $ret)) {
            return $this->cacheData[$url]['data'];
        }

        if ($ident && $this->cacheData[$url] && $this->cacheData[$url]['data']) {
            $ret = self::_identByCacheData($ret, $this->cacheData[$url]['data'], $ident);
        }

        // ���ʂ��i���L���b�V���ɕۑ�
        $this->storeCache($url, array('code' => $code,
            'responseHeaders' => $req->getResponseHeader(),
            'data' => $ret));

        return $ret;
    }

    protected function _checkLost($url, $data)
    {
        if (count($data) == 0 && $this->cacheData[$url] &&
                $this->cacheData[$url]['data'] &&
                count($this->cacheData[$url]['data']) > 0) {
            $rec = $this->cacheData[$url];
            $rec['lost'] = true;
            $this->storeCache($url, $rec);
            return true;
        }
        return false;
    }

    /**
     * �O��L���b�V���̓��e�ɍ���擾�̉摜URL�����邩��
     * �w��̐��K�\���ŒT���A�������ꍇ�̓L���b�V���̂��̂�
     * �g�p����悤�ɒu��������.
     *
     * �摜URL�ɔF�ؗp�N�G���Ȃǂ��t���Ă���A
     * �t�@�C�����ɋK���I�ɃZ�b�V���������񂪕t���A
     * �Ȃǂ̏ꍇ�ł������摜�����ɂ����Ȃ��悤�ɂ���������.
     */
    protected static function _identByCacheData($data, $cache, $identRegex)
    {
        $ret = $data;
        foreach ($ret as &$d) {
            $ident_match = array();
            if (!preg_match('{^'.$identRegex.'}', $d['url'], $ident_match)) {
                continue;
            }

            // �}�b�`��������Q�Ƃ�����Ȃ炻�ꂾ����r�������̂�
            // �}�b�`�S��[0]��h��Ԃ�
            if (count($ident_match) > 1) $ident_match[0] = '';

            foreach ($cache as $c) {
                $ident_cache_match = array();
                if (!preg_match('{^'.$identRegex.'}', $c['url'],
                    $ident_cache_match))
                {
                    continue;
                }

                // �}�b�`��������Q�Ƃ�����Ȃ炻�ꂾ����r�������̂�
                // �}�b�`�S��[0]��h��Ԃ�
                if (count($ident_cache_match) > 1) {
                    $ident_cache_match[0] = '';
                }

                if ($ident_match === $ident_cache_match) {
                    // �L���b�V���f�[�^���g�p����
                    $d = $c;
                    break;
                }
            }
        }
        unset($d);
        return $ret;
    }

}
