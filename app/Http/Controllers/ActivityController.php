<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;


class ActivityController extends Controller
{
    public function index(){
        $title = 'activity';
        return view('activities.index',compact(
            'title'
        ));
    }

    public function markAsRead(){
        foreach (auth()->user()->unreadNotifications as $notification) {
            $notification->markAsRead();
        }
        return back()->with('success',"Notifications has been cleared");
    }
}
