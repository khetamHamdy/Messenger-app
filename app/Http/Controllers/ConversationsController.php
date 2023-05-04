<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Recipient;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConversationsController extends Controller
{
    //الهدف منه ترجع محادثات الا تابعه لمستخدم محدد
    public function index()
    {
        $user = Auth::user();
        return $user->conversations()->with( [
            'participants' => function ( $builder ) use ( $user ) {
                $builder->where( 'id', '<>', $user->id );
            }
            , 'lastMessage' ] )->paginate();
    }

    public function show(Conversation $conversation)
    {
        //participants المشاركين
        return $conversation->load('participants');
    }

    public function addParticipants(Request $request, Conversation $conversation)
    {
        $request->validate(
            [
                'user_id' => ['required', 'int', 'exists:users,id']
            ]);
        $conversation->participants()->attach(
            $request->post('user_id', ['joined_at' => Carbon::now()])
        );
    }

    public function removeParticipants(Request $request, Conversation $conversation)
    {
        $request->validate(
            [
                'user_id' => ['required', 'int', 'exists:users,id']
            ]);
        $conversation->participants()->detach($request->post('user_id'));
    }

    public function markAsRead($id)
    {
        Recipient::where('user_id', '=', Auth::id())
            ->whereNull('read_at')
            ->whereRaw('message_id IN (
                SELECT id FROM messages WHERE conversation_id = ?
            )', [$id])
            ->update([
                'read_at' => Carbon::now(),
            ]);

        return [
            'message' => 'Messages marked as read',
        ];
    }
}
