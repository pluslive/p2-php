<?php
/**
 * rep2expack - �������݃f�[�^�Ǘ��N���X
 */

// {{{ PostDataStore

class PostDataStore extends AbstractDataStore
{
    // {{{ getKVS()

    /**
     * �������݃f�[�^��ۑ�����P2KeyValueStore�I�u�W�F�N�g���擾����
     *
     * @param void
     * @return P2KeyValueStore
     */
    static public function getKVS()
    {
        return self::_getKVS($GLOBALS['_conf']['post_db_path'],
                             P2KeyValueStore::CODEC_ARRAYSHIFTJIS);
    }

    // }}}
    // {{{ getKeyForBackup()

    /**
     * �������݃o�b�N�A�b�v�̂��߂̃L�[���擾����
     *
     * @param string $host
     * @param string $bbs
     * @param numeric $key
     * @param bool $newthread
     */
    static public function getKeyForBackup($host, $bbs, $key, $newthread = false)
    {
        if ($newthread) {
            $key = 'new';
        }
        return 'backup:' . self::_getKeySuffix($host, $bbs, $key);
    }

    // }}}
    // {{{ getKeyForConfig()

    /**
     * ��/�X�����Ƃ̏������ݐݒ�̂��߂̃L�[���擾����
     *
     * @param string $host
     * @param string $bbs
     * @param numeric $key
     * @param bool $newthread
     */
    static public function getKeyForConfig($host, $bbs, $key = null)
    {
        if ($key === null) {
            $key = '';
        }
        return 'config:' . self::_getKeySuffix($host, $bbs, $key);
    }

    // }}}
    // {{{ _getKeySuffix()

    /**
     * �L�[�̐ڔ����𐶐�����
     *
     * @param string $host
     * @param string $bbs
     * @param string $key
     * @param bool $newthread
     */
    static private function _getKeySuffix($host, $bbs, $key)
    {
        global $_login;

        return rtrim($_login->user_u . P2Util::pathForHostBbs($host, $bbs) . $key, '/');
    }

    // }}}
    // {{{ clearBackup()

    /**
     * ���ׂĂ̏������݃o�b�N�A�b�v�܂���
     * �w�肳�ꂽ���[�U�[�̏������݃o�b�N�A�b�v���폜����
     *
     * @param string $user
     * @return int
     * @see AbstractDataStore::clear()
     */
    static public function clearBackup($user = null)
    {
        $prefix = 'backup:';
        if ($user !== null) {
            $prefix .= $user . '/';
        }
        return self::clear($prefix);
    }

    // }}}
    // {{{ clearConfig()

    /**
     * ���ׂĂ̏������ݐݒ�܂��͎w�肳�ꂽ���[�U�[�̏������ݐݒ���폜����
     *
     * @param string $user
     * @return int
     * @see AbstractDataStore::clear()
     */
    static public function clearConfig($user = null)
    {
        $prefix = 'config:';
        if ($user !== null) {
            $prefix .= $user . '/';
        }
        return self::clear($prefix);
    }

    // }}}
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
