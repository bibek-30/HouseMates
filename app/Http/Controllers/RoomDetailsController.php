<?php

namespace App\Http\Controllers;

use App\Models\roomDetails;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;


class RoomDetailsController extends Controller
{

    public function index()
    {
        $details = roomDetails::all();

        return response()->json($details, 200);
    }


    public function create(Request $request)
    {
        $request->validate([
            'data*.title',
            'data*.city',
            'data*.state',
            'data*.zip',
            'data*.price',
            'data*.available',
            'image' => 'required',
            'data*.desc'
        ]);

        $owner = Auth::user();
        // return $owner;
        if ($request->hasFile('image')) {
            $file_room = $request->file('image');
            $filename_room = uniqid() . '.' . $file_room->extension();
            $file_room->storeAs('public/images/rooms', $filename_room);
        } else {
            return response()->json("Please add the image");
        }

        $data = json_decode($request->data);

        $roomDetails = roomDetails::create([
            'title' => $data->title,
            'user_id' => $owner->id,
            'city' => $data->city,
            'state' => $data->state,
            'zip' => $data->zip,
            'price' => $data->price,
            'available' => $data->available,
            'image' =>  env('APP_URL') . Storage::url('public/images/rooms/' . $filename_room),
            'desc' => $data->desc,
        ]);

        $response = [
            "status"  => 200,
            "room_details" => $roomDetails
        ];

        return response()->json($response, 200);
    }


    public function search(Request $request)
    {
        $query = roomDetails::query();

        // if ($request->has('city')) {
        //     $query->where('city', 'like', '%' . $request->input('city') . '%');
        // }

        if ($request->has('title')) {
            // return $request;
            $query->where('title', 'LIKE', '%' . $request->input('title') . '%');
        }

        if ($request->has('min_price') && $request->has('max_price')) {
            $minPrice = $request->input('min_price');
            $maxPrice = $request->input('max_price');

            $query->whereBetween('price', [$minPrice, $maxPrice]);
        }


        $rooms = $query->get();

        return response()->json($rooms);
    }

    public function feed(Request $request)
    {
        $query = roomDetails::query();

        if ($request->has('city')) {
            $query->where('city', 'like', '%' . $request->input('city') . '%');
        }

        $rooms = $query->get();

        return response()->json($rooms);
    }



    public function show($id)
    {
        $singleRoom = RoomDetails::find($id);
        return response()->json($singleRoom, 200);
    }


    public function update(Request $request, $id)
    {
        $user = auth()->user();

        $details = RoomDetails::find($id);

        // Check if the room exists
        if (!$details) {
            return response('Room not found.', 404);
        }

        // Check if the user is the owner of the room
        if ($user->id !== $details->user_id) {
            return response('Unauthorized action.', 403);
        }

        $request->validate([
            'title',
            'price',
            'desc'
        ]);


        $details->title = $request->title ? $request->title : $details->title;
        $details->price = $request->input('price', $details->price);
        $details->desc = $request->input('desc', $details->desc);
        $details->save();

        $successResponse = [
            "status" => true,
            "message" => "Successfully updated the room."
        ];

        return response()->json($successResponse, 200);
    }




    // public function update(RoomDetails $roomDetails, Request $request, $id)
    // {

    //     $user = auth()->user();

    //     // Check if the user is the owner of the room
    //     if ($user->id !== $roomDetails->user_id) {
    //         return response('Unauthorized action.');
    //     } else {

    //         $request->validate([
    //             'title' => 'unique:room_details',
    //             'country',
    //             'state',
    //             'city',
    //             'price',
    //             'available',
    //             'desc'
    //         ]);

    //         $details = RoomDetails::find($id);
    //         $details->title = $request->title ? $request->title : $details->title;
    //         $details->country = $request->country ? $request->country : $details->country;
    //         $details->state = $request->state ? $request->state : $details->state;
    //         $details->city = $request->city ? $request->city : $details->city;
    //         $details->price = $request->price ? $request->price : $details->price;
    //         $details->available = $request->available ? $request->available : $details->available;
    //         $details->desc = $request->desc ? $request->desc : $details->desc;
    //         $details->update();



    //         $errResponse = [
    //             "status" => false,
    //             "message" => "Update error"
    //         ];

    //         if (!$details) {
    //             return response()->json($errResponse, 404);
    //         }

    //         $successResponse = [
    //             "status" => true,
    //             "message" => "Successfully Updated"
    //         ];

    //         return response()->json($successResponse, 201);
    //     }
    // }

    // Delete post
    public function delete($id)
    {
        $data = RoomDetails::find($id);
        if (!$data) {
            return Response()->json("Invalid data", 404);
        }
        $data->delete();
        $successResponse = ["message" => "Data deleted successfully"];
        return response()->json($successResponse, 200);
    }

    // public function bulkDelete(Request $request)
    // {
    //     $delete_post = DB::table("room_details")->whereIn('id', $request->id)->delete();
    //     if (!$delete_post) {
    //         return response()->json([
    //             'message' => 'The Post does not exist in the database!',
    //         ], 404);
    //     }
    //     return response()->json(['success' => "Post is deleted successfully"]);
    // }
}
