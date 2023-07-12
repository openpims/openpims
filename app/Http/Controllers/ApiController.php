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

        $categories = [];

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
                $categories_array = json_decode($result, true);
                Log::info($categories_array);

                $necessary_category = false;

                foreach ($categories_array as $category) {

                    $standard = null;
                    if (array_key_exists('mapping', $category)) {
                        $standard = Standard::where('mapping', $category['mapping'])
                            ->where('user_id', $user_id)
                            ->first();

                        if ($category['mapping']=='necessary') {
                            $necessary_category = true;
                        }
                    }

                    $cat = Category::firstOrCreate([
                        'site_id' => $site_id,
                        'category' => $category['category'],
                        'standard_id' => $standard instanceof Standard? $standard->standard_id: null,
                    ]);

                    //init consent
                    Consent::create([
                        'user_id' => $user_id,
                        'category_id' => $cat->category_id
                    ]);

                    //Vendors in DB speichern
                    if (array_key_exists('vendors', $category)) {
                        foreach ($category['vendors'] as $vendor) {
                            Vendor::create([
                                'vendor' => $vendor['vendor'],
                                'url' => $vendor['url'],
                                'category_id' => $cat->category_id
                            ]);
                        }
                    }
                }

                //necessary category checken & Insert into consense
                if (!$necessary_category) {

                    $standard = Standard::where('mapping', 'necessary')
                        ->where('user_id', $user_id)
                        ->first();

                    $nes_cat = Category::create([
                        'site_id' => $site_id,
                        'category' => $standard->standard,
                    ], [
                        'standard_id' => $standard->standard_id,
                    ]);

                    Consent::create([
                        'user_id' => $user_id,
                        'category_id' => $nes_cat->category_id
                    ]);
                }

                // clear not loaded flag
                $site->not_loaded = 0;
                $site->save();

                //Log user_sites Last visit
                Visit::updateOrCreate(
                    ['site_id' =>  $site_id],
                    ['user_id' => $user_id, 'updated_at' => now()]
                );
            }

            //load categories from site and save in consents
//            $visit = Visit::where('site_id', $site_id)
//                ->where('user_id', $user_id)
//                ->first();
//            if ($visit instanceof Visit && $visit->first) {
//                $categories = Category::where('site_id', $site_id)->get('category_id');
//                foreach ($categories as $category) {
//                    Consent::updateOrCreate([
//                       'user_id' => $user_id,
//                       'category_id' => $category->category_id
//                    ]);
//                }
//                $visit->first = 0;
//                $visit->save();
//            }

            //consenses auslesen und ausgeben
            $sql = sprintf("
                SELECT category
                FROM consents
                JOIN categories USING (category_id)
                LEFT JOIN standards USING (standard_id)
                WHERE consents.user_id=%d
                AND site_id=%d
                AND COALESCE(consents.checked, standards.checked, 0)
                ORDER BY standard_id DESC
            ",
                $user_id,
                $site_id
            );
            foreach(DB::select($sql) as $category) {
                $categories[] = $category->category;
            }
        }

        return response()->json($categories);
    }
}
