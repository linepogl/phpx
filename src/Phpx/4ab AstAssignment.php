<?php

class AstAssignment extends AstExpression {

	/** @var AstVariable */
	private $variable;

	/** @var AstExpression */
	private $expression;

	public function __construct( AstVariable $variable , AstExpression $expression ) {
		parent::__construct($variable->GetSourcePos());
		$this->variable = $variable;
		$this->expression = $expression;
	}

	public function Debug($level = 0) {
		parent::Debug($level);
		$this->variable->Debug($level + 1);
		$this->expression->Debug($level + 1);
	}


	public function CalculateType(Scope $scope, Validator $v){
		$type1 = $this->variable->CalculateType($scope,$v);
		$type2 = $this->expression->CalculateType($scope,$v);


		if (!is_null($type1) && !is_null($type2) && $type1 != $type2) {
			$v[] = new CompileTimeException('Type error: ' . $type1 . ' != ' . $type2 .'.',$this->GetSourcePos());
			return null;
		}

		$this->compile_time_type = $type1;
		return $this->compile_time_type;
	}

}
