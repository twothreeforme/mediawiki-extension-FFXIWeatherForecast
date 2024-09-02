<?php

class VanaTime {

    private $elementalDay =     ["Firesday",    "Earthsday",        "Watersday",        "Windsday",         "Iceday",           "Lightningday",     "Lightsday",    "Darksday"];
    private $moonPhaseName =    ["New Moon",    "Waxing Crescent",  "First Quarter",    "Waxing Gibbous",   "Full Moon",        "Waning Gibbous",   "Last Quarter", "Waning Crescent"];

    private $VTIME_BIRTH = 1024844400000;
    private $VTIME_BASEDATE  = 1009810800;
    //private $vanaBirthday = (((898 * 360) + 30) * 24 * 60 * 60) / (25 / 1000); // 1117359360000 - in earth time milliseconds

    // Conversions in Minutes
    // private $VTIME_YEAR  =      518400;   // 360 * GameDay
    // private $VTIME_MONTH =      43200;      // 30 * GameDay
    private $VTIME_WEEK  =      11520;      // 8 * GameDay
    private $VTIME_DAY   =      1440;       // 24 hours * GameHour
    // private $VTIME_HOUR  =      60;         // 60 minutes
    private $VMULTIPLIER =      25;
    private $MOON_CYCLE_DAYS =  84;

    public function now_inEarthMS(){
        return ((898 * 360 + 30) * (24 * 60 * 60 * 1000)) + (floor(microtime(true)) - $this->VTIME_BIRTH) * $this->VMULTIPLIER;
    }

    public function now(){ return $this->now_inEarthMS() / ( 60 * 1000); }

    public function getVanaDate(){
        return intval(((floor(microtime(true) ) - $this->VTIME_BASEDATE) / 3456)) % 2160 ;
    }

    public function weekDayFrom($daysAhead){
        //print_r("<br/>" . ((int)$this->now() % $this->VTIME_WEEK) + ( $daysAhead * $this->VTIME_DAY));
        return floor(( ( (int)$this->now() + ($daysAhead * $this->VTIME_DAY)) % $this->VTIME_WEEK)  / $this->VTIME_DAY);
    }

    public function weekDay($vanatime){
        if ( !isset($vanatime) ) $vanatime = $this->now();
        return floor(((int)$vanatime % $this->VTIME_WEEK) / $this->VTIME_DAY);
    }

    public function getWeekDayElement($vanaWeekDay){
        return $this->elementalDay[$this->weekDayFrom($vanaWeekDay)];
    }

    public function moonLatentPhase($vanatime, $daysAhead){
        if ( !isset($daysAhead) ) $daysAhead = 0;
        if ( !isset($vanatime) ) $vanatime = $this->now();
        $vanatime = $vanatime + ($daysAhead * $this->VTIME_DAY);

        $moonPhase = $this->moonPercent($vanatime);
        $moonDirection = $this->moonDirection($vanatime);

        if ($moonPhase <= 5 || ($moonPhase <= 10 && $moonDirection == 1)) {return 0;} // New Moon - 10% waning -> 5% waxing
        else if ($moonPhase >= 7 && $moonPhase <= 38 && $moonDirection == 2) {return 1;}  // Waxing Crescent - 7% -> 38% waxing
        else if ($moonPhase >= 40 && $moonPhase <= 55 && $moonDirection == 2){return 2;}  // First Quarter - 40%% -> 55% waxing
        else if ($moonPhase >= 57 && $moonPhase <= 88 && $moonDirection == 2){return 3;}  // Waxing Gibbous - 57% -> 88%
        else if ($moonPhase >= 95 || ($moonPhase >= 90 && $moonDirection == 2)){return 4;}  // Full Moon - waxing 90% -> waning 95%
        else if ($moonPhase >= 62 && $moonPhase <= 93 && $moonDirection == 1){return 5;}  // Waning Gibbous - 93% -> 62%
        else if ($moonPhase >= 45 && $moonPhase <= 60 && $moonDirection == 1){return 6;}  // Last Quarter - 60% -> 45%
        else{return 7;}  // Waning Crescent - 43% -> 12%
    }

    public function moonDirection($vanatime){
        if ( !isset($vanatime) ) $vanatime = $this->now();
            $moondays = floor($this->moonDays($vanatime));
            //console.log(daysmod);
            if ($moondays == 42 || $moondays == 0) { return 0; }// neither waxing nor waning
            else if ($moondays < 42){ return 1; } // waning
            else{ return 2; } // waxing
        }

    public function moonDays($vanatime){
        if ( !isset($vanatime) ) $vanatime = $this->now();
        return  ( (int)(( $vanatime /  $this->VTIME_DAY ) + 26) % $this->MOON_CYCLE_DAYS);
    }

    public function moonPercent($vanatime){
        if ( !isset($vanatime) ) $vanatime = $this->now();
        return abs( round((42 - floor($this->moonDays($vanatime))) / 42 * 100) );
    }

    public function moonPhaseNameFrom($daysAhead){
        $vt = (int)$this->now() + ($daysAhead * $this->VTIME_DAY);
        return $this->moonPhaseName[$this->moonLatentPhase($vt, null)] . " " .$this->moonPercent($vt) . "% ";
    }

}

 ?>