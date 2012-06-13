<?php

class AstVariableDeclarationStatement extends AstStatement {

	private $name;

	/** @var AstType */
	private $type = null;

	/** @var AstExpression */
	private $initial_expression = null;

	public function __construct( Token $name_token , AstType $type = null, AstExpression $expr = null ) {
		$this->name = $name_token->GetLexeme();
		$this->type = $type;
		$this->initial_expression = $expr;
	}


	public function Debug($level = 0) {
		$tabs = str_repeat('  ',$level);
		echo $tabs . get_called_class() . ' ' . $this->name . ' ['.$this->compile_time_type. '] ' . $this->source_pos . "\n";
		if (!is_null($this->type))
			$this->type->Debug($level + 1);
		if (!is_null($this->initial_expression))
			$this->initial_expression->Debug($level + 1);
	}

	public function CalculateType(Scope $scope, Validator $v) {
		$type1 = is_null($this->type) ? null : $this->type->CalculateType($scope,$v);
		$type2 = is_null($this->initial_expression) ? null : $this->initial_expression->CalculateType($scope,$v);

		if (!is_null($type1)) {
			if (!is_null($type2)) {
				if ($type1 !== $type2) {
					$v[] = new CompileTimeException('Type error: '.$type1.' != '.$type2.'.',$this->GetSourcePos());
				}
			}
			$scope->SetType($this->name,$type1);
		}

		elseif (!is_null($type2)) {
			$scope->SetType($this->name,$type2);
		}

		$this->compile_time_type = 'void';
		return $this->compile_time_type;
	}
}
