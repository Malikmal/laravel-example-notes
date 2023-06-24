<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNoteRequest;
use App\Http\Requests\UpdateNoteRequest;
use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class NoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $notes = Note::query()
            ->when($request->search, fn($q) => $q->where(function($query) use ($request) {
                $request->where('title', 'LIKE', '%'.$request->search.'%')
                    ->orWhere('description', 'LIKE', '%'.$request->search.'%')
                    ;
            }))
            ->latest()
            ->paginate($request?->limit ?? 10)
            ->appends($request->query())
            ;
        return $notes;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreNoteRequest $request)
    {
        $note = Note::create([
            ...Arr::only(
                array: $request->validated(),
                keys: (new Note)->getFillable(),
            )
        ]);

        return $note;
    }

    /**
     * Display the specified resource.
     */
    public function show(Note $note)
    {
        return $note;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateNoteRequest $request, Note $note)
    {
        $note->update([
            ...Arr::only(
                array: $request->validated(),
                keys: (new Note)->getFillable(),
            )
        ]);

        return $note->refresh();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Note $note)
    {
        $note->delete();

        return $note;
    }
}
