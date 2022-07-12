<?php

namespace PedroData\Hotel\Service; 

use GuzzleHttp\Client;

class HotelService {

	private $response;
	private const EARTH_RADIOUS_KM = 6371.009;

	public function __construct(
		private float $clientLatitude,
		private float $clientLongitude,
		private string $orderBy
	) {

		// calling webservice
		$this->getWebServiceHotels();
	}

	private function getWebServiceHotels() {
		$client = new Client();
		$response = $client->request(
			'GET',
			'https://xlr8-interview-files.s3.eu-west-2.amazonaws.com/source_2.json'
		);

		$this->response = json_decode($response->getBody(), false);
	}

	private function sortHotelsByLocation() : array 
	{

		$places = [];

		foreach($this->response->message as $key => $hotels) {
			if(count($hotels) === 4) {
				$hotelLat = (float)$hotels[1];
				$hotelLon = (float)$hotels[2];
				$places[$key]['distance'] = $this->calculateDistance($hotelLat, $hotelLon);
			}
			
		}

		asort($places);
		return $places;
	}

	private function sortHotelsByPrice() 
	{
		$hotels = [];

		foreach($this->response->message as $key=>$hotel) {
		    if(count($hotel) === 4) {
				$hotels[] = $hotel; 
			}
		}

		foreach($hotels as $key => $hotel) {
			$sort['price'][$key] = $hotel[3];
			$sort['distance'][$key] = $this->calculateDistance(
				(float)$hotels[1], (float)$hotels[2]
			);
		}

		$keys = array_keys($hotels);

		array_multisort($sort['price'], SORT_ASC, $sort['distance'], SORT_ASC, $hotels, $keys);

		return array_combine($keys, $hotels);
	}

	private function setDistanceToUnits(float $distance)
	{
		return round(self::EARTH_RADIOUS_KM * $distance, 3);
	}

	private function sortByAge($a, $b) 
	{
	    return $a['price'] > $b['price'] ;
	}

	private function calculateDistance(float $latitude, float $longitude) 
	{
		$lat1 = deg2rad($latitude);
		$lon1 = deg2rad($longitude);
		$lat2 = deg2rad($this->clientLatitude);
		$lon2 = deg2rad($this->clientLongitude);

		$deltaLat = $lat2 - $lat1;
		$deltaLon = $lon2 - $lon1;

		$havLat = (sin($deltaLat / 2))**2;
		$havLng = (sin($deltaLon / 2))**2;

		return 2 * asin(sqrt($havLat + cos($lat1) * cos($lat2) * $havLng));
	}

	private function aggregateDataHotel($dataHotel, $order)
	{
		$hotels = [];
		foreach($dataHotel as $key => $place) {

			$hotels[$key]['name'] = $this->response->message[$key][0];

			if($order === 'proximity') {
				$hotels[$key]['distance'] = $this->setDistanceToUnits($place['distance']);
			} 

			if( $order === 'pricepernight') {
				$hotels[$key]['distance'] = $this->setDistanceToUnits(
					$this->calculateDistance(
						(float)$place[1], (float)$place[2]
					)
				);
			}

			$hotels[$key]['price'] = $this->response->message[$key][3];
		}

		return $hotels;
	}

	public function getNearbyHotels() {

		$order = ($this->orderBy !== 'pricepernight') ? 'proximity' : 'pricepernight' ; 

		$hotelOrder = match ($order) {
		    'proximity' 	=> $this->sortHotelsByLocation(),
		    'pricepernight' => $this->sortHotelsByPrice(),
		};
			
		return $this->aggregateDataHotel($hotelOrder, $order);
	}
}
