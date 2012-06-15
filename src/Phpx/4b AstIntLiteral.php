<?php

class AstIntLiteral extends AstExpression {

	private $value;

	public function __construct( Token $x ){
		$lexeme = $x->GetLexeme();

		if (substr($lexeme,0,2) == '0x')
			$this->value = intval( substr($lexeme,2) , 16 );

		if (substr($lexeme,0,1) == '0')
			$this->value = intval( ltrim($lexeme,'0') , 8 );

		else
			$this->value = intval( $lexeme , 10 );

	}

	public function AsString(){
		return parent::AsString() . ' ' . $this->value;
	}



	protected function OnAnalyze(Scope $scope, Validator $v){
		$this->compile_time_type = 'int';
	}


}
