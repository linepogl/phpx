<?php

class AstComplexType extends AstType {

	private $namespaces = array();
	private $name;

	public static function Make(ParseTree $parse_tree) {
		$r = new self();
		$r->source_pos = $parse_tree->GetChild(0)->GetToken()->GetSourcePos();
		$r->Devour($parse_tree);
		return $r;
	}
	private function Devour(ParseTree $parse_tree) {
		$head = $parse_tree->GetChild(0);
		$tail = $parse_tree->GetChild(1);
		if ($tail->HasChildren()){
			$this->namespaces[] = $head->GetToken()->GetLexeme();
			$this->Devour($tail->GetChild(1));
		}
		else {
			$this->name = $head->GetToken()->GetLexeme();
		}
	}

	public function DebugReport($level = 0){
		$tabs = str_repeat('  ',$level);
		echo $tabs . get_called_class() . ': ';
		foreach ($this->namespaces as $ns)
			echo $ns.'.';
		echo $this->name . ' ['.$this->compile_time_type. '] ' . $this->source_pos . "\n";
	}

	public function CalculateType(Scope $scope, Validator $v){
		$this->compile_time_type = implode('.',$this->namespaces).'.'.$this->name;
		return $this->compile_time_type;
	}

}
