<?php
//require('expressions.php');
//require('lexer.php');



//class Parser {
//	private $lexer;
//	public function __construct($filename){ $this->lexer = new Lexer($filename); }




//	public function Parse(){
//		while ( $this->lexer->Expect(TAccessModifier,TComment,TEof) ) {
//			if ($this->lexer->Match(TAccessModifier)){

//				$this->lexer->Expect(TAccessModifier);
//				echo Token::Translate($this->lexer->token) .': ' . $this->lexer->string . "\n";
//				$this->lexer->Consume();

//				while ($this->lexer->Match(TComment)) {
//					echo Token::Translate($this->lexer->token) .': ' . $this->lexer->string . "\n";
//					$this->lexer->Consume();
//				}

//				$this->lexer->Expect(TOverrideModifier);
//				echo Token::Translate($this->lexer->token) .': ' . $this->lexer->string . "\n";
//				$this->lexer->Consume();

//				while ($this->lexer->Match(TComment)) {
//					echo Token::Translate($this->lexer->token) .': ' . $this->lexer->string . "\n";
//					$this->lexer->Consume();
//				}

//				$this->lexer->Expect(TClass);
//				echo Token::Translate($this->lexer->token) .': ' . $this->lexer->string . "\n";
//				$this->lexer->Consume();

//				while ($this->lexer->Match(TComment)) {
//					echo Token::Translate($this->lexer->token) .': ' . $this->lexer->string . "\n";
//					$this->lexer->Consume();
//				}

//				$this->lexer->Expect(TIdentifier);
//				echo Token::Translate($this->lexer->token) .': ' . $this->lexer->string . "\n";
//				$this->lexer->Consume();

//				while ($this->lexer->Match(TComment)) {
//					echo Token::Translate($this->lexer->token) .': ' . $this->lexer->string . "\n";
//					$this->lexer->Consume();
//				}

//				$this->lexer->Expect(TCurlyOpen);
//				echo Token::Translate($this->lexer->token) .': ' . $this->lexer->string . "\n";
//				$this->lexer->Consume();

//				while ($this->lexer->Match(TComment)) {
//					echo Token::Translate($this->lexer->token) .': ' . $this->lexer->string . "\n";
//					$this->lexer->Consume();
//				}

//				$this->lexer->Expect(TCurlyClose);
//				echo Token::Translate($this->lexer->token) .': ' . $this->lexer->string . "\n";
//				$this->lexer->Consume();
//			}
//			elseif ($this->lexer->Match(TComment)){
//				echo Token::Translate($this->lexer->token) .': ' . $this->lexer->string . "\n";
//				$this->lexer->Consume();
//			}
//			else break;
//		}


//		return new ProgramExpression();
//	}


//	/**
//	* <program> ::= <function_declaration>*
//	*/
//	public function Parse1(){
//		$r = new ProgramExpression();

//		while ( $this->lexer->Expect(TAccessModifier,TComment,TEof) ) {
//			if ($this->lexer->Match(TAccessModifier)){
//				$x = $this->ParceFunctionDeclaration();
//				$r->Add($x);
//			}
//			elseif ($this->lexer->Match(TComment)){
//				$x = new CommentsExpression();
//				$x->comments = $this->lexer->string	;
//				$this->lexer->Consume();
//				$r->Add($x);
//			}
//			else break;
//		}

//		return $r;
//	}


//	/**
//	* <function_declaration> ::=
//	* <access> <extend> <identifier> <identifier> ( [<identifier> <identifier> [,<identifier> <identifier>]*] ) { <block> }
//	*/
//	private function ParceFunctionDeclaration(){
//		$r = new FunctionDeclarationExpression();

//		$this->lexer->Expect(TAccessModifier);
//		$r->access_modifier = $this->lexer->string;
//		$this->lexer->Consume();

//		$this->lexer->Expect(TOverrideModifier);
//		$r->overridability_modifier = $this->lexer->string;
//		$this->lexer->Consume();

//		$this->lexer->Expect(TIdentifier);
//		$r->type = $this->lexer->string;
//		$this->lexer->Consume();

//		$this->lexer->Expect(TIdentifier);
//		$r->name = $this->lexer->string;
//		$this->lexer->Consume();

//		$this->lexer->Expect(TParenOpen);
//		$this->lexer->Consume();

//		if ($this->lexer->Match(TIdentifier)){
//			$x = new ArgumentDeclarationExpression();
//			$x->type = $this->lexer->string;
//			$this->lexer->Consume();
//			$this->lexer->Expect(TIdentifier);
//			$x->name = $this->lexer->string;
//			$this->lexer->Consume();
//			$r->args[] = $x;
//		}
//		while ($this->lexer->Match(TComma)){
//			$this->lexer->Consume();
//			$this->lexer->Expect(TIdentifier);
//			$x->type = $this->lexer->string;
//			$this->lexer->Consume();
//			$this->lexer->Expect(TIdentifier);
//			$x->name = $this->lexer->string;
//			$this->lexer->Consume();
//			$r->args[] = $x;
//		}

//		$this->lexer->Expect(TParenClose);
//		$this->lexer->Consume();

//		$this->lexer->Expect(TCurlyOpen);
//		$this->lexer->Consume();

//		while (true){
//			if ($this->lexer->Match(TCurlyClose)){
//				$this->lexer->Consume();
//				break;
//			}
//			$this->lexer->Consume();
//		}

		//$r->body = $this->ParceBlock();

//		return $r;
//	}

