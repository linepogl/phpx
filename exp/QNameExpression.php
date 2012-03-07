<?php
namespace Phpx;

class QNameExpression extends Expression {
	public $QName;

	public function RenderText( $tabs = 0 ){
		parent::RenderText($tabs);
		parent::WriteText($tabs+1,'QName = ' . $this->QName->ToString());
	}

	public function Parse( Lexer $lexer ){
		$x = $lexer->Consume( TIdentifier );
		$this->QName = new QName($x->GetString());

		while ( $lexer->Match( TDot ) ){
			$lexer->Consume();
			$x = $lexer->Consume( TIdentifier );
			$this->QName->AppendString($x->GetString());
		}

	}

	public function Compile(Compiler $compiler) {
	}
}

?>
