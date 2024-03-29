<?php

namespace App\Http\Controllers;

use App\Models\Booking;
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
        // return $request;
        $request->validate([
            'data*.title',
            'data*.latitude',
            'data*.longitude',
            'data*.address',
            'data*.price',
            'data*.available',
            'image' => 'required',
            'data*.desc',
            'data*.amenities' => 'array',
            'data*.amenities*.' => 'string',
            'data*.conditions' => 'nullable|json',
        ]);


        $user = Auth::user();
        // return $user;

        if ($request->hasFile('image')) {
            $file_room = $request->file('image');
            $filename_room = uniqid() . '.' . $file_room->extension();
            $file_room->storeAs('public/images/rooms', $filename_room);
        } else {
            return response()->json("Please add the image before submitting.");
        }

        $data = json_decode($request->data);

        $roomDetails = roomDetails::create([
            'title' => $data->title,
            'user_id' => $user->id,
            'latitude' => $data->latitude,
            'longitude' => $data->longitude,
            'address' => $data->address,
            'price' => $data->price,
            'available' => $data->available,
            'image' =>  env('APP_URL') . Storage::url('public/images/rooms/' . $filename_room),
            'desc' => $data->desc,
            'amenities' => $data->amenities,
        ]);

        $response = [
            "status"  => 200,
            "room_details" => $roomDetails
        ];

        return response()->json($response, 200);
    }

    public function AddedRoom(roomDetails $roomDetails)
    {
        $user_id = Auth::id();
        // return $user_id;
        $roomDetails = roomDetails::getByUserId($user_id);
        if ($roomDetails->count() === 0) {
            return response()->json("No room added by the user.");
        }
        return response()->json($roomDetails);
    }

    public function ShareRoom(Request $request, $id)
    {
        // return $request->conditions;
        $request->validate([
            'conditions' => 'array',
            'conditions*.' => 'required|string',
        ]);
        $user = Auth::user();
        $booking = Booking::find($id);

        if ($user->id !== $booking->user_id) {
            return response('Unauthorized action.', 401);
        }

        // return $booking->room_details_id;

        $details = roomDetails::find($booking->room_details_id);

        $details->isShared = true;
        $details->conditions = $request->conditions;
        $details->available = true;
        $details->save();

        $SucessMessage = [
            "details" => $details,
            "message" => "Room shared sucessfully!"
        ];

        return response()->json($SucessMessage);
    }

    public function ShareList()
    {
        $sharedRooms = RoomDetails::where('isShared', true)->get();

        return response()->json($sharedRooms);
    }

    public function mySharedRooms()
    {
        $sharedRooms = RoomDetails::where('isShared', true)
            ->join('users', 'users.id', '=', 'room_details.user_id')
            ->select('room_details.*', 'users.name', 'users.email')
            ->get();

        return response()->json($sharedRooms);
    }

    public function RemoveSharedRoom($roomId)
    {

        $room = RoomDetails::find($roomId);

        $user_id = Auth::user();

        if ($room->user_id != $user_id) {
            return "You are not authorized to remove this room";
        }

        $room->isShared = false;
        $room->isAvailable = false;
        $room->save();

        $response = [
            "message" => "Room share removed succesfully",
            "status" => 200
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

        if ($request->has('address')) {
            $query->where('address', 'like', '%' . $request->input('address') . '%');
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
            'desc',
            'image'
        ]);

        if ($request->hasFile('image')) {
            $file_room = $request->file('image');
            $filename_room = uniqid() . '.' . $file_room->extension();
            $file_room->storeAs('public/images/rooms', $filename_room);
        }


        $details->title = $request->title ? $request->title : $details->title;
        $details->price = $request->input('price', $details->price);
        $details->desc = $request->input('desc', $details->desc);
        $details->image = $request->image ? $request->image : env('APP_URL') . Storage::url('public/images/rooms/' . $filename_room);


        $details->save();

        $successResponse = [
            "status" => true,
            "message" => "Successfully updated the room."
        ];

        return response()->json($successResponse, 200);
    }

    public function RoomCount()
    {
        $roomsCounted = roomDetails::count();
        return $roomsCounted;
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
