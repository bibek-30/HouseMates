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

    // public function create(Request $request)
    // {
    //     // Authenticate the user
    //     if (!auth()->check()) {
    //         return response()->json("Unauthorized", 401);
    //     }

    //     $request->validate([
    //         'data.title' => 'required',
    //         'data.country' => 'required',
    //         'data.state' => 'required',
    //         'data.city' => 'required',
    //         'data.price' => 'required|numeric',
    //         'data.available' => 'required|boolean',
    //         'image' => 'required|image|max:1024',
    //         'data.desc' => 'nullable|string'
    //     ]);

    //     $user = auth()->user();
    //     $user_id = $user->id;

    //     if ($request->hasFile('image')) {
    //         $file_room = $request->file('image');
    //         $filename_room = uniqid() . '.' . $file_room->getClientOriginalExtension();
    //         $file_room->storeAs('public/images/rooms', $filename_room);
    //     } else {
    //         return response()->json("Please add the image");
    //     }

    //     $data = $request->input('data');

    //     $roomDetails = roomDetails::create([
    //         'title' => $data['title'],
    //         'user_id' => $user_id,
    //         'country' => $data['country'],
    //         'state' => $data['state'],
    //         'city' => $data['city'],
    //         'price' => $data['price'],
    //         'available' => $data['available'],
    //         'image' =>  env('APP_URL') . Storage::url('public/images/rooms/' . $filename_room),
    //         'desc' => $data['desc'] ?? '',
    //     ]);

    //     $response = [
    //         "status"  => 200,
    //         "room_details" => $roomDetails
    //     ];

    //     return response()->json($response, 200);
    // }


    public function store(Request $request)
    {
        $request->validate([
            'data*.title',
            'data*.country',
            'data*.state',
            'data*.city',
            'data*.price',
            'data*.available',
            'image' => 'required',
            'data*.desc'
        ]);
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
            // 'user_id' => 1,
            'country' => $data->country,
            'state' => $data->state,
            'city' => $data->city,
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


    public function create(Request $request)
    {

        // Check if the user is authenticated
        // if (!auth()->check()) {
        //     return response()->json("Unauthorized", 401);
        // }

        $request->validate([
            'data*.title',
            'data*.country',
            'data*.state',
            'data*.city',
            'data*.price',
            'data*.available',
            'image' => 'required',
            'data*.desc'
        ]);

        // $user_id = auth()->id();

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
            // 'user_id' => 1,
            'country' => $data->country,
            'state' => $data->state,
            'city' => $data->city,
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


    public function show($id)
    {
        $singleRoom = RoomDetails::find($id);
        return response()->json($singleRoom, 200);
    }

    public function update(Request $request, $id)
    {
        // return $request;
        $request->validate([
            'title' => 'unique:room_details',
            'country',
            'state',
            'city',
            'price',
            'available',
            'desc'
        ]);

        $details = RoomDetails::find($id);
        $details->title = $request->title ? $request->title : $details->title;
        $details->country = $request->country ? $request->country : $details->country;
        $details->state = $request->state ? $request->state : $details->state;
        $details->city = $request->city ? $request->city : $details->city;
        $details->price = $request->price ? $request->price : $details->price;
        $details->available = $request->available ? $request->available : $details->available;
        $details->desc = $request->desc ? $request->desc : $details->desc;
        $details->update();



        $errResponse = [
            "status" => false,
            "message" => "Update error"
        ];

        if (!$details) {
            return response()->json($errResponse, 404);
        }

        $successResponse = [
            "status" => true,
            "message" => "Successfully Updated"
        ];

        return response()->json($successResponse, 201);
    }

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
