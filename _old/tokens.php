<?php
//

//const TIdentifier            = 0x01000001;   // _0.....1 ...1 ...................1
//const TComment               = 0x01000002;   // _0.....1 ...1 ..................1.

//const TLiteral               = 0x02000000;   // _0....1. xxxx xxxxxxxxxxxxxxxxxxxx
//const TStringLiteral         = 0x02100001;   // _0....1. ...1 ...................1
//const TDateTimeLiteral       = 0x02100002;   // _0....1. ...1 ..................1.
//const TTimeSpanLiteral       = 0x02100004;   // _0....1. ...1 .................1..
//const TNumberLiteral         = 0x02200000;   // _0....1. ..1. xxxxxxxxxxxxxxxxxxxx
//const TIntegerLiteral        = 0x02210000;   // _0....1. ..1. ...1xxxxxxxxxxxxxxxx
//const TDecIntegerLiteral     = 0x02210001;   // _0....1. ..1. ...1...............1
//const THexIntegerLiteral     = 0x02210002;   // _0....1. ..1. ...1..............1.
//const TBinIntegerLiteral     = 0x02210004;   // _0....1. ..1. ...1.............1..
//const TFloatLiteral          = 0x02220000;   // _0....1. ..1. ..1.xxxxxxxxxxxxxxxx

//const TFixed                 = 0x40000000;   // _1xxxxxx xxxx xxxxxxxxxxxxxxxxxxxx

//const TDelimeter             = 0x44000000;   // _1...1.. xxxx xxxxxxxxxxxxxxxxxxxx
//const TSemiColon             = 0x44100001;   // _1...1.. ...1 ...................1   ;
//const TDot                   = 0x44100002;   // _1...1.. ...1 ..................1.   .
//const TCurlyOpen             = 0x44100004;   // _1...1.. ...1 .................1..   {
//const TCurlyClose            = 0x44100008;   // _1...1.. ...1 ................1...   }
//const TParenOpen             = 0x44100010;   // _1...1.. ...1 ...............1....   (
//const TParenClose            = 0x44100020;   // _1...1.. ...1 ..............1.....   )
//const TTagOpen               = 0x44100040;   // _1...1.. ...1 .............1......   <
//const TTagClose              = 0x44100080;   // _1...1.. ...1 ............1.......   >
//const TBracketOpen           = 0x44100100;   // _1...1.. ...1 ...........1........   [
//const TBracketClose          = 0x44100200;   // _1...1.. ...1 ..........1.........   ]
//const TComma                 = 0x44100400;   // _1...1.. ...1 .........1..........   ,

//const TEof                   = 0x44200001;   // _1...1.. ..1. ...................1   <EOF>

//const TOperator              = 0x48000000;   // _1..1... xxxx xxxxxxxxxxxxxxxxxxxx
//const TPlus                  = 0x48100001;   // _1..1... ...1 ...................1 +
//const TMinus                 = 0x48100002;   // _1..1... ...1 ..................1. -
//const TMul                   = 0x48100004;   // _1..1... ...1 .................1.. *
//const TExp                   = 0x48100008;   // _1..1... ...1 ................1... **
//const TDiv                   = 0x48100010;   // _1..1... ...1 ...............1.... /
//const TEuclideanDiv          = 0x48100020;   // _1..1... ...1 ..............1..... \
//const TMod                   = 0x48100040;   // _1..1... ...1 .............1...... %
//const TAnd                   = 0x48100080;   // _1..1... ...1 ............1....... &&
//const TOr                    = 0x48100100;   // _1..1... ...1 ...........1........ ||
//const TXor                   = 0x48100200;   // _1..1... ...1 ..........1......... ^^
//const TNot                   = 0x48100400;   // _1..1... ...1 .........1.......... !
//const TBinaryAnd             = 0x48100800;   // _1..1... ...1 ........1........... &
//const TBinaryOr              = 0x48101000;   // _1..1... ...1 .......1............ |
//const TBinaryXor             = 0x48102000;   // _1..1... ...1 ......1............. ^
//const TShiftLeft             = 0x48104000;   // _1..1... ...1 .....1.............. <<
//const TShiftRight            = 0x48108000;   // _1..1... ...1 ....1............... >>

