<?php

const TUnknown = 'TUnknown';
const TEndOfFile = 'TEndOfFile';
const TEndOfInput = 'TEndOfInput';

const TIdentifier = 'TIdentifier';
const TIntLiteral = 'TIntLiteral';
const TComment = 'TComment';



abstract class Grammar implements IteratorAggregate,ArrayAccess {

	private $symbols = array();
	public function getIterator(){ return new ArrayIterator($this->symbols); }
	public function offsetExists( $offset ){ return isset($this->symbols[$offset]); }
	public function offsetUnset( $offset ){ throw new Exception('Cannot change grammar.'); }
	public function offsetGet( $offset ){ return $this->symbols[$offset]; }
	public function offsetSet( $offset , $value ){
		if (is_null($offset)) throw new Exception('Define a key');
		if (is_string($value)){
			if (array_key_exists($offset,$this->symbols)) throw new Exception('Terminal exists.');
			$this->symbols[$offset] = new Terminal( $offset , $value );
		}
		elseif ($value == Terminal::SPECIAL){
			if (array_key_exists($offset,$this->symbols)) throw new Exception('Terminal exists.');
			$this->symbols[$offset] = new Terminal( $offset );
		}
		elseif (is_array($value)) {
			if (!array_key_exists($offset,$this->symbols)) $this->symbols[$offset] = new NonTerminal( $offset );
			if (!($this->symbols[$offset] instanceof NonTerminal)) throw new Exception('Not a non-terminal.');
			$rule = new Rule($value);
			$this->symbols[$offset][] = $rule;
		}
		else throw new Exception( 'Invalid value.' );
	}


	public function __construct(){
		$this[TUnknown] = Terminal::SPECIAL;
		$this[TEndOfFile] = Terminal::SPECIAL;
		$this[TEndOfInput] = Terminal::SPECIAL;
		$this[TIdentifier] = Terminal::SPECIAL;
		$this[TIntLiteral] = Terminal::SPECIAL;
		$this[TComment] = Terminal::SPECIAL;
		$this->Init();
	}



	private $terminals = array();
	private $non_terminals = array();
	private $rules = array();

	private function IsTerminalKey($symbol_key){ return array_key_exists($symbol_key,$this->terminals); }
	private function IsNonTerminalKey($symbol_key){ return array_key_exists($symbol_key,$this->non_terminals); }
	private function IsRuleKey($symbol_key){ return array_key_exists($symbol_key,$this->rules); }




	public function GetRuleItems( $rule_key ){
		return $this->rules[$rule_key];
	}

	protected abstract function OnInit();
	public function Init(){
		$this->OnInit();

		/** @var $terminal Terminal */
		/** @var $non_terminal NonTerminal */
		/** @var $symbol Symbol */
		/** @var $rule Rule */

		// Separate symbols
		foreach ($this->symbols as $symbol_key => $symbol) {
			if ($symbol instanceof Terminal)
				$this->terminals[$symbol_key] = $symbol;
			elseif ($symbol instanceof NonTerminal) {
				$this->non_terminals[$symbol_key] = $symbol;
				foreach ($symbol as $rule_key => $rule) {
					$this->rules[$rule_key] = $rule;
				}
			}
		}

		// Add rules to symbols
		foreach ($this->rules as $rule_key => $rule){
			$this->symbols[$rule_key] = $rule;
		}

		// Find all symbols inside rules
		foreach ($this->rules as $rule){
			foreach ($rule as $symbol_key => $symbol){
				if (!array_key_exists($symbol_key,$this->symbols)) throw new Exception('Unknown symbol '.$symbol_key.'.');
				$rule->Init($symbol_key,$this->symbols[$symbol_key]);
			}
		}


		$this->InitFixedLexemeMap();
		$this->InitStartingNonTerminal();
		$this->InitGoesToEpsilon();
		$this->InitFirstSets();
		$this->InitFollowSets();
		$this->InitLL1ParsingTable();
	}


