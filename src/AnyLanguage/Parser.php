<?php

class Parser {

	private $grammar;
	public function __construct(Grammar $grammar){
		$this->grammar = $grammar;
	}




	/** @var Token */
	private $token = null;
	private $lexers = array();
	private $current_lexer_index = 0;
	public function AddFile($filename){
		$this->lexers[] = new Lexer($this->grammar,new Reader($filename));
	}
	private function Reset(){
		$this->token = null;
		$this->current_lexer_index = 0;
	}
	private function ReadNextToken(Validator $v){
		if ($this->current_lexer_index >= count($this->lexers)) {
			$this->token = new Token($this->grammar[TEndOfInput],'',new SourcePos());
		}
		else {
			$this->lexers[$this->current_lexer_index]->SkipComments();
			$this->token = $this->lexers[$this->current_lexer_index]->Consume();
			if ($this->token->Is(TEndOfFile)) $this->current_lexer_index++;
			elseif ($this->token->Is(TUnknown)) {
				$v[] = new CompileTimeException('Unknown token '.$this->token->GetLexeme().'.',$this->token->GetSourcePos());
				$this->ReadNextToken($v);
			}
		}
	}


	/** @return AstNode */
	public function Parse( Validator $v ){
		$this->Reset();
		$this->ReadNextToken($v);
		if ( $this->grammar->IsLL1Grammar() ) {
			$root = new ParseNode( $this->grammar->GetStartingNonTerminal() );
			$root = $this->InnerParseLL1( $root , $v );
			return $root;
		}
		elseif ( $this->grammar->IsLR1Grammar() ){
			throw new Exception('LR(1) Parsing not implemented.');
		}
		throw new GrammarException( 'Grammar is neither LL(1) nor LR(1).' );
	}


	private $panic_mode = false;
	/** @return ParseNode|AstNode */
	private function InnerParseLL1( ParseNode $node , Validator $v ){

		/** @var $non_terminal NonTerminal */
		/** @var $terminal Terminal */
		/** @var $rule Rule */
		/** @var $sync SymbolSet */
		$non_terminal = $node->GetNonTerminal();
		$sync = $non_terminal->GetSynchronisationTerminals();

		$next_terminal = $this->token->GetTerminal();
		$rule = $this->grammar->GetNextRule( $non_terminal , $next_terminal );

		if (is_null($rule)){
			if (!$this->panic_mode){
				$v[] = new CompileTimeException('Unexpected token '.$this->token.'.',$this->token->GetSourcePos());
				$this->panic_mode = true;
			}
			elseif ($sync->Contains($next_terminal)){
				return null;
			}
			if (count($sync) == 0) return null;
			while(true) {
				$this->ReadNextToken($v);
				$next_terminal = $this->token->GetTerminal();
				if ($sync->Contains($next_terminal)){
					$rule = $this->grammar->GetNextRule( $non_terminal , $next_terminal );
					$this->panic_mode = false;
					if (is_null($rule)){
						$this->ReadNextToken($v);
						return null;
					}
					else
						break;
				}
			}
		}


		/** @var $symbol Symbol */
		foreach ($rule->GetSymbols() as $symbol ){

			if ($symbol instanceof Terminal) {

				if ($this->token->Is( $symbol )){
					$node[] = $this->token;
					$this->ReadNextToken($v);
				}
				else {
					if (!$this->panic_mode) {
						$v[] = new CompileTimeException('Unexpected token '.$this->token.'.',$this->token->GetSourcePos());
						$this->panic_mode = true;
						if (count($sync) == 0) return null;
					}
					while(true) {
						$this->ReadNextToken($v);
						$next_terminal = $this->token->GetTerminal();
						if ($sync->Contains($next_terminal)){
							$rule = $this->grammar->GetNextRule( $non_terminal , $next_terminal );
							$this->panic_mode = false;
							if (is_null($rule))
								return null;
							else
								return $this->InnerParseLL1($node,$v);
						}
					}

				}

			}
			else {
				$x = $this->InnerParseLL1( new ParseNode( $symbol , $node ), $v );
				if (is_null($x))
					return $this->InnerParseLL1($node,$v);
				$node[] = $x;
			}
		}


		/** @var $reduction callable */
		$reduction = $this->grammar->GetReduction( $non_terminal , $rule );
		if (!is_null($reduction)) {
			$node = $reduction( $node );
		}

		return $node;
	}





}
