<?php

use Wikimedia\Rdbms\DatabaseFactory;

class DBConnection_Forecast {
	private $dbUsername = 'horizon_wiki'; 
	private $dbPassword = 'KamjycFLfKEyFsogDtqM';

    public function openConnection() {
        if ( $_SERVER['SERVER_NAME'] == 'localhost' ){ 
			$this->dbUsername = 'root'; $this->dbPassword = '';
		}

        try {
            $db = ( new DatabaseFactory() )->create( 'mysql', [
                'host' => 'localhost',
                'user' => $this->dbUsername,
                'password' => $this->dbPassword,
                // 'user' => 'horizon_wiki',
                // 'password' => 'KamjycFLfKEyFsogDtqM',
                'dbname' => 'ASB_Data',
                'flags' => 0,
                'tablePrefix' => ''] );
            //$status->value = $db;
            $returnDB = $db;
        } catch ( DBConnectionError $e ) {
            //$status->fatal( 'config-connection-error', $e->getMessage() );
            print_r('issue');
        }


        // return $status;
        return $returnDB;
    }

    public function getZoneForecastFromDB($zone){
        $dbr = $this->openConnection();
        $query = "zone_weather.zone = $zone";
		return $dbr->newSelectQueryBuilder()
			->select( [ '*' ] )
			->from( 'zone_weather' )
			->where( $query )
			->fetchResultSet(); 
    }

    public function getForecastFromDB() {
        $dbr =  $this->openConnection();
        // $query = "zone_weather.zone = $zone";
		return $dbr->newSelectQueryBuilder()
			->select( [ 'name', 'zoneid', 'weather' ] )
			->from( 'zone_weather' )
            ->join( 'zone_settings', null, 'zone_weather.zone=zone_settings.zoneid' )
			// ->where( $query )
			->fetchResultSet();
    }

    public function getWeather($forDiggersPage){
        $forDiggersPage ? $forDiggersPage : false;

        $dbr = new DBConnection_Forecast();
        $allZonesWeather = $dbr->getForecastFromDB();

        $result = [ ];
        foreach( $allZonesWeather as $row ){
            //Filter zones for those in Era
            $temp = ParserHelper_Forecast::zoneERA_forList($row->name);
			if ( !isset($temp) ) { continue; }

            $dayCount = 30;
            //check if on the diggers special page
            //should only include the weather for the zones listed in ExclusionsHelper_Forecast::$diggingRelevantZones
            if ( $forDiggersPage == true) {
                if ( !array_key_exists($row->zoneid, ExclusionsHelper_Forecast::$diggingRelevantZones) ) continue;
                $dayCount = 4;
            }

            //Start stripping out weather details
            //Should occur for each relevant zone
            $zoneWeatherArray = ParserHelper_Forecast::createCompleteWeatherArrayForZone($row->weather, $dayCount);
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

        if ( $forDiggersPage == false ) {
            $allzones= array(
                'name' => ' ** Search All Zones ** ',
                'pagelinkname' => 'searchallzones',
                'weather' => NULL,
                'id' => NULL
            );
            $result[] = $allzones;
        }

        return $result;
    }

}

?>