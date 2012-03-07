<?php
namespace Phpx;

class ClassBodyExpression extends Expression {
	public $Expressions = array();

	public function RenderText( $tabs = 0 ){
		parent::RenderText($tabs);
		foreach ($this->Expressions as $exp)
			$exp->RenderText( $tabs+1 );
	}

	public function Parse( Lexer $lexer ){

		while( $lexer->Match( TAccessModifier , TPartial , TComment ) ){
			if ($lexer->Is( TComment ))
				$this->Expressions[] = new CommentExpression($this,$lexer);

			elseif ($lexer->Is( TAccessModifier ))
				$this->Expressions[] = new ClassDeclarationExpression($this,$lexer);

			elseif ($lexer->Is( TPartial ))
				$this->Expressions[] = new ClassPartExpression($this,$lexer);
		}
	}

	public function ConstructQName(){
		return $this->parent->ConstructQName();
	}

	public function Compile(Compiler $compiler) {
		foreach ($this->Expressions as $exp)
			$compiler->Queue[] = $exp;
	}
}

?>
