<?php

const TUnknown = 'TUnknown';
const TEndOfFile = 'TEndOfFile';
const TEndOfInput = 'TEndOfInput';

const TIdentifier = 'TIdentifier';
const TIntLiteral = 'TIntLiteral';
const TComment = 'TComment';



abstract class Grammar implements IteratorAggregate,ArrayAccess,Countable {


	/** @var SymbolSet */ private $symbols;
	/** @var SymbolSet */ private $terminals;
	/** @var SymbolSet */ private $non_terminals;
	/** @var SymbolSet */ private $rules;
	/** @var SymbolMap */ private $reductions;
	public final function __construct(){
		$this->symbols = new SymbolSet();
		$this->terminals = new SymbolSet();
		$this->non_terminals = new SymbolSet();
		$this->rules = new SymbolSet();
		$this->reductions = new SymbolMap();
		$this[TUnknown] = Terminal();
		$this[TEndOfFile] = Terminal();
		$this[TEndOfInput] = Terminal();
		$this[TIdentifier] = Terminal();
		$this[TIntLiteral] = Terminal();
		$this[TComment] = Terminal();
		$this[NProgram] = Panic( TEndOfInput );
		$this->Init();
	}

	public function Count(){ return $this->symbols->Count(); }
	public function GetIterator(){ return $this->symbols->GetIterator(); }
	public function OffsetExists( $offset ){ return $this->symbols->Contains( $offset instanceof Symbol ? $offset : new Symbol($offset) ); }
	public function OffsetUnset( $offset ){ $this->symbols->Remove( $offset instanceof Symbol ? $offset : new Symbol($offset) ); }
	public function OffsetGet( $offset ){ return $this->symbols->Get( $offset instanceof Symbol ? $offset : new Symbol($offset) ); }
	public function OffsetSet( $offset , $value ){ $this->Place($offset,$value); }









	protected abstract function OnInit();
	public function Init(){
		$this->OnInit();

		$this->InitProductions();
		$this->DebugProductions();

		$this->InitFixedLexemeMap();
		$this->DebugFixesLexemes();

		$this->InitFirstSets();
		$this->DebugFirstSets();

		$this->InitFollowSets();
		$this->DebugFollowSets();

		$this->InitLL1Parsing();
		$this->DebugLL1Parsing();

		$this->InitLR1Parsing();
		$this->DebugLR1Parsing();
	}





	//
	//
	// PRODUCTIONS
	//
	//
	/** @var NonTerminal */ private $starting_non_terminal = null;
	/** @return NonTerminal */
	public function GetStartingNonTerminal(){
		return $this->starting_non_terminal;
	}
	public function GetReduction( NonTerminal $non_terminal , Rule $rule ){
		/** @var $map SymbolMap */
		$map = $this->reductions->Get( $non_terminal );
		return is_null($map) ? null : $map->Get( $rule );
	}
	private function Place($key,$value){
		if (is_null($key)) throw new Exception('Define a key');
		/** @var $symbol Symbol */
		$symbol = $key instanceof Symbol ? $key : new Symbol($key);
		if ($value instanceof TerminalInstruction) {
			/** @var $value TerminalInstruction */
			if ($this->symbols->Contains($symbol)) throw new Exception('Symbol already exists '.$symbol.'.');
			$terminal = new Terminal( $key , $value->fixed_lexeme );
			$this->symbols->Add($terminal);
			$this->terminals->Add($terminal);
		}
		elseif ($value instanceof RuleInstruction || $value instanceof PanicInstruction) {

			/** @var $non_terminal NonTerminal */
			$non_terminal = $this->symbols->Get( $symbol );
			if (is_null($non_terminal)) {
				$non_terminal = new NonTerminal( $key );
				$this->symbols->Add( $non_terminal );
				$this->non_terminals->Add( $non_terminal );
			}
			elseif (!($non_terminal instanceof NonTerminal)) {
				throw new Exception('Not a non-terminal.');
			}

			if ($value instanceof RuleInstruction) {
				/** @var $value RuleInstruction */
				/** @var $rule Rule */
				$rule = new Rule( $value->symbols );
				$rule = $this->rules->GetOrAdd( $rule );
				$this->symbols->Add( $rule );
				$non_terminal->AddRule( $rule );

				if (!is_null($value->reduction)) {
					/** @var $map SymbolMap */
					$map = $this->reductions->GetOrSet( $non_terminal , new SymbolMap() );
					$map->Set( $rule , $value->reduction );
				}
			}
			elseif ($value instanceof PanicInstruction){
				foreach ($value->synchronisation_terminals as $terminal){
					/** @var $real_terminal Terminal */
					$real_terminal = $this->symbols->Get($terminal);
					if (is_null($real_terminal)) throw new GrammarException('Unknown terminal '.$terminal.'.');
					if (!($real_terminal instanceof Terminal)) throw new GrammarException('Not a terminal '.$terminal.'.');
					$non_terminal->AddSynchronisationTerminal( $real_terminal );
				}
			}
		}
	}
	private function InitProductions(){
		/** @var $rule Rule */
		foreach ($this->rules as $rule) {
			$rule->Init( $this->symbols );
		}
		/** @var $non_terminal NonTerminal */
		foreach ($this->non_terminals as $non_terminal){
			$this->starting_non_terminal = $non_terminal;
			break;
		}
	}
	private function DebugProductions(){
		echo "PRODUCTIONS\n-----------\n";
		echo "START -> " . $this->starting_non_terminal . "\n";
		/** @var $non_terminal NonTerminal */
		foreach ($this->non_terminals as $non_terminal)
			/** @var $rule Rule */
			foreach ($non_terminal->GetRules() as $rule)
				echo $non_terminal.' -> '.$rule . "\n";
		echo "\n";
	}







