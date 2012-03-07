<?php

class DBItem {
	public $id;

	public function GetAnotherItem(){
		$r = new DBItem();
		$r->id = 13;
		return new MaybeDBItem($r);
	}
}

class Nothing {

}



class MaybeDBItem {
	/** @var DBItem */
	private $dbitem;
	public function __construct($dbitem = null){
		$this->dbitem = $dbitem;
	}

	/** @return MaybeDBItem */
	public function Run($f){
		if (!is_null($this->dbitem))
			$f($this->dbitem);
		return $this;
	}

	/** @return MaybeDBItem */
	public function Select($f){
		if (is_null($this->dbitem))
			return $this;
		else
			return $f($this->dbitem);
	}
}


/** @return MaybeDBItem */
function Retrieve($id){
	$r = new MaybeDBItem();
	if ($id == 0) {
		return new MaybeDBItem(null);
	}
	else {
		$dbitem = new DBItem();
		$dbitem->id = $id;
		return new MaybeDBItem($dbitem);
	}
	return $r;
}



Retrieve(1)
	->Select(function($x){ return $x->GetAnotherItem(); })
	->Run(function($x){ echo $x->id; })
	;



Something FindSomething(){
	if (....)
		return Something();
	else
		return null;
}

/*
$sel->GetTraitement()
	->Bind(function($x){return $x->GetType();})
	->Bind(function($x){return $x->GetPathLivraison();})
	->Bind(function($x){return $x->GetCode();})
	->Join('');
*/





public class Maybe<T> {

	T obj;

	public Maybe<B> Bind<B>( Func<T,B> f ){
			if (this.obj == null)
				return new Maybe<B>(null);
			else
				return f(this.obj);
		}
}


public Maybe<Something> FindSomething(){


}

FindSomething().DoSomething();

FindSomething() >>= DoSomething()

	.Bind( x => x.DoSomething() )
	.Bind( x => x.DoSomething() )
	.Bind( x => x.DoSomething() )





