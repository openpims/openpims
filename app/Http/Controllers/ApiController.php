<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

            //get site
            $site = Site::where('url', $url)->first();
            if ($site instanceof Site) {

                //get user
                $user = User::where('token', $token)->first();
                if ($user instanceof User) {

                    //load consent
                    $sql = sprintf("
                        SELECT cookie, checked
                        FROM consents
                        LEFT JOIN cookies USING (cookie_id)
                        WHERE user_id=%d
                        AND site_id=%d
                    ",
                        $user->user_id,
                        $site->site_id
                    );
                    foreach (DB::select($sql) as $cookie) {
                        $cookies[$cookie->cookie] = $cookie->checked;
                    }

                    //Log user_sites Last visit
                    Visit::updateOrCreate([
                        'site_id' => $site->site_id
                    ], [
                        'user_id' => $user->user_id,
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        return response()->json($cookies);
    }
}
