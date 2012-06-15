<?php

class Token extends SyntaxNode {

	private $terminal;
	private $lexeme;

	public function __construct(Terminal $terminal,$lexeme,SourcePos $source_pos = null){
		$this->terminal = $terminal;
		$this->lexeme = $lexeme;
		$this->source_pos = $source_pos;
	}

	public function GetTerminal(){ return $this->terminal; }
	public function GetLexeme(){ return $this->lexeme; }


	public function Is($terminal) {
		return $this->terminal->Is( $terminal );
	}

	public function GetChildren(){ return array(); }
	public final function Analyze(Scope $scope, Validator $v) {}

	public function AsString(){ return $this->terminal . ' \'' . $this->lexeme . '\' ' . $this->source_pos ; }

}