	//
	//
	// LEXEMES
	//
	//
	/** @var array */
	private $fixed_lexeme_map;
	/** @return array */
	public function GetFixedLexemeMap(){
		return $this->fixed_lexeme_map;
	}
	public function InitFixedLexemeMap(){
		$this->fixed_lexeme_map = array();
		/** @var $terminal Terminal */
		foreach ($this->terminals as $terminal){
			$fixed_lexeme = $terminal->GetFixedLexeme();
			if (empty($fixed_lexeme)) continue;
			$this->fixed_lexeme_map[$fixed_lexeme] = $terminal;
		}
	}
	private function DebugFixesLexemes(){
		echo "FIXED LEXEMES\n-------------\n";
		/** @var $non_terminal NonTerminal */
		foreach ($this->fixed_lexeme_map as $lexeme => $non_terminal)
			echo $lexeme.' -> '.$non_terminal. "\n";
		echo "\n";
	}







	//
	//
	// FIRST SETS
	//
	//
	/** @var SymbolMap */
	private $goes_to_epsilon;
	/** @var SymbolMap */
	private $first_sets;
	private function GoesToEpsilon( Symbol $symbol ) {
		if (!$this->goes_to_epsilon->Contains($symbol)){
			$r = false;
			if ( $symbol instanceof NonTerminal ) {
				/** @var $symbol NonTerminal */
				foreach ($symbol->GetRules() as $rule){
					$r = $this->GoesToEpsilon($rule);
					if ($r) break;
				}
			}
			elseif ( $symbol instanceof Rule ){
				/** @var $symbol Rule */
				$found = false;
				foreach ($symbol->GetSymbols() as $symbol2) {
					if (!$this->GoesToEpsilon($symbol2)) {
						$found = true;
						break;
					}
				}
				$r = !$found;
			}
			$this->goes_to_epsilon->Set($symbol,$r);
		}
		return $this->goes_to_epsilon->Get($symbol);
	}
	private function InitFirstSets() {
		$this->goes_to_epsilon = new SymbolMap();
		$this->first_sets = new SymbolMap();
		foreach ($this->symbols as $symbol )
			$this->GoesToEpsilon( $symbol );
		foreach ($this->symbols as $symbol){
			$r = new SymbolSet();
			if ($symbol instanceof Terminal) {
				$r->Add($symbol);
			}
			elseif ($symbol instanceof Rule) {
				/** @var $symbol Rule */
				/** @var $symbol2 Symbol */
				foreach ($symbol->GetSymbols() as $symbol2){
					if (!$symbol2->IsEqualTo($symbol)) $r->Add($symbol2);
					if (!$this->goes_to_epsilon->Get($symbol2)) break;
				}
			}
			elseif ($symbol instanceof NonTerminal) {
				/** @var $symbol NonTerminal */
				/** @var $rule Rule */
				foreach ($symbol->GetRules() as $rule){
					$r->Add($rule);
				}
			}
			$this->first_sets->Set($symbol,$r);
		}
		$this->ReduceFirstSets();
	}
	private function ReduceFirstSets(){
		$again = 0;
		/** @var $symbol Symbol */
		foreach ($this->symbols as $symbol) {
			/** @var $set SymbolSet */
			$set = $this->first_sets->Get($symbol);
			$add_set = new SymbolSet();
			/** @var $symbol2 Symbol */
			foreach ($set as $symbol2){
				if (!($symbol2 instanceof Terminal)) {
					$new_set = $this->first_sets->Get($symbol2);
					$add_set->AddMany( $new_set );
					$add_set->Remove($symbol);
					$set->Remove($symbol2);
				}
			}
			if ($add_set->Count() > 0) {
				$again++;
				$set->AddMany( $add_set );
			}
		}
		if ($again>0) $this->ReduceFirstSets();
	}
	public function DebugFirstSets(){
		echo "FIRST SETS\n----------\n";
		foreach ($this->symbols as $symbol) {
			if ($symbol instanceof Terminal) continue;
			/** @var $set SymbolSet */
			$set = $this->first_sets->Get($symbol);
			echo $symbol.' : { ';
			if ($this->goes_to_epsilon->Get($symbol)) {
				echo '&epsilon;';
				if ($set->Count() > 0)
					echo ' , ';
			}
			echo $set->Implode(' , ');
			echo " }\n";
		}
		echo "\n";
	}








