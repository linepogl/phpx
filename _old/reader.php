<?php
//
//class Reader {
//	private $file = null;
//	public $char = null;
//	public $line = 1;
//	public $col = 0;
//	private $consumed = true;
//	public function __construct($filename){ $this->file	= fopen($filename, 'r'); $this->Read(); }
//	function __destruct() { fclose($this->file); }
//	private function Read(){
//		if (!$this->consumed) return;
//		$c = stream_get_contents($this->file,1);
//		if ($c === false || $c == '') {
//			$this->char = null;
//		}
//		else {
//			$this->char = $c;
//			if ($this->char == "\n") { $this->line++; $this->col=0; } else { $this->col++; }
//		}
//		$this->consumed = false;
//	}
//	public function Consume(){ $r = $this->char; $this->consumed = true; $this->Read(); return $r; }
//	public function MatchNull(){ return is_null($this->char); }
//	public function Match($char){ return $this->char==$char; }
//	public function MatchDigit(){ $x = ord($this->char); return ($x>47&&$x<58); }
//	public function MatchHexDigit(){ $x = ord($this->char); return ($x>47&&$x<58)||($x>96&&$x<103)||($x>64&&$x<71); }
//	public function MatchBinaryDigit(){ return $this->char == '0' || $this->char == '1'; }
//	public function MatchAlpha(){ $x = ord($this->char); return ($x>96&&$x<123)||($x>64&&$x<91)||$this->char=='_'; }
//	public function SkipWhite(){ while(''==trim($this->char)) { $this->Consume(); if (is_null($this->char)) return; } }
//}


?>