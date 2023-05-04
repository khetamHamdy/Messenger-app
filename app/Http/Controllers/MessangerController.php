<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessangerController extends Controller {
    public function index( $conversation_id = null ) {

        $user = Auth::user();

        $friends = User::where( 'id', '<>', $user->id )
        ->orderBy( 'name' )
        ->paginate();

        // // return many conversations is auth user
        // $conversations = $user->conversations()->with( [
        //     'participants' => function ( $builder ) use ( $user ) {
        //         $builder->where( 'id', '<>', $user->id );
        //     }
        //     , 'lastMessage' ] )->get();
        //     // dd( $chat );
        //     $messages = [];
        //     $actvie_chat =new Conversation();
        //     if ( $conversation_id ) {

        //         //laravel allow where in query bulider
        //         // return one conversation
        //         $actvie_chat = $conversations->where( 'id', $conversation_id )->first();
        //         $messages = $actvie_chat->messages()->with( 'user' )->paginate();
        //     }

            return view( 'messenger', [
                'friends' => $friends,
                // 'chats' =>$conversations,
                // 'actvie_chat'=>$actvie_chat,
                // 'messages' => $messages,
            ] );
        }
    }