	//
	//
	// FOLLOW SETS
	//
	//
	/** @var SymbolMap */
	private $follow_sets;
	private function InitFollowSets(){
		$this->follow_sets = new SymbolMap();
		/** @var $symbol Symbol */
		foreach ($this->symbols as $symbol ){
			$set = new SymbolSet();
			if ($symbol->IsEqualTo( $this->starting_non_terminal ))
				$set->Add( $this[TEndOfInput] );
			/** @var $non_terminal NonTerminal */
			foreach ($this->non_terminals as $non_terminal) {
				/** @var $rule Rule */
				foreach ($non_terminal->GetRules() as $rule) {
					foreach ($rule->GetSymbols() as $i => $symbol_i){
						if ($symbol->IsEqualTo($symbol_i)) {
							$finished = false;
							foreach ($rule->GetSymbols() as $j => $symbol_j){
								if ($j > $i) {
									$set->AddMany( $this->first_sets->Get($symbol_j) );
									if (!$this->goes_to_epsilon->Get($symbol_j)) {
										$finished = true;
										break;
									}
								}
							}
							if (!$finished && !$non_terminal->IsEqualTo($symbol_i))
								$set->Add( $non_terminal );
						}
					}
				}
			}
			$this->follow_sets->Set( $symbol , $set );
		}
		$this->ReduceFollowSets();
	}
	private function ReduceFollowSets(){
		$again = 0;
		/** @var $symbol Symbol */
		foreach ($this->symbols as $symbol){
			/** @var $set SymbolSet */
			$set = $this->follow_sets->Get($symbol);
			$add_set = new SymbolSet();
			/** @var $symbol2 Symbol */
			foreach ($set as $symbol2){
				if ($symbol2 instanceof NonTerminal) {
					$add_set->AddMany( $this->follow_sets->Get($symbol2) );
					$add_set->Remove( $symbol );
					$set->Remove( $symbol2 );
				}
			}
			if ($add_set->Count() > 0) {
				$again++;
				$set->AddMany( $add_set );
			}
		}
		if ($again > 0) $this->ReduceFollowSets();
	}
	public function DebugFollowSets(){
		echo "FOLLOW SETS\n-----------\n";
		foreach ($this->symbols as $symbol) {
			if ($this instanceof Rule) continue;
			echo $symbol.' : '.$this->follow_sets->Get($symbol)."\n";
		}
		echo "\n";
	}





