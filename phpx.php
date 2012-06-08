<?php

foreach (scandir('src') as $f) {
	if ($f == '.' || $f == '..') continue;
	if (is_dir("src/$f")) {
		foreach (scandir("src/$f") as $ff) {
			if (is_dir("src/$f/$ff")) continue;
			require("src/$f/$ff");
		}
	}
	else {
		require("src/$f");
	}
}



echo '<pre>';


$grammar = new PhpxGrammar();
$grammar->DebugReport();
die;

$parser = new Parser($grammar);

foreach (scandir('tst') as $f) {
	$ff = "tst/$f";
	if (!is_file($ff)) continue;
	$reader = new Reader($ff);
	$lexer = new Lexer($reader);
	$parser->Add($lexer);
}

$parse_tree = $parser->Parse();
$parse_tree->DebugReport();

$v = new Validator();
$ast = AstProgram::Make($parse_tree);
$ast->CalculateType(new Scope(),$v);

$ast->DebugReport();

$v->RenderText();

