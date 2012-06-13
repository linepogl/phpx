<?php

class Validator implements ArrayAccess, IteratorAggregate, Countable {
	private $exceptions = array();
	public function count(){ return count($this->exceptions); }
	public function getIterator(){ return new ArrayIterator($this->exceptions); }
	public function offsetExists($offset) { return isset($this->exceptions[$offset]); }
	public function offsetGet($offset) { return $this->exceptions[$offset]; }
	public function offsetUnset($offset) { unset($this->exceptions[$offset]); }
	public function offsetSet($offset, $value) {
		if (is_null($offset)){
			if ($value instanceof Exception)
				$this->exceptions[] = $value ;
			else
				throw new InvalidArgumentException('Cannot insert anything but exceptions to a Validator.');
		}
		else
			throw new InvalidArgumentException('Cannot modify existing exceptions of a Validator.');
	}
	public function Debug(){
		/** @var $ex Exception */
		foreach ($this->exceptions as $ex) {
			echo '* '. $ex->GetMessage()."\n";
		}
	}
}
