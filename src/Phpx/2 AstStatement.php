<?php

abstract class AstStatement extends AstNode {

	public static function Make(ParseTree $parse_tree) {
		$x = $parse_tree->GetChild(0);
		switch ($x->GetItem()){
			case NExpressionStatement:
				return AstExpressionStatement::Make($x);
				break;
			case NVariableDeclarationStatement:
				return AstVariableDeclarationStatement::Make($x);
				break;
			case NGroupStatement:
				return AstGroupStatement::Make($x);
				break;
		}
	}

}
