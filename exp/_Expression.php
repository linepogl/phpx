<?php
namespace Phpx;

abstract class Expression {

	public final function __construct(Expression $parent = null, Lexer $lexer = null) {
		$this->parent = $parent;
		if (is_null($lexer))
			$this->source = new Source();
		else {
			$lexer->StopSniffing();
			$this->source = $lexer->GetSource();
			$this->Parse($lexer);
		}
	}
	protected $source;
	public function GetSource(){ return $this->source; }

	protected $parent = null;
	public function GetParent(){ return $this->parent; }
	public function SetParent(Expression $parent){ $this->parent = $parent; }



	public function RenderText( $tabs = 0 ){
		if ( $tabs > 1 )
			echo str_repeat("   ",$tabs-1);
		if ( $tabs > 0 )
			echo ":- ";
		echo '+ ' . get_class($this) . /* ' ' . $this->source->ToDebugString() . */ "\n";
	}
	protected function WriteText( $tabs = 0 , $text ){
		if ( $tabs > 1 )
			echo str_repeat("   ",$tabs-1);
		if ( $tabs > 0 )
			echo ":- ";
		echo '> ' . $text . "\n";
	}

	public abstract function Parse(Lexer $lexer);
	public abstract function Compile(Compiler $compiler);

}

?>
