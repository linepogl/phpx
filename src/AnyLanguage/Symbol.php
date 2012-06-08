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
