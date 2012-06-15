<?php

abstract class AstNode {

	/** @var SourcePos */
	protected $source_pos;
	protected $compile_time_type = 'undefined';

	public function __construct( SourcePos $source_pos ) {
		$this->source_pos = $source_pos;
	}

	public function __toString(){ return get_called_class(); }
	public function AsString(){ return get_called_class(); }


	public function Debug($level = 0){
		$tabs = str_repeat('  ',$level);
		echo $tabs . get_called_class() . ' ['.$this->compile_time_type. '] ' . $this->source_pos . "\n";
	}


	/** @var SourcePos */
	public function GetSourcePos(){
		return $this->source_pos;
	}
	public function GetCompileTimeType(){
		return $this->compile_time_type;
	}
	public abstract function CalculateType(Scope $scope, Validator $v);

}
