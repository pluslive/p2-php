<?php
// p2 - �X���b�h���X�g�N���X

//=============================================================================
// ThreadList �N���X
//=============================================================================

class ThreadList{
	var $threads; //�N���XThread�̃I�u�W�F�N�g���i�[����z��
	var $num; //�i�[���ꂽThread�I�u�W�F�N�g�̐�
	var $host; // ex)pc.2ch.net
	var $bbs; // ex)mac
	var $itaj; //�� ex)�V�Emac��
	var $spmode; //���ʔȊO�̃X�y�V�������[�h
	var $ptitle; //�y�[�W�^�C�g��
	
	//==============================================
	function ThreadList(){
		$this->num=0;
	}
	
	//==============================================
	function setSpMode($name){
		global $ktai;
		if($name=="recent"){
			$this->spmode=$name;
			$ktai?$this->ptitle="�ŋߓǂ񂾽�":$this->ptitle="�ŋߓǂ񂾃X��";
		}elseif($name=="res_hist"){
			$this->spmode=$name;
			$this->ptitle="�������ݗ���";
		}elseif($name=="fav"){
			$this->spmode=$name;
			$ktai?$this->ptitle="���C�ɽ�":$this->ptitle="���C�ɃX��";
		}elseif($name=="taborn"){
			$this->spmode=$name;
			$ktai?$this->ptitle="$this->itaj (���ݒ�)":$this->ptitle="$this->itaj (���ځ[��)";
		}elseif($name=="soko"){
			$this->spmode=$name;
			$this->ptitle="$this->itaj (dat�q��)";
		}elseif($name=="palace"){
			$this->spmode=$name;
			$ktai?$this->ptitle="�ڂ̓a��":$this->ptitle="�X���̓a��";
		}elseif($name=="news"){
			$this->spmode=$name;
			$ktai?$this->ptitle="ƭ������":$this->ptitle="�j���[�X�`�F�b�N";
		}
	}
	
	//==============================================
	// setIta -- �����Z�b�g���郁�\�b�h
	//==============================================
	function setIta($host, $bbs, $itaj=""){
		$this->host = $host;
		$this->bbs = $bbs;
		if(!$this->itaj){
			if($itaj){
				$this->itaj = $itaj;
			}else{
				$this->itaj = $bbs;
			}
		}
		$this->ptitle = $this->itaj;
	}
	
