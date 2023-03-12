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

    public function create(Request $request, $roomId)
    {
        $validatedData = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'booking_amount' => 'numeric'
        ]);
        // return $request;

        $user = Auth::user();
        // return $user;


        $room = roomDetails::find($roomId);


        if (!$room) {
            return response()->json(['error' => 'Room not found'], 404);
        }
        $price = $room->price;

        // Calculate the total rent amount
        $start_date = Carbon::parse($request->start_date);
        $end_date = Carbon::parse($request->end_date);
        $rent_duration = $end_date->diffInMonths($start_date);
        $discountFactor = 1.0;
        if ($rent_duration >= 3) {
            $discountFactor = 0.9; // 10% discount for 3-month booking
        }

        $book_amount = $request->booking_amount;

        $rent_amount = ($price * $rent_duration * $discountFactor) - $book_amount;



        $startDate = Carbon::parse($validatedData['start_date']);
        $endDate = Carbon::parse($validatedData['end_date']);
        $bookings = Booking::where('room_details_id', $room->id)
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function ($query) use ($startDate, $endDate) {
                        $query->where('start_date', '<', $startDate)
                            ->where('end_date', '>', $endDate);
                    });
            })
            ->get();

        if ($bookings->count() > 0) {
            return response()->json(['error' => 'Room not available during selected dates'], 422);
        }

        $booking = new Booking();
        $booking->room_details_id = $room->id;
        $booking->room_title = $room->title;
        $booking->location = $room->city . ' ' . $room->state . ' ' . $room->zip;
        $booking->user_id = $user->id;
        $booking->start_date = $startDate;
        $booking->end_date = $endDate;
        $booking->rent_amount = $rent_amount;
        $booking->booking_amount = $book_amount;
        $booking->save();


        return response()->json(['message' => 'Room booked successfully'], 200);
    }

    public function show(Booking $booking)
    {
        $user_id = Auth::id();
        // return $user_id;
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
