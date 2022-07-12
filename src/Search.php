<?php

namespace PedroData\Hotel;

use PedroData\Hotel\Service\HotelService;

class Search {


	public static function getNearbyHotels(
		float $latitude, 
		float $longitude, 
		string $orderby
	) {

	$hotelService = new HotelService($latitude, $longitude, $orderby);

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
