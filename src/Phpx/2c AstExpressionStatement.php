<?php

class AstExpressionStatement extends AstStatement {

	/** @var AstExpression */
	private $expression = null;

	public static function Make(ParseTree $parse_tree) {
		$r = new self();
		$r->expression = AstExpression::Make($parse_tree->GetChild(0));
		$r->source_pos = $r->expression->GetSourcePos();
		return $r;
	}

	public function DebugReport($level = 0) {
		parent::DebugReport($level);
		if (!is_null($this->expression))
			$this->expression->DebugReport($level+1);
	}

	public function CalculateType(Scope $scope, Validator $v) {

		$this->expression->CalculateType($scope,$v);

		$this->compile_time_type = 'void';
		return $this->compile_time_type;
	}
}
