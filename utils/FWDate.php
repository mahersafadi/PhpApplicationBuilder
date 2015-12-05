<?php
/*
 * Auther: Maher Safadi
* March - 2014
* */
class FWDate{
	private $format = "Y-m-d";
	private $dateObject;
	private static $daysNamesShort1 = array("Saturday"=>"sat", 			"Sunday"=>"sun",
			"Monday"=>"mon",			"Tuesday"=>"tues",
			"Wednesday"=>"wed",			"Thursday"=>"thur",
			"Friday"=>"fri"									);
	
	private static $daysNamesShort2 = array("1"=>"sat", 			"2"=>"sun",
			"3"=>"mon",				"4"=>"tues",
			"5"=>"wed",				"6"=>"thur",
			"7"=>"fri"							);
	
	private static $daysNamesShort3 = array("sat"=>"1", 			"sun"=>"2",
			"mon"=>"3",				"tues"=>"4",
			"wed"=>"5",				"thur"=>"6",
			"fri"=>"7"							);
	public static $defaultTimeZone = "";
	function __construct(){
		$this->dateObject = new DateTime();
	}
	public function fromDate2String(){
		return $d->format($this->format);
	}
	public function toString(){
		$str = $this->dateObject->format($this->format);
		return $str;
	}
	public function  setFromStr($dateAsStr){
		$this->dateObject = DateTime::createFromFormat($this->format, $dateAsStr);
	}
	public function setDateTimeObject($dateTimeObj){
		$this->dateObject = $dateTimeObj;
	}
	public function getDateTimeObject(){
		return $this->dateObject;
	}
	public function setFormat($fmt){
		$this->format = $fmt;
	}
	public function getFormat(){
		return $this->format;
	}
	public function getToday(){
		$nervaDate = new NervaDate();
		$nervaDate->setDateTimeObject(new DateTime());
		return $nervaDate;
	}
	public function getNexDay(){
		try{
			$res = new NervaDate();
			$d1 = $this->dateObject;
			$d2 = new DateTime();
			$d2->setDate($d1->format('Y'), $d1->format('m'), $d1->format('d')+1);
			$res->setDateTimeObject($d2);
			return $res;
		}
		catch(Exception $e){
			echo "".$e->getMessage();
		}
		return null;
	}
	public function getNextWeek(){
		try{
			$res = new NervaDate();
			$d1 = $this->dateObject;
			$d2 = new DateTime();
			$d2->setDate($d1->format('Y'), $d1->format('m'), $d1->format('d')+7);
			$res->setDateTimeObject($d2);
			return $res;
		}
		catch(Exception $e){
			echo "".$e->getMessage();
		}
		return null;
	}
	public function getNexYear(){
		try{
			$res = new NervaDate();
			$d1 = $this->dateObject;
			$d2 = new DateTime();
			$d2->setDate($d1->format('Y')+1, $d1->format('m'), $d1->format('d'));
			$res->setDateTimeObject($d2);
			return $res;
		}
		catch(Exception $e){
			echo "".$e->getMessage();
		}
		return null;
	}
	public function getShortName(){
		$str = $this->toString();
		$dayname = date('l', strtotime($str));
		return NervaDate::$daysNamesShort1[$dayname];
	}
	public function getDayName(){
		try{
			$c = $this->getShortName();
			$dayNumber = NervaDate::$daysNamesShort3[$c];
			if(($dayNumber <= 7) && ($dayNumber >= 1))
				return NervaDate::$daysNamesShort2[$dayNumber];
		}
		catch (Exception $e){
			echo $e->getMessage();
		}
		return null;
	}
	public function shiftToFirstSaterday(){
		$d1 = new NervaDate();
		$d1->setDateTimeObject($this->getDateTimeObject());
		$dn = $d1->getDayName();
		while($dn != 'sat'){
			$d2 = $d1->getNexDay();
			$dn = $d2->getDayName();
			$d1 = $d2;
		}
		$this->setDateTimeObject($d1->getDateTimeObject());
	}
	//------------------------------------------------
	public function getDayNumberInWeek(){
		$dayName = $this->getDayName();
		return (int)NervaDate::$daysNamesShort3[$dayName];
	}
	public static function getEmptyWeek(){
		$arr = array("sat" => null, "sun"=>null, "mon"=>null, "tues"=>null, "wed"=>null, "thur"=>null, "fri"=>null);
		return $arr;
	}
	public static  function isEmptyWeekArray($arr){
		if($arr == null)
			return true;
		$empty = true;
		foreach ($arr as $key=>$val){
			if($val != null && $val != '')
				$empty = false;
		}
		return $empty;
	}
	public function toStringFullDate($sep1='-', $sep2=' ', $sep3){
		$x = $this->getDateTimeObject()->format("Y".$sep1."m".$sep1."d".$sep2."h".$sep3."i".$sep3."s");
		if(is_array($x))
			$x = $x[0];
		return $x;
	}
	
	public function getYear(){
		return $this->getDateTimeObject()->format("Y");
	}
}
?>