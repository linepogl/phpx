<?php
namespace Phpx;

class CommentExpression extends Expression {

	public $Comment = '';

	public function RenderText( $tabs = 0 ){
		parent::RenderText($tabs);
		//echo str_repeat("\t",$tabs) . ' -Comment = ' . $this->Comment . "\n";

	}

	public function Parse( Lexer $lexer ){
		$x = $lexer->Consume( TComment );
		$this->Comment = $x->GetString();
	}

	public function Compile(Compiler $compiler) {
	}
}

?>
