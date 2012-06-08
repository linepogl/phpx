<?php

abstract class AstExpression extends AstNode {

	public static function Make(ParseTree $parse_tree) {
		$x = $parse_tree->GetChild(0);
		switch ($x->GetItem()){
			case NAssignTarget:
				if ($parse_tree->GetChild(1)->HasChildren())
					return AstAssignment::Make($parse_tree);
				else
					return AstVariable::Make($x->GetChild(0));
			case TIntLiteral:
				return AstIntLiteral::Make($x);
		}
	}

}
