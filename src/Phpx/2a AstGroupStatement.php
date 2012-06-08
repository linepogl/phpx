<?php

class AstGroupStatement extends AstStatement {

	private $statements;

	public static function Make(ParseTree $parse_tree) {
		$r = new self();
		$r->source_pos = $parse_tree->GetChild(0)->GetToken()->GetSourcePos();
		foreach ($parse_tree->GetChild(1)->GetChildren() as $xx)
			$r->Devour($xx);
		return $r;
	}
	private function Devour(ParseTree $parse_tree){
		switch ($parse_tree->GetItem()){
			case NStatementSequence:
				foreach ($parse_tree->GetChildren() as $xx)
					$this->Devour($xx);
				break;
			case NStatement:
				$this->statements[] = AstStatement::Make( $parse_tree );
				break;
		}
	}

	public function DebugReport($level = 0){
		parent::DebugReport($level);
		foreach ($this->statements as $x)
			$x->DebugReport($level + 1);
	}

	public function CalculateType(Scope $scope, Validator $v) {
		$new_scope = $scope->Extend();

		/** @var $x AstStatement */
		foreach ($this->statements as $x){
			$x->CalculateType($new_scope,$v);
		}

		$this->compile_time_type = 'void';
		return $this->compile_time_type;
	}
}
