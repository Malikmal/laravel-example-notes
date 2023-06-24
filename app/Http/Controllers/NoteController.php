<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNoteRequest;
use App\Http\Requests\UpdateNoteRequest;
use App\Http\Resources\NoteResource;
use App\Models\Note;
use App\Utils\ResponseJson;
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
                $query->where('title', 'LIKE', '%'.$request->search.'%')
                    ->orWhere('description', 'LIKE', '%'.$request->search.'%')
                    ;
            }))
            ->latest()
            ->paginate($request?->limit ?? 10)
            ->appends($request->query())
            ;

        return ResponseJson::success(
            data: NoteResource::collection($notes)
        );
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

        return ResponseJson::success(
            data: new NoteResource($note),
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Note $note)
    {
        return ResponseJson::success(
            data: new NoteResource($note),
        );
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

        // udpate note variable to get newest
        $note->refresh();

        return ResponseJson::success(
            data: new NoteResource($note),
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Note $note)
    {
        $note->delete();

        return ResponseJson::success(
            data: new NoteResource($note),
        );
    }
}
