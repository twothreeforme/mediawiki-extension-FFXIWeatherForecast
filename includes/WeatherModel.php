<?php


class WeatherModel {
    public function __construct() {
    }
    
    static function onParserInit( Parser $parser ) {
        $parser->setHook('WeatherModel', 'WeatherModel::generateWeatherTable' );
        return true;
	}

    

    public static function generateWeatherTable( $input, array $params, Parser $parser, PPFrame $frame ) {
        $db = new DBConnection();
        
        $zoneWeather = $db->getZoneWeather(1, 1) ;
    
        $html = "";

        //$html = $html . "<p>Normal:" . $zoneWeather["normal"] . "  Common:" . $zoneWeather["common"] . "  Rare:" . $zoneWeather["rare"] . "</p>";

        return 	$html;
    }
}

class SpecialWeatherForecast extends SpecialPage {
    public function __construct( ) {
        parent::__construct( 'WeatherForecast' );
    }

    static function onBeforePageDisplay( $out, $skin ) : void  { 
        $out->addModules(['inputHandler']);
    }

    function execute( $par ) {
        $request = $this->getRequest();
		$output = $this->getOutput();
		//$output->addModules(['inputHandler']);
		$output->setPageTitle( $this->msg( 'weatherforecast' ) );

        $wikitext = "<i>* Page loaded correctly.</i>";
        $output->addWikiTextAsInterface( $wikitext );
    }

    public static function processInput( $formData ) {
		
		// If true is returned, the form won't display again
		// If a string is returned, it will be displayed as an error message with the form
		// if ( $formData['mobNameTextField'] == ''  && $formData['itemNameTextField'] == '' && $formData['zoneNameDropDown'] != 'searchallzones' ) {
		// 	return '*Either the Mob field or Item field must be filled.';
		// }
		return false;
	}
}

?>