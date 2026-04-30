<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Note;
use Illuminate\Support\Facades\Auth;

class NoteController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'collection_id' => 'required',
            'content' => 'nullable|string',
        ]);

        $note = Note::create([
            'user_id' => Auth::id(),
            'collection_id' => $request->collection_id,
            'title' => $request->title ?? 'New Note',
            'content' => $request->content,
            'color' => $request->color ?? '#6366f1',
        ]);

        return response()->json($note);
    }

    public function update(Request $request, $id)
    {
        $note = Note::where('user_id', Auth::id())->findOrFail($id);
        $note->update($request->only(['title', 'content', 'color']));

        return response()->json($note);
    }

    public function destroy($id)
    {
        $note = Note::where('user_id', Auth::id())->findOrFail($id);
        $note->delete();

        return response()->json(['success' => true]);
    }
}
