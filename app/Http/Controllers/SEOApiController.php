<?php

namespace App\Http\Controllers;

use App\Http\Requests\SEOApiRequest;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SEOApiController extends Controller
{
    public function search(SEOApiRequest $request){
        $data = $request->validated();

        $login = env('DFS_LOGIN');
        $password = env('DFS_PASS');

        $response = Http::withBasicAuth($login,$password)
            ->post("https://api.dataforseo.com/v3/serp/{$data['domain']}/organic/live/advanced",[
            [
                "language_code"=> $data['language'],
                "location_code"=> (int)$data['location'],
                "keyword"=> $data['keyword'],
                "depth" => 20,
                "max_crawl_pages" =>2
            ]
        ]);

        $results = $response['tasks'][0]['status_code'] == 20000 ? $response['tasks'][0]['result'] : ['error' => 'Some error happens with your request. Please check data twice!'];

        return view('main-form')->with('response',$results );
    }
    public function getLocations(Request $request){
        $query = $request->get('query');
        return Location::where('location_name', 'like', "%$query%")
            ->orderBy('keywords','desc')
            ->limit(20)
            ->get();
    }
}
