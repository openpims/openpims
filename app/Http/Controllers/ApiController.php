<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use App\Models\Category;
use App\Models\Consent;
use App\Models\Site;
use App\Models\Standard;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use League\Flysystem\Visibility;

class ApiController extends Controller
{
    /**
     * Show the profile for a given user.
     */
    public function index(Request $request, string $token)
    {

        $url = $request->get('url');
        Log::info($token);
        Log::info($url);

        $cookies = [];

        if (!is_null($url)) {
            $site_extracted = parse_url($url, PHP_URL_HOST);
            //get Site
            $site_id = null;
            $site = Site::firstOrCreate([
                'site' => $site_extracted,
                'url' => $url
            ], [
                'not_loaded' => 1,
            ]);
            $site_id =$site->site_id;

            $user = User::where('token', $token)->first();
            $user_id = $user->user_id;

            if($user instanceof User && $site->not_loaded) {

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
                        isset($cookie['necessary'])? $cookie['necessary']? 1: 0: 0,
                    );
                    DB::insert($sql);

                }

                // clear not loaded flag
                $site->not_loaded = 0;
                $site->save();
            }

            //Log user_sites Last visit
            Visit::updateOrCreate([
                    'site_id' =>  $site_id
                ], [
                    'user_id' => $user_id,
                    'updated_at' => now()
                ]
            );

            //consenses auslesen und ausgeben
            $cookies = [];
            $sql = sprintf("
                SELECT *
                FROM consents
                LEFT JOIN cookies USING (cookie_id)
                WHERE user_id=%d
                AND site_id=%d
            ",
                $user_id,
                $site_id
            );
            foreach(DB::select($sql) as $cookie) {
                $cookies[$cookie->cookie] = $cookie->checked;
            }
        }

        return response()->json($cookies);
    }
}
