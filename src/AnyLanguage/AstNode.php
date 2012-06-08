<?php

abstract class AstNode {

	protected $compile_time_type = 'undefined';

	/** @var SourcePos */
	protected $source_pos;

	public function GetClassName(){ return get_called_class(); }

	//public abstract function DebugReport($level = 0);
	public function DebugReport($level = 0){
		$tabs = str_repeat('  ',$level);
		echo $tabs . get_called_class() . ' ['.$this->compile_time_type. '] ' . $this->source_pos . "\n";
	}


	public abstract function CalculateType(Scope $scope, Validator $v);

	/** @var SourcePos */
	public function GetSourcePos(){
		return $this->source_pos;
	}
}
