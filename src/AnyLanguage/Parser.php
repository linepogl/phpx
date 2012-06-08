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
	/** @return Token */
	private function GetToken(){
		if (is_null($this->token)) $this->ConsumeToken();
		return $this->token;
	}
	private function Reset(){
		$this->token = null;
		$this->current_lexer_index = 0;
	}
	private function ConsumeToken(){
		if ($this->current_lexer_index >= count($this->lexers)) {
			$this->token = new Token(TEndOfInput,'',new SourcePos());
		}
		else {
			$this->lexers[$this->current_lexer_index]->SkipComments();
			$this->token = $this->lexers[$this->current_lexer_index]->Consume();
			if ($this->token->GetTerminal() == TEndOfFile) $this->current_lexer_index++;
		}
	}


	/** @return AstNode */
	public function Parse( ){
		$this->Reset();
		$r = new ParseTree( $this->grammar->GetStartingNonTerminal() );
		$this->InnerParse( $r );
		return $r;
	}

	/** @return ParseTree */
	private function InnerParse( ParseTree $parse_tree ){
		$item = $parse_tree->GetItem();

		if ($this->grammar->IsTerminal($item)){
			$parse_tree->SetToken($this->GetToken());
			$this->ConsumeToken();
		}
		else {
			$rule = $this->grammar->GetNextRuleKey( $parse_tree->GetItem() , $this->GetToken()->GetTerminal() );

			if (is_null($rule)){
				$parse_tree->SetToken( $this->GetToken() , true );
				$this->ConsumeToken();
			}

			else {
				$items = $this->grammar->GetRuleItems( $rule );

				$children = array();
				foreach ($items as $item)
					$children[] = new ParseTree($item);
				$parse_tree->SetRule( $rule , $children );

				foreach ($children as $child_tree)
					$this->InnerParse( $child_tree );
			}
		}

	}





}
