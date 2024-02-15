<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketControllerTest extends TestCase
{
    
    use RefreshDatabase;

    public function testCreateTicket()
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $ticketData = [
            'name' => 'Novo Ticket',
            'client' => 'Cliente Teste',
            'occupation_area' => 'Área de Teste',
        ];

        $response = $this->actingAs($user)->postJson('/api/postTicket', $ticketData);

        $response->assertStatus(201);
        $response->assertJson([
            'success' => true,
            'data' => [
                'name' => $ticketData['name'],
                'client' => $ticketData['client'],
                'occupation_area' => $ticketData['occupation_area'],
            ],
        ]);
        $this->assertDatabaseHas('tickets', $ticketData);
    }

    public function testUpdateTicket()
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $ticket = Ticket::factory()->create();

        $updatedTicketData = [
            'id' => $ticket->id,
            'name' => 'Ticket Atualizado',
            'client' => 'Novo Cliente',
            'occupation_area' => 'Nova Área de Teste',
        ];
        $response = $this->actingAs($user)->putJson('/api/putTicket', $updatedTicketData);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => [
                'id' => $updatedTicketData['id'],
                'name' => $updatedTicketData['name'],
                'client' => $updatedTicketData['client'],
                'occupation_area' => $updatedTicketData['occupation_area'],
            ],
        ]);
        $this->assertDatabaseHas('tickets', $updatedTicketData);
    }

    public function testFindAllTickets()
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $tickets = Ticket::factory(3)->create();
        $response = $this->actingAs($user)->getJson('/api/getTickets');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                '*' => ['id', 'name', 'client', 'occupation_area', 'created_at', 'updated_at'],
            ],
        ]);
    }

    public function testFindTicketById()
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $ticket = Ticket::factory()->create();

        $response = $this->actingAs($user)->get('/api/getTicket?id=' . $ticket->id);
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => [
                'id' => $ticket->id,
                'name' => $ticket->name,
                'client' => $ticket->client,
                'occupation_area' => $ticket->occupation_area,
            ],
        ]);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'id', 'name', 'client', 'occupation_area', 'created_at', 'updated_at',
            ],
        ]);
    }

    public function testFindTicketByInvalidId()
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAs($user)->get('/api/getTicket?id=999');;
        $response->assertStatus(404);
        $response->assertJson(['message' => 'Ticket not found']);
    }

    public function testDeleteTicketById()
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $ticket = Ticket::factory()->create();

        $response = $this->actingAs($user)->deleteJson('/api/deleteTicket', ['id' => $ticket->id]);
        $response->assertStatus(200);
        $this->assertSoftDeleted('tickets', ['id' => $ticket->id]);
    }

    public function testDeleteTicketByInvalidId()
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAs($user)->deleteJson('/api/deleteTicket', ['id' => 999]);
        $response->assertStatus(404);
        $response->assertJson(['message' => 'Ticket not found']);
    }

    public function testRestoreTicketById()
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $ticket = Ticket::factory()->create();
        $id = $ticket->id;
        $ticket->delete();

        $response = $this->actingAs($user)->post('/api/restoreTicket', ['id' => $id]);

        $response->assertStatus(200);
        $restoredTicket = Ticket::withTrashed()->find($ticket->id);
        $this->assertNotNull($restoredTicket);
        $this->assertNull($restoredTicket->deleted_at);
    }
}
