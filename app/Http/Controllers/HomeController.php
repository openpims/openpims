<?php

namespace App\Http\Controllers;

use App\Models\Cookie;
use App\Models\Site;
use App\Models\Vendor;
use App\Models\Consent;
use App\Models\Standard;
use App\Models\Visit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        //check for correct site or create one
        $site = null;
        $url = $request->get('url');
        if ($url) {
            $url = trim($url);
            $site = Site::where('url', $url)->first();
            if (!$site instanceof Site) {
                $site = $this->saveSite($url);
            }
        }

        //Load cookies
        $cookies = [];
        if ($site instanceof Site) {
            $sql = sprintf("
                SELECT cookie_id, cookie, necessary, checked
                FROM cookies
                LEFT JOIN consents USING (cookie_id)
                WHERE site_id=%d
            ",
                $site->site_id,
                Auth::user()->user_id
            );
            $cookies = DB::select($sql);

            //Log user_sites Last visit
            Visit::updateOrCreate([
                'site_id' => $site->site_id
            ], [
                'user_id' => Auth::user()->user_id,
                'updated_at' => now(),
            ]);
        }

        //load all visited sites
        $sql = sprintf("
            SELECT 
                v.site_id, 
                s.site,
                COALESCE(SUM(CASE WHEN c.necessary = 1 AND con.checked = 1 THEN 1 ELSE 0 END), 0) as necessary_count,
                COALESCE(SUM(CASE WHEN c.necessary = 0 AND con.checked = 1 THEN 1 ELSE 0 END), 0) as voluntary_count
            FROM visits v
            JOIN sites s ON v.site_id = s.site_id
            LEFT JOIN cookies c ON s.site_id = c.site_id
            LEFT JOIN consents con ON c.cookie_id = con.cookie_id AND con.user_id = %d
            WHERE v.user_id=%d
            GROUP BY v.site_id, s.site
            ORDER BY v.updated_at DESC
        ",
            Auth::user()->user_id,
            Auth::user()->user_id
        );
        $sites = DB::select($sql);

        //check for necessary extension and correct setup
        $host = str_replace([
            'http://',
            'https://'
        ], [
            'http://'.Auth::user()->token.'.',
            'https://'.Auth::user()->token.'.'
        ], env('APP_URL'));

        $extension_installed = false;
        $valid_url = false;
        $headers = array_change_key_case(getallheaders(), CASE_LOWER);
        if(array_key_exists('x-openpims', $headers)) {

            //extension installiert
            $extension_installed = true;

            //url vergleichen
            if ($headers['x-openpims'] == $host) {
                $valid_url = true;
            }
        }

        $setup_unfinished = !($extension_installed && $valid_url);

        return view('home', [
            'user' => Auth::user(),
            'sites' => $sites,
            'site' => $site,
            'cookies' => $cookies,
            'url' => $url,
            'show_site' => !is_null($url) && $site instanceof Site,
            'extension_installed' => $extension_installed,
            'valid_url' => $valid_url,
            'setup_unfinished' => $setup_unfinished,
            'host' => $host,
        ]);
    }

    private function saveSite(string $url)
    {
        //load categories and parse url
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, Array("User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.15) Gecko/20080623 Firefox/2.0.0.15") );
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result= curl_exec ($ch);
        curl_close ($ch);
        $array = json_decode($result, true);
        Log::info($array);

        $site = Site::firstOrCreate([
            'site' => $array['site'],
            'url' => $url,
        ]);
        $site_id = $site->site_id;

        foreach ($array['cookies'] as $cookie) {
            $sql = sprintf("
                INSERT IGNORE 
                INTO cookies (cookie, site_id, necessary, created_at, updated_at)
                VALUES (
                   '%s', 
                   %d,
                   %d,
                   TIMESTAMP(NOW()), 
                   TIMESTAMP(NOW())
            )",
                $cookie['cookie'],
                $site_id,
                isset($cookie['necessary']) ? $cookie['necessary'] ? 1 : 0 : 0,
            );
            DB::insert($sql);
        }

        return $site;
    }

    public function save(Request $request)
    {
        #dd($request->all());

        $site_id = $request->input('site_id');
        $consents = $request->input('consents', []);

        $cookies = Cookie::where('site_id', $site_id)->get();
        foreach ($cookies AS $cookie) {
            Consent::updateOrCreate([
                'user_id' => Auth::user()->user_id,
                'cookie_id' => $cookie->cookie_id,
            ], [
                'checked' => $cookie->necessary? 1: 0,
            ]);
        }

        foreach ($consents AS $consent) {
            Consent::updateOrCreate([
                'user_id' => Auth::user()->user_id,
                'cookie_id' => $consent,
            ], [
                'checked' => 1,
            ]);
        }

        // Get the site information
        $site = Site::find($site_id);
        if ($site && $site->url) {
            // Extract the host part of the URL with proper error handling
            $parsedUrl = parse_url($site->url);

            if ($parsedUrl && isset($parsedUrl['scheme']) && isset($parsedUrl['host'])) {
                $host = $parsedUrl['scheme'] . '://' . $parsedUrl['host'];

                // Add port if it exists and is not default
                if (isset($parsedUrl['port']) && 
                    !(($parsedUrl['scheme'] === 'http' && $parsedUrl['port'] === 80) || 
                      ($parsedUrl['scheme'] === 'https' && $parsedUrl['port'] === 443))) {
                    $host .= ':' . $parsedUrl['port'];
                }

                return redirect($host);
            }
        }

        return redirect('/');
    }

    public function visit($siteId)
    {
        // Get the site information
        $site = Site::find($siteId);
        if ($site && $site->url) {
            $parsedUrl = parse_url($site->url);
            if ($parsedUrl && isset($parsedUrl['scheme']) && isset($parsedUrl['host'])) {
                $host = $parsedUrl['scheme'] . '://' . $parsedUrl['host'];
                if (isset($parsedUrl['port']) && !(($parsedUrl['scheme'] === 'http' && $parsedUrl['port'] === 80) || ($parsedUrl['scheme'] === 'https' && $parsedUrl['port'] === 443))) {
                    $host .= ':' . $parsedUrl['port'];
                }
                return redirect($host);
            }
        }
        return redirect('/');
    }

    public function export(Request $request)
    {
        $fileName = 'openpims.csv';

        $sql = "SELECT DISTINCT sites.site
            FROM consents
            JOIN cookies USING (cookie_id)
            JOIN sites USING (site_id)
            WHERE consents.checked = 1
            AND consents.user_id = " . Auth::user()->user_id;
        $consents = DB::select($sql);

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array('Site');

        $callback = function() use ($consents, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($consents as $consent) {
                $row['Site']  = $consent->site;

                fputcsv($file, array($row['Site']));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }


    /**
     * Get site cookies for AJAX request
     *
     * @param string $siteId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSiteCookies($siteId)
    {
        $site = Site::find($siteId);
        $cookies = Cookie::where('site_id', $siteId)->get();

        // Get user's current consents for this site
        $userConsents = Consent::where('user_id', Auth::user()->user_id)
            ->whereIn('cookie_id', $cookies->pluck('cookie_id'))
            ->pluck('checked', 'cookie_id');

        // Add checked status to cookies
        $cookies = $cookies->map(function($cookie) use ($userConsents) {
            $cookie->checked = $userConsents->get($cookie->cookie_id, 0) == 1;
            return $cookie;
        });

        return response()->json([
            'site' => $site,
            'cookies' => $cookies
        ]);
    }

    /**
     * Save consent without redirect (for editModal)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function editConsent(Request $request)
    {
        $site_id = $request->input('site_id');
        $consents = $request->input('consents', []);

        $cookies = Cookie::where('site_id', $site_id)->get();
        foreach ($cookies AS $cookie) {
            Consent::updateOrCreate([
                'user_id' => Auth::user()->user_id,
                'cookie_id' => $cookie->cookie_id,
            ], [
                'checked' => $cookie->necessary? 1: 0,
            ]);
        }

        foreach ($consents AS $consent) {
            Consent::updateOrCreate([
                'user_id' => Auth::user()->user_id,
                'cookie_id' => $consent,
            ], [
                'checked' => 1,
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Consents saved successfully']);
    }

    /**
     * Save cookie consents for a site.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function saveConsent(Request $request)
    {
        $site_id = $request->input('site_id');
        $cookies = $request->input('cookies', []);

        Log::info($request->all());

        Log::info('Saving consents for site: ' . $site_id);
        Log::info('Cookies: ', $cookies);

        // Log the site_id
        Log::info('$site_id: ', [$site_id]);

        // First, set all cookies for this site to unchecked
        DB::table('consents')
            ->join('cookies', 'consents.cookie_id', '=', 'cookies.cookie_id')
            ->where('cookies.site_id', $site_id)
            ->where('consents.user_id', Auth::user()->user_id)
            ->update(['consents.checked' => 0]);

        // Then, set the selected cookies to checked
        foreach ($cookies as $cookie) {
            $cookieModel = DB::table('cookies')
                ->where('cookie', trim($cookie))
                ->where('site_id', $site_id)
                ->first();

            if ($cookieModel) {
                Consent::updateOrCreate(
                    [
                        'user_id' => Auth::user()->user_id,
                        'cookie_id' => $cookieModel->cookie_id
                    ],
                    [
                        'checked' => 1
                    ]
                );
            }
        }

        return redirect()->back()->with('success', 'Cookie preferences saved successfully');
    }

}
