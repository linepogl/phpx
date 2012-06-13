<?php

class AstIntLiteral extends AstExpression {

	private $value;

	public function __construct( Token $x ){
		parent::__construct( $x->GetSourcePos() );
		$lexeme = $x->GetLexeme();

		if (substr($lexeme,0,2) == '0x')
			$this->value = intval( substr($lexeme,2) , 16 );

		if (substr($lexeme,0,1) == '0')
			$this->value = intval( ltrim($lexeme,'0') , 8 );

		else
			$this->value = intval( $lexeme , 10 );

		$this->compile_time_type = 'int';
	}

	public function Debug($level = 0){
		$tabs = str_repeat('  ',$level);
		echo $tabs . get_called_class() . ' ' . $this->value . ' ['.$this->compile_time_type. ']' . "\n";
	}

	public function CalculateType(Scope $scope, Validator $v){
		return $this->compile_time_type;
	}


}
