<?php

namespace App\Http\Controllers;

use App\Console\Supplier;
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

                foreach ($categories_array as $category => $suppliers) {

                    $standard = Standard::where('standard', $category)
                        ->where('user_id', $user_id)
                        ->first();

                    $cat = Category::firstOrCreate([
                        'site_id' => $site_id,
                        'category' => $category,
                        'standard_id' => $standard instanceof Category? $standard->standard_id: null,
                    ]);

                    //Suppliers in DB speichern
                    foreach ($suppliers as $supplier) {
                        Supplier::create([
                            'supplier' => $supplier['supplier'],
                            'category_id' => $cat->category_id
                        ]);
                    }
                }

                //necessary category checken & Insert into consense
                $standard = Standard::where('standard', 'necessary')
                    ->where('user_id', $user_id)
                    ->first();
                $necessary_category = Category::firstOrCreate([
                    'site_id' => $site_id,
                    'category' => 'necessary',
                ], [
                    'standard_id' => $standard->standard_id,
                ]);

                Consent::create([
                    'user_id' => $user_id,
                    'category_id' => $necessary_category->category_id
                ]);

                $site->not_loaded = 0;
                $site->save();

                //Log user_sites Last visit
                Visit::updateOrCreate(
                    ['site_id' =>  $site_id],
                    ['user_id' => $user_id, 'updated_at' => now()]
                );
            }

            //load categories from site and save in consents
            $visit = Visit::where('site_id', $site_id)
                ->where('user_id', $user_id)
                ->first();
            if ($visit instanceof Visit && $visit->first) {
                $categories = Category::where('site_id', $site_id)->get('category_id');
                foreach ($categories as $category) {
                    Consent::create([
                       'user_id' => $user_id,
                       'category_id' => $category->category_id
                    ]);
                }
                $visit->first = 0;
                $visit->save();
            }

            //TODO get consenses
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
