<?php


class SymbolMap implements IteratorAggregate, Countable {
	private $data = array();

	public function GetIterator(){
		return new ArrayIterator($this->data);
	}

	public function Count(){
		return count($this->data);
	}

	public function Set( Symbol $symbol , $value ){
		$this->data[ $symbol->AsString() ] = $value;
		return $value;
	}

	public function Get( Symbol $symbol ){
		$key = $symbol->AsString();
		return array_key_exists($key,$this->data) ? $this->data[$key] : null;
	}

	public function GetOr( Symbol $symbol , $value ){
		$key = $symbol->AsString();
		return array_key_exists($key,$this->data) ? $this->data[$key] : $value;
	}

	public function GetOrSet( Symbol $symbol , $value ){
		$key = $symbol->AsString();
		if (!array_key_exists($key,$this->data))
			$this->data[$key] = $value;
		return $this->data[$key];
	}

	public function Contains( Symbol $symbol ){
		return array_key_exists( $symbol->AsString() , $this->data );
	}

	public function Remove( Symbol $symbol ){
		unset( $this->data[$symbol->AsString()] );
	}

}
