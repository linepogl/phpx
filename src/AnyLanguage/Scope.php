<?php

class Scope {

	/** @var Scope */
	private $parent = null;
	private $data = array();


	public function GetType( $symbol ) {
		if (array_key_exists($symbol,$this->data))
			return $this->data[$symbol];
		if (!is_null($this->parent))
			return $this->parent->GetType($symbol);
		return null;
	}

	public function SetType( $symbol , $type ){
		$this->data[$symbol] = $type;
	}

	/** @return Scope */
	public function Extend(){
		$r = new Scope();
		$r->parent = $this;
		return $r;
	}
}
