<?php


class Lexer {
	private $grammar;
	private $reader;
	private $token = null;
	public function __construct(Grammar $grammar, Reader $reader){
		$this->grammar = $grammar;
		$this->reader = $reader;
		$this->token = $this->Scan();
	}

	/** @return Token */
	public function GetToken(){ return $this->token; }
	public function Is( $terminal ){ return $this->token->Is( $terminal ); }
	public function SkipComments(){ while($this->token->Is(TComment)) $this->token = $this->Scan(); }


	/** @return Token */
	public function Consume(){
		$r = $this->token;
		$this->token = $this->Scan();
		return $r;
	}








	private function Scan(){
		$this->reader->SkipWhite();
		$src = $this->reader->GetSourcePos();

		$lexeme = '';

		//
		// End of file
		//
		if ($this->reader->IsEndOfFile()){
			return new Token($this->grammar[TEndOfFile],'',$src);
		}


		//
		// Keyword or identifier
		//
		elseif ($this->reader->IsAlpha()){
			$lexeme .= $this->reader->Consume();
			while ($this->reader->IsAlpha() || $this->reader->IsDigit())
				$lexeme .= $this->reader->Consume();
			$src_range = $src->UpTo($this->reader->GetSourcePos());
			$map = $this->grammar->GetKeywordMap();
			if (array_key_exists($lexeme,$map))
				return new Token($map[$lexeme],$lexeme,$src_range);
			else
				return new Token($this->grammar[TIdentifier],$lexeme,$src_range);
		}



		//
		// Number
		//
		elseif ($this->reader->IsDigit()){
			$lexeme = $this->reader->Consume();
			if ($this->reader->Is('x')) {
				$lexeme .= $this->reader->Consume();
				if (!$this->reader->IsHexDigit()) {
					$src_range = $src->UpTo($this->reader->GetSourcePos());
					return new Token($this->grammar[TUnknown],$lexeme,$src_range);
				}
				while ($this->reader->IsHexDigit())
					$lexeme .= $this->reader->Consume();
				$src_range = $src->UpTo($this->reader->GetSourcePos());
				return new Token($this->grammar[TIntLiteral],$lexeme,$src_range);
			}
			elseif ($this->reader->Is('b')) {
				$lexeme .= $this->reader->Consume();
				if (!$this->reader->IsBinaryDigit()) {
					$src_range = $src->UpTo($this->reader->GetSourcePos());
					return new Token($this->grammar[TUnknown],$lexeme,$src_range);
				}
				while ($this->reader->IsBinaryDigit())
					$lexeme .= $this->reader->Consume();
				$src_range = $src->UpTo($this->reader->GetSourcePos());
				return new Token($this->grammar[TIntLiteral],$lexeme,$src_range);
			}
			else {
				while($this->reader->IsDigit())
					$lexeme .= $this->reader->Consume();
				$src_range = $src->UpTo($this->reader->GetSourcePos());
				return new Token($this->grammar[TIntLiteral],$lexeme,$src_range);
			}
		}


		//
		// Comment or ?
		//
		elseif ($this->reader->Is('/')) {
			$lexeme = $this->reader->Consume();

			if ($this->reader->Is('/')) {
				while(!$this->reader->IsEndOfLine())
					$lexeme .= $this->reader->Consume();
				return new Token($this->grammar[TComment],$lexeme,$src,$this->reader->GetSourcePos());
			}
			elseif ($this->reader->Is('*')) {
				$lexeme .= $this->reader->Consume();
				$level = 1;
				$curry = '';
				while ($level > 0 && !$this->reader->IsEndOfFile()) {
					if     ($this->reader->Is('/')) { if ($curry != '*') $curry = '/'; else { $level--; $curry = ''; } }
					elseif ($this->reader->Is('*')) { if ($curry != '/') $curry = '*'; else { $level++; $curry = ''; } }
					else    $curry = '';
					$lexeme .= $this->reader->Consume();
					if ($level < 0) break;
				}
				return new Token($this->grammar[TComment],$lexeme,$src,$this->reader->GetSourcePos());
			}
		}


		$map = $this->grammar->GetPunctuationMap();
		if ($lexeme == '') $lexeme .= $this->reader->Consume();
		if (!array_key_exists($lexeme,$map))
			return new Token($this->grammar[TUnknown],$lexeme,$src->UpTo($this->reader->GetSourcePos()));

		while(true) {
			$test = $lexeme . $this->reader->GetChar();

			if (!array_key_exists($test,$map))
				return new Token($map[$lexeme],$lexeme,$src->UpTo($this->reader->GetSourcePos()));

			$lexeme .= $this->reader->Consume();
		}


	}

}

