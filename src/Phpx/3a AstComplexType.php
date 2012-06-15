<?php

class AstComplexType extends AstType {

	private $namespaces = array();
	private $name;

	public function __construct( $tokens = array() ) {
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




	protected function OnAnalyze(Scope $scope, Validator $v){
		$this->compile_time_type = $this->GetFullName();
	}

}
