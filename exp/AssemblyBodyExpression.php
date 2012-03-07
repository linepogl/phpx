<?php
namespace Phpx;

class AssemblyBodyExpression extends Expression {
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

			elseif ($lexer->Is( TAccessModifier )) {
				$lexer->SniffNext();
				if ($lexer->Is( TModule ))
					$this->Expressions[] = new ModuleDeclarationExpression($this,$lexer);
				elseif ($lexer->Is( TClass ))
					$this->Expressions[] = new ClassDeclarationExpression($this,$lexer);
			}

			elseif ($lexer->Is( TPartial )) {
				$lexer->SniffNext();
				if ($lexer->Is( TModule ))
					$this->Expressions[] = new ModulePartExpression($this,$lexer);
				elseif ($lexer->Is( TClass ))
					$this->Expressions[] = new ClassPartExpression($this,$lexer);
			}
		}
		$lexer->Consume( TEndOfFile );
	}


	public function Compile(Compiler $compiler) {
		foreach ($this->Expressions as $exp)
			$compiler->Queue[] = $exp;
	}
}

?>
