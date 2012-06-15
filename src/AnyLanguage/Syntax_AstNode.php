<?php

abstract class AstNode extends SyntaxNode {

	public function GetClassName(){ return get_called_class(); }
	public function AsString(){
		return $this->GetClassName() . ' {' . $this->compile_time_type . '} ' . $this->source_pos; }
	public function GetChildren(){ return array(); }


	const UNDEFINED = '-UNDEFINED-';
	const VOID = '-VOID-';




	protected $compile_time_type = null;
	public function GetCompileTimeType(){
		return $this->compile_time_type;
	}



	protected abstract function OnAnalyze(Scope $scope, Validator $v);
	public final function Analyze(Scope $scope, Validator $v) {
		/** @var $x SyntaxNode */
		foreach ($this->GetChildren() as $x)
			$x->Analyze($scope,$v);

		$this->OnAnalyze($scope,$v);
		if (is_null($this->compile_time_type))
			throw new Exception('Undefined compile time type');
	}

}
