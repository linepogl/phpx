<?php


const TVar = 'TVar';
const TDot = 'TDot';
const TInt = 'TInt';
const TAssign = 'TAssign';
const TCurlyOpen = 'TCurlyOpen';
const TCurlyClose = 'TCurlyClose';
const TSemicolon = 'TSemicolon';


const NStatementSequence = 'NStatementSequence';
const NStatement = 'NStatement';
const NGroupStatement = 'NGroupStatement';
const NExpressionStatement = 'NExpressionStatement';
const NVariableDeclarationStatement = 'NVariableDeclarationStatement';
const NVariableDeclarationStatementTail = 'NVariableDeclarationStatementTail';
const NExpression = 'NExpression';
const NAssignTarget = 'NAssignTarget';
const NQualifiedIdentifier = 'NQualifiedIdentifier';
const NQualifiedIdentifierTail = 'NQualifiedIdentifierTail';
const NType = 'NType';
const NAssign = 'NAssign';



class PhpxGrammar extends Grammar {
	protected function OnInit(){
		$this[TVar] = 'var';
		$this[TDot] = '.';
		$this[TInt] = 'int';
		$this[TAssign] = '=';
		$this[TCurlyOpen] = '{';
		$this[TCurlyClose] = '}';
		$this[TSemicolon] = ';';


		$this[NProgramFile] = array( NStatementSequence , TEndOfFile );

		$this[NStatementSequence] = array( );
		$this[NStatementSequence] = array( NStatement , NStatementSequence );

		$this[NStatement] = array( NGroupStatement );
		$this[NStatement] = array( NExpressionStatement );
		$this[NStatement] = array( NVariableDeclarationStatement );

		$this[NGroupStatement] = array( TCurlyOpen , NStatementSequence , TCurlyClose );
		$this[NExpressionStatement] = array( NExpression , TSemicolon  );
		$this[NVariableDeclarationStatement] = array( TVar , TIdentifier , NVariableDeclarationStatementTail , TSemicolon );

		$this[NVariableDeclarationStatementTail] = array( NType , NAssign );
		$this[NVariableDeclarationStatementTail] = array( NAssign );

		$this[NType] = array( TInt );
		$this[NType] = array( NQualifiedIdentifier );

		$this[NQualifiedIdentifier] = array( TIdentifier , NQualifiedIdentifierTail );
		$this[NQualifiedIdentifierTail] = array( );
		$this[NQualifiedIdentifierTail] = array( TDot , NQualifiedIdentifier );

		$this[NAssign] = array( );
		$this[NAssign] = array( TAssign , NExpression );

		$this[NExpression] = array( TIntLiteral );
		$this[NExpression] = array( NAssignTarget , NAssign );

		$this[NAssignTarget] = array( NQualifiedIdentifier );
	}
}

