<?php

class Symbol {
	private $key;
	public function __construct( $key ) { $this->key = strval($key); }
	public function AsString(){ return $this->key; }
	public function __toString(){ return $this->key; }
	public function IsEqualTo( Symbol $symbol ){
		return $this->key === $symbol->AsString();
	}
	public function Is( $symbol ){
		return $this->key === strval($symbol);
	}
}


class Terminal extends Symbol {
	const SPECIAL = 0;
	private $fixed_lexeme;
	public function __construct( $key , $fixed_lexeme = self::SPECIAL ) {
		parent::__construct($key);
		if (is_string($fixed_lexeme))
			$this->fixed_lexeme = $fixed_lexeme;
	}
	public function GetFixedLexeme(){
		return $this->fixed_lexeme;
	}


}


class NonTerminal extends Symbol {
	/** @var SymbolSet */
	private $rules;
	/** @var SymbolSet */
	private $synchronisation_terminals;
	public function __construct( $key ){
		parent::__construct($key);
		$this->rules = new SymbolSet();
		$this->synchronisation_terminals = new SymbolSet();
	}
	public function AddRule( Rule $rule ){
		$this->rules->Add( $rule );
	}
	public function AddSynchronisationTerminal( Terminal $terminal ){
		$this->synchronisation_terminals->Add( $terminal );
	}
	/** @return SymbolSet */
	public function GetRules(){ return $this->rules; }
	/** @return SymbolSet */
	public function GetSynchronisationTerminals(){ return $this->synchronisation_terminals; }
}




class Rule extends Symbol {
	/** @var SymbolList */
	private $symbols;
	public function __construct( SymbolList $symbols ) {
		$this->symbols = $symbols;
		parent::__construct( $symbols );
	}
	public function Init( SymbolSet $all_symbols ){
		foreach ($this->symbols as $i => $symbol){
			if (!$all_symbols->Contains($symbol)) throw new GrammarException('Unknown symbol '.$symbol.'.');
			$this->symbols->Set( $i , $all_symbols->Get($symbol) );
		}
	}
	/** @return SymbolList */
	public function GetSymbols(){ return $this->symbols; }

	private $ast_class_name;
	/** @return Rule */
	public function Ast($class_name){
		$this->ast_class_name = $class_name;
		return $this;
	}
	public function GetAstClassName(){
		return $this->ast_class_name;
	}
}






class SRItem extends Symbol {
	private $non_terminal;
	private $rule;
	private $index;
	private $next_symbol;

	/** @return NonTerminal */
	public function GetNonTerminal(){ return $this->non_terminal; }
	/** @return Rule */
	public function GetRule(){ return $this->rule; }
	/** @return Symbol */
	public function GetNextSymbol(){ return $this->next_symbol; }
	public function __construct( NonTerminal $non_terminal , Rule $rule , $index = 0 ) {
		$this->non_terminal = $non_terminal;
		$this->rule = $rule;
		$this->index = $index;
		$this->next_symbol = null;
		$i = 0;
		$list = new SymbolList();
		/** @var $symbol Symbol */
		foreach ($rule->GetSymbols() as $symbol) {
			if ($i == $index) {
				$list->Add(new Symbol('.'));
				$this->next_symbol = $symbol;
			}
			$list->Add($symbol);
			$i++;
		}
		if (is_null($this->next_symbol)) $list->Add(new Symbol('.'));
		parent::__construct($non_terminal.'->'.$list);
	}
	public function IsReduce(){ return is_null($this->next_symbol); }
	/** @return SRItem */
	public function GetItemAfterShift(){
		return is_null($this->next_symbol) ? null : new SRItem($this->non_terminal,$this->rule,$this->index+1);
	}
}




