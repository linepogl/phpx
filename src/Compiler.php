<?php
namespace Phpx;

class CompileTimeQueue implements \ArrayAccess, \IteratorAggregate, \Countable {
	private $expressions = array();
	public function count(){ return count($this->expressions); }
	public function getIterator(){ return new \ArrayIterator($this->expressions); }
	public function offsetExists($offset) { return isset($this->expressions[$offset]); }
	public function offsetGet($offset) { return $this->expressions[$offset]; }
	public function offsetUnset($offset) { unset($this->expressions[$offset]); }
	public function offsetSet($offset, $value) {
		if (is_null($offset)){
			if ($value instanceof Expression)
				$this->expressions[] = $value ;
			else
				throw new \InvalidArgumentException('Cannot insert anything but expressions to a CompileTime queue.');
		}
		else
			throw new \InvalidArgumentException('Cannot modify existing expressions of a CompileTime queue.');
	}
	public function IsStuck(){
		return $this->is_stuck;
	}

	public function IsEqualTo(CompileTimeQueue $q) {
		if (count($q) != count($this))
			return false;
		for ($i = 0; $i < count($q); $i++)
			if ( $q[$i] !== $this[$i] )
				return false;
		return true;
	}
}




class Compiler {
	public $Assembly;
	public $Queue;
	public $Validator;
	public $IsStuck = false;
	private $filenames = array();
	public function AddFilename($filename){
		$this->filenames[] = $filename;
		$this->Assembly = new PhpxAssembly();
		$this->Queue = new CompileTimeQueue();
		$this->Validator = new Validator();
	}



	public function Compile(){
		foreach ($this->filenames as $filename){
			try{
				$lexer = new Lexer($filename);
				$exp = new AssemblyBodyExpression( null,$lexer );
				$lexer->Consume( TEndOfFile );
				$this->Queue[] = $exp;
			}
			catch (CompileTimeException $ex){
				$this->Validator[] = $ex;
			}
		}

		while (count($this->Queue) > 0){
			$q = $this->Queue;
			$this->Queue = new CompileTimeQueue();
			foreach ($q as $exp)
				$exp->Compile($this);

			if ($this->Queue->IsEqualTo($q)) {
				$this->IsStuck = true;
				$this->Queue = new CompileTimeQueue();
				foreach ($q as $exp)
					$exp->Compile($this);
			}
		}
	}

}

?>