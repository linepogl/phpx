<?php

class AstIntType extends AstType {

	public function __construct( Token $token ) {
		parent::__construct($token->GetSourcePos());
		$this->compile_time_type = 'int';
	}

	public function CalculateType(Scope $scope, Validator $v){
		return $this->compile_time_type;
	}
}
