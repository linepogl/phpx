<?php

function Terminal( $fixed_lexeme = null ){
	return new TerminalInstruction( $fixed_lexeme );
}
class TerminalInstruction {
	public $fixed_lexeme;
	public function __construct( $fixed_lexeme ){
		$this->fixed_lexeme = $fixed_lexeme;
	}
}

/** @return RuleInstruction */
function Rule( ) {
	$a = new SymbolList();
	foreach (func_get_args() as $symbol) {
		$a->Add( $symbol instanceof Symbol ? $symbol : new Symbol($symbol) );
	}
	return new RuleInstruction( $a );
}

class RuleInstruction {
	/** @var SymbolList */
	public $symbols;
	public $reduction;
	/** @var SymbolSet */
	public $synchronisation_terminals;

	public function __construct( SymbolList $symbols ){
		$this->symbols = $symbols;
		$this->synchronisation_terminals = new SymbolSet();
	}
	/** @return RuleInstruction */
	public function OnReduce( $function ){
		$this->reduction = $function;
		return $this;
	}
	/** @return RuleInstruction */
	public function SynchronizedOn( $terminal ){
		$this->synchronisation_terminals->Add(new Symbol($terminal));
		return $this;
	}
}


/** @return PanicInstruction */
function Panic( ) {
	$a = new SymbolSet();
	foreach (func_get_args() as $symbol) {
		$a->Add( $symbol instanceof Symbol ? $symbol : new Symbol($symbol) );
	}
	return new PanicInstruction( $a );
}

class PanicInstruction {
	/** @var SymbolSet */
	public $synchronisation_terminals;
	public function __construct( SymbolSet $symbols ){
		$this->synchronisation_terminals = $symbols;
	}
}

