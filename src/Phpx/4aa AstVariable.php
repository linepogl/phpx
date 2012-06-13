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

	public function Debug($level = 0){
		$tabs = str_repeat('  ',$level);
		echo $tabs . get_called_class() . ': ';
		foreach ($this->namespaces as $ns)
			echo $ns.'.';
		echo $this->name . ' ['.$this->compile_time_type. ']' . "\n";
	}

	public function CalculateType(Scope $scope, Validator $v) {
		$type = $scope->GetType($this->name);

		if (is_null($type)) {
			$v[] = new CompileTimeException('Undefined variable:'.$this->name,$this->GetSourcePos());
			return null;
		}

		$this->compile_time_type = $type;
		return $this->compile_time_type;
	}
}
