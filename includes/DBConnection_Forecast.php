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
}

?>