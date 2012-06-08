<?php

class AstIntType extends AstType {

	public static function Make(ParseTree $parse_tree) {
		$r = new self();
		$r->source_pos = $parse_tree->GetToken()->GetSourcePos();
		return $r;
	}

	public function CalculateType(Scope $scope, Validator $v){
		$this->compile_time_type = 'int';
		return $this->compile_time_type;
	}
}
