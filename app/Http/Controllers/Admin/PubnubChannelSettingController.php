<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class PubnubChannelSettingController extends Controller
{
    public function index(){
        return view('admin.admin-pages.pubnub-channels-settings.index');
    }
}
