<?php

class SpecialWeatherForecast extends SpecialPage {

    private $weatherelements = [
        0 => [ "Hot Spell", "Heat Wave" ],
        1 => [ "Blizzard", "Snow" ],
        2 => [ "Wind", "Gales" ],
        3 => [ "Dust Storm", "Sand Storm"],
        4 => [ "Thunder", "Thunderstorms"],
        5 => [ "Rain", "Squall"],
        6 => [ "Gloom", "Darkness"],
        7 => [ "Auroras", "Stellar Glare"],
        8 => [ "None", "Sunshine", "Clouds", "Fog" ]
    ];

    private $elements = [
        "Fire" => 0,
        "Ice" => 1,
        "Wind" => 2,
        "Earth" => 3,
        "Thunder" => 4,
        "Water" => 5,
        "Dark" => 6,
        "Light" => 7,
        "** All Weather Types **" => 8
    ];

    private function getWeatherTypeName($int){
        return;
    }

    // private function getWeatherElementFrom($int){
    //     switch ($int) {
    //         case 4; return "Fire"

    //         case 2;
    //             $weatherType = "[[File:clouds.png]] Clouds";
    //             break;
    //         case 3;
    //             $weatherType = "Fog";
    //             break;
    //         case 4;
    //             $weatherType = "{{Fire|Weather}} Hot Spell";
    //             break;
    //         case 5;
    //             $weatherType = "{{Fire|Double Weather}} Heat Wave";
    //             break;
    //         case 6;
    //             $weatherType = "{{Water|Weather}} Rain";
    //             break;
    //         case 7;
    //             $weatherType = "{{Water|Double Weather}} Squalls";
    //             break;
    //         case 8;
    //             $weatherType = "{{Earth|Weather}} Dust Storm";
    //             break;
    //         case 9;
    //             $weatherType = "{{Earth|Double Weather}} Sand Storm";
    //             break;
    //         case 10;
    //             $weatherType = "{{Wind|Weather}} Wind";
    //             break;
    //         case 11;
    //             $weatherType = "{{Wind|Double Weather}} Gales";
    //             break;
    //         case 12;
    //             $weatherType = "{{Ice|Weather}} Snow";
    //             break;
    //         case 13;
    //             $weatherType = "{{Ice|Double Weather}} Blizzard";
    //             break;
    //         case 14;
    //             $weatherType = "{{Lightning|Weather}} Thunder";
    //             break;
    //         case 15;
    //             $weatherType = "{{Lightning|Double Weather}} Thunderstorms";
    //             break;
    //         case 16;
    //             $weatherType = "{{Light|Weather}} Auroras";
    //             break;
    //         case 17;
    //             $weatherType = "{{Light|Double Weather}} Stellar Glare";
    //             break;
    //         case 18;
    //             $weatherType = "{{Dark|Weather}} Gloom";
    //             break;
    //         case 19;
    //             $weatherType = "{{Dark|Double Weather}} Darkness";
    //             break;
    //         default:
    //             break;
    //     }
    // }

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
            $zoneWeatherArray = ParserHelper_Forecast::createCompleteWeatherArrayForZone($row->weather, 30);
            // print_r("<br>". $temp . "<br>");
            // print_r($zoneWeatherArray);

            $weatherdetails = array(
                'name' => $temp,
                'pagelinkname' => $row->name,
				'weather' => $zoneWeatherArray,
                'id' => $row->zoneid
            );

			$result[] = $weatherdetails;
            //print_r("<br />" . $row->zoneid . " " . $row->name);
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
            $wikitext = $this->showWeatherPressed( $weatherArray, $zoneNameDropDown, $weatherTypeDropDown );
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
				'options' => $this->elements ,
				'default' => 8,
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
		if ( $formData['weatherTypeDropDown'] == 8 && $formData['zoneNameDropDown'] == 'searchallzones' ) {
			//return 'Select something first....';
            return;
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
		<table class=\"zone-weathertable\">
			<tr><th>Zone Name</th>
			<th>Vana Days</th>
			<th>Normal (50%)</th>
			<th>Common (35%)</th>
            <th>Rare (15%)</th>
			</tr>
            ";
		return $html;
	}

    function showWeatherPressed($weatherArray, $zone, $weatherType){
        $html = $this->_tableHeaders();

       // print_r($zone ." : ". $weatherType ." : ". count($weatherArray) );

        $shouldAddDay = null;
        if ( $zone == "searchallzones" && $weatherType == 8){
            $html = "<br /><div><i><b> You must select a zone or specific element. </b></i></div>";
        }
        else {
            foreach( $weatherArray as $row ){
            //print_r("<br />" . $row['name'] . " : " . $zone );
                if (!$row['weather']) continue;

                foreach($row['weather'] as $key => $day) {

                    foreach( $day as $dayWeatherType){
                        if ( $row['pagelinkname'] == $zone || $zone == "searchallzones"){
                            if ( $weatherType == 8 ){
                                $shouldAddDay = $day;
                                break;
                            }
                            foreach ( $this->weatherelements[$weatherType] as $value){
                                if ( str_contains($dayWeatherType, $value ) ) {
                                    //print_r( "<br> match: " . $value . " : ". $dayWeatherType);
                                    $shouldAddDay = $day;
                                    break 2;
                                }
                            }
                        }
                    }

                    // Add row to table
                    if ( $shouldAddDay != null && $shouldAddDay != 0){
                        $html .= "<tr><td>". $row['name'] ."</td><td> $key </td><td>". $day['normal']. "</td><td>". $day['common']. "</td><td>". $day['rare']. "</td>";
                        $shouldAddDay = 0;
                    }

                }
            }

            if ( !isset($shouldAddDay) ){
                //print_r( $shouldAddDay )
                $html = "<br /><div><i><b> No weather data matches this request. </b></i></div>";
            }
         }
        return $html;
    }
}

?>