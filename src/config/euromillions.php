<?php
// Comma separated list of urls
$defaultUrls = 'https://www.magayo.com/api/results.php?api_key=QS538DW5AKABASBMLD&game=euromillions';

return [

/*
|--------------------------------------------------------------------------
| Refresh time
|--------------------------------------------------------------------------
|
| This option controls the window time (in minutes) between http request to retriev new values|
*/

'refresh' => env('EUROMILLIONS_REFRESH', 1),

/*
|--------------------------------------------------------------------------
| API Url's
|--------------------------------------------------------------------------
|
| Each of the url's for use to get the results, Must be an array
*/

'apiUrls'=> explode(',', env('EUROMILLIONS_API_URL', $defaultUrls))

];
