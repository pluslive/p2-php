<?php
// p2 - �X���b�h��\������ �N���X

class ShowThread{
	var $thread; //�X���b�h�I�u�W�F�N�g
	
	function ShowThread($aThread){
		$this->thread = $aThread;
	}

	//===================================
	// �������ځ[�񃁃\�b�h
	//===================================
	function aborn($aborn_list, $resfield){
		if($aborn_list){
			foreach($aborn_list as $an_aborn_word){
				$an_aborn_word = trim($an_aborn_word);
				if( @strstr($resfield, $an_aborn_word) ){
					return true;
				}
			}
		}
		return false;
	}

	//===================================
	// NG�`�F�b�N���\�b�h
	//===================================
	function ngCheck($ng_list, $resfield){
		if($ng_list){
			foreach($ng_list as $a_ng_word){
				$a_ng_word = trim($a_ng_word);
				if( @strstr($resfield, $a_ng_word) ){
					return $a_ng_word;
				}
			}
		}
		return false;
	}

}
?>