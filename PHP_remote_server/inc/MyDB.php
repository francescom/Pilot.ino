<?php

// v. 1.3 added lookupw() lookupSql() and modified lookup(), added delete() and countQuery() selectQuery() deleteQuery() updateQuery() replaceQuery() insertQuery()
// v. 1.2 added countSql()
// v. 1.1 added collectSql()


class MyDB {

	public $gMy_db_link;


	public function startConnectionArea() {
		$this->gMy_db_link=mysqli_connect(DB_SERVER,DB_SERVER_USERNAME,DB_SERVER_PASSWORD,DB_DATABASE);	
	
		if (mysqli_connect_errno($this->gMy_db_link)) {
			echo( "Failed to connect to MySQL: " . mysqli_connect_error());
			die;
		}

	
	}

	public function stopConnectionArea() {
		mysqli_close($this->gMy_db_link);
		$this->gMy_db_link=null;
	}


	public function query($sql) {
		$result=mysqli_query ($this->gMy_db_link,$sql) or die('<br>Query "'.$sql.'" failed : ' .mysql_error().'<br>');
		return $result;
	}

	public function num_rows($query_id) {
		return mysqli_num_rows ($query_id);
	}

	public function fetchArray($prod_fnd_id,$Const=MYSQLI_ASSOC) {
		if($Const===MYSQLI_ASSOC) return mysqli_fetch_assoc($prod_fnd_id);
		else return mysqli_fetch_array($prod_fnd_id,$Const);
	}

	public function freeResult($prod_fnd_id) {
		return mysqli_free_result($prod_fnd_id);
	}
	
	public function queryWId($sql) {
		$result1=mysqli_query ($sql) or die('<br>Query "'.$sql.'" failed : ' .mysqli_error($this->gMy_db_link).'<br>');
		if($result1===FALSE) return FALSE;
		else if($result1===TRUE) {
			$result2=mysqli_insert_id();
			return $result2;
		} else return $result1;
	}

	public function lookupw($LookupTable,$WhereArray,$ReturnColumn='',$SortBy='') {
		$sql =$this->selectQuery($LookupTable,$ReturnColumn,$WhereArray,'0,1',$SortBy);
		
		$prod_fnd_id = $this->query($sql);
		$prod_fnd_num = $this->num_rows($prod_fnd_id);
		if($prod_fnd_num==0) return FALSE;
		
		$Arr=$this->fetchArray($prod_fnd_id ,MYSQL_ASSOC);

		$this->freeResult($prod_fnd_id);

		if($ReturnColumn=='') return $Arr;
		if(isset($Arr[$ReturnColumn])) return $Arr[$ReturnColumn];
	 }

	public function lookup($LookupTable,$LookupCol /* if empty...*/,$LookupVal /* =$WhereArray */,$ReturnColumn='',$SortBy='') {

		if(is_array($LookupCol)) return $this->lookupw($LookupTable,/*$WhereArray=*/$LookupCol,/*$ReturnColumn=*/$LookupVal,/*$SortBy=*/$ReturnColumn);
		if(FALSE!==strpos($LookupCol, '=')) return $this->lookupw($LookupTable,/*$WhereArray=*/$LookupCol,/*$ReturnColumn=*/$LookupVal,/*$SortBy=*/$ReturnColumn);
	
		if($LookupCol=='') {
			$sql =$this->selectQuery($LookupTable,$ReturnColumn,$LookupVal,'0,1',$SortBy);
		} else {
			$sql = 'SELECT * FROM `'.$LookupTable.'` WHERE `'.$LookupCol.'` = \''.$this->escape($LookupVal).'\'';
		}
		$prod_fnd_id = $this->query($sql);
		$prod_fnd_num = $this->num_rows($prod_fnd_id);
		if($prod_fnd_num==0) return FALSE;
		
		$Arr=$this->fetchArray($prod_fnd_id ,MYSQL_ASSOC);

		$this->freeResult($prod_fnd_id);

		if($ReturnColumn=='') return $Arr;
		if(isset($Arr[$ReturnColumn])) return $Arr[$ReturnColumn];
	 }

	public function lookupSql($sql,$ReturnColumn='') {

		$prod_fnd_id = $this->query($sql);
		$prod_fnd_num = $this->num_rows($prod_fnd_id);
		if($prod_fnd_num==0) return FALSE;
		
		$Arr=$this->fetchArray($prod_fnd_id ,MYSQL_ASSOC);

		$this->freeResult($prod_fnd_id);

		if($ReturnColumn=='') return $Arr;
		if(isset($Arr[$ReturnColumn])) return $Arr[$ReturnColumn];
	 }

