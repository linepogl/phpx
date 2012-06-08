<?php

abstract class AstType extends AstNode {

	public static function Make(ParseTree $parse_tree) {
		$x = $parse_tree->GetChild(0);
		switch($x->GetItem()){
			case TInt:
				return AstIntType::Make($x);

			default:
			case NQualifiedIdentifier;
				return AstComplexType::Make($x);
		}
	}


}
