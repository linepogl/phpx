<?php

class SymbolList implements IteratorAggregate, Countable {
	private $data = array();

	public function GetIterator(){
		return new ArrayIterator($this->data);
	}

	public function Count(){
		return count($this->data);
	}

	/** @return Symbol */
	public function Pop( ){
		return array_pop($this->data);
	}

	/** @return Symbol */
	public function Add( Symbol $symbol ){
		$this->data[] = $symbol;
		return $symbol;
	}

	/** @return Symbol */
	public function Get( $index ){
		return $this->data[ $index ];
	}

	/** @return Symbol */
	public function Set( $index , Symbol $symbol ){
		$this->data[ $index ] = $symbol;
		return $symbol;
	}

	public function Contains( Symbol $symbol ){
		foreach ($this->data as $x)
			if ($symbol->IsEqualTo( $x ))
				return true;
		return false;
	}

	public function Remove( $index ){
		unset( $this->data[ $index ] );
	}

	public function Implode($glue = ''){
		$r = '';
		foreach ($this->data as $symbol){
			if (!empty($r)) $r .= $glue;
			$r .= $symbol;
		}
		return $r;
	}

	public function __toString(){ return '['.$this->Implode(' ').']'; }
	public function AsString(){ return '['.$this->Implode(' ').']'; }
}
