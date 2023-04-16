<?php

namespace App\Http\Controllers;

use App\Models\Test;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class TestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        // Validate input data
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'price' => 'required|numeric',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric'
        ]);

        // Use Mapbox API to reverse geocode latitude and longitude into an address
        $client = new Client(['base_uri' => 'https://api.mapbox.com']);
        $response = $client->request('GET', '/geocoding/v5/mapbox.places/' . $request->longitude . ',' . $request->latitude . '.json', [
            'query' => [
                'access_token' => config('services.mapbox.key')
            ]
        ]);
        $data = json_decode($response->getBody()->getContents(), true);
        $address = $data['features'][0]['place_name'];

        // Create new room
        $room = new Test();
        $room->name = $request->name;
        $room->description = $request->description;
        $room->price = $request->price;
        $room->latitude = $request->latitude;
        $room->longitude = $request->longitude;
        $room->address = $address;
        $room->save();

        return response()->json(['success' => true, 'data' => $room]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Test  $test
     * @return \Illuminate\Http\Response
     */
    public function show(Test $test)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Test  $test
     * @return \Illuminate\Http\Response
     */
    public function edit(Test $test)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Test  $test
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Test $test)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Test  $test
     * @return \Illuminate\Http\Response
     */
    public function destroy(Test $test)
    {
        //
    }
}
