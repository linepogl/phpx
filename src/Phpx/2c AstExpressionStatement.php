<?php

class AstExpressionStatement extends AstStatement {

	/** @var AstExpression */
	private $expression = null;

	public function __construct( AstExpression $expression ){
		parent::__construct( $expression->GetSourcePos() );
		$this->expression = $expression;
	}

	public function Debug($level = 0) {
		parent::Debug($level);
		if (!is_null($this->expression))
			$this->expression->Debug($level+1);
	}

	public function CalculateType(Scope $scope, Validator $v) {

		$this->expression->CalculateType($scope,$v);

		$this->compile_time_type = 'void';
		return $this->compile_time_type;
	}
}
