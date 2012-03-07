<?php
namespace Phpx;

class PhpxAssembly {
	public $Modules = array();

	public function __construct(){
		$this->validator = new Validator();
	}

	public function RenderText(){
		foreach ($this->Modules as $m)
			$m->RenderText();
	}


	public function FindQName(QName $qname){
		$key = $qname->GetNameAt(0);
		if (array_key_exists($key,$this->Modules)) {
			$module = $this->Modules[$key];
			if (1 == $qname->Count())
				return $module;
			else
				return $module->FindQName($qname);
		}
		return null;
	}


	public function Export(Writer $w){

		foreach ($this->Modules as $x)
			$x->Export($w);

	}


}

?>
