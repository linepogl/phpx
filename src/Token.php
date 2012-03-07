<?php
namespace Phpx;

const TKeyword                = 0x01000000;
const TAccessModifier         = 0x01010000;
const TPrivate                = 0x01010001;
const TProtected              = 0x01010002;
const TPublic                 = 0x01010004;
const TOverridabilityModifier = 0x01020000;
const TAbstract               = 0x01020001;
const TVirtual                = 0x01020002;
const TOverride               = 0x01020004;
const TFinal                  = 0x01020008;
const TClass                  = 0x01040001;
const TModule              = 0x01040002;
const TPartial                = 0x01040004;

const TDelimeter              = 0x02000000;
const TEndOfFile              = 0x02000001;
const TUnknownSymbol          = 0x02000002;
const TCurlyOpen              = 0x02000004;
const TCurlyClose             = 0x02000008;
const TColon                  = 0x02000010;
const TSemiColon              = 0x02000020;
const TDot                    = 0x02000040;

const TOther                  = 0x40000000;
const TComment                = 0x40000001;
const TIdentifier             = 0x40000002;




class Token {
	private $number;
	private $string;
	private $source;
	public function __construct($number,$string,Source $source) {
		$this->number = $number;
		$this->string = $string;
		$this->source = $source;
	}
	public function GetNumber(){ return $this->number; }
	public function GetString(){ return $this->string; }
	public function GetSource(){ return $this->source; }

	public function Is( $mask ){ return ( $this->number & $mask ) == $mask; }
	public function Translate(){ return self::$translations[$this->number]; }
	public function ToDebugString(){ return $this->Translate() . ' ' . $this->source->ToDebugString(); }


	private static $translations = array
		(TKeyword                => 'TKeyword'
		,TAccessModifier         => '(TPublic|TPrivate|TProtected)'
		,TPublic                 => 'TPublic'
		,TProtected              => 'TProtected'
		,TPrivate                => 'TPrivate'
		,TOverridabilityModifier => '(TAbstract|TVirtual|TOverride|TFinal)'
		,TAbstract               => 'TAbstract'
		,TVirtual                => 'TVirtual'
		,TOverride               => 'TOverride'
		,TFinal                  => 'TFinal'
		,TClass                  => 'TClass'
		,TModule              => 'TModule'
		,TPartial                => 'TPartial'

		,TDelimeter              => 'TDelimeter'
		,TEndOfFile              => 'TEndOfFile'
		,TUnknownSymbol          => 'TUnknownSymbol'
		,TCurlyOpen              => 'TCurlyOpen'
		,TCurlyClose             => 'TCurlyClose'
		,TColon                  => 'TColon'
		,TSemiColon              => 'TSemiColon'
		,TDot                    => 'TDot'

		,TOther                  => 'TOther'
		,TComment                => 'TComment'
		,TIdentifier             => 'TIdentifier'
		);


	private static $keywords = array
		('public'    => TPublic
		,'protected' => TProtected
		,'private'   => TPrivate
		,'abstract'  => TAbstract
		,'virtual'   => TVirtual
		,'final'     => TFinal
		,'override'  => TOverride
		,'class'     => TClass
		,'module' => TModule
		,'partial'   => TPartial
		);
	public static function IsKeyword($s){ return array_key_exists($s,self::$keywords); }
	public static function GetKeywordNumber($s){ return self::$keywords[$s]; }
	public static function TranslateNumber($number){ return self::$translations[$number]; }



}

?>
