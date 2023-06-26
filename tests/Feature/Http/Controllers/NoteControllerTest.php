<?php

namespace Tests\Feature\Http\Controllers;

use App\Http\Resources\NoteResource;
use App\Models\Note;
use App\Utils\ResponseJson;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class NoteControllerTest extends TestCase
{
    public function test_user_can_get_all_notes()
    {
        Note::factory(2)->create();
        $notes = Note::paginate(2);
        $notesResource = NoteResource::collection($notes);

        $response = $this->json(
            method: 'GET',
            uri: '/api/note'
        );

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => $notesResource['data']->response()->getData(true)
           ]);
    }

    public function test_user_can_get_detail_note()
    {
        $note = Note::factory()->create();
        $noteResource = new NoteResource($note);

        $response = $this->json(
            method: 'GET',
            uri: '/api/note/'.$note->id,
        );

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => $noteResource->response()->getData(true)['data']
           ])
           ;
    }

    public function test_user_can_create_note()
    {
        $noteBody = Note::factory()->make();

        $response = $this->json(
            method: 'POST',
            uri: '/api/note',
            data: $noteBody->toArray(),
        );

        $note = Note::latest()->first();
        $noteResource = new NoteResource($note);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => $noteResource->response()->getData(true)['data']
           ])
           ;
    }

    public function test_user_can_update_note()
    {
        $note = Note::factory()->create();
        $noteBody = Note::factory()->make();

        $response = $this->json(
            method: 'PUT',
            uri: '/api/note/'.$note->id,
            data: $noteBody->toArray(),
        );

        $note->refresh();
        $noteResource = new NoteResource($note);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => $noteResource->response()->getData(true)['data']
           ])
           ;
    }

    public function test_user_can_delete_note()
    {
        $note = Note::factory()->create();
        $noteResource = new NoteResource($note);

        $response = $this->json(
            method: 'DELETE',
            uri: '/api/note/'.$note->id,
        );

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => $noteResource->response()->getData(true)['data']
           ])
           ;
    }
}
