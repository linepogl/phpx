<?php
namespace Phpx;

class ClassDeclarationExpression extends Expression {
	public $Access;
	public $QNameExpression;
	public $BodyExpression;

	public function RenderText( $tabs = 0 ){
		parent::RenderText($tabs);
		parent::WriteText($tabs+1,'Name = ');
		$this->QNameExpression->RenderText($tabs+2);
		parent::WriteText($tabs+1,'Access = ' . $this->Access);
		parent::WriteText($tabs+1,'Body = ');
		$this->BodyExpression->RenderText($tabs+2);
	}

	public function Parse( Lexer $lexer ){
		$x = $lexer->Consume( TAccessModifier );

		if ( $x->Is( TPublic ) )
			$this->Access = AccessPublic;
		elseif ( $x->Is( TPrivate ) )
			$this->Access = AccessPrivate;
		elseif ( $x->Is( TProtected ) )
			$this->Access = AccessProtected;

		$lexer->Consume( TClass );

		$this->QNameExpression = new QNameExpression($this,$lexer);

		$lexer->Consume( TCurlyOpen );
		$this->BodyExpression = new ClassBodyExpression($this,$lexer);
		$lexer->Consume( TCurlyClose );
	}


	public function ConstructQName(){
		if ($this->parent instanceof ModuleBodyExpression)
			return $this->parent->ConstructQName()->Append($this->QNameExpression->QName);
		elseif ($this->parent instanceof ClassBodyExpression)
			return $this->parent->ConstructQName()->Append($this->QNameExpression->QName);
		else
			return clone $this->QNameExpression->QName;
	}


	public function Compile(Compiler $compiler) {
		$qname = $this->ConstructQName();

		echo '<li>Compiling class declaration ' . $qname->ToString() . ': ';

		$x = $compiler->Assembly->FindQName($qname);
		if (!is_null($x)){
			$compiler->Validator[] = new CompileTimeException('Cannot redeclare '.$qname->ToString().' as class.',$this->GetSource());
			$compiler->Validator[] = new CompileTimeException($qname->ToString().' is already declared.',$x->GetSource());
			echo 'Error</li>';
			return;
		}

		$parent = null;
		$parent_qname = $qname->GetParent();
		if (!is_null($parent_qname)){
			$parent = $compiler->Assembly->FindQName($parent_qname);
			if (is_null($parent)){
				if ($compiler->IsStuck) {
					$compiler->Validator[] = new CompileTimeException('Cannot find namespace '.$parent_qname->ToString().'.',$this->GetSource());
					echo 'Error</li>';
				}
				else {
					$compiler->Queue[] = $this;
					echo 'Pass</li>';
				}
				return;
			}
		}

		$x = new PhpxClass();
		$x->Source = $this->GetSource();
		$x->Name = $qname->GetName();
		$x->Access = $this->Access;
		$x->Parent = $parent;

		if ($parent instanceof PhpxModule)
			$parent->Classes[$x->Name] = $x;
		elseif ($parent instanceof PhpxClass)
			$parent->Classes[$x->Name] = $x;

		$compiler->Queue[] = $this->BodyExpression;
		echo 'OK</li>';
	}
}

?>
