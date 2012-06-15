<?php

class AstVariable extends AstExpression {

	private $namespaces = array();
	private $name;

	public function __construct( $tokens = array() ) {
		$this->source_pos = $tokens[0]->GetSourcePos();
		/** @var $token Token */
		foreach ($tokens as $token)
			$this->namespaces[] = $token->GetLexeme();
		$this->name = array_pop($this->namespaces);
	}

	public function AsString(){
		return parent::AsString() . ' ' . $this->GetFullName();
	}

	public function GetFullName(){
		$r = '';
		foreach ($this->namespaces as $ns)
			$r .= $ns.'.';
		$r .= $this->name;
		return $r;
	}



	protected function OnAnalyze(Scope $scope, Validator $v) {
		$type = $scope->GetType($this->name);

		if (is_null($type)) {
			$v[] = new CompileTimeException('Undefined variable:'.$this->name,$this->GetSourcePos());
			$this->compile_time_type = self::UNDEFINED;
		}
		else {
			$this->compile_time_type = $type;
		}
	}
}