//const TPlusPlus              = 0x48200001;   // _1..1... ..1. ...................1 ++
//const TMinusMinus            = 0x48200002;   // _1..1... ..1. ..................1. --

//const TAssignOperator        = 0x48400000;   // _1..1... .1.. xxxxxxxxxxxxxxxxxxxx
//const TAssign                = 0x48400001;   // _1..1... .1.. ...................1 +=
//const TPlusAssign            = 0x48400002;   // _1..1... .1.. ..................1. +=
//const TMinusAssign           = 0x48400004;   // _1..1... .1.. .................1.. -=
//const TMulAssign             = 0x48400008;   // _1..1... .1.. ................1... *=
//const TExpAssign             = 0x48400010;   // _1..1... .1.. ...............1.... **=
//const TDivAssign             = 0x48400020;   // _1..1... .1.. ..............1..... /=
//const TEuclideanDivAssign    = 0x48400040;   // _1..1... .1.. .............1...... \=
//const TModAssign             = 0x48400080;   // _1..1... .1.. ............1....... %=
//const TAndAssign             = 0x48400100;   // _1..1... .1.. ...........1........ &&=
//const TOrAssign              = 0x48400200;   // _1..1... .1.. ..........1......... ||=
//const TXorAssign             = 0x48400400;   // _1..1... .1.. .........1.......... ^^=
//const TBinaryAndAssign       = 0x48400800;   // _1..1... .1.. ........1........... &=
//const TBinaryOrAssign        = 0x48401000;   // _1..1... .1.. .......1............ |=
//const TBinaryXorAssign       = 0x48402000;   // _1..1... .1.. ......1............. ^=
//const TShiftLeftAssign       = 0x48404000;   // _1..1... .1.. .....1.............. <<=
//const TShiftRightAssign      = 0x48408000;   // _1..1... .1.. ....1............... >>=

//const TComparisonOperator    = 0x48800000;   // _1..1... 1... xxxxxxxxxxxxxxxxxxxx
//const TEqual                 = 0x48800001;   // _1..1... 1... ...................1 ==
//const TNotEqual              = 0x48800002;   // _1..1... 1... ..................1. !=
//const TGreater               = 0x48800004;   // _1..1... 1... .................1.. >
//const TGreaterEqual          = 0x48800008;   // _1..1... 1... ................1... >=
//const TLess                  = 0x48800010;   // _1..1... 1... ...............1.... <
//const TLessEqual             = 0x48800020;   // _1..1... 1... ..............1..... <=

//const TKeyword               = 0x50000000;   // _1.1.... xxxx xxxxxxxxxxxxxxxxxxxx
//const TIf                    = 0x50100001;   // _1.1.... ...1 ...................1
//const TElseif                = 0x50100002;   // _1.1.... ...1 ..................1.
//const TElse                  = 0x50100004;   // _1.1.... ...1 .................1..
//const TMatch                 = 0x50100008;   // _1.1.... ...1 ................1...
//const TWith                  = 0x50100010;   // _1.1.... ...1 ...............1....
//const TBreak                 = 0x50100020;   // _1.1.... ...1 ..............1.....
//const TContinue              = 0x50100040;   // _1.1.... ...1 .............1......
//const TReturn                = 0x50100080;   // _1.1.... ...1 ............1.......

//const TAccessModifier        = 0x50200000;   // _1.1.... ..1. xxxxxxxxxxxxxxxxxxxx
//const TPrivate               = 0x50200001;   // _1.1.... ..1. ...................1
//const TProtected             = 0x50200002;   // _1.1.... ..1. ..................1.
//const TPublic                = 0x50200004;   // _1.1.... ..1. .................1..

//const TOverrideModifier      = 0x50400000;   // _1.1.... .1.. xxxxxxxxxxxxxxxxxxxx
//const TFinal                 = 0x50400001;   // _1.1.... .1.. ...................1
//const TVirtual               = 0x50400002;   // _1.1.... .1.. ..................1.
//const TAbstract              = 0x50400004;   // _1.1.... .1.. .................1..

//const TClass                 = 0x50800010;   // _1.1.... 1... ...................1




//class Token {

//	public static function Translate($token){ return self::$all[$token]; }
//	private static $all = array (
//		 TIdentifier            => 'Identifier'
//		,TComment               => 'Comment'

