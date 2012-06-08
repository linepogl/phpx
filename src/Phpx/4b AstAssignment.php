<?php

class AstAssignment extends AstExpression {

	/** @var AstVariable */
	private $variable;

	/** @var AstExpression */
	private $expression;

	public static function Make(ParseTree $parse_tree) {
		$r = new self();
		$r->variable = AstVariable::Make($parse_tree->GetChild(0)->GetChild(0));
		$r->source_pos = $r->variable->GetSourcePos();
		$r->expression = AstExpression::Make($parse_tree->GetChild(1)->GetChild(1));
		return $r;
	}

	public function DebugReport($level = 0) {
		parent::DebugReport($level);
		$this->variable->DebugReport($level + 1);
		$this->expression->DebugReport($level + 1);
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
