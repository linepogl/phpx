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
$parser = new Parser($grammar);

foreach (scandir('tst') as $f) {
	$ff = "tst/$f";
	if (!is_file($ff)) continue;
	$parser->AddFile($ff);
}

$v = new Validator();
$ast = $parser->Parse( $v );
$ast->Debug();
$v->Debug();
die;


$v = new Validator();
$ast->CalculateType(new Scope(),$v);
$ast->Debug();
$v->Debug();

