<?php
/*
	+live - �f�t�H���g�X���b�h�\�� ../ShowThreadPc.php ���ǂݍ��܂��
*/
        if ($_conf['backlink_block'] > 0) {
            // ��Q�ƃu���b�N�\���p��onclick��ݒ�
            $tores .= "<div id=\"{$res_id}\" class=\"res\" onclick=\"toggleResBlk(event, this, " . $_conf['backlink_block_readmark'] . ")\">\n";
        } else {
            $tores .= "<div id=\"{$res_id}\" class=\"res\">\n";
        }
        $tores .= "<div class=\"res-header\">";

        if ($this->thread->onthefly) {
            $GLOBALS['newres_to_show_flag'] = true;
            //�ԍ��i�I���U�t���C���j
            $tores .= "<span class=\"ontheflyresorder spmSW\"{$spmeh}>{$i}</span> : ";
        } elseif ($i > $this->thread->readnum) {
            $GLOBALS['newres_to_show_flag'] = true;
            // �ԍ��i�V�����X���j
            $tores .= "<span style=\"color:{$STYLE['read_newres_color']}\" class=\"spmSW\"{$spmeh}>{$i}</span> : ";
        } elseif ($_conf['expack.spm.enabled']) {
            // �ԍ��iSPM�j
            $tores .= "<span class=\"spmSW\"{$spmeh}>{$i}</span> : ";
        } else {
            // �ԍ�
            $tores .= "{$i} : ";
        }

// ���X�{�^��
if ($_GET['live']) {
	$tores .= "$res_button";
}

        // ���O
        $tores .= preg_replace('{<b>[ ]*</b>}i', '', "<span class=\"name\"><b>{$name}</b></span> : ");

        // ���[��
        if ($mail) {
            if (strpos($mail, 'sage') !== false && $STYLE['read_mail_sage_color']) {
                $tores .= "<span class=\"sage\">{$mail}</span> : ";
            } elseif ($STYLE['read_mail_color']) {
                $tores .= "<span class=\"mail\">{$mail}</span> : ";
            } else {
                $tores .= $mail . ' : ';
            }
        }

        // ID�t�B���^
        if ($_conf['flex_idpopup'] == 1 && $id && $this->thread->idcount[$id] > 1) {
            $date_id = str_replace($idstr, $this->idFilter($idstr, $id), $date_id);
        }

        $tores .= $date_id; // ���t��ID
        if ($this->am_side_of_id) {
            $tores .= ' ' . $this->activeMona->getMona($msg_id);
        }
        $tores .= "</div>\n"; // res-header�����

        // �탌�X���X�g(�c�`��)
        if ($_conf['backlink_list'] == 1 || $_conf['backlink_list'] > 2) {
            $tores .= $this->_quotebackListHtml($i, 1);
        }

        $tores .= "<div id=\"{$msg_id}\" class=\"{$msg_class}\">{$msg}</div>\n"; // ���e
        // �탌�X�W�J�p�u���b�N
        if ($_conf['backlink_block'] > 0) {
            $backlinks = $this->_getBacklinkComment($i);
            if (strlen($backlinks)) {
                $tores .= '<div class="resblock"><img src="img/btn_plus.gif" width="15" height="15" align="left"></div>';
                $tores .= $backlinks;
            }
        }
        // �탌�X���X�g(���`��)
        if ($_conf['backlink_list'] == 2 || $_conf['backlink_list'] > 2) {
            $tores .= $this->_quotebackListHtml($i, 2, false);
        }
        $tores .= "</div>\n";

//      $tores .= $rpop; // ���X�|�b�v�A�b�v�p���p

?>