class SRState extends Symbol {
	/** @var SymbolSet */
	private $items;
	public function __construct( SymbolSet $initial_items ){
		$this->items = new SymbolSet();
		foreach ($initial_items as $item)
			$this->Add($item);
		$this->items->Sort();
		parent::__construct(md5($this->items->Implode("\n")));
	}
	private function Add(SRItem $item){
		if ($this->items->Contains($item)) return;
		$this->items->Add($item);
		$next_symbol = $item->GetNextSymbol();
		if ($next_symbol instanceof NonTerminal)
			/** @var $next_symbol NonTerminal */
			foreach ($next_symbol->GetRules() as $rule)
				$this->Add( new SRItem($next_symbol,$rule) );
	}
	/** @return SymbolSet */
	public function GetItems(){ return $this->items; }

	private $number = null;
	public function GetNumber(){ return $this->number; }
	public function InitNumber( $value ){
		if (!is_null($this->number)) throw new Exception();
		$this->number = $value;
	}

	const SHIFT = 'SHIFT';
	const REDUCE = 'REDUCE';
	/** @var SymbolMap */
	private $next_symbol_items;
	/** @var SymbolMap */
	private $next_symbol_items_after_shift;
	/** @var SymbolMap */
	private $next_symbol_actions;
	public function InitNextSymbolsTable( SymbolMap $follow_sets ){
		$this->next_symbol_items = new SymbolMap();
		$this->next_symbol_items_after_shift = new SymbolMap();
		$this->next_symbol_actions = new SymbolMap();
		/** @var $item SRItem */
		foreach ($this->items as $item){
			if ($item->IsReduce()){
				foreach ($follow_sets->Get($item->GetNonTerminal()) as $terminal){
					$next_symbol = $terminal;
					/** @var $set SymbolSet */
					$set = $this->next_symbol_items->GetOrSet($next_symbol,new SymbolSet());
					$set->Add($item);
				}
			}
			else {
				$next_symbol = $item->GetNextSymbol();
				/** @var $set SymbolSet */
				$set = $this->next_symbol_items->GetOrSet($next_symbol,new SymbolSet());
				$set->Add($item);

				$item_after_shift = $item->GetItemAfterShift();
				/** @var $set SymbolSet */
				$set = $this->next_symbol_items_after_shift->GetOrSet($next_symbol,new SymbolSet());
				$set->Add($item_after_shift);
			}
		}
		foreach ($this->next_symbol_items as $next_symbol => $items){
			$a = array();
			$shifts = 0;
			/** @var $item SRItem */
			foreach ($items as $item) {
				if ($item->IsReduce())
					$a[] = self::REDUCE;
				else
					$shifts++;
			}
			if ( $shifts > 0)
				$a[] = self::SHIFT;
			$this->next_symbol_actions->Set(new Symbol($next_symbol),$a);
		}
	}
	public function GetItemsForNextSymbol( Symbol $next_symbol ){
		return $this->next_symbol_items->GetOr($next_symbol,new SymbolSet());
	}
	public function GetItemsAfterShiftForNextSymbol( Symbol $next_symbol ){
		return $this->next_symbol_items_after_shift->GetOr($next_symbol,new SymbolSet());
	}
	public function GetActionsForNextSymbol( Symbol $next_symbol ){
		return $this->next_symbol_actions->GetOr($next_symbol,array());
	}
	/** @return SymbolMap */
	public function GetNextSymbolItems(){ return $this->next_symbol_items; }
	/** @return SymbolMap */
	public function GetNextSymbolActions(){ return $this->next_symbol_actions; }



	/** @var SymbolMap */
	private $state_after_shift_map;
	public function InitStateAfterShiftMap( SymbolMap $state_after_shift_map ){
		if (!is_null($this->state_after_shift_map)) throw new Exception();
		$this->state_after_shift_map = $state_after_shift_map;
	}
	/** @return SRState */
	public function GetStateAfterShiftForNextSymbol( Symbol $next_symbol ){
		return $this->state_after_shift_map->Get($next_symbol);
	}

}



