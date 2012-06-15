<?php

class AstEmptyStatement extends AstStatement {

	protected function OnAnalyze(Scope $scope, Validator $v) {
		$this->compile_time_type = self::VOID;
	}

}
