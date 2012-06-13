<?php

class ParseNode implements ArrayAccess,Countable {

	/** @var NonTerminal */
	private $non_terminal;
	public function __construct( NonTerminal $non_terminal ){ $this->non_terminal = $non_terminal; }
	public function GetNonTerminal(){ return $this->non_terminal; }
	public function Is( $symbol ){ return $this->non_terminal->Is($symbol); }

	public function __toString(){ return $this->non_terminal->AsString(); }
	public function AsString(){ return $this->non_terminal->AsString(); }

	private $children = array();
	public function Count(){ return count($this->children); }
	public function OffsetUnset($offset){ unset($this->children[$offset]); }
	public function OffsetExists($offset){ return array_key_exists($offset,$this->children); }
	public function OffsetGet($offset){ return $this->children[$offset]; }
	public function OffsetSet($offset,$value){ if (is_null($offset)) $this->children[] = $value; else $this->children[$offset] = $value; }


	public function GetChildren(){ return $this->children; }
	public function HasChildren(){ return count($this->children)!=0; }




	public function Debug($level = 0){
		if ($level == 0){
			echo "PARSE TREE\n----------\n";
		}

		$tab = str_repeat('  ',$level);
		echo $tab;

		echo $this->non_terminal;
		echo ' -> ';
		//echo $this->rule;
		echo "\n";

		/** @var $parse_node ParseNode */
		foreach ($this->children as $parse_node)
			$parse_node->Debug($level+1);

		if ($level == 0){
			echo "\n";
		}
	}




}

