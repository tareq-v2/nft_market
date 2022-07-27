<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function notification_seen(Request $request)
    {
        Notification::find($request->notify_id)->update([
            'status' => 'seen'
        ]);
        return response()->json([
            'notify_id' => $request->notify_id,
        ]);
    }

    public function mark_all_notify($id)
    {
        $all_nots = Notification::where('message_to',$id)->get();
        foreach($all_nots as $not){
            $not->update([
                'status' => 'seen',
            ]);
        }
        return back()->with('Your All Notifications Marked as Read');
    }

    public function view_all_notify($id)
    {
        $all_nots = Notification::where('message_to',$id)->get();
        return view('admin.notify.index',compact('all_nots'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Notification  $notification
     * @return \Illuminate\Http\Response
     */
    public function show(Notification $notification)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Notification  $notification
     * @return \Illuminate\Http\Response
     */
    public function edit(Notification $notification)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Notification  $notification
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Notification $notification)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Notification  $notification
     * @return \Illuminate\Http\Response
     */
    public function destroy(Notification $notification)
    {
        //
    }
}
