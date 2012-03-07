<?php
namespace Phpx;

class PhpxModule {
	public $Name;
	public $Access;
	public $Parent = null;
	public $Assembly = null;
	public $Modules = array();
	public $Classes = array();

	public $Source;

	public function RenderText(){
		echo $this->Name . "\n";
	}

	public function GetCompiledName(){
		return (is_null($this->Parent) ? '__phpx' : $this->Parent->GetCompiledName() ) . '___' . $this->Name;
	}

	public function GetFullName(){
		$r = $this->Name;
		for ($m = $this->Parent; !is_null($m); $m = $m->Parent)
			$r = $m->Name . '.' . $r;
		return $r;
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
		if (array_key_exists($key,$this->Modules)) {
			$module = $this->Modules[$key];
			if ($level + 1 == $qname->Count())
				return $module;
			else
				return $module->FindQName($qname);
		}
		elseif (array_key_exists($key,$this->Classes)) {
			$class = $this->Classes[$key];
			if ($level + 1 == $qname->Count())
				return $class;
			else
				return $class->FindQName($qname);
		}
		return null;
	}


	public function Export(Writer $w){

		foreach ($this->Modules as $x)
			$x->Export($w);
		foreach ($this->Classes as $x)
			$x->Export($w);

	}








}

?>