	/** @var array */
	private $fixed_lexeme_map = array();
	/** @return array */
	public function GetFixedLexemeMap(){
		return $this->fixed_lexeme_map;
	}
	public function InitFixedLexemeMap(){
		/** @var $terminal Terminal */
		foreach ($this->terminals as $terminal_key => $terminal){
			$fixed_lexeme = $terminal->GetFixedLexeme();
			if (empty($fixed_lexeme)) continue;
			$this->fixed_lexeme_map[$fixed_lexeme] = $terminal_key;
		}
	}


	/** @var NonTerminal */
	private $starting_non_terminal = null;
	/** @return NonTerminal */
	public function GetStartingNonTerminal(){
		return $this->starting_non_terminal;
	}
	private function InitStartingNonTerminal(){
		foreach ($this->non_terminals as $non_terminal){
			$this->starting_non_terminal = $non_terminal;
			break;
		}
	}



	private $goes_to_epsilon = array();
	private function InitGoesToEpsilon(){
		foreach ($this->symbols as $symbol )
			$this->GoesToEpsilon( $symbol );
	}
	private function GoesToEpsilon( Symbol $symbol ) {
		if (!array_key_exists($symbol->GetKey(),$this->goes_to_epsilon)){
			$r = false;
			if ( $symbol instanceof NonTerminal ) {
				foreach ($symbol as $rule){
					$r = $this->GoesToEpsilon($rule);
					if ($r) break;
				}
			}
			elseif ( $symbol instanceof Rule ){
				$found = false;
				foreach ($symbol as $symbol2) {
					if (!$this->GoesToEpsilon($symbol2)) {
						$found = true;
						break;
					}
				}
				$r = !$found;
			}
			$this->goes_to_epsilon[$symbol->GetKey()] = $r;
		}
		return $this->goes_to_epsilon[$symbol->GetKey()];
	}



	private $first_sets = array();
	private function InitFirstSets() {
		foreach ($this->symbols as $symbol_key => $symbol){
			$r = array();
			if ($symbol instanceof Terminal) {
				$r[$symbol_key] = $symbol;
			}
			elseif ($symbol instanceof Rule) {
				foreach ($symbol as $symbol2_key => $symbol2){
					if ($symbol2_key != $symbol_key) $r[$symbol2_key] = $symbol2;
					if (!$this->goes_to_epsilon[$symbol2_key]) break;
				}
			}
			elseif ($symbol instanceof NonTerminal) {
				foreach ($symbol as $rule_key => $rule){
					$r[$rule_key] = $rule;
				}
			}
			$this->first_sets[$symbol_key] = $r;
		}
		$this->ReduceFirstSets();
	}
	private function ReduceFirstSets(){
		$again = 0;
		foreach ($this->symbols as $symbol_key => $symbol) {
			$a = array();
			$found = false;
			foreach ($this->first_sets[$symbol_key] as $symbol2_key => $symbol2){
				if (!($symbol2 instanceof Terminal)) {
					$found = true;
					$a = $a + $this->first_sets[$symbol2_key];
					unset($a[$symbol_key]);
					unset($this->first_sets[$symbol_key][$symbol2_key]);
				}
			}
			if ($found) {
				$again++;
				$this->first_sets[$symbol_key] = $this->first_sets[$symbol_key] + $a;
			}
		}
		if ($again>0) $this->ReduceFirstSets();
	}




