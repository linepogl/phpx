<?php


const TVar = 'TVar';
const TDot = 'TDot';
const TInt = 'TInt';
const TAssign = 'TAssign';
const TEquals = 'TEquals';
const TCurlyOpen = 'TCurlyOpen';
const TCurlyClose = 'TCurlyClose';
const TSemicolon = 'TSemicolon';

const NProgram = 'NProgram';
const NProgramFileSequence = 'NProgramFileSequence';
const NProgramFile = 'NProgramFile';

const NStatementSequence = 'NStatementSequence';
const NStatement = 'NStatement';
const NErroneousStatementEnding = 'NErroneousStatementEnding';
const NVariableDeclarationStatement = 'NVariableDeclarationStatement';
const NVariableDeclarationStatementTail = 'NVariableDeclarationStatementTail';
const NExpression = 'NExpression';
const NAssignTarget = 'NAssignTarget';
const NQualifiedIdentifier = 'NQualifiedIdentifier';
const NQualifiedIdentifierTail = 'NQualifiedIdentifierTail';
const NType = 'NType';
const NMaybeAssign = 'NMaybeAssign';



class PhpxGrammar extends Grammar {
	protected function OnInit(){
		$this[TVar] = Terminal('var');
		$this[TDot] = Terminal('.');
		$this[TInt] = Terminal('int');
		$this[TAssign] = Terminal('=');
		$this[TEquals] = Terminal('==');
		$this[TCurlyOpen] = Terminal('{');
		$this[TCurlyClose] = Terminal('}');
		$this[TSemicolon] = Terminal(';');

		$this[NProgram] = Panic( TSemicolon , TEndOfFile , TEndOfInput );
		$this[NProgram] = Rule( NProgramFileSequence , TEndOfInput )
			->OnReduce(function(ParseNode $x){
				$statements = array();
				for ($x = $x[0]; count($x) > 0; $x = $x[1])
					for ($xx = $x[0][0]; count($xx) > 0; $xx = $xx[1])
						$statements[] = $xx[0];
				return new AstProgram($statements);
			});

		$this[NProgramFileSequence] = Rule( );
		$this[NProgramFileSequence] = Rule( NProgramFile , NProgramFileSequence );

		$this[NProgramFile] = Panic( TSemicolon , TEndOfFile , TEndOfInput );
		$this[NProgramFile] = Rule( NStatementSequence , TEndOfFile );
		$this[NProgramFile] = Rule( NStatementSequence , TEndOfFile );

		$this[NStatementSequence] = Rule( );
		$this[NStatementSequence] = Rule( NStatement , NStatementSequence );

		$this[NStatement] = Panic( TSemicolon , TEndOfFile , TCurlyClose );
		$this[NStatement] = Rule( TCurlyOpen , NStatementSequence , TCurlyClose )
			->SynchronizedOn( TCurlyClose )
			->OnReduce(function(ParseNode $x){
				$statements = array();
				for ($x = $x[1]; count($x) > 0; $x = $x[1]) {
					$statements[] = $x[0];
				}
				return new AstGroupStatement( $statements );
			});


		$this[NStatement] = Rule( TSemicolon )
			->OnReduce(function(ParseNode $x){ return new AstEmptyStatement(); });

		$this[NStatement] = Rule( TDot, TDot , TSemicolon)
			->OnReduce(function(ParseNode $x){ return new AstEmptyStatement(); });

		$this[NStatement] = Rule( NExpression , TSemicolon  )
			->OnReduce(function(ParseNode $x){ return new AstExpressionStatement($x[0]); });

		$this[NStatement] = Rule( TVar , TIdentifier , NVariableDeclarationStatementTail , TSemicolon )
			->OnReduce(function(ParseNode $x){
				$type = count($x[2]) == 1 ? null : $x[2][0];
				$assign = count($x[2]) == 1 ? $x[2][0] : $x[2][1];
				$expr = count($assign) != 2 ? null : $assign[1];
				return new AstVariableDeclarationStatement( $x[1] , $type , $expr );
			});

		$this[NVariableDeclarationStatementTail] = Rule( NType , NMaybeAssign );
		$this[NVariableDeclarationStatementTail] = Rule( NMaybeAssign );



		$this[NType] = Rule( TInt )
			->OnReduce( function(ParseNode $x){ return new AstIntType($x[0]); } );
		$this[NType] = Rule( NQualifiedIdentifier )
			->OnReduce(function(ParseNode $x) {
				$tokens = array($x[0][0]);
				for ($x = $x[0][1]; count($x)>0; $x = $x[1][1] )
					$tokens[] = $x[1][0];
				return new AstComplexType($tokens);
			});

		$this[NMaybeAssign] = Rule( );
		$this[NMaybeAssign] = Rule( TAssign , NExpression );

		$this[NExpression] = Rule( TIntLiteral )
			->OnReduce( function(ParseNode $x){ return new AstIntLiteral($x[0]); } );
		$this[NExpression] = Rule( NAssignTarget , NMaybeAssign )
			->OnReduce(function(ParseNode $x){ return count($x[1])==0 ? $x[0] : new AstAssignment( $x[0] , $x[1][1] ); });

//		$this[NExpression] = Rule( NAssignTarget , TAssign );

		$this[NAssignTarget] = Rule( NQualifiedIdentifier )
			->OnReduce(function(ParseNode $x) {
				$tokens = array($x[0][0]);
				for ($x = $x[0][1]; count($x)>0; $x = $x[1][1] )
					$tokens[] = $x[1][0];
				return new AstVariable($tokens);
			});

		$this[NQualifiedIdentifier] = Rule( TIdentifier , NQualifiedIdentifierTail );
		$this[NQualifiedIdentifierTail] = Rule( );
		$this[NQualifiedIdentifierTail] = Rule( TDot , NQualifiedIdentifier );
	}
}

