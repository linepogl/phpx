<?php
namespace Phpx;

class ModulePartExpression extends Expression {
	public $QNameExpression;
	public $BodyExpression;

	public function RenderText( $tabs = 0 ){
		parent::RenderText($tabs);
		parent::WriteText($tabs+1,'QName = ');
		$this->QNameExpression->RenderText($tabs+2);
		parent::WriteText($tabs+1,'Body = ');
		$this->BodyExpression->RenderText( $tabs + 1 );
	}

	public function Parse( Lexer $lexer ){
		$lexer->Consume( TPartial );
		$lexer->Consume( TModule );
		$this->QNameExpression = new QNameExpression($this,$lexer);
		$lexer->Consume( TCurlyOpen );
		$this->BodyExpression = new ModuleBodyExpression($this,$lexer);
		$lexer->Consume( TCurlyClose );
	}

	public function ConstructQName(){
		if ($this->parent instanceof ModuleBodyExpression)
			return $this->parent->ConstructQName()->Append($this->QNameExpression->QName);
		else
			return clone $this->QNameExpression->QName;
	}



	public function Compile(Compiler $compiler) {
		$qname = $this->ConstructQName();
		echo '<li>Compiling module part ' . $qname->ToString() . ': ';

		$x = $compiler->Assembly->FindQName($qname);
		if ($x instanceof PhpxModule){
			$compiler->Queue[] = $this->BodyExpression;
			echo 'OK</li>';
		}
		elseif (is_null($x)) {
			if ($compiler->IsStuck) {
				$compiler->Validator[] = new CompileTimeException('Cannot find module '.$qname->ToString().'.',$this->GetSource());
				echo 'Error</li>';
			}
			else {
				$compiler->Queue[] = $this;
				echo 'Pass</li>';
			}
		}
		else {
			$compiler->Validator[] = new CompileTimeException($qname->ToString().' is not a module.',$x->GetSource());
			echo 'Error</li>';
		}
	}
}

?>