	private $follow_sets = array();
	private function InitFollowSets(){
		foreach ($this->symbols as $symbol_key => $symbol ){
			$r = array();
			if ($symbol_key == $this->starting_non_terminal->GetKey())
				$r[TEndOfInput] = TEndOfInput;
			foreach ($this->non_terminals as $non_terminal_key => $non_terminal) {
				foreach ($non_terminal as $rule_key => $rule){
					$i = 0;
					foreach ($rule as $symbol_i_key => $symbol_i){
						if ($symbol_i_key == $symbol_key) {
							$finished = false;
							$j = 0;
							foreach ($rule as $symbol_j_key => $symbol_j){
								if ($j > $i) {
									$r = $r + $this->first_sets[$symbol_j_key];
									if (!$this->goes_to_epsilon[$symbol_j_key]) {
										$finished = true;
										break;
									}
								}
								$j++;
							}
							if (!$finished && $symbol_i_key != $non_terminal_key)
								$r[$non_terminal_key] = $non_terminal;
						}
						$i++;
					}
				}
			}
			$this->follow_sets[$symbol_key] = $r;
		}
		$this->ReduceFollowSets();
	}
	private function ReduceFollowSets(){
		$again = 0;
		foreach ($this->symbols as $symbol_key => $symbol){
			$a = array();
			$found = false;
			foreach ($this->follow_sets[$symbol_key] as $symbol2_key => $symbol2){
				if ($symbol2 instanceof NonTerminal) {
					$found = true;
					$a = $a + $this->follow_sets[$symbol2_key];
					unset($a[$symbol_key]);
					unset($this->follow_sets[$symbol_key][$symbol2_key]);
				}
			}
			if ($found) {
				$again++;
				$this->follow_sets[$symbol_key] = $this->follow_sets[$symbol_key] + $a;
			}
		}
		if ($again > 0) $this->ReduceFollowSets();
	}





	private $ll1_parsing_table = array();
	private function InitLL1ParsingTable(){
		foreach ($this->non_terminals as $non_terminal_key => $non_terminal){
			$this->ll1_parsing_table[$non_terminal_key] = array();
			foreach ($non_terminal as $rule_key => $rule){
				foreach ($this->first_sets[$rule_key] as $terminal_key => $terminal) {
					if (!array_key_exists($terminal_key,$this->ll1_parsing_table[$non_terminal_key])) $this->ll1_parsing_table[$non_terminal_key][$terminal_key] = array();
					$this->ll1_parsing_table[$non_terminal_key][$terminal_key][$rule_key] = $rule;
				}
				if ($this->goes_to_epsilon[$rule_key]) {
					foreach ($this->follow_sets[$non_terminal_key] as $terminal_key => $terminal) {
						if (!array_key_exists($terminal_key,$this->ll1_parsing_table[$non_terminal_key])) $this->ll1_parsing_table[$non_terminal_key][$terminal_key] = array();
						$this->ll1_parsing_table[$non_terminal_key][$terminal_key][$rule_key] = $rule;
					}
				}
			}
		}
	}





	public function DebugReport(){
		/** @var $terminal Terminal */
		/** @var $non_terminal NonTerminal */
		/** @var $symbol Symbol */
		/** @var $rule Rule */

		echo "PRODUCTIONS\n-----------\n";
		foreach ($this->non_terminals as $non_terminal_key => $non_terminal)
			foreach ($non_terminal as $rule_key => $rule)
				echo $non_terminal_key.' -> '.$rule_key . "\n";
		echo "\n";


		echo "FIRST SETS\n----------\n";
		foreach ($this->symbols as $symbol_key => $symbol) {
			echo $symbol_key.' : { ';
			if ($this->goes_to_epsilon[$symbol_key]) {
				echo '&epsilon;';
				if (count($this->first_sets[$symbol_key]) > 0)
					echo ' , ';
			}
			echo implode(' , ',array_keys($this->first_sets[$symbol_key]));
			echo " }\n";
		}
		echo "\n";



		echo "FOLLOW SETS\n-----------\n";
		foreach ($this->symbols as $symbol_key => $symbol) {
//			if ($this instanceof Rule) continue;
			echo $symbol_key.' : { '.implode(' , ',array_keys($this->follow_sets[$symbol_key]))." }\n";
		}
		echo "\n";


		echo "LL(1) PARSING TABLE\n-------------------\n";
		foreach ($this->ll1_parsing_table as $non_terminal => $table){
			echo $non_terminal . ":\n";
			foreach ($table as $terminal => $possible_rules) {
				echo '  on '.$terminal.' -> '.implode(' ',array_keys($possible_rules)) . (count($possible_rules)>1?' ******':'')."\n";
			}
		}
		echo "\n";
	}





	public function GetNextRuleKey( $non_terminal , $terminal ){
		if (isset($this->ll1_parsing_table[$non_terminal][$terminal]))
			return $this->ll1_parsing_table[$non_terminal][$terminal][0];
		else
			return null;
	}

}

