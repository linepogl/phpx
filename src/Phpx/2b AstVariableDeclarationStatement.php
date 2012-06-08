<?php

class AstVariableDeclarationStatement extends AstStatement {

	private $name;


	/** @var AstType */
	private $type = null;

	/** @var AstExpression */
	private $initial_expression = null;

	public static function Make(ParseTree $parse_tree) {
		$r = new self;

		$r->source_pos = $parse_tree->GetChild(0)->GetToken()->GetSourcePos();

		/** @var $name_node ParseTree */
		$r->name = $parse_tree->GetChild(1)->GetToken()->GetLexeme();

		/** @var $node ParseTree */
		foreach ($parse_tree->GetChild(2)->GetChildren() as $node){
			if ($node->GetItem() == NType) {
				$r->type = AstType::Make( $node );
			}
			elseif ($node->GetItem() == NAssign) {
				if ($node->HasChildren()) {
					$r->initial_expression = AstExpression::Make( $node->GetChild(1) );
				}
			}
		}

		return $r;
	}


	public function DebugReport($level = 0) {
		$tabs = str_repeat('  ',$level);
		echo $tabs . get_called_class() . ' ' . $this->name . ' ['.$this->compile_time_type. '] ' . $this->source_pos . "\n";
		if (!is_null($this->type))
			$this->type->DebugReport($level + 1);
		if (!is_null($this->initial_expression))
			$this->initial_expression->DebugReport($level + 1);
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
