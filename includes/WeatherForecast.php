<?php

class SpecialWeatherForecast extends SpecialPage {

    private $weatherelements = [
        "None" => 0,
        "Sunshine" => 1,
        "Clouds" => 2,
        "Fog" => 3,
        "Hot Spell" => 4,
        "Heat Wave" => 5,
        "Rain" => 6,
        "Squall" => 7,
        "Dust Storm" => 8,
        "Sand Storm" => 9,
        "Wind" => 10,
        "Gales" => 11,
        "Snow" => 12,
        "Blizzard" => 13,
        "Thunder" => 14,
        "Thunderstorms" => 15,
        "Auroras" => 16,
        "Stellar Glare" => 17,
        "Gloom" => 18,
        "Darkness" => 19
    ];


    public function __construct( ) {
        parent::__construct( 'WeatherForecast' );
    }

    static function onBeforePageDisplay( $out, $skin ) : void  { 
        $out->addModules(['inputHandler']);
    }

    function zoneNameArray($weatherArray){
        $result = array();
        foreach ( $weatherArray as $zone){
            $result[$zone['name']] =  $zone['pagelinkname'] ;
        }
        ksort($result);
        return $result;
    }

    function getWeather(){
        $dbr = new DBConnection_Forecast();
        $allZonesWeather = $dbr->getForecastFromDB();

        $result = [ ];
        foreach( $allZonesWeather as $row ){
            //Filter zones for those in Era
            $temp = ParserHelper_Forecast::zoneERA_forList($row->name);
			if ( !isset($temp) ) { continue; }

            //Start stripping out weather details
            //Should occur for each relevant zone
            $zoneWeatherArray = ParserHelper_Forecast::createCompleteWeatherArrayForZone($row->weather, 2);
            // print_r("<br>". $temp . "<br>");
            // print_r($zoneWeatherArray);

            $weatherdetails = array(
                'name' => $temp,
                'pagelinkname' => $row->name,
				'weather' => $zoneWeatherArray,
                'id' => $row->zoneid
            );

			$result[] = $weatherdetails;
            //print_r("<br />" . $row->zoneid . " " . $result[$row->zoneid]['name']);
        }

        $allzones= array(
            'name' => ' ** Search All Zones ** ',
            'pagelinkname' => 'searchallzones',
            'weather' => NULL,
            'id' => NULL
        );
        $result[] = $allzones;

        return $result;
    }

    function execute( $par ) {

        $request = $this->getRequest();
		$output = $this->getOutput();
		//$output->addModules(['inputHandler']);
		$output->setPageTitle( $this->msg( 'weatherforecast' ) );
        $this->setHeaders();

		$zoneNameDropDown = $request->getText( 'zoneNameDropDown' );
		$weatherTypeDropDown = $request->getText( 'weatherTypeDropDown' );

        //$zoneNamesList
        $weatherArray = $this->getWeather();
        $zoneNamesList = $this->zoneNameArray($weatherArray);

        // print_r($zoneNameDropDown);
        // print_r($weatherTypeDropDown);

        $wikitext = "";
        if ( !$zoneNameDropDown  && !$weatherTypeDropDown  ) {
			//$wikitext = ' Make a selection ';
		}
        else {
            //create new array with weather values
            print_r($zoneNameDropDown);
            print_r($weatherTypeDropDown);
            $wikitext = $this->showWeatherPressed($weatherArray);
        }
        //print_r( array_values($zoneNamesList) );

        $formDescriptor = [
			// 'info' => [
			// 	'type' => 'info',
			// 	'label' => 'info',
			// 	// Value to display
			// 	'default' => 'Select a zone, and enter characters to search for in the mob name. Leave \'Mob name\' field blank to see all mobs. ',
			// 	// If true, the above string won't be HTML escaped
			// 	'raw' => true,
			// ],
            'weatherTypeDropDown' => [
				'type' => 'limitselect',
				'name' => 'weatherTypeDropDown',
				'label' => 'Select Weather Type', // Label of the field
				'class' => 'HTMLSelectField', // Input type
				'options' => $this->weatherelements ,
				'default' => "None",
			],
			'zoneNameDropDown' => [
				'type' => 'limitselect',
				'name' => 'zoneNameDropDown',
				'label' => 'Select Zone', // Label of the field
				'class' => 'HTMLSelectField', // Input type
				'options' => $zoneNamesList,
				'default' => "searchallzones",
            ]
        ];


    	$htmlForm = new HTMLForm( $formDescriptor, $this->getContext(), 'WeatherForecast_Form' );
		$htmlForm->setMethod( 'get' );
		// Text to display in submit button
		$htmlForm->setSubmitText( 'Show Weather' );

		// We set a callback function
		$htmlForm->setSubmitCallback( [ $this, 'processInput' ] );
		// Call processInput() in your extends SpecialPage class on submit
		$htmlForm->show(); // Display the form

		//print_r($htmlForm->wasSubmitted());
        $output->addWikiTextAsInterface( $wikitext );
    }


    public static function processInput( $formData ) {
        // print_r( $formData['weatherTypeDropDown']);
        // print_r( $formData['zoneNameDropDown']);

		// If true is returned, the form won't display again
		// If a string is returned, it will be displayed as an error message with the form
		if ( $formData['weatherTypeDropDown'] == 0 && $formData['zoneNameDropDown'] == 'searchallzones' ) {
			return 'Select something first....';
		}
		return false;
	}

    function _tableHeaders(){
		$html = "";
		/************************
		 * Initial HTML for the table
		 */
		$html .= "<br>
		<div ><i> All data and probabilities are based on AirSkyBoat. </i></div>
		<div style=\"max-height: 900px; overflow: auto; display: inline-block; width: 100%;\">
		<table class=\"sortable zone-weathertable\">
			<tr><th>Zone Name</th>
			<th>Vana Days</th>
			<th>Normal (50%)</th>
			<th>Common (35%)</th>
            <th>Rare (15%)</th>
			</tr>
            ";
		return $html;
	}

    function showWeatherPressed($weatherArray){
        $html = $this->_tableHeaders();

        return $html;
    }
}

?>