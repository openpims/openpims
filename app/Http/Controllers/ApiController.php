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
     * Identify user from deterministic 32-bit token
     */
    private function identifyUser(string $subdomainToken, string $requestingDomain): ?User
    {
        $today = intval(floor(time() / 86400));

        $users = User::all();

        foreach ($users as $user) {
            $input = "{$user->user_id}{$requestingDomain}{$today}";
            $expectedToken = substr(
                hash_hmac('sha256', $input, $user->token),
                0,
                32
            );

            if ($expectedToken === $subdomainToken) {
                return $user;
            }
        }

        $yesterday = $today - 1;
        foreach ($users as $user) {
            $input = "{$user->user_id}{$requestingDomain}{$yesterday}";
            $expectedToken = substr(
                hash_hmac('sha256', $input, $user->token),
                0,
                32
            );

            if ($expectedToken === $subdomainToken) {
                return $user;
            }
        }

        return null;
    }

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

                //get user via deterministic token
                $requestingDomain = parse_url($url, PHP_URL_HOST);
                $user = $this->identifyUser($token, $requestingDomain);
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
                    $cookies = DB::select($sql);

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
