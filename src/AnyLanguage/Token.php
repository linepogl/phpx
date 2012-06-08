<?php

class Token {

	private $terminal;
	private $lexeme;
	private $source_pos;
	private $source_pos_to;

	public function __construct($terminal,$lexeme,SourcePos $source_pos,SourcePos $source_pos_to = null){
		$this->terminal = $terminal;
		$this->lexeme = $lexeme;
		$this->source_pos = $source_pos;
		$this->source_pos_to = is_null($source_pos_to) ? $source_pos : $source_pos_to;
	}

	public function GetTerminal(){ return $this->terminal; }
	public function GetLexeme(){ return $this->lexeme; }
	public function GetSourcePos(){ return $this->source_pos; }
	public function GetSourcePosTo(){ return $this->source_pos_to; }



	public function Is($terminal) {
		return $this->terminal === $terminal;
	}


}
