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


		//
		// End of file
		//
		if ($this->reader->IsEndOfFile()){
			return new Token($this->grammar[TEndOfFile],'',$src,$src);
		}


		//
		// Keyword or identifier
		//
		elseif ($this->reader->IsAlpha()){
			$s = $this->reader->Consume();
			while ($this->reader->IsAlpha() || $this->reader->IsDigit())
				$s .= $this->reader->Consume();
			$map = $this->grammar->GetFixedLexemeMap();
			if (array_key_exists($s,$map)){
				$terminal = $map[$s];
				return new Token($terminal,$s,$src,$this->reader->GetSourcePos());
			}
			return new Token($this->grammar[TIdentifier],$s,$src,$this->reader->GetSourcePos());
		}



		//
		// Number
		//
		elseif ($this->reader->IsDigit()){
			$s = $this->reader->Consume();
			if ($this->reader->Is('x')) {
				$s .= $this->reader->Consume();
				if (!$this->reader->IsHexDigit())
					return new Token($this->grammar[TUnknown],$s,$src,$this->reader->GetSourcePos());
				while ($this->reader->IsHexDigit())
					$s .= $this->reader->Consume();
				return new Token($this->grammar[TIntLiteral],$s,$src,$this->reader->GetSourcePos());
			}
			else {
				while($this->reader->IsDigit())
					$s .= $this->reader->Consume();
				return new Token($this->grammar[TIntLiteral],$s,$src,$this->reader->GetSourcePos());
			}
		}


		//
		// Comment or ?
		//
		elseif ($this->reader->Is('/')) {
			$s = $this->reader->Consume();

			if ($this->reader->Is('/')) {
				while(!$this->reader->IsEndOfLine())
					$s .= $this->reader->Consume();
				return new Token($this->grammar[TComment],$s,$src,$this->reader->GetSourcePos());
			}
			elseif ($this->reader->Is('*')) {
				$s .= $this->reader->Consume();
				$level = 1;
				$curry = '';
				while ($level > 0 && !$this->reader->IsEndOfFile()) {
					if     ($this->reader->Is('/')) { if ($curry != '*') $curry = '/'; else { $level--; $curry = ''; } }
					elseif ($this->reader->Is('*')) { if ($curry != '/') $curry = '*'; else { $level++; $curry = ''; } }
					else    $curry = '';
					$s .= $this->reader->Consume();
					if ($level < 0) break;
				}
				return new Token($this->grammar[TComment],$s,$src,$this->reader->GetSourcePos());
			}
			else {
				return new Token($this->grammar[TUnknown],$s,$src,$this->reader->GetSourcePos());
			}
		}



		elseif ($this->reader->Is('=')) {
			$s = $this->reader->Consume();
			return new Token($this->grammar[TAssign],$s,$src,$this->reader->GetSourcePos());
		}
		elseif ($this->reader->Is(';')) {
			$s = $this->reader->Consume();
			return new Token($this->grammar[TSemicolon],$s,$src,$this->reader->GetSourcePos());
		}
		elseif ($this->reader->Is('.')) {
			$s = $this->reader->Consume();
			return new Token($this->grammar[TDot],$s,$src,$this->reader->GetSourcePos());
		}
		elseif ($this->reader->Is('{')) {
			$s = $this->reader->Consume();
			return new Token($this->grammar[TCurlyOpen],$s,$src,$this->reader->GetSourcePos());
		}
		elseif ($this->reader->Is('}')) {
			$s = $this->reader->Consume();
			return new Token($this->grammar[TCurlyClose],$s,$src,$this->reader->GetSourcePos());
		}


		else {
			$s = $this->reader->Consume();
			return new Token($this->grammar[TUnknown],$s,$src,$this->reader->GetSourcePos());
		}

	}

}

