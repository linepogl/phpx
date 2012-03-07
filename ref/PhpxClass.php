<?php
namespace Phpx;

class PhpxClass {
	public $Name;
	public $Access = AccessPublic;
	public $Parent = null;
	public $Classes = array();

	public $Source;

	public function GetCompiledName(){
		return $this->Parent->GetCompiledName() . '___' . $this->Name;
	}


	public function GetLevel(){
		$r = 1;
		for ($x = $this->Parent; !is_null($x); $x = $x->Parent)
			$r++;
		return $r;
	}
	public function FindQName(QName $qname){
		$level = $this->GetLevel();
		$key = $qname->GetNameAt($level);
		if (array_key_exists($key,$this->Classes)) {
			$class = $this->Classes[$key];
			if ($level + 1 == $qname->Count())
				return $class;
			else
				return $class->FindQName($qname);
		}
		return null;
	}


	public function Export(Writer $w){

		$w->WriteLn('class '.$this->GetCompiledName().' {');
		$w->WriteLn('}');

		foreach ($this->Classes as $x)
			$x->Export($w);

	}


}

?>
