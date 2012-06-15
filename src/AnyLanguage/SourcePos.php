<?php

class SourcePos {
	private $filename = null;
	private $line = 0;
	private $col = 0;
	private $line_to = 0;
	private $col_to = 0;
	public function __construct($filename=null,$line=0,$col=0,$line_to=null,$col_to=null){
		$this->filename = $filename;
		$this->line = $line;
		$this->col = $col;
		$this->line_to = is_null($line_to) ? $line : $line_to;
		$this->col_to = is_null($col_to) ? $col : $col_to;
	}
	public function GetFilename(){ return $this->filename; }
	public function GetLine(){ return $this->line; }
	public function GetCol(){ return $this->col; }
	public function GetLineTo(){ return $this->line; }
	public function GetColTo(){ return $this->col; }

	public function __toString(){ return $this->ToDebugString(); }
	public function ToDebugString(){ return '@'.$this->filename.'['.$this->line.':'.$this->col.']'; }


	public function UpTo( SourcePos $pos ){
		if ($pos->filename != $this->filename)
			throw new Exception();
		return new SourcePos(
			$this->filename,
			$this->line,
			$this->col,
			$pos->GetLineTo(),
			$pos->GetColTo()
			);
	}
}
