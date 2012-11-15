<?php
/**
 * rep2expack - �V���܂Ƃߓǂ݃L���b�V���Ǘ��N���X
 */

// {{{ MatomeCacheDataStore

class MatomeCacheDataStore extends AbstractDataStore
{
    // {{{ getKVS()

    /**
     * �܂Ƃߓǂ݃f�[�^��ۑ�����P2KeyValueStore�I�u�W�F�N�g���擾����
     *
     * @param void
     * @return P2KeyValueStore
     */
    static public function getKVS()
    {
        return self::_getKVS($GLOBALS['_conf']['matome_db_path'],
                             P2KeyValueStore::CODEC_COMPRESSING);
    }

    // }}}
    // {{{ setRaw()

    /**
     * Codec�ɂ��ϊ��Ȃ��Ńf�[�^��ۑ�����
     *
     * @param string $key
     * @param string $value
     * @return bool
     */
    static public function setRaw($key, $value)
    {
        $kvs = self::getKVS()->getRawKVS();
        if ($kvs->exists($key)) {
            return $kvs->update($key, $value);
        } else {
            return $kvs->set($key, $value);
        }
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
