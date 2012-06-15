<?php

class AstProgram extends AstNode {

	private $statements;

	public function __construct( $statements ) {
		$this->statements = $statements;
		$this->compile_time_type = 'void';
	}


	public function Debug($level = 0){
		parent::Debug($level);
		foreach ($this->statements as $x)
			$x->Debug($level + 1);
	}


	public function CalculateType(Scope $scope, Validator $v){

		/** @var $x AstStatement */
		foreach ($this->statements as $x)
			$x->CalculateType($scope,$v);

		$this->compile_time_type = 'void';
		return $this->compile_time_type;
	}

}
