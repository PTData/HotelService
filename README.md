# HotelService
Get the list of Hotels available in system by using our API by calling the endpoints using a GET method.

# usage 

@param float $latitude
@param float $longitude
@param string $order 'pricepernight' | 'proximity' 

in 'proximity' will give hotels near location defined by params $latitude and $longitude
in the other option, 'pricepernight' will give hotels price order

if you do not define param $orderby, the list will retrieved by 'proximity' param 

$search = Search::getNearbyHotels($latitude, $longitude, $orderby);
        
