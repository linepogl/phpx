<?php
namespace Phpx;

class Source {
	private $filename = null;
	private $line = 0;
	private $col = 0;
	public function __construct($filename=null,$line=0,$col=0){
		$this->filename = $filename;
		$this->line = $line;
		$this->col = $col;
	}
	public function GetFilename(){ return $this->filename; }
	public function GetLine(){ return $this->line; }
	public function GetCol(){ return $this->col; }

	public function ToDebugString(){ return '@'.$this->filename.'['.$this->line.':'.$this->col.']'; }
}
