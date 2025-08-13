<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use Illuminate\Http\Request;

class ChatMessagesController extends Controller
{
    public function getMessages(Request $request, $sessionId)
    {
        return ChatMessage::all()->where('task_session_id', $sessionId);
    }

    public function postMessage(Request $request, $sessionId)
    {
        return true;
    }
}