	public function collect($Table,$What='',$WhereArray='',$MainKey='',$SortBy='',$Limit='',$KeyIsUnique=TRUE) {
		$sql =$this->selectQuery($Table,$What,$WhereArray,$Limit,$SortBy);
		$Out=array();
	
		$res=$this->query($sql);
		if($MainKey=='') {
			while ( $line =$this->fetchArray ($res ,MYSQL_ASSOC )) {
				$Out[]=$line;
			}
		} else {
			while ( $line =$this->fetchArray ($res ,MYSQL_ASSOC )) {
				if(!$KeyIsUnique) $Out[$line[$MainKey]][]=$line;
				elseif(isset($line[$MainKey])) {
					$Out[$line[$MainKey]]=$line;
				} else {
					$Out[]=$line;
				}
			}
		}
		$this->freeResult($res );
		return $Out;
	}
	public function collectSql($sql,$MainKey='',$KeyIsUnique=TRUE) {
		$Out=array();
	
		$res=$this->query($sql);
		if($MainKey=='') {
			while ( $line =$this->fetchArray ($res ,MYSQL_ASSOC )) {
				$Out[]=$line;
			}
		} else {
			while ( $line =$this->fetchArray ($res ,MYSQL_ASSOC )) {
				if(!$KeyIsUnique) $Out[$line[$MainKey]][]=$line;
				elseif(isset($line[$MainKey])) {
					$Out[$line[$MainKey]]=$line;
				} else {
					$Out[]=$line;
				}
			}
		}
		$this->freeResult($res );
		return $Out;
	}
	function count($tab,$col='',$WhereArray='',$Distinct='') {
		$sql=$this->countQuery($tab,$col,$WhereArray,$Distinct);
		$res=$this->query($sql);
		$res=$this->fetchArray($res,MYSQL_NUM);

		if(!isset($res[0])) return 0;
		return $res[0];
	}
	function delete($tab,$WhereArray='',$Limit='',$Sort='') {
		$sql=$this->deleteQuery($tab,$WhereArray,$Limit,$Sort);
		
		$res=$this->query($sql);
		
		return $res;
	}
	function countSql($sql) {
		$res=$this->query($sql);
		$res=$this->fetchArray($res,MYSQL_NUM);

		if(!isset($res[0])) return 0;
		return $res[0];
	}
	
	

	public function countQuery($TableName,$What='',$WhereArray='',$Distinct='') {
		if($Distinct==FALSE) $Distinct='';
		elseif($Distinct!='') $Distinct='DISTINCT ';

		if($What=='') $What='*';

		$sql='SELECT COUNT('.$Distinct.$What.') FROM `'.$TableName.'`';

		if(is_array($WhereArray)) {
			if(count($WhereArray)>0) $sql.=' WHERE ';
			$sep='';
			foreach($WhereArray as $FieldName=>$FieldVal) {
				$sql.=$sep.'`'.$this->escape($FieldName).'`=\''.$this->escape($FieldVal).'\'';
				$sep=' AND ';
			}
		} elseif($WhereArray!='') {
			$sql.=' WHERE ';
			$sql.=$WhereArray;
		}
		return $sql;
	}

	public function selectQuery($TableName,$What='',$WhereArray='',$Limit='',$Sort='',$Distinct='') {
		if($Distinct==FALSE) $Distinct='';
		elseif($Distinct!='') $Distinct='DISTINCT ';
		$WhatTxt='';
		if($Sort!='') {
			$Sort=' ORDER BY '.$Sort;
		}
		if($Limit!='') $Limit=' LIMIT '.$Limit;
	
		if(is_array($What)) {
			$sep='';
			foreach($What as $FieldVal) {
				$WhatTxt.=$sep.'`'.$this->escape($FieldVal).'`';
				$sep=',';
			}
		} elseif($What=='') {
			$WhatTxt='*';
		} else {
			$WhatTxt=$What;
		}

		$sql='SELECT '.$Distinct.$WhatTxt.' FROM `'.$TableName.'`';

		if(is_array($WhereArray)) {
			if(count($WhereArray)>0) $sql.=' WHERE ';
			$sep='';
			foreach($WhereArray as $FieldName=>$FieldVal) {
				$sql.=$sep.'`'.$this->escape($FieldName).'`=\''.$this->escape($FieldVal).'\'';
				$sep=' AND ';
			}
		} elseif($WhereArray!='') {
			$sql.=' WHERE ';
			$sql.=$WhereArray;
		}
	
		$sql.=$Sort;
		$sql.=$Limit;
	
		//echo($sql);
		return $sql;
	}


