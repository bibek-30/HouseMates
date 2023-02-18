<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\roomDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class BookingController extends Controller
{

    public function index()
    {
        $booking = Booking::all();
        return response()->json($booking, 200);
    }


    public function create(Request $request)
    {
        // Validate the user input
        $validated = $request->validate([
            'room_name' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        // Get the user ID of the currently authenticated user
        $user_id = Auth::id();

        // Get the room ID based on the room name
        $room_name = $request->room_name;
        $room = roomDetails::where('title', $room_name)->firstOrFail();
        $room_id = $room->id;
        $price = $room->price;

        // return $price;



        // Calculate the total rent amount
        $start_date = Carbon::parse($request->start_date);
        $end_date = Carbon::parse($request->end_date);
        $rent_duration = $end_date->diffInMonths($start_date);
        $discountFactor = 1.0;
        if ($rent_duration >= 3) {
            $discountFactor = 0.9; // 10% discount for 3-month booking
        }
        $rent_amount = $price * $rent_duration * $discountFactor;


        $existing_bookings = Booking::where('room_details_id', $room_id)
            ->where(function ($query) use ($start_date, $end_date) {
                $query->whereBetween('start_date', [$start_date, $end_date])
                    ->orWhereBetween('end_date', [$start_date, $end_date])
                    ->orWhere(function ($query) use ($start_date, $end_date) {
                        $query->where('start_date', '<=', $start_date)
                            ->where('end_date', '>=', $end_date);
                    });
            })
            ->get();
        if ($existing_bookings->count() > 0) {
            $response = [
                "message" => "This room is already booked for the requested dates.",
                "status" => 400
            ];
            return response()->json($response, 400);
        }


        // Create a new booking record
        $booking = new Booking;
        $booking->room_name = $request->room_name;
        $booking->user_id = $user_id;
        $booking->room_details_id = $room_id; // Set the room ID
        $booking->start_date = $request->start_date;
        $booking->end_date = $request->end_date;
        $booking->rent_amount = $rent_amount;
        $booking->save();

        $response = [
            "message" => "Booking Placed succesfully",
            "details" => $booking,
            "status" => 200
        ];
        return response()->json($response, 201);
    }

    public function show(Booking $booking)
    {
        $user_id = Auth::id();
        $bookings = Booking::getByUserId($user_id);
        if ($bookings->count() === 0) {
            return response()->json("No booking made", 400);
        }
        return response()->json($bookings, 200);
    }

    //not working
    public function edit(Request $request, $id)
    {
        $validated = $request->validate([
            'room_name' => 'string',
            'start_date' => 'date',
            'end_date' => 'date|after:start_date',
        ]);

        // Get the booking record to be updated
        $booking = Booking::findOrFail($id);

        // Check if the booking is owned by the current user
        if ($booking->user_id != Auth::id()) {
            $response = [
                "message" => "You are not authorized to update this booking.",
                "status" => 403
            ];

            return response()->json($response, 403);
        }
        $room_name = $request->room_name;
        $room = roomDetails::where('title', $room_name)->firstOrFail();
        $price = $room->price;

        // Calculate the total rent amount
        $start_date = Carbon::parse($request->start_date);
        $end_date = Carbon::parse($request->end_date);
        $rent_duration = $end_date->diffInMonths($start_date);
        $discountFactor = 1.0;
        if ($rent_duration >= 3) {
            $discountFactor = 0.9; // 10% discount for 3-month booking
        }
        $rent_amount = $price * $rent_duration * $discountFactor;

        // Update the booking record
        $booking->$start_date = $request->start_date ? $request->start_date : $booking->start_date;
        $booking->$end_date = $request->end_date ? $request->end_date : $booking->end_date;
        $booking->rent_amount = $rent_amount;
        $booking->update();

        $response = [
            "message" => "Booking details updated successfully.",
            "details" => $booking,
            "status" => 200
        ];
        return response()->json($response, 200);
    }

    public function destroy($id)
    {
        $data = Booking::find($id);
        if (!$data) {
            return Response()->json("Invalid data", 404);
        }
        $data->delete();
        $successResponse = ["message" => "Booking deleted successfully"];
        return response()->json($successResponse, 200);
    }
}
