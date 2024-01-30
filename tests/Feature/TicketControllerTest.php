<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TicketControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     */
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
            'name' => $ticketData['name'],
            'client' => $ticketData['client'],
            'occupation_area' => $ticketData['occupation_area'],
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
            'id' => $updatedTicketData['id'],
            'name' => $updatedTicketData['name'],
            'client' => $updatedTicketData['client'],
            'occupation_area' => $updatedTicketData['occupation_area'],
        ]);
        $this->assertDatabaseHas('tickets', $updatedTicketData);
    }
}
