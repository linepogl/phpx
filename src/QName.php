<?php
namespace Phpx;

class QName {
	private $names;

	public function __construct($string=null){
		if (is_null($string))
			$this->names = array();
		else
			$this->names = explode('.',$string);
	}

	public function Append(QName $qname){
		$this->names = array_merge( $this->names , $qname->names );
		return $this;
	}
	public function AppendString($string){
		$this->names = array_merge( $this->names , explode('.',$string) );
		return $this;
	}

	public function GetParent(){
		if (count($this->names) <= 1)
			return null;
		else {
			$r = clone $this;
			array_pop($r->names);
			return $r;
		}
	}

	public function ToString(){
		return implode('.',$this->names);
	}

	public function GetNameAt($index){
		return $this->names[$index];
	}

	public function Count(){
		return count($this->names);
	}

	public function GetName(){
		return $this->names[count($this->names)-1];
	}

}
?>