//	/**
//	* <block> ::= <assignement>; | {[<assignement>;]*}
//	*/
//	private function ParceBlock(){
//		$r = new BlockExpression();
//		if ($this->IsSymbol('{')){
//			$this->lexer->Consume();
//			while ($this->IsIdentifier() || $this->IsSymbol('{') || $this->IsSymbol(';')) {
//				if ($this->IsIdentifier() || $this->IsSymbol(';'))
//					$r->Add($this->ParceStatement());
//				elseif ($this->IsSymbol('{'))
//					$r->Add($this->ParceBlock());
//			}
//			$this->ExpectSymbol('}');
//			$this->lexer->Consume();
//		}
//		else {
//			$r->Add($this->ParceStatement());
//		}
//		return $r;
//	}
//	private function ParceStatement(){
//		if ($this->IsIdentifier()) {
//			$x1 = $this->lexer->token->token;
//			$this->lexer->Consume();

//			$this->ExpectIdentifier();

//			if ($this->IsIdentifier()){
//				$x2 = $this->lexer->token->token;
//				$this->lexer->Consume();

//				$r = new VariableDeclarationExpression();
//				$r->type = $x1;
//				$r->name = $x2;
//				if ($this->lexer->token instanceof AssignOperatorToken){

//				}
//				$this->ExpectSymbol(';');
//				$this->lexer->Consume();
//			}

//		}
//		elseif ($this->IsSymbol(';')) {
//			$this->lexer->Consume();
//			$r = new EmptyExpression();
//		}
//		return $r;
//	}




//	/**
//	* <statement> ::= <assignement>; | <expression>;
//	*/
//	private function ParseStatement(){
//		$this->SkipWhite();
//		if ($this->MatchAlpha()){
//			$x = $this->ParseIdentifier();
//			if ($x instanceof VariableExpression){
//				$x->SkipWhite();
//				if ($x->Match('='))
//			}

//		$this->SkipWhite();
//		if ($this->Match(';'))
//			$this->Consume();
//		else
//			$this->Error('Expected: ;.');
//		return $x;
//	}

//	/**
//	* <statement> ::= <assignement>; | <expression>;
//	*/

//	/** @return Expression */
//	private function ParseE0(){
//		$x = $this->ParseE1();

//		$this->SkipWhite();
//		while($this->Match('+','-')){
//			if ($this->Match('+')){
//				$this->Consume();
//				$xx = $this->ParseE1();
//				$x = new OpPlusExpression($x,$xx);
//			}
//			elseif ($this->Match('-')){
//				$this->Consume();
//				$xx = $this->ParseE1();
//				$x = new OpMinusExpression($x,$xx);
//			}
//		}
//		return $x;
//	}

//	/** @return Expression */
//	private function ParseE1(){
//		$x = $this->ParseE2();

//		$this->SkipWhite();
//		while($this->Match('*','/','%')){
//			if ($this->Match('*')){
//				$this->Consume();
//				$xx = $this->ParseE2();
//				$x = new OpMulExpression($x,$xx);
//			}
//			elseif ($this->Match('/')){
//				$this->Consume();
//				$xx = $this->ParseE2();
//				$x = new OpDivExpression($x,$xx);
//			}
//			elseif ($this->Match('%')){
//				$this->Consume();
//				$xx = $this->ParseE2();
//				$x = new OpModExpression($x,$xx);
//			}
//		}
//		return $x;
//	}

//	/** @return Expression */
//	private function ParseE2(){
//		$this->SkipWhite();
//		if ($this->MatchDigit() || $this->MatchAlpha() || $this->Match('('))
//			$x = $this->ParseE3();
//		elseif ($this->Match('+')){
//			$this->Consume();
//			$x = $this->ParseE3();
//			$x = new OpUnaryPlusExpression($x);
//		}
//		elseif ($this->Match('-')){
//			$this->Consume();
//			$x = $this->ParseE3();
//			$x = new OpUnaryMinusExpression($x);
//		}
//		else
//			$this->Error('Expected: digit alphanumeric + - (.');

//		return $x;
//	}

//	/** @return Expression */
//	private function ParseE3(){
//		$this->SkipWhite();
//		if ($this->MatchDigit())
//			$x = $this->ParseNumber();
//		elseif ($this->MatchAlpha())
//			$x = $this->ParseIdentifier();
//		elseif ($this->Match('(')) {
//			$this->Consume();
//			$x = $this->Parse();
//			$this->SkipWhite();
//			if ($this->Match(')'))
//				$this->Consume();
//			else
//				$this->Error('Expected: ).');
//		}
//		else
//			$this->Error('Expected: digit alphanumeric (.');

//		return $x;
//	}
//	private function ParseNumber(){
//		$this->SkipWhite();
//		if (!$this->MatchDigit())
//			$this->Error('Expected: digit.');
//		else {
//			$s = $this->Peek();
//			$this->Consume();
//			while($this->MatchDigit()){
//				$s .= $this->Peek();
//				$this->Consume();
//			}
//			$x = new NumberExpression($s);
//		}
//		return $x;
//	}
//	private function ParseIdentifier(){
//		$this->SkipWhite();
//		if (!$this->MatchAlpha())
//			$this->Error('Expected: alphanumeric.');
//		else {
//			$s = $this->Peek();
//			$this->Consume();
//			while($this->MatchDigit()||$this->MatchAlpha()){
//				$s .= $this->Peek();
//				$this->Consume();
//			}
//		}
//		$this->SkipWhite();
//		if ($this->Match('(')){
//			$this->Consume();
//			if ($this->Match(')'))
//				$this->Consume();
//			else
//				$this->Error('Expected: ).');
//			$x = new FunctionCallExpression($s);
//		}
//		else{
//			$x = new VariableExpression($s);
//		}
//		return $x;
//	}
//}


?>