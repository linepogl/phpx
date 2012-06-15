<?php

class AstProgram extends AstNode {

	private $statements;

	public function __construct( $statements ) {
		$this->statements = $statements;
	}
	public function GetChildren(){ return $this->statements; }


	protected function OnAnalyze(Scope $scope, Validator $v){
		$this->compile_time_type = self::VOID;
	}

}
