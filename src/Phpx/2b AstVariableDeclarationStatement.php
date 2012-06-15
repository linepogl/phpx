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

	public function AsString(){ return parent::AsString() .' '.$this->name; }
	public function GetChildren(){
		$r = array();
		if (!is_null($this->type)) $r[] = $this->type;
		if (!is_null($this->initial_expression)) $r[] = $this->initial_expression;
		return $r;
	}



	protected function OnAnalyze(Scope $scope, Validator $v) {
		$type1 = is_null($this->type) ? null : $this->type->GetCompileTimeType();
		$type2 = is_null($this->initial_expression) ? null : $this->initial_expression->GetCompileTimeType();

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

		$this->compile_time_type = self::VOID;
	}
}
