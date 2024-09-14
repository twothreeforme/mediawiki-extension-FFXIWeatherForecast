<?php

class SpecialDiggingWeatherForecast extends SpecialPage {

    public function __construct( ) {
        parent::__construct( 'DiggingWeatherForecast' );
    }

    static function onBeforePageDisplay( $out, $skin ) : void  { 
        $out->addModules(['inputHandler']);
    }

    function execute( $par ) {

        $request = $this->getRequest();
		$output = $this->getOutput();
		//$output->addModules(['inputHandler']);
		$output->setPageTitle( $this->msg( 'diggingweatherforecast' ) );
        $this->setHeaders();

        $time = new VanaTime();
        $html = HTMLTableHelper::buildTableHeaders();

        $db = new DBConnection_Forecast();
        $weatherArray = $db->getWeather(true);

        //print_r(count($weatherArray));
        foreach( $weatherArray as $row ){
            //print_r( count($row['weather']). "<br/>");
            foreach($row['weather'] as $key => $day) {
                $show = true;

                //foreach ( WeatherForecast_ElementMaps::$weatherelements[8] as $value){
                    if ( ParserHelper_Forecast::contains( $day['normal'], WeatherForecast_ElementMaps::$weatherelements[8] ) &&
                    ParserHelper_Forecast::contains( $day['common'], WeatherForecast_ElementMaps::$weatherelements[8]) &&
                    ParserHelper_Forecast::contains( $day['rare'], WeatherForecast_ElementMaps::$weatherelements[8]) )  {
                        $show = false;
                        continue;
                    }
                //}
                if ( $show == false ) continue;

                $vt = $time->getVanaTimeFromDaysAhead($key);
                $vanadays = ( $key == 0 ) ? "0 (Today)" : $key;
                $name = ParserHelper_Forecast::zoneName($row['name']);

                //print_r($time->dayColor($vt) . " ");
                //$html .= "<tr><td>". $row['name'] ."</td><td style=\"text-align:center;\">$vanadays</td><td>" . $time->earthTime(null) . "</td><td style=\"text-align:center; color:" . $time->dayColor($vt) . "\" >" . $time->getWeekDayElement($key) .  "</td><td style=\"text-align:center;\">" . $time->moonPhaseNameFrom($key) . "</td><td>". $day['normal']. "</td><td>". $day['common']. "</td><td>". $day['rare']. "</td>";
                $html .= "<tr><td>". $name ."</td><td style=\"text-align:center;\">$vanadays</td><td style=\"text-align:center; color:" . $time->dayColor($vt) . "\" >" . $time->getWeekDayElement($key) .  "</td><td style=\"text-align:center;\">" . $time->moonPhaseNameFrom($key) . "</td><td>". $day['normal']. "</td><td>". $day['common']. "</td><td>". $day['rare']. "</td>";
            }
        }
        //$time = null;
        $output->addWikiTextAsInterface( $html );
    }

}

?>