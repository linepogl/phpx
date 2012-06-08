<?php

class AstIntLiteral extends AstExpression {

	private $value;


	public static function Make(ParseTree $parse_tree) {
		$r = new self();

		$r->source_pos = $parse_tree->GetToken()->GetSourcePos();

		$lexeme = $parse_tree->GetToken()->GetLexeme();

		if (substr($lexeme,0,2) == '0x')
			$r->value = intval( substr($lexeme,2) , 16 );

		if (substr($lexeme,0,1) == '0')
			$r->value = intval( ltrim($lexeme,'0') , 8 );

		else
			$r->value = intval( $lexeme , 10 );

		return $r;
	}

	public function DebugReport($level = 0){
		$tabs = str_repeat('  ',$level);
		echo $tabs . get_called_class() . ': ' . $this->value . ' ['.$this->compile_time_type. '] ' . $this->source_pos . "\n";
	}

	public function CalculateType(Scope $scope, Validator $v){
		$this->compile_time_type = 'int';
		return $this->compile_time_type;
	}


}