	/**
	 * �� readList ���\�b�h
	 */
	function readList()
	{
		global $rctfile, $favlistfile, $datdir, $prefdir, $word_fm, $debug, $prof, $_info_msg_ht;
		
		if ($this->spmode) {
		
			// ���[�J���̗����t�@�C�� �ǂݍ���
			if ($this->spmode == "recent") {
				if ($lines = @file($rctfile)) {
					//$_info_msg_ht = "<p>�����͋���ۂł�</p>";
					//return false;
				}
			
			// ���[�J���̏������ݗ����t�@�C�� �ǂݍ���
			} elseif ($this->spmode == "res_hist") {
				$rh_idx = $prefdir."/p2_res_hist.idx";
				if ($lines = @file($rh_idx)) {
					//$_info_msg_ht = "<p>�������ݗ����͋���ۂł�</p>";
					//return false;
				}
			
			//���[�J���̂��C�Ƀt�@�C�� �ǂݍ���
			} elseif ($this->spmode == "fav") {
				if ($lines = @file($favlistfile)) {
					//$_info_msg_ht = "<p>���C�ɃX���͋���ۂł�</p>";
					//return false;
				}
			
			// �j���[�X�n�T�u�W�F�N�g�ǂݍ���
			} elseif ($this->spmode == "news") {
			
				unset($news);
				$news[] = array(host=>"news2.2ch.net", bbs=>"newsplus"); // �j���[�X����+
				$news[] = array(host=>"news2.2ch.net", bbs=>"liveplus"); // �j���[�X����
				$news[] = array(host=>"book.2ch.net", bbs=>"bizplus"); // �r�W�l�X�j���[�X����+
				$news[] = array(host=>"live2.2ch.net", bbs=>"news"); // �j���[�X����
				$news[] = array(host=>"news3.2ch.net", bbs=>"news2"); // �j���[�X�c�_
				
				foreach ($news as $n) {

					$datdir_host = datdirOfHost($n['host']);
					$subject_url = "http://".$n['host']."/".$n['bbs']."/subject.txt";
					$subjectfile = $datdir_host."/".$n['bbs']."/subject.txt";
			
					FileCtl::mkdir_for($subjectfile); // �f�B���N�g����������΍��
		
					subjectDownload($subject_url, $subjectfile);
					
					if (extension_loaded('zlib') and strstr($n['host'], ".2ch.net")) {
						$slines = @gzfile($subjectfile);
					} else {
						$slines = @file($subjectfile);
					}
					if ($slines) {
						foreach ($slines as $l) {
							if (preg_match("/^([0-9]+)\.(dat|cgi)(,|<>)(.+) ?(\(|�i)([0-9]+)(\)|�j)/", $l, $matches)) {
								//$this->isonline = true;
								unset($al);
								$al['key'] = $matches[1];
								$al['ttitle'] = rtrim($matches[4]);
								$al['rescount'] = $matches[6];
								$al['host'] = $n['host'];
								$al['bbs'] = $n['bbs'];
								$lines[] = $al;
							}
						}
					}
				}
		
			// p2_threads_aborn.idx �ǂݍ���
			} elseif ($this->spmode=="taborn") {
				$datdir_host = datdirOfHost($this->host);
				$lines = @file($datdir_host."/".$this->bbs."/p2_threads_aborn.idx");
			
			// dat�q�� ======================
			} elseif ($this->spmode == "soko") {

				$itadir = datdirOfHost($this->host)."/".$this->bbs;
				$lines = array();
				
				$debug && $prof->startTimer( "dat" );//
				// ���O�f�B���N�g���𑖍����ČǗ�dat��idx�t�� =================
				if ($cdir = dir($itadir)) { // or die ("���O�f�B���N�g�����Ȃ���I");
					// �f�B���N�g������
					while ($entry=$cdir->read()) {
						if (preg_match("/([0-9]+)\.dat$/",$entry, $matches)) {
							$theidx = $itadir."/".$matches[1].".idx";
							if (!file_exists($theidx)) {
								if ($datlines = @file($itadir."/".$entry)) {
									$firstdatline = rtrim($datlines[0]);
									if (strstr($firstdatline, "<>")) {
										$datline_sepa = "<>";
									} else {
										$datline_sepa = ",";
									}
									$d = explode($datline_sepa, $firstdatline);
									$atitle = $d[4];
									$arnum = sizeof($datlines);
									$anewline = $arnum;
									$data = "$atitle<>$matches[1]<><>$arnum<><><><><><>$anewline\n";
									setKeyIdx($theidx, $data);
								}
							}
							// array_push($lines, $idl[0]);
						}
					}
					$cdir->close();
				}			
				$debug && $prof->stopTimer( "dat" );//
				
				$debug && $prof->startTimer( "idx" );//
				//���O�f�B���N�g���𑖍�����idx���𒊏o���ă��X�g��===========
				if ($cdir = dir($itadir)) { // or die ("���O�f�B���N�g�����Ȃ���I");
					//�f�B���N�g������
					while ($entry = $cdir->read()) {
						if (preg_match("/\.idx$/", $entry)) {
							$idl = @file($itadir."/".$entry);
							array_push($lines, $idl[0]);
						}
					}
					$cdir->close();
				}
				$debug && $prof->stopTimer( "idx" );//
				
			} elseif ($this->spmode == "palace") { // p2_palace.idx �ǂݍ���
				$palace_idx = $prefdir. '/p2_palace.idx';
				if ($lines = @file($palace_idx)) {
					//$_info_msg_ht = "<p>�a���͂����ǂ��ł�</p>";
					//return false;
				}
			}
		
		// �I�����C����� subject.txt ��ǂݍ��ށi�m�[�}�����[�h�j
		} else {
			
			$datdir_host = datdirOfHost($this->host);
			$subject_url = "http://".$this->host."/".$this->bbs."/subject.txt";
			$subjectfile = $datdir_host."/".$this->bbs."/subject.txt";
	
			FileCtl::mkdir_for($subjectfile); // �f�B���N�g����������΍��

			// subject�_�E�����[�h
			subjectDownload($subject_url, $subjectfile);
			
			if (extension_loaded('zlib') and strstr($this->host, ".2ch.net")) {
				$lines = @gzfile($subjectfile);
			} else {
				$lines = @file($subjectfile);
			}
			
			// JBBS@������΂Ȃ�d���X���^�C���폜����
			if (P2Util::isHostJbbsShitaraba($this->host)) {
				$lines = array_unique($lines);
			}
			
			// be.2ch.net �Ȃ�EUC��SJIS�ϊ�
			if (P2Util::isHostBe2chNet($this->host)) {
				include_once './strctl_class.inc';
				$lines = array_map(create_function('$str', 'return StrCtl::p2EUCtoSJIS($str);'), $lines);
			}
			
		}
		return $lines;
	}
	
	/**
	 * �� addThread ���\�b�h
	 */
	function addThread($aThread)
	{
		$this->threads[] = $aThread;
		$this->num++;
		return $this->num;
	}

}

?>