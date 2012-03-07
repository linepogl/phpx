<?php
namespace Phpx;

class Lexer {
	private $reader;
	private $buffer = array();
	private $index = 0;
	private $bookmark = null;
	public function __construct($filename){
		$this->reader = new Reader($filename);
		$this->buffer[] = $this->Scan();
	}

	/** @return Token */
	public function GetToken(){ return $this->buffer[$this->index]; }
	public function GetSource(){ return $this->GetToken()->GetSource(); }
	public function Is( $mask ){ return $this->GetToken()->Is($mask); }
	public function SkipComments(){ while($this->Is(TComment)) $this->Consume(); }


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
		foreach ($a as $mask) if ($this->Is($mask)) return true;
		return false;
	}
	public function Expect(){ $this->ExpectX(func_get_args()); }
	private function ExpectX($a){
		if (empty($a)) return;
		if (!in_array(TComment,$a)) $this->SkipComments();
		foreach ($a as $mask) if ($this->Is( $mask )) return;
		$s = ''; foreach ($a as $mask) $s .= ($s==''?'':' or ').Token::TranslateNumber($mask);
		throw new CompileTimeException( 'Unexpected token ' . $this->GetToken()->Translate() . ' while waiting for ' . $s . '.' , $this->GetToken()->GetSource() );
	}







	private function Scan(){
		$this->reader->SkipWhite();

		$src = $this->reader->GetSource();


		//
		// End of file
		//
		if ($this->reader->IsEndOfFile()){
			return new Token(TEndOfFile,'',$src);
		}


		//
		// Keyword or idendifier
		//
		elseif ($this->reader->IsAlpha()){
			$s = $this->reader->Consume();
			while ($this->reader->IsAlpha() || $this->reader->IsDigit())
				$s .= $this->reader->Consume();
			$n = Token::IsKeyword($s)
				? Token::GetKeywordNumber($s)
				: TIdentifier;
			return new Token($n,$s,$src);
		}


		//
		// Comment or ?
		//
		elseif ($this->reader->Is('/')) {
			$s = $this->reader->Consume();

			if ($this->reader->Is('/')) {
				while(!$this->reader->IsEndOfLine())
					$s .= $this->reader->Consume();
				return new Token(TComment,$s,$src);
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
				return new Token(TComment,$s,$src);
			}
			else {
				return new Token(TUnknownSymbol,$s,$src);
			}
		}
		elseif ($this->reader->Is('{')) {
			$s = $this->reader->Consume();
			return new Token(TCurlyOpen,$s,$src);
		}
		elseif ($this->reader->Is('}')) {
			$s = $this->reader->Consume();
			return new Token(TCurlyClose,$s,$src);
		}
		elseif ($this->reader->Is(':')) {
			$s = $this->reader->Consume();
			return new Token(TColon,$s,$src);
		}
		elseif ($this->reader->Is(';')) {
			$s = $this->reader->Consume();
			return new Token(TSemiColon,$s,$src);
		}
		elseif ($this->reader->Is('.')) {
			$s = $this->reader->Consume();
			return new Token(TDot,$s,$src);
		}
		else {
			$s = $this->reader->Consume();
			return new Token(TUnknownSymbol,$s,$src);
		}

	}

}

?>
