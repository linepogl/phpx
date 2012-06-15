<?php

abstract class SyntaxNode {

	/** @var SourcePos */
	protected $source_pos = null;
	/** @return SourcePos */
	public function GetSourcePos(){ return $this->source_pos; }
	/** @return SourcePos */
	public function InitSourcePos( SourcePos $source_pos = null ){ $this->source_pos = $source_pos; return $this->source_pos; }

	abstract function AsString();
	abstract function GetChildren();
	public final function __toString(){ return $this->AsString(); }

	public final function Debug($level = 0) {
		$tab = str_repeat('  ',$level);
		echo $tab;
		echo $this->AsString();
		echo "\n";

		/** @var $node SyntaxNode */
		foreach ($this->GetChildren() as $node)
			$node->Debug($level+1);
	}

	public abstract function Analyze(Scope $scope, Validator $v);
}