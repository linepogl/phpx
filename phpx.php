<?php

require('src/Source.php');
require('src/Token.php');
require('src/Validator.php');
require('src/QName.php');

require('src/Reader.php');
require('src/Lexer.php');
require('src/Compiler.php');
require('src/Writer.php');

require 'ref/_.php';
require 'ref/PhpxAssembly.php';
require 'ref/PhpxModule.php';
require 'ref/PhpxClass.php';

require 'exp/_Expression.php';
require 'exp/CommentExpression.php';
require 'exp/QNameExpression.php';
require 'exp/AssemblyBodyExpression.php';
require 'exp/ModuleDeclarationExpression.php';
require 'exp/ModulePartExpression.php';
require 'exp/ModuleBodyExpression.php';
require 'exp/ClassDeclarationExpression.php';
require 'exp/ClassPartExpression.php';
require 'exp/ClassBodyExpression.php';


echo '<pre>';

$compiler = new Phpx\Compiler();
$compile->Name = 'Test';
foreach (scandir('tst') as $f) {
	if (!is_file("tst/$f")) continue;
	$compiler->AddFilename("tst/$f");
}

$compiler->Compile();

echo '<hr/>';
$compiler->Validator->RenderText();

echo '<hr/>';
$w = new Phpx\Writer('obj/tst.php');
$compiler->Assembly->Export($w);
echo file_get_contents('obj/tst.php');

?>