	public function deleteQuery($TableName,$WhereArray='',$Limit='',$Sort='') {
		if($Sort!='') {
			$Sort=' ORDER BY '.$Sort;
		}
		if($Limit!='') $Limit=' LIMIT '.$Limit;

		$sql='DELETE FROM `'.$TableName.'`';

		if(is_array($WhereArray)) {
			if(count($WhereArray)>0) $sql.=' WHERE ';
			$sep='';
			foreach($WhereArray as $FieldName=>$FieldVal) {
				$sql.=$sep.'`'.$this->escape($FieldName).'`=\''.$this->escape($FieldVal).'\'';
				$sep=' AND ';
			}
		} elseif(trim($WhereArray)!='') {
			$sql.=' WHERE ';
			$sql.=$WhereArray;
		} else {
			echo("WARNING: TOTAL TABLE DELETE BLOCKED");
			return '';
		}
	
		$sql.=$Limit;
	
		//echo($sql);
		return $sql;
	}


	public function updateQuery($TableName,$InfoArray,$WhereArray) {

		$sql='UPDATE `'.$TableName.'` SET ';
		$sep='';
		foreach($InfoArray as $FieldName=>$FieldVal) {
			$sql.=$sep.'`'.$this->escape($FieldName).'`=\''.$this->escape($FieldVal).'\'';
			$sep=',';
		}
		if(is_array($WhereArray)) {
			if(count($WhereArray)>0) $sql.=' WHERE ';
			$sep='';
			foreach($WhereArray as $FieldName=>$FieldVal) {
				$sql.=$sep.'`'.$this->escape($FieldName).'`=\''.$this->escape($FieldVal).'\'';
				$sep=' AND ';
			}
		} elseif($WhereArray!='') {
			$sql.=' WHERE ';
			$sql.=$WhereArray;
		}
		//echo($sql);
		return $sql;
	}
	public function updateArrayQuery($TableName,$InfoArray,$ListCol,$ListVals,$WhereArray=array()) {

		$sql='UPDATE `'.$TableName.'` SET ';
		$sep='';
		foreach($InfoArray as $FieldName=>$FieldVal) {
			$sql.=$sep.'`'.$this->escape($FieldName).'`=\''.$this->escape($FieldVal).'\'';
			$sep=',';
		}
		
		$ors='';
		$sep2='(';
		foreach($ListVals as $FieldVal) {
			$ors.=$sep2.'`'.$this->escape($ListCol).'`=\''.$this->escape($FieldVal).'\'';
			$sep2=' OR ';
		}
		if($sep2!='(') $ors.=')';
		
		if($ors=='') $ors='FALSE'; // block empty array from being evaluated as -all- empty array= none

		$whr='';
		
		if(is_array($WhereArray)) {
			$sep='';
			foreach($WhereArray as $FieldName=>$FieldVal) {
				$whr.=$sep.'`'.$this->escape($FieldName).'`=\''.$this->escape($FieldVal).'\'';
				$sep=' AND ';
			}
		} elseif($WhereArray!='' || count($ListVals)>0) {
			$whr=$WhereArray;
		}
		
		if($whr!='') {
			$sql.=' WHERE '.$whr;
			if($ors!='') {
				$sql.=' AND '.$ors;
			}
		} else {
			if($ors!='') {
				$sql.=' WHERE '.$ors;
			}
		}
		
		//echo($sql);
		return $sql;
	}
	public function replaceQuery($TableName,$InfoArray) {
		return $this->insertQuery($TableName,$InfoArray,TRUE);
	}

	public function insertQuery($TableName,$InfoArray,$Replace=FALSE) {

		if($Replace===TRUE) $sql='REPLACE INTO `'.$TableName.'` ';
		else $sql='INSERT INTO `'.$TableName.'` ';
	
		$sep='';
		$names='';
		$values='';
		foreach($InfoArray as $FieldName=>$FieldVal) {
	
			if(
		
				!is_array($FieldVal)
		
				|| !isset($FieldVal['kind'])
		
				|| $FieldVal['kind']!='label'
			) {
		
				$names.=$sep.'`'.$this->escape($FieldName).'`';
				$values.=$sep.'\''.$this->escape($FieldVal).'\'';
				$sep=',';
			}
		}
		$sql.='('.$names.') VALUES ('.$values.')';
		return $sql;
	}
	
	public function escape($val) {
		return mysqli_real_escape_string($this->gMy_db_link,$val);
	}

}