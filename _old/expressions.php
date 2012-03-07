<?php
//
//abstract class Expression {
//	public abstract function RenderTree($level=0);
//}

//abstract class UnaryOperatorExpression extends Expression {
//	private $expr;
//	public function __construct(Expression $expr){ $this->expr = $expr; }
//	public function RenderTree($level=0){
//		echo str_repeat('  ',$level) . get_class($this) .':' . "\n";
//		$this->expr->RenderTree($level+1);
//	}
//}

//abstract class BinaryOperatorExpression extends Expression {
//	private $expr1;
//	private $expr2;
//	public function __construct(Expression $expr1, Expression $expr2){ $this->expr1 = $expr1; $this->expr2 = $expr2; }
//	public function RenderTree($level=0){
//		echo str_repeat('  ',$level) . get_class($this) .':' . "\n";
//		$this->expr1->RenderTree($level+1);
//		$this->expr2->RenderTree($level+1);
//	}
//}
//abstract class LiteralExpression extends Expression {
//	private $literal;
//	public function __construct($literal){ $this->literal=$literal; }
//	public function RenderTree($level=0){
//		echo str_repeat('  ',$level) . get_class($this) . ': ' . $this->literal . "\n";
//	}
//}
//class IdentifierExpression extends Expression {
//	private $identifier;
//	public function __construct($identifier){ $this->identifier=$identifier; }
//	public function RenderTree($level=0){
//		echo str_repeat('  ',$level) . get_class($this) . ': ' . $this->identifier . "\n";
//	}
//}

//class ProgramExpression extends Expression {
//	private $exprs = array();
//	public function Add(Expression $expr){
//		$this->exprs[] = $expr;
//	}
//	public function RenderTree($level=0){
//		echo str_repeat('  ',$level) . get_class($this) . ':' . "\n";
//		foreach ($this->exprs as $expr)
//			$expr->RenderTree($level+1);
//	}
//}

//class FunctionDeclarationExpression extends Expression {
//	public $type;
//	public $name;
//	public $access_modifier;
//	public $overridability_modifier;
//	public $args = array();
//	public $body;
//	public function __construct(){ $this->body = new BlockExpression(); }
//	public function RenderTree($level=0){
//		echo str_repeat('  ',$level) . get_class($this) . ': ' .$this->access_modifier.' '.$this->overridability_modifier.' '.$this->type.' '.$this->name . "\n";
//		foreach ($this->args as $expr)
//			$expr->RenderTree($level+1);
//		$this->body->RenderTree($level+1);
//	}
//}

//class CommentsExpression extends Expression {
//	public $comments = '';
//	public function RenderTree($level=0){
//		echo str_repeat('  ',$level) . get_class($this) . ': ' . $this->comments . "\n";
//	}
//}

//class ArgumentDeclarationExpression extends Expression {
//	public $type;
//	public $name;
//	public function RenderTree($level=0){
//		echo str_repeat('  ',$level) . get_class($this) . ': ' .$this->type.' '.$this->name . "\n";
//	}
//}

//class BlockExpression extends Expression {
//	private $statements = array();
//	public function Add(Expression $expr){
//		$this->statements[] = $expr;
//	}
//	public function RenderTree($level=0){
//		echo str_repeat('  ',$level) . get_class($this) . ': ' . "\n";
//		foreach ($this->statements as $expr)
//			$expr->RenderTree($level+1);
//	}
//}

//class VariableDeclarationExpression extends Expression {
//	public $type;
//	public $name;
//	public $init = null;
//	public function RenderTree($level=0){
//		echo str_repeat('  ',$level) . get_class($this) . ': ' .$this->type.' '.$this->name . "\n";
//		if (!is_null($this->init)){
//			$this->init->RenderTree($level+1);
//		}
//	}
//}

//class EmptyExpression extends Expression {
//	public function RenderTree($level=0){
//		echo str_repeat('  ',$level) . get_class($this) . "\n";
//	}
//}



//class NumberExpression extends LiteralExpression { }
//class VariableExpression extends IdentifierExpression {}
//class FunctionCallExpression extends IdentifierExpression {}

//class OpUnaryPlusExpression extends UnaryOperatorExpression  {}
//class OpUnaryMinusExpression extends UnaryOperatorExpression {}
//class OpPlusExpression extends BinaryOperatorExpression { }
//class OpMinusExpression extends BinaryOperatorExpression { }
//class OpMulExpression extends BinaryOperatorExpression { }
//class OpDivExpression extends BinaryOperatorExpression { }
//class OpModExpression extends BinaryOperatorExpression { }

?>
