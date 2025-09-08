<?php

namespace App\Http\Controllers;

use App\Http\Requests\SEOApiRequest;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SEOApiController extends Controller
{
    public function search(SEOApiRequest $request)
    {
        $data = $request->validated();

        $login    = env('DFS_LOGIN');
        $password = env('DFS_PASS');

        $response = Http::withBasicAuth($login, $password)
            ->post("https://api.dataforseo.com/v3/serp/{$data['domain']}/organic/live/advanced", [[
                "language_code"   => $data['language'],
                "location_code"   => (int) $data['location'],
                "keyword"         => $data['keyword'],
                "depth"           => 20,
                "max_crawl_pages" => 2,
            ]]);

        $json  = $response->json();
        $task  = isset($json['tasks'][0]) ? $json['tasks'][0] : null;

        // Твій стиль з тернарником: або беремо перший result, або віддаємо error
        $results = ($response->ok() && $task && (($task['status_code'] ?? null) == 20000))
            ? ($task['result'][0] ?? [])
            : ['error' => ($task['status_message'] ?? 'Some error happens with your request. Please check data twice!')];

        $rank = null;
        if (!isset($results['error'])) {
            $items  = isset($results['items']) && is_array($results['items']) ? $results['items'] : [];
            $target = strtolower(trim($data['site'] ?? ''));
            $target = preg_replace('/^www\./', '', $target);

            foreach ($items as $item) {
                if (($item['type'] ?? null) !== 'organic') continue;

                $host = parse_url($item['url'] ?? '', PHP_URL_HOST);
                if (!$host) $host = $item['domain'] ?? '';
                $host = strtolower($host);
                $host = preg_replace('/^www\./', '', $host);

                // збіг домену або піддомену (sub.example.com → example.com)
                $isSame = ($host === $target);
                $isSub  = (!$isSame && $target !== '' && substr($host, -strlen('.'.$target)) === '.'.$target);

                if ($isSame || $isSub) {
                    $rank = [
                        'rank_absolute' => $item['rank_absolute'] ?? null,
                        'rank_group'    => $item['rank_group'] ?? null,
                        'title'         => $item['title'] ?? null,
                        'url'           => $item['url'] ?? null,
                        'domain'        => $item['domain'] ?? null,
                    ];
                    break;
                }
            }
        }
        return view('main-form')->with([
            'response' => $results,
            'rank'     => $rank,
            'data'     => $data,
        ]);
    }
    public function getLocations(Request $request){
        $query = $request->get('query');
        return Location::where('location_name', 'like', "%$query%")
            ->orderBy('keywords','desc')
            ->limit(20)
            ->get();
    }
}
