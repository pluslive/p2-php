<?php
/**
 * rep2expack - Cookie�Ǘ��N���X
 */

// {{{ CookieDataStore

class CookieDataStore extends AbstractDataStore
{
    // {{{ getKVS()

    /**
     * Cookie��ۑ�����P2KeyValueStore�I�u�W�F�N�g���擾����
     *
     * @param void
     * @return P2KeyValueStore
     */
    static public function getKVS()
    {
        return self::_getKVS($GLOBALS['_conf']['cookie_db_path']);
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
