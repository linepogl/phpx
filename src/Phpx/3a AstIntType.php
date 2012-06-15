<?php

class AstIntType extends AstType {

	protected function OnAnalyze(Scope $scope, Validator $v){
		$this->compile_time_type = 'int';
	}
}
