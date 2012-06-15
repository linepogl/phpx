<?php

class ParseNode extends SyntaxNode implements ArrayAccess,Countable {

	/** @var NonTerminal */
	private $non_terminal;
	public function __construct( NonTerminal $non_terminal ){ $this->non_terminal = $non_terminal; }
	public function GetNonTerminal(){ return $this->non_terminal; }
	public function Is( $symbol ){ return $this->non_terminal->Is($symbol); }

	private $children = array();
	public function Count(){ return count($this->children); }
	public function OffsetUnset($offset){ unset($this->children[$offset]); }
	public function OffsetExists($offset){ return array_key_exists($offset,$this->children); }
	public function OffsetGet($offset){ return $this->children[$offset]; }
	public function OffsetSet($offset,$value){ if (is_null($offset)) $this->children[] = $value; else $this->children[$offset] = $value; }

	public function GetChildren(){ return $this->children; }
	public function HasChildren(){ return count($this->children)!=0; }


	public function AsString(){ return $this->non_terminal->AsString() . ' ' . $this->source_pos; }


	/** @return SourcePos */
	public function UpdateSourcePosFromChildren(){
		$src = null;
		/** @var $x SyntaxNode */
		foreach ($this->children as $x){
			$to = $x->GetSourcePos();
			if (is_null($to))
				continue;
			if (is_null($src))
				$src = $x->GetSourcePos();
			else
				$src = $src->UpTo( $to );
		}
		$this->source_pos = $src;
		return $this->source_pos;
	}

	public function Analyze(Scope $scope, Validator $v) {
		/** @var $x SyntaxNode */
		foreach ($this->GetChildren() as $x)
			$x->Analyze($scope,$v);
	}




}

