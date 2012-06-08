<?php

class ParseTree {

	private $symbol;
	public function __construct( $symbol ){ $this->symbol = $symbol; }
	public function GetSymbol(){ return $this->symbol; }


	private $rule = null;
	private $children = array();

	public function GetRule(){ return $this->rule; }
	public function GetChildren(){ return $this->children; }
	public function HasChildren(){ return count($this->children)!=0; }
	/** @return ParseTree */
	public function GetChild($index){ return $this->children[$index]; }
	public function SetRule( $rule , $children ){
		$this->rule = $rule;
		$this->children = $children;
	}


	/** @var Token */
	private $token = null;
	private $unexpected = false;
	public function SetToken(Token $token, $unexpected = false){ $this->token = $token; $this->unexpected = $unexpected; }
	public function GetToken(){ return $this->token; }
	public function HasToken(){ return !is_null($this->token); }



	public function DebugReport($level = 0){
		if ($level == 0){
			echo "PARSE TREE\n----------\n";
		}

		$tab = str_repeat('  ',$level);
		echo $tab;


		if (!is_null($this->token)) {
			if ($this->unexpected) echo 'UNEXPECTED ';
			echo $this->token->GetTerminal();
			echo ' '.$this->token->GetLexeme();
			echo ' '.$this->token->GetSourcePos();
			echo ' - '.$this->token->GetSourcePosTo();
			echo "\n";
		}
		else {
			echo $this->item;
			echo ' -> ';
			echo $this->rule;
			echo "\n";
		}

		/** @var $parse_tree ParseTree */
		foreach ($this->children as $parse_tree)
			$parse_tree->DebugReport($level+1);

		if ($level == 0){
			echo "\n";
		}
	}




}

