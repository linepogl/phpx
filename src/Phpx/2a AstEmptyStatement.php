<?php

class AstEmptyStatement extends AstStatement {

	public function __construct( ) {
		$this->compile_time_type = 'void';
	}

	public function CalculateType(Scope $scope, Validator $v) {
		return $this->compile_time_type;
	}
}
