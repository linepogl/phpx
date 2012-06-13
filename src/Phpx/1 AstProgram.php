<?php

class AstProgram extends AstNode {

	private $statements;

	public function __construct( $statements ) {
		$this->statements = $statements;
		parent::__construct(new SourcePos(),'void');
	}


	public function Debug($level = 0){
		parent::Debug($level);
		foreach ($this->statements as $x)
			$x->Debug($level + 1);
	}

}
