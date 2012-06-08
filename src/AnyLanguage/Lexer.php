<?php


class Lexer {
	private $reader;
	private $buffer = array();
	private $index = 0;
	private $bookmark = null;
	public function __construct(Reader $reader){
		$this->reader = $reader;
		$this->buffer[] = $this->Scan();
	}

	/** @return Token */
	public function GetToken(){ return $this->buffer[$this->index]; }
	public function Is( $terminal ){ return $this->GetToken()->Is( $terminal ); }
	public function SkipComments(){ while($this->GetToken()->Is(TComment)) $this->Consume(); }


	/** @return Token */
	public function Consume(){
		$this->ExpectX(func_get_args());
		$r = $this->GetToken();
		$this->index++;
		while ($this->index >= count($this->buffer))
			$this->buffer[] = $this->Scan();
		return $r;
	}
	public function Sniff(){ if (is_null($this->bookmark)) $this->bookmark = $this->index; }
	public function SniffNext(){ $this->Sniff(); $this->Consume(); }
	public function StopSniffing(){ if (is_null($this->bookmark)) return; $this->index = $this->bookmark; $this->bookmark = null; }

	public function Match(){
		$a = func_get_args();
		if (!in_array(TComment,$a)) $this->SkipComments();
		foreach ($a as $terminal) if ($this->Is($terminal)) return true;
		return false;
	}
	public function Expect(){ $this->ExpectX(func_get_args()); }
	private function ExpectX($a){
		if (empty($a)) return;
		if (!in_array(TComment,$a)) $this->SkipComments();
		foreach ($a as $terminal) if ($this->Is( $terminal )) return;
		throw new CompileTimeException( 'Unexpected token ' . $this->GetToken()->GetTerminal() . ' while waiting for ' . implode($a,' or ') . '.' , $this->GetToken()->GetSourcePos() );
	}







	private function Scan(){
		$this->reader->SkipWhite();
		$src = $this->reader->GetSourcePos();


		//
		// End of file
		//
		if ($this->reader->IsEndOfFile()){
			return new Token(TEndOfFile,'',$src,$src);
		}


		//
		// Keyword or identifier
		//
		elseif ($this->reader->IsAlpha()){
			$s = $this->reader->Consume();
			while ($this->reader->IsAlpha() || $this->reader->IsDigit())
				$s .= $this->reader->Consume();
			switch ($s){
				case 'var': return new Token(TVar,$s,$src,$this->reader->GetSourcePos());
				case 'int': return new Token(TInt,$s,$src,$this->reader->GetSourcePos());
			}
			return new Token(TIdentifier,$s,$src,$this->reader->GetSourcePos());
		}



		//
		// Number
		//
		elseif ($this->reader->IsDigit()){
			$s = $this->reader->Consume();
			if ($this->reader->Is('x')) {
				$s .= $this->reader->Consume();
				if (!$this->reader->IsHexDigit())
					return new Token(TUnknown,$s,$src,$this->reader->GetSourcePos());
				while ($this->reader->IsHexDigit())
					$s .= $this->reader->Consume();
				return new Token(TIntLiteral,$s,$src,$this->reader->GetSourcePos());
			}
			else {
				while($this->reader->IsDigit())
					$s .= $this->reader->Consume();
				return new Token(TIntLiteral,$s,$src,$this->reader->GetSourcePos());
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
				return new Token(TComment,$s,$src,$this->reader->GetSourcePos());
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
				return new Token(TComment,$s,$src,$this->reader->GetSourcePos());
			}
			else {
				return new Token(TUnknownSymbol,$s,$src,$this->reader->GetSourcePos());
			}
		}



		elseif ($this->reader->Is('=')) {
			$s = $this->reader->Consume();
			return new Token(TAssign,$s,$src,$this->reader->GetSourcePos());
		}
		elseif ($this->reader->Is(';')) {
			$s = $this->reader->Consume();
			return new Token(TSemicolon,$s,$src,$this->reader->GetSourcePos());
		}
		elseif ($this->reader->Is('.')) {
			$s = $this->reader->Consume();
			return new Token(TDot,$s,$src,$this->reader->GetSourcePos());
		}
		elseif ($this->reader->Is('{')) {
			$s = $this->reader->Consume();
			return new Token(TCurlyOpen,$s,$src,$this->reader->GetSourcePos());
		}
		elseif ($this->reader->Is('}')) {
			$s = $this->reader->Consume();
			return new Token(TCurlyClose,$s,$src,$this->reader->GetSourcePos());
		}


		else {
			$s = $this->reader->Consume();
			return new Token(TUnknownSymbol,$s,$src,$this->reader->GetSourcePos());
		}

	}

}

