<?php

class Token {

	private $terminal;
	private $lexeme;
	private $source_pos;

	public function __construct(Terminal $terminal,$lexeme,SourcePos $source_pos){
		$this->terminal = $terminal;
		$this->lexeme = $lexeme;
		$this->source_pos = $source_pos;
	}

	public function GetTerminal(){ return $this->terminal; }
	public function GetLexeme(){ return $this->lexeme; }
	public function GetSourcePos(){ return $this->source_pos; }

	public function __toString(){ return $this->terminal->AsString(); }

	public function Is($terminal) {
		return $this->terminal->Is( $terminal );
	}

	public function Debug($level = 0){
		$tab = str_repeat('  ',$level);
		echo $tab;
		echo $this->terminal;
		echo ' \'';
		echo $this->lexeme;
		echo "'\n";
	}

}