	//
	//
	// LL(1) PARSING
	//
	//
	/** @var SymbolMap */
	private $ll1_parsing_table;
	/** @var Validator */
	private $ll1_validator;
	public function IsLL1Grammar(){ return count($this->ll1_validator) == 0; }
	private function InitLL1Parsing(){
		$this->ll1_parsing_table = new SymbolMap();
		$this->ll1_validator = new Validator();
		/** @var $non_terminal NonTerminal */
		foreach ($this->non_terminals as $non_terminal){
			/** @var $map SymbolMap */
			$map = $this->ll1_parsing_table->Set( $non_terminal , new SymbolMap() );
			/** @var $rule Rule */
			foreach ($non_terminal->GetRules() as $rule){
				foreach ($this->first_sets->Get($rule) as $terminal) {
					/** @var $set SymbolSet */
					$set = $map->Get($terminal);
					if (is_null($set)) $set = $map->Set($terminal,new SymbolSet());
					$set->Add( $rule );
				}
				if ($this->goes_to_epsilon->Get($rule)) {
					foreach ($this->follow_sets->Get($non_terminal) as $terminal) {
						/** @var $set SymbolSet */
						$set = $map->Get($terminal);
						if (is_null($set)) $set = $map->Set($terminal,new SymbolSet());
						$set->Add($rule);
					}
				}
			}
		}
		foreach ($this->ll1_parsing_table as $non_terminal => $map)
			foreach ($map as $terminal => $set)
				if (count($set) > 1)
					$this->ll1_validator[] = new GrammarException('More than one rules for '.$non_terminal.' on '.$terminal.'.');
	}
	public function DebugLL1Parsing(){
		echo "LL(1) PARSING TABLE\n-------------------\n";
		/** @var $map SymbolMap */
		foreach ($this->ll1_parsing_table as $non_terminal => $map){
			echo $non_terminal . ":\n";
			/** @var $set SymbolSet */
			foreach ($map as $terminal => $set) {
				echo '  on '.$terminal.': '.$set->Implode(' ') . ($set->Count()>1?' ******':'')."\n";
			}
		}
		echo "\n";
		$this->ll1_validator->Debug();
		echo "\n";
	}
	public function GetNextRule( NonTerminal $non_terminal , Terminal $terminal ){
		/** @var $map SymbolMap */
		$map = $this->ll1_parsing_table->Get($non_terminal);
		if (is_null($map)) return null;

		/** @var $set SymbolSet */
		$set = $map->Get($terminal);
		if (is_null($set)) return null;

		foreach ($set as $rule)
			return $rule;

		return null;
	}




	/** @var SymbolSet */
	private $lr1_parsing_states;
	/** @var Validator */
	private $lr1_validator;
	public function IsLR1Grammar(){ return count($this->lr1_validator) == 0; }
	private function InitLR1Parsing( ){
		$this->lr1_parsing_states = new SymbolSet();
		$this->lr1_validator = new Validator();
		$set = new SymbolSet();
		foreach ($this->starting_non_terminal->GetRules() as $rule)
			$set->Add( new SRItem($this->starting_non_terminal,$rule) );

		$state = new SRState( $set );
		$stack = new SymbolSet();
		$stack->Add( $state );
		$this->AnalyzeStates( $stack );
	}
	private function AnalyzeStates( SymbolSet $stack ) {
		if ($stack->Count() == 0) return;

		/** @var $state SRState */
		$state = $stack->Pop();
		$this->lr1_parsing_states->Add($state);
		$state->InitNumber( $this->lr1_parsing_states->Count() );
		$state->InitNextSymbolsTable( $this->follow_sets );


		$state_after_shift_map = new SymbolMap();
		foreach ($state->GetNextSymbolActions() as $next_symbol => $actions) {
			/** @var $next_symbol Symbol */
			$next_symbol = $this[$next_symbol];
			if (count($actions) > 1)
				$this->lr1_validator[] = new GrammarException( implode('-',$actions) . ' conflict on next symbol '.$next_symbol.'.' );

			$items_after_shift = $state->GetItemsAfterShiftForNextSymbol($next_symbol);

			$state_after_shift = new SRState($items_after_shift);

			if ($this->lr1_parsing_states->Contains($state_after_shift))
				$state_after_shift = $this->lr1_parsing_states->Get($state_after_shift);
			else
				$state_after_shift = $stack->GetOrAdd($state_after_shift);

			$state_after_shift_map->Set($next_symbol,$state_after_shift);
		}
		$state->InitStateAfterShiftMap( $state_after_shift_map );

		$this->AnalyzeStates( $stack );
	}
	public function DebugLR1Parsing(){
		echo "LR(1) PARSING TABLE\n-------------------\n";
		foreach ($this->lr1_parsing_states as $state){
			/** @var $state SRState */
			echo $state->GetNumber() . '. '.$state->AsString() . ":\n";
			foreach ($state->GetItems() as $item){
				echo '  '. $item ."\n";
			}
			/** @var $items SymbolSet */
			foreach ($state->GetNextSymbolItems() as $next_symbol => $items) {
				/** @var $next_symbol Symbol */
				$next_symbol = $this[$next_symbol];
				$actions = $state->GetActionsForNextSymbol($next_symbol);

				echo '    on '.$next_symbol.': ';
				echo implode('-',$actions);
				if (count($actions) > 1) echo ' *****';
				echo ' ';
				echo $items->Implode(' ');
				echo "\n";
			}
		}
		$this->lr1_validator->Debug();
		echo "\n";
	}







	public function DebugReport(){
		$this->DebugProductions();
		$this->DebugFirstSets();
		$this->DebugFollowSets();
		$this->DebugLL1ParsingTable();
		$this->DebugLR1ParsingTable();
	}
}

