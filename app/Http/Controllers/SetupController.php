<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SetupController extends Controller
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
        // Generate deterministic token for host subdomain
        $today = intval(floor(time() / 86400));
        $appDomain = parse_url(env('APP_URL'), PHP_URL_HOST);
        $input = Auth::user()->user_id . $appDomain . $today;
        $deterministicToken = substr(
            hash_hmac('sha256', $input, Auth::user()->token),
            0,
            32
        );

        $host = str_replace([
            'http://',
            'https://'
        ], [
            'http://' . $deterministicToken . '.',
            'https://' . $deterministicToken . '.'
        ], env('APP_URL'));

        $extension = false;
        $valid_url = false;
        $headers = array_change_key_case(getallheaders(), CASE_LOWER);
        if(array_key_exists('x-openpims', $headers)) {
            $headerValue = $headers['x-openpims'];

            // Check if extension is installed (either "not-configured" or a valid URL)
            if ($headerValue === 'not-configured' || filter_var($headerValue, FILTER_VALIDATE_URL)) {
                $extension = true;

                // Check if extension is also logged in (URL matches)
                if ($headerValue == $host) {
                    $valid_url = true;
                }
            }
        }

        if ($extension && $valid_url) {
            Auth::user()->update([
                'setup' => 0
            ]);
        } else {
            Auth::user()->update([
                'setup' => 1
            ]);
        }

        // setup completed
        //if ($request->has('setup')) {
        //    Auth::user()->update([
        //        'setup' => $request->get('setup')
        //    ]);
        //    return redirect('/home');
        //}

        return view('setup', [
            'user' => Auth::user(),
            'host' => $host,
            'extension' => $extension,
            'valid_url' => $valid_url,
            'isPost' => $request->isMethod('post')
        ]);
    }
}