//		,TLiteral               => 'Literal'
//		,TStringLiteral         => 'String literal'
//		,TNumberLiteral         => 'Number literal'
//		,TIntegerLiteral        => 'Integer literal'
//		,TDecIntegerLiteral     => 'Decimal integer literal'
//		,THexIntegerLiteral     => 'Hexademical integer literal'
//		,TBinIntegerLiteral     => 'Binary integer literal'
//		,TFloatLiteral          => 'Float literal'
//		,TDateTimeLiteral       => 'DateTime literal'
//		,TTimeSpanLiteral       => 'TimeSpan literal'

//		,TFixed                 => 'Fixed length token'

//		,TDelimeter             => 'Delimeter'
//		,TSemiColon             => 'Delimeter ;'
//		,TDot                   => 'Delimeter .'
//		,TCurlyOpen             => 'Delimeter {'
//		,TCurlyClose            => 'Delimeter }'
//		,TParenOpen             => 'Delimeter ('
//		,TParenClose            => 'Delimeter )'
//		,TTagOpen               => 'Delimeter <'
//		,TTagClose              => 'Delimeter >'
//		,TBracketOpen           => 'Delimeter {'
//		,TBracketClose          => 'Delimeter }'
//		,TComma                 => 'Delimeter ,'

//		,TEof                   => 'End of file'

//		,TOperator              => 'Operator'
//		,TPlus                  => 'Operator +'
//		,TMinus                 => 'Operator -'
//		,TMul                   => 'Operator *'
//		,TExp                   => 'Operator **'
//		,TDiv                   => 'Operator /'
//		,TEuclideanDiv          => 'Operator \\'
//		,TMod                   => 'Operator %'
//		,TAnd                   => 'Operator &&'
//		,TOr                    => 'Operator ||'
//		,TXor                   => 'Operator ^^'
//		,TNot                   => 'Operator !'
//		,TBinaryAnd             => 'Operator &'
//		,TBinaryOr              => 'Operator |'
//		,TBinaryXor             => 'Operator ^'
//		,TShiftLeft             => 'Operator <<'
//		,TShiftRight            => 'Operator >>'

//		,TPlusPlus              => 'Operator ++'
//		,TMinusMinus            => 'Operator --'

//		,TAssignOperator        => 'Assign operator'
//		,TPlusAssign            => 'Assign operator +='
//		,TMinusAssign           => 'Assign operator -='
//		,TMulAssign             => 'Assign operator *='
//		,TExpAssign             => 'Assign operator **='
//		,TDivAssign             => 'Assign operator /='
//		,TEuclideanDivAssign    => 'Assign operator \\='
//		,TModAssign             => 'Assign operator %='
//		,TAndAssign             => 'Assign operator &&='
//		,TOrAssign              => 'Assign operator ||='
//		,TXorAssign             => 'Assign operator ^^='
//		,TBinaryAndAssign       => 'Assign operator &='
//		,TBinaryOrAssign        => 'Assign operator |='
//		,TBinaryXorAssign       => 'Assign operator ^='
//		,TShiftLeftAssign       => 'Assign operator <<='
//		,TShiftRightAssign      => 'Assign operator >>='

//		,TComparisonOperator    => 'Comparison operator'
//		,TEqual                 => 'Comparison operator =='
//		,TNotEqual              => 'Comparison operator !='
//		,TGreater               => 'Comparison operator >'
//		,TGreaterEqual          => 'Comparison operator >='
//		,TLess                  => 'Comparison operator <'
//		,TLessEqual             => 'Comparison operator <='

//		,TKeyword               => 'Keyword'
//		,TIf                    => 'Keyword if'
//		,TElseif                => 'Keyword elseif'
//		,TElse                  => 'Keyword else'
//		,TMatch                 => 'Keyword match'
//		,TWith                  => 'Keyword with'
//		,TBreak                 => 'Keyword break'
//		,TContinue              => 'Keyword continue'
//		,TReturn                => 'Keyword return'

//		,TAccessModifier        => 'Access modifier keyword'
//		,TPrivate               => 'Access modifier keyword private'
//		,TProtected             => 'Access modifier keyword protected'
//		,TPublic                => 'Access modifier keyword public'

//		,TOverrideModifier      => 'Override modifier keyword'
//		,TFinal                 => 'Override modifier keyword final'
//		,TVirtual               => 'Override modifier keyword virtual'
//		,TAbstract              => 'Override modifier keyword abstract'

