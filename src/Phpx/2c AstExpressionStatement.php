<?php

class AstExpressionStatement extends AstStatement {

	/** @var AstExpression */
	private $expression = null;

	public function __construct( AstExpression $expression ){
		$this->expression = $expression;
	}

	public function GetChildren(){
		return array($this->expression);
	}





	protected function OnAnalyze(Scope $scope, Validator $v) {
		$this->compile_time_type = self::VOID;
	}
}
