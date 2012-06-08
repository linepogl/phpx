<?php

class Reader {
	private $file = null;
	private $char = null;
	private $filename = null;
	private $line = 1;
	private $col = 0;
	private $consumed = true;
	private $curry_new_line = false;
	public function __construct($filename){
		$this->filename = $filename;
		$this->file	= @fopen($filename, 'r');
		if ($this->file === false) throw new CompileTimeException('Cannot open file '.$filename.'.',new SourcePos($filename));
		$this->Scan();
	}
	public function __destruct() { if ($this->file !== false) fclose($this->file); }
	private function Scan(){
		if (!$this->consumed) return;
		$c = stream_get_contents($this->file,1);
		if ($c === false || $c == ''){ $c = null; $this->curry_new_line = false; }
		elseif ($c == "\r") { $c = "\n"; $this->curry_new_line = true; }
		elseif ($c == "\n" && $this->curry_new_line) { $this->curry_new_line = false; $this->Scan(); return; }
		else $this->curry_new_line = false;
		if ($this->char == "\n") { $this->line++; $this->col = 0; }
		$this->char = $c;
		$this->col++;
		$this->consumed = false;
	}
	public function Consume(){ $r = $this->char; $this->consumed = true; $this->Scan(); return $r; }
	public function SkipWhite(){ while($this->IsWhite()) $this->Consume(); }

	public function GetChar(){ return $this->char; }
	public function GetSourcePos(){ return new SourcePos($this->filename,$this->line,$this->col); }

	public function IsEndOfFile(){ return is_null($this->char); }
	public function IsEndOfLine(){ return $this->char == "\n"; }
	public function Is($char){ return $this->char==$char; }
	public function IsDigit(){ $x = ord($this->char); return ($x>47&&$x<58); }
	public function IsHexDigit(){ $x = ord($this->char); return ($x>47&&$x<58)||($x>96&&$x<103)||($x>64&&$x<71); }
	public function IsBinaryDigit(){ return $this->char == '0' || $this->char == '1'; }
	public function IsAlpha(){ $x = ord($this->char); return ($x>96&&$x<123)||($x>64&&$x<91)||$this->char=='_'; }
	public function IsWhite(){ return !is_null($this->char) && ''==trim($this->char); }
}

