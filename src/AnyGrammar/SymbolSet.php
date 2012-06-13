<?php

class SymbolSet implements IteratorAggregate, Countable {
	private $data = array();

	public function Sort(){
		ksort($this->data);
	}

	public function GetIterator(){
		return new ArrayIterator($this->data);
	}

	public function Count(){
		return count($this->data);
	}

	public function Pop(){
		return array_pop($this->data);
	}

	public function Add( Symbol $symbol ){
		$this->data[ $symbol->AsString() ] = $symbol;
	}

	public function AddMany( SymbolSet $symbols ){
		foreach ($symbols as $symbol)
			$this->Add( $symbol );
	}

	public function Get( Symbol $symbol ){
		$key = $symbol->AsString();
		return array_key_exists($key,$this->data) ? $this->data[$key] : null;
	}

	public function GetOrAdd( Symbol $symbol ){
		$key = $symbol->AsString();
		if (!array_key_exists($key,$this->data))
			$this->data[ $key ] = $symbol;
		return $this->data[$key];
	}


	public function Contains( Symbol $symbol ){
		return array_key_exists( $symbol->AsString() , $this->data );
	}

	public function Remove( Symbol $symbol ){
		unset( $this->data[$symbol->AsString()] );
	}

	public function Implode($glue = ''){
		$r = '';
		foreach ($this->data as $symbol){
			if (!empty($r)) $r .= $glue;
			$r .= $symbol;
		}
		return $r;
	}

	public function __toString(){ return '{'.$this->Implode(' , ').'}'; }
	public function AsString(){ return '{'.$this->Implode(' , ').'}'; }
}
