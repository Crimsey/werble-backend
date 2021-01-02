<?php

namespace App\Http\Controllers\Api\UserActions;

use App\Http\Controllers\Controller;
use App\Http\Resources\EventParticipantResource;
use App\Http\Resources\EventParticipantResourceCollection;
use App\Http\Resources\EventResource;
use App\Http\Resources\ProfileResource;
use App\Models\Event;
use App\Models\EventParticipant;

use App\Models\EventReview;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventParticipantController extends Controller
{
    public function joinEvent(Request $request, $eventId)
    {
        $user = $request->user();

        $event = Event::where('event_id', $eventId)->first();

        if (!$event)
            return response()->json(['message' => 'Event not found'],404);

        $eventParticipant = new EventParticipant;
        $eventParticipant->event_id = $eventId;
        $eventParticipant->user_id = $user->user_id;
        $eventParticipant->is_creator = ($event->event_creator_id === $user->user_id);
        $eventParticipant->participant_status_id = $request->participant_status_id;

        $eventParticipant->save();
        return response()->json(['message' => 'Successfully joined event'],200);
    }

    public function changeParticipantStatus(Request $request)
    {
        $user = $request->user();

        $this->validate($request, ['event_id' => 'required', 'participant_status_id' => 'required']);

        $eventId = $request['event_id'];
        $newParticipantStatus = $request->participant_status_id;

        $participant = $user->participants()->where('event_id', $eventId)->first();
        $participant->participant_status_id = $newParticipantStatus;

        return (new EventParticipantResource($participant))->response()->setStatusCode(200);
    }

    public function leaveEvent()
    {

    }


    public function getUserParticipatingEvents(Request $request)
    {
        $user = $request->user();
        $events = [];
        $participants = $user->participants()->get();
        foreach ($participants as $participant) {
            array_push($events, $participant->event()->first());
        }

        return EventResource::collection($events);
    }

    public function getEventParticipantsProfiles(Request $request, $eventId)
    {
        //
        $event = Event::where('event_id', $eventId)->first();
        $users = [];

        $participants = $event->participants()->get();

        foreach ($participants as $participant) {
            array_push($users, $participant->user()->first());
        }

        return ProfileResource::collection($users);
    }

    public function softDeleteParticipant(Request $request, $participantId){
        $participantId =  EventReview::findOrFail($participantId);
        $participantId->delete();
        return response()->json(['message' => 'You have deactivated participant!'],200);
    }


}
