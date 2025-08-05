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
            SELECT site_id, site
            FROM visits
            JOIN sites USING (site_id)
            WHERE user_id=%d
            ORDER BY visits.updated_at DESC
        ",
            Auth::user()->user_id
        );
        $sites = DB::select($sql);

        return view('home', [
            'user' => Auth::user(),
            'sites' => $sites,
            'site' => $site,
            'cookies' => $cookies,
            'url' => $url,
            'show_site' => !is_null($url) && $site instanceof Site,
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
                INTO cookies 
                VALUE (
                   NULL, 
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

    public function export(Request $request)
    {
        $fileName = 'openpims.csv';

        $sql = "SELECT site, category
            FROM consents
            JOIN categories USING (category_id)
            LEFT JOIN standards USING (standard_id)
            JOIN sites USING (site_id)
            WHERE COALESCE(consents.checked, standards.checked, 0)
        ";
        $consents = DB::select($sql);

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array('Site', 'Category');

        $callback = function() use ($consents, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($consents as $consent) {
                $row['Site']  = $consent->site;
                $row['Category']    = $consent->category;

                fputcsv($file, array($row['Site'], $row['Category']));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /*public function standard()
    {
        $sql = sprintf("SELECT
                standard_id AS category_id,
                standard AS category,
                IF(checked, 'checked', '') AS checked,
                IF(disabled, 'disabled', '') AS disabled
            FROM standards
            WHERE user_id = %d
            ORDER BY standard_id
        ", Auth::user()->user_id);
        $categories = DB::select($sql);
        foreach ($categories AS $id => $category) {
            $categories[$id]->vendors = [];
        }

        return $categories;
    }*/

    public function category(int $site_id)
    {
        $sql = sprintf("SELECT
                category_id,
                category,
                IF(COALESCE(consents.checked, standards.checked, 0), 'checked', '') AS checked,
                IF(COALESCE(disabled, 0), 'disabled', '') AS disabled
            FROM consents
            JOIN categories USING (category_id)
            LEFT JOIN standards USING (standard_id)
            WHERE consents.user_id = %d
            AND site_id = %d
            ORDER BY disabled DESC
        ",
            Auth::user()->user_id,
            $site_id
        );
        $categories = DB::select($sql);

        foreach ($categories AS $id => $category) {
            $vendors = Vendor::where('category_id', $category->category_id)->get(['vendor_id', 'vendor', 'url']);
            $categories[$id]->vendors = $vendors;
            $categories[$id]->amount = count($vendors);
        }

        return $categories;
    }

    public function consent(bool $standard_bool, int $category_id)
    {
        if ($standard_bool) {
            $standard = Standard::find($category_id);
            $standard->checked = $standard->checked? 0: 1;
            $standard->save();
        } else {
            $consent = Consent::where('user_id', Auth::user()->user_id)
                ->where('category_id', $category_id)
                ->first();
            if ($consent instanceof Consent) {
                $consent->checked = $consent->checked? 0: 1;
                $consent->save();
            }
        }

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
