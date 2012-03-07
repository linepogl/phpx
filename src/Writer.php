<?php
namespace Phpx;

class Writer {

	private $filename = null;
	public function __construct($filename){
		$this->filename = $filename;
		$this->file	= @fopen($filename, 'w');
		if ($this->file === false) throw new CompileTimeException('Cannot open file '.$filename.'.',new Source($filename,0,0));
	}
	public function __destruct() { if ($this->file !== false) fclose($this->file); }


	public function Write($value){
		fputs( $this->file , strval($value) );
	}

	public function WriteLn($value){
		$this->Write($value);
		$this->Write("\r\n");
	}

}

