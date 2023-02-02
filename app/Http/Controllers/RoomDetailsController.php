<?php

namespace App\Http\Controllers;

use App\Models\roomDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class RoomDetailsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $details = roomDetails::all();

        return response()->json($details, 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        return $request;
        $request->validate([
            'data*.title' => 'required',
            'data*.location' => 'required',
            'data*.price' => 'required',
            'image' => 'required|mimes:jpg,jpeg,png',
            'data*.desc' => 'required',
        ]);

        $roomData = json_decode($request->data);
        // return $roomData;
        // $roomData = $request;

        $file_room = $request->file('image');
        $filename_room = uniqid() . '.' . $file_room->extension();
        $file_room->storeAs('public/images/rooms', $filename_room);

        $roomDetails = roomDetails::create([
            'title' => $roomData->title,
            'location' => $roomData->location,
            'price' => $roomData->price,
            'image' =>  env('APP_URL') . Storage::url('public/images/rooms/' . $filename_room),
            'desc' => $roomData->desc,
        ]);
        $response = [
            "status"  => 200,
            "room_details" => $roomDetails
        ];

        return response()->json($response, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\roomDetails  $roomDetails
     * @return \Illuminate\Http\Response
     */
    public function show(roomDetails $roomDetails)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\roomDetails  $roomDetails
     * @return \Illuminate\Http\Response
     */
    public function edit(roomDetails $roomDetails)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\roomDetails  $roomDetails
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, roomDetails $roomDetails)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\roomDetails  $roomDetails
     * @return \Illuminate\Http\Response
     */
    public function destroy(roomDetails $roomDetails)
    {
        //
    }
}
