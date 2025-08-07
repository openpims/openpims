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
        $host = str_replace([
            'http://',
            'https://'
        ], [
            'http://'.Auth::user()->token.'.',
            'https://'.Auth::user()->token.'.'
        ], env('APP_URL'));

        $extension = false;
        $valid_url = false;
        $headers = array_change_key_case(getallheaders(), CASE_LOWER);
        if(array_key_exists('x-openpims', $headers)) {

            //extension installiert
            $extension = true;

            //url vergleichen
            if ($headers['x-openpims'] == $host) {
                $valid_url = true;
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

        //setup erledigt
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