//		,TClass                 => 'Keyword class'

//	);





//	public static function &GetKeywords(){ return self::$keywords; }
//	private static $keywords = array(
//		 TIf => 'if'
//		,TElseif => 'elseif'
//		,TElse => 'else'
//		,TMatch => 'match'
//		,TWith => 'with'
//		,TBreak => 'break'
//		,TContinue => 'continue'
//		,TReturn => 'return'

//		,TPrivate => 'private'
//		,TProtected => 'protected'
//		,TPublic => 'public'

//		,TFinal => 'final'
//		,TVirtual => 'virtual'
//		,TAbstract => 'abstract'

//		,TClass => 'class'
//	);






//	public static function &GetSymbols(){ return self::$symbols; }
//	private static $symbols = array (
//		 TSemiColon => ';'
//		,TDot => '.'
//		,TCurlyOpen => '{'
//		,TCurlyClose => '}'
//		,TParenOpen => '('
//		,TParenClose => ')'
//		,TTagOpen => '<'
//		,TTagClose => '>'
//		,TBracketOpen => '['
//		,TBracketClose => ']'
//		,TComma => ','


//		,TPlus => '+'
//		,TMinus => '-'
//		,TMul => '*'
//		,TExp => '**'
//		,TDiv => '/'
//		,TEuclideanDiv => '\\'
//		,TMod => '%'
//		,TAnd => '&&'
//		,TOr => '||'
//		,TXor => '^^'
//		,TNot => '!'
//		,TBinaryAnd => '&'
//		,TBinaryOr => '|'
//		,TBinaryXor => '^'
//		,TShiftLeft => '<<'
//		,TShiftRight => '>>'

//		,TPlusPlus => '++'
//		,TMinusMinus => '--'

//		,TPlusAssign => '+='
//		,TMinusAssign => '-='
//		,TMulAssign => '*='
//		,TExpAssign => '**='
//		,TDivAssign => '/='
//		,TEuclideanDivAssign => '\\='
//		,TModAssign => '%='
//		,TAndAssign => '&&='
//		,TOrAssign => '||='
//		,TXorAssign => '^^='
//		,TBinaryAndAssign => '&='
//		,TBinaryOrAssign => '|='
//		,TBinaryXorAssign => '^='
//		,TShiftLeftAssign => '<<='
//		,TShiftRightAssign => '>>='

//		,TEqual => '=='
//		,TNotEqual => '!='
//		,TGreater => '>'
//		,TGreaterEqual => '>='
//		,TLess => '<'
//		,TLessEqual => '<='
//	);


//	private static $symbols_map = array();
//	private static function InitSymbolsMap(){
//		foreach (self::$symbols as $token=>$string) {
//			$a =& self::$symbols_map;
//			$s = '';
//			foreach (str_split($string) as $x) {
//				$s .= $x;
//				if (!array_key_exists($s,$a)) $a[$s] = array();
//			}
//			$a[$s][] = $token;
//		}
//	}
//	public static function ExistsSymbolStartingWith($string){
//		return array_key_exists($string,self::$symbols_map);
//	}
//	public static function &GetSymbolsFor($string){
//		if (array_key_exists($string,self::$symbols_map)){
//			$r =& self::$symbols_map[$string];
//			return $r;
//		}
//		else
//			return self::$empty_readonly_array;
//	}





//	private static $empty_readonly_array = array();
//	private static $keywords_map = array();
//	private static function InitKeywordsMap(){
//		$a =& self::$keywords_map;
//		foreach (self::$keywords as $token=>$string) {
//			if (!array_key_exists($string,$a)) $a[$string] = array();
//			$a[$string][] = $token;
//		}
//	}
//	public static function ExistsKeyword($string){
//		return array_key_exists($string,self::$keywords_map);
//	}
//	public static function &GetKeywordsFor($string){
//		if (array_key_exists($string,self::$keywords_map)){
//			$r =& self::$keywords_map[$string];
//			return $r;
//		}
//		else
//			return self::$empty_readonly_array;
//	}






//	public static function Init(){
//		self::InitKeywordsMap();
//		self::InitSymbolsMap();
//	}
//}
//Token::Init();

?>
