<?php

class AstComplexType extends AstType {

	private $namespaces = array();
	private $name;

	public function __construct( $tokens = array() ) {
		$this->source_pos = $tokens[0]->GetSourcePos();
		/** @var $token Token */
		foreach ($tokens as $token)
			$this->namespaces[] = $token->GetLexeme();
		$this->name = array_pop($this->namespaces);
	}

	public function DebugReport($level = 0){
		$tabs = str_repeat('  ',$level);
		echo $tabs . get_called_class() . ': ';
		foreach ($this->namespaces as $ns)
			echo $ns.'.';
		echo $this->name . ' ['.$this->compile_time_type. '] ' . $this->source_pos . "\n";
	}

	public function CalculateType(Scope $scope, Validator $v){
		$this->compile_time_type = implode('.',$this->namespaces).'.'.$this->name;
		return $this->compile_time_type;
	}

}
