<?php

class CompileTimeException extends Exception {
	private $source_pos;
	public function __construct($string,SourcePos $source_pos){
		parent::__construct($string . ' ' . $source_pos);
		$this->source_pos = $source_pos;
	}
	public function GetSourcePos(){
		return $this->source_pos;
	}
}


