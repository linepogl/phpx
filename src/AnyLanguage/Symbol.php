<?php

abstract class Symbol {
	protected $key;
	public function GetKey(){ return $this->key; }
}

class Terminal extends Symbol {
	const SPECIAL = 0;
	private $fixed_lexeme;
	public function __construct( $key , $fixed_lexeme = null ) {
		$this->key = $key;
		$this->fixed_lexeme = $fixed_lexeme;
	}
	public function GetFixedLexeme(){
		return $this->fixed_lexeme;
	}
}


class NonTerminal extends Symbol implements ArrayAccess, IteratorAggregate {
	public function __construct( $key ){
		$this->key = $key;
	}
	private $data = array();
	public function getIterator(){ return new ArrayIterator($this->data); }
	public function offsetExists( $offset ){ return isset($this->data[$offset]); }
	public function offsetUnset( $offset ){ unset($this->data[$offset]); }
	public function offsetGet( $offset ){ return $this->data[$offset]; }
	public function offsetSet( $offset , $value ){
		if (!is_null($offset)) throw new Exception('Can only append rules to a non-terminal.');
		if (!($value instanceof Rule)) throw new Exception();
		/** @var $value Rule */
		$this->data[$value->GetKey()] = $value;
	}

}

class Rule extends Symbol implements ArrayAccess,IteratorAggregate{
	private $symbols = array();

	public function __construct( array $symbol_keys) {
		foreach ($symbol_keys as $symbol_key)
			$this->symbols[$symbol_key] = null;
		$this->key = '['.implode(',',$symbol_keys).']';
	}

	public function getIterator(){ return new ArrayIterator($this->symbols); }
	public function offsetSet( $offset , $value ){ throw new Exception('Cannot change a rule.'); }
	public function offsetExists( $offset ){ return array_key_exists($offset,$this->symbols); }
	public function offsetGet( $offset ){ return $this->symbols[$offset]; }
	public function offsetUnset( $offset ){ unset($this->symbols[$offset]); }

	public function GetSymbols(){ return $this->symbols; }
	public function Init($symbol_key,Symbol $symbol){
		if (!is_null($this->symbols[$symbol_key])) throw new Exception('Cannot change a rule.');
		$this->symbols[$symbol_key] = $symbol;
	}

}


class SRItem {
	private $non_terminal;
	private $rule;
	private $index;
	private $next_symbol;
	private $key;

	/** @return NonTerminal */
	public function GetNonTerminal(){ return $this->non_terminal; }
	/** @return Rule */
	public function GetRule(){ return $this->rule; }
	/** @return Symbol */
	public function GetNextSymbol(){ return $this->next_symbol; }
	public function GetKey(){ return $this->key; }
	public function __construct( NonTerminal $non_terminal , Rule $rule , $index = 0 ) {
		$this->non_terminal = $non_terminal;
		$this->rule = $rule;
		$this->index = $index;
		$this->next_symbol = null;
		$this->key = $non_terminal->GetKey() . ' ->';
		$i = 0;
		/** @var $symbol Symbol */
		foreach ($rule as $symbol_key => $symbol) {
			if ($i == $index) {
				$this->key .= ' .';
				$this->next_symbol = $symbol;
			}
			$this->key .= ' ' . $symbol_key;
			$i++;
		}
		if (is_null($this->next_symbol)) $this->key .= ' .';
	}

	/** @return SRItem */
	public function GetShiftedItem(){
		return is_null($this->next_symbol) ? null : new SRItem($this->non_terminal,$this->rule,$this->index+1);
	}
}


class SRState implements IteratorAggregate{
	private $items = array();
	private $key;
	public function __construct( $initial_items ){
		foreach ($initial_items as $item)
			$this->Add($item);
		ksort($this->items);
		$this->key = implode("\n",array_keys($this->items));
	}
	private function Add(SRItem $item){
		if (array_key_exists($item->GetKey(),$this->items)) return;
		$this->items[$item->GetKey()] = $item;
		$next_symbol = $item->GetNextSymbol();
		if ($next_symbol instanceof NonTerminal)
			foreach ($next_symbol as $rule)
				$this->Add( new SRItem($next_symbol,$rule) );
	}
	public function GetKey(){ return $this->key; }
	public function getIterator(){ return new ArrayIterator($this->items); }
}



