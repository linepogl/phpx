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

foreach (scandir('tst') as $f) {
	if ($f == '.' || $f == '..') continue;
	if (!is_dir("tst/$f")) continue;


	echo "<h1>PROJECT: $f</h1>";

	$parser = new Parser($grammar);
	foreach (scandir("tst/$f") as $ff) {
		if (!is_file("tst/$f/$ff")) continue;
		$parser->AddFile("tst/$f/$ff");
	}

	$v = new Validator();
	$ast = $parser->Parse( $v );
	$ast->Analyze(new Scope(),$v);
	$ast->Debug();
	$v->Debug();

}

