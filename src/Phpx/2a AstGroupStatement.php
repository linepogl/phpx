<?php

class AstGroupStatement extends AstStatement {

	private $statements;

	public function __construct( $statements ) {
		$this->statements = $statements;
	}

	public function Debug($level = 0){
		parent::Debug($level);
		foreach ($this->statements as $x)
			$x->Debug($level + 1);
	}

	public function CalculateType(Scope $scope, Validator $v) {
		$new_scope = $scope->Extend();

		/** @var $x AstStatement */
		foreach ($this->statements as $x){
			$x->CalculateType($new_scope,$v);
		}

		$this->compile_time_type = 'void';
		return $this->compile_time_type;
	}
}
