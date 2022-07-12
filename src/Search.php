<?php

namespace Hotel;

class Search {


	public static function getNearbyHotels(
		float $latitude, 
		float $longitude, 
		string $orderby
	) {

		$hotelService = new hotel;

        $html = '';
        foreach($hotelService->getNearbyHotels() as $hotel) {
            $html .= '<p><b>'. $hotel['name'] .'</b>,';
            $html .= ' '. $hotel['distance'] .' KM ';
            $html .= ', '. $hotel['price'] .' EUR </p>';
        }

        return $html;
	}
}

?>