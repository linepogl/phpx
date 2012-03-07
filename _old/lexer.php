<?php
//require('reader.php');
//require('tokens.php');

//class ParseException extends Exception {
//	private $code_line;
//	private $code_col;
//	public function __construct($message,$line,$col){
//		parent::__construct($message.' ['.$line.':'.$col.']');
//		$this->code_line = $line;
//		$this->code_col = $col;
//	}
//}


//class Lexer {
//	private $reader;
//	public $string = null;
//	public $buffer = null;
//	public $token = 0;
//	public $line = 0;
//	public $col = 0;
//	public $context = array();
//	private $expected = false;
//	private $consumed = true;
//	public function __construct($filename){ $this->reader = new Reader($filename); }


//	private function ContextContains($token){
//		foreach ($this->context as $mask){
//			if ( ($token & $mask) == $mask )
//				return true;
//		}
//		return false;
//	}
//	private function Is($mask){
//		return ($this->token & $mask) == $mask;
//	}

//	private function TryToken($token,&$string){
//		$this->token = $token;
//		$this->string = $string;
//		$this->expected = $this->ContextContains($token);
//	}
//	private function ConsumeChar(){
//		$r = $this->reader->Consume();
//		$this->buffer .= $r;
//		return $r;
//	}




//	public function Match($__context__args__) { $this->context = func_get_args(); return $this->Scan(false); }
//	public function Expect($__context__args__) { $this->context = func_get_args(); return $this->Scan(true); }

//	public function Consume(){ $this->consumed = true; }
//	private function Scan($strict) {
//		if (!$this->consumed) {
//			$this->expected = $this->ContextContains($this->token);
//		}
//		else {
//			$this->reader->SkipWhite();
//			$this->buffer = '';
//			$this->string = '';
//			$this->token = 0;
//			$this->line = $this->reader->line;
//			$this->col = $this->reader->col;
//			$this->consumed = false;
//			$this->expected = false;

//			if ($this->reader->MatchNull()) {
//				$s = $this->ConsumeChar();
//				$this->TryToken(TEof,$s);
//			}
//			elseif ($this->reader->MatchAlpha()) {
//				$s = $this->ConsumeChar();
//				while($this->reader->MatchDigit()||$this->reader->MatchAlpha()){ $s .= $this->ConsumeChar(); }
//				if (Token::ExistsKeyword($s)){
//					foreach (Token::GetKeywordsFor($s) as $token){
//						$this->TryToken($token,$s);
//						if ($this->expected) break;
//					}
//				}
//				else
//					$this->TryToken(TIdentifier,$s);
//			}
//			elseif ($this->reader->Match('0')) {
//				$s = '';
//				$this->ConsumeChar();
//				if ($this->reader->Match('x')) {
//					$this->ConsumeChar(); while($this->reader->Match('0')) $this->ConsumeChar();
//					while($this->reader->MatchHexDigit()) $s .= $this->ConsumeChar();
//					$this->TryToken(THexIntegerLiteral,$s);
//				}
//				elseif ($this->reader->Match('b')) {
//					$this->ConsumeChar(); while($this->reader->Match('0')) $this->ConsumeChar();
//					while($this->reader->MatchBinaryDigit()) $s .= $this->ConsumeChar();
//					$this->TryToken(TBinIntegerLiteral,$s);
//				}
//				else {
//					$this->ConsumeChar(); while($this->reader->Match('0')) $this->ConsumeChar();
//					while($this->reader->MatchDigit()) $s .= $this->ConsumeChar();
//					if ($s == '') $s = '0';
//					if ($this->reader->Match('.')) {
//						$s .= $this->ConsumeChar();
//						while($this->reader->MatchDigit()) $s .= $this->ConsumeChar();
//						$this->TryToken(TFloatLiteral,$s);
//					}
//					else{
//						$this->TryToken(TDecIntegerLiteral,$s);
//					}
//				}
//			}
//			elseif ($this->reader->MatchDigit()){
//				$s = $this->ConsumeChar();
//				while($this->reader->MatchDigit()) $s .= $this->ConsumeChar();
//				if ($this->reader->Match('.')) {
//					$s .= $this->ConsumeChar();
//					while($this->reader->MatchDigit()) $s .= $this->ConsumeCha();
//					$this->TryToken(TFloatLiteral,$s);
//				}
//				else{
//					$this->TryToken(TDecIntegerLiteral,$s);
//				}
//			}
//			elseif ($this->reader->Match('"')){
//				$s = $this->ConsumeChar();
//			}
//			elseif ($this->reader->Match("'")){
//				$s = $this->ConsumeChar();
//			}
//			elseif ($this->reader->Match('/')){
//				$s = $this->ConsumeChar();

//				if ($this->reader->Match('/')){
//					$this->ConsumeChar();
//					$s = '';
//					while( !$this->reader->Match("\n") && !$this->reader->Match("\r") && !$this->reader->MatchNull() )
//						$s .= $this->ConsumeChar();
//					$this->TryToken(TComment,$s);
//				}
//				elseif ($this->reader->Match('*')){
//					$this->ConsumeChar();
//					$s = '';
//					$curry = '';
//					$level = 0;
//					while (!$this->reader->MatchNull()){
//						if ($curry == '*' && $this->reader->Match('/')) {
//							$this->ConsumeChar();
//							$level--;
//							if ($level<0) break;
//							$s .= $curry . '/';
//							$curry = '';
//						}
//						elseif ($curry == '/' && $this->reader->Match('*')) {
//							$level++;
//							$s .= $curry . $this->ConsumeChar(); $curry = '';
//						}
//						elseif ($this->reader->Match('*') || $this->reader->Match('/')){
//							$s .= $curry;
//							$curry = $this->ConsumeChar();
//						}
//						else {
//							$s .= $curry . $this->ConsumeChar();
//							$curry = '';
//						}
//					}
//					$this->TryToken(TComment,$s);
//				}
//				else {
//					foreach (Token::GetSymbolsFor($s) as $token) {
//						$this->TryToken($token,$s);
//						if ($this->expected) break;
//					}
//					$this->FindSymbolsStartingWith($s);
//				}
//			}
//			else {
//				$this->FindSymbolsStartingWith('');
//			}
//		}

//		if ($strict && !$this->expected) {
//			$s = 'Unexpected token ';
//			$s .= '"' . Token::Translate($this->token) . '"';
//			if (!$this->Is(TFixed))
//				$s .= ' (' . $this->buffer . ')';
//			$a = array();
//			foreach ($this->context as $mask)
//				$a[] = '"' . Token::Translate($mask) . '"';
//			$s .=  ' instead of ' . implode(' or ',$a) . '.';
//			throw new ParseException($s,$this->line,$this->col);
//		}

//		return $this->expected;
//	}

//	private function FindSymbolsStartingWith($s){
//		while (true) {
//			$s .= $this->reader->char;
//			if (!Token::ExistsSymbolStartingWith($s)) break;
//			$this->ConsumeChar();
//			foreach (Token::GetSymbolsFor($s) as $token) {
//				$this->TryToken($token,$s);
//				if ($this->expected) break;
//			}
//		}
//	}

//}



?>
