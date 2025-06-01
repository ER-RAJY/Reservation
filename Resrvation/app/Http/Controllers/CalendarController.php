<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TypeRoom;
use App\Models\Room;
use App\Models\Reservation;

class CalendarController extends Controller
{
    public function index()
    {
        $typeRooms = TypeRoom::with('rooms')->get();
        return view('calendar.timeline', compact('typeRooms'));
    }

    public function getEvents(Request $request)
    {
        $roomId = $request->input('room_id');
        
        $query = Reservation::with('room');
        
        if ($roomId) {
            $query->where('room_id', $roomId);
        }
        
        $events = $query->get()->map(function ($reservation) {
            return [
                'id' => $reservation->id,
                'title' => $reservation->client_name,
                'start' => $reservation->start_date,
                'end' => $reservation->end_date,
                'resourceId' => $reservation->room_id,
                'extendedProps' => [
                    'activity_type' => $reservation->activity_type,
                    'client_phone' => $reservation->client_phone,
                    'notes' => $reservation->notes,
                ],
                'className' => 'fc-event-' . $reservation->activity_type,
            ];
        });

        return response()->json($events);
    }

    public function checkAvailability(Request $request)
    {
        $roomId = $request->input('room_id');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        
        $conflictingReservations = Reservation::where('room_id', $roomId)
            ->where(function($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                      ->orWhereBetween('end_date', [$startDate, $endDate])
                      ->orWhere(function($query) use ($startDate, $endDate) {
                          $query->where('start_date', '<=', $startDate)
                                ->where('end_date', '>=', $endDate);
                      });
            })
            ->count();

        return response()->json(['available' => $conflictingReservations === 0]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'client_name' => 'required|string|max:255',
            'client_phone' => 'nullable|string|max:20',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'activity_type' => 'required|in:stay,conference,meeting',
            'notes' => 'nullable|string',
        ]);

        $reservation = Reservation::create($validated);

        return response()->json($reservation, 201);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        $reservation = Reservation::findOrFail($id);
        $reservation->update($validated);

        return response()->json($reservation);
    }

    public function extend(Request $request, $id)
    {
        $validated = $request->validate([
            'new_end_date' => 'required|date|after:start_date',
        ]);

        $reservation = Reservation::findOrFail($id);
        $reservation->update(['end_date' => $validated['new_end_date']]);

        return response()->json($reservation);
    }

    public function destroy($id)
    {
        $reservation = Reservation::findOrFail($id);
        $reservation->delete();

        return response()->json(null, 204);
    }
}
