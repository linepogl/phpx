<?php

class AstAssignment extends AstExpression {

	/** @var AstVariable */
	private $variable;

	/** @var AstExpression */
	private $expression;

	public function __construct( AstVariable $variable , AstExpression $expression ) {
		$this->variable = $variable;
		$this->expression = $expression;
	}

	public function GetChildren(){
		return array($this->variable,$this->expression);
	}




	protected function OnAnalyze(Scope $scope, Validator $v){
		$type1 = $this->variable->GetCompileTimeType();
		$type2 = $this->expression->GetCompileTimeType();


		if (!is_null($type1) && !is_null($type2) && $type1 != $type2) {
			$v[] = new CompileTimeException('Type error: ' . $type1 . ' != ' . $type2 .'.',$this->GetSourcePos());
			$this->compile_time_type = self::UNDEFINED;
		}
		else {
			$this->compile_time_type = $type1;
		}
	}

}
