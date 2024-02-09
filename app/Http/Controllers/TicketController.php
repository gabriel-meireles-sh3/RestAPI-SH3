<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


use App\Models\Ticket;
use Illuminate\Support\Facades\Validator as Validator;


class TicketController extends Controller
{

    /**
     * @OA\Post(
     *     path="/api/postTicket",
     *     summary="Create a new ticket",
     *     tags={"Ticket"},
     *     security={{"bearer_token":{}}},
     *     description="Create a new ticket with the provided details",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "client", "occupation_area"},
     *             @OA\Property(property="name", type="string", description="Ticket name"),
     *             @OA\Property(property="client", type="string", description="Client name or identifier"),
     *             @OA\Property(property="occupation_area", type="string", description="Occupation area of the ticket")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ticket created successfully",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Ticket Name"),
     *                 @OA\Property(property="client", type="string", example="Client Name"),
     *                 @OA\Property(property="occupation_area", type="string", example="Occupation Area"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error or missing required fields",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", description="Error message")
     *         )
     *     ),
     * )
     */

    public function create(Request $request)
    { // create ticket
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'client' => 'required',
            'occupation_area' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation error', 'errors' => $validator->errors()], 422);
        }

        $ticket = Ticket::create([
            'name' => $request->input('name'),
            'client' => $request->input('client'),
            'occupation_area' => $request->input('occupation_area'),
        ]);

        return $ticket;
    }

    /**
     * @OA\Put(
     *     path="/api/putTicket",
     *     summary="Update a ticket",
     *     tags={"Ticket"},
     *     security={{"bearer_token":{}}},
     *     description="Update the details of an existing ticket",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"id", "name", "client", "occupation_area"},
     *             @OA\Property(property="id", type="integer", description="ID of the ticket to be updated"),
     *             @OA\Property(property="name", type="string", description="Updated ticket name"),
     *             @OA\Property(property="client", type="string", description="Updated client name or identifier"),
     *             @OA\Property(property="occupation_area", type="string", description="Updated occupation area of the ticket")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ticket updated successfully",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Ticket Name"),
     *                 @OA\Property(property="client", type="string", example="Client Name"),
     *                 @OA\Property(property="occupation_area", type="string", example="Occupation Area"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Ticket not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", description="Error message")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error or missing required fields",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", description="Error message")
     *         )
     *     ),
     * )
     */

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'name' => 'required',
            'client' => 'required',
            'occupation_area' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation error', 'errors' => $validator->errors()], 422);
        }

        $ticket = Ticket::find($request->input('id'));

        if ($ticket) {
            $ticket->name = $request->input('name');
            $ticket->client = $request->input('client');
            $ticket->occupation_area = $request->input('occupation_area');

            $ticket->save();
            return $ticket;
        }

        return response()->json(['message' => 'Ticket not found'], 404);
    }

    /**
     * @OA\Get(
     *     path="/api/getTickets",
     *     summary="Get all tickets",
     *     tags={"Ticket"},
     *     security={{"bearer_token":{}}},
     *     description="Retrieve a list of all tickets",
     *     @OA\Response(
     *         response=200,
     *         description="List of all tickets",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Ticket Name"),
     *                 @OA\Property(property="client", type="string", example="Client Name"),
     *                 @OA\Property(property="occupation_area", type="string", example="Occupation Area"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Tickets not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", description="Error message")
     *         )
     *     ),
     * )
     */

    public function findAll()
    {
        $ticket = Ticket::all();

        if ($ticket) {
            return $ticket;
        }
        return response()->json(['message' => 'Ticket not found'], 404);
    }

    /**
     * @OA\Get(
     *     path="/api/getTicket",
     *     summary="Get ticket by ID",
     *     tags={"Ticket"},
     *     security={{"bearer_token":{}}},
     *     description="Retrieve a ticket by its ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the ticket",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ticket found",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Ticket Name"),
     *             @OA\Property(property="client", type="string", example="Client Name"),
     *             @OA\Property(property="occupation_area", type="string", example="Occupation Area"),
     *             @OA\Property(property="created_at", type="string", format="date-time"),
     *             @OA\Property(property="updated_at", type="string", format="date-time"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Ticket not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", description="Error message")
     *         )
     *     ),
     * )
     */

    public function findById(Request $request)
    {
        $request->validate([
            'id' => 'required'
        ]);

        $ticket = Ticket::find($request->input('id'));

        if ($ticket) {
            return $ticket;
        }
        return response()->json(['message' => 'Ticket not found'], 404);
    }

    /**
     * @OA\Delete(
     *     path="/api/deleteTicket",
     *     summary="Delete ticket by ID",
     *     tags={"Ticket"},
     *     security={{"bearer_token":{}}},
     *     description="Delete a ticket by its ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the ticket",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=302,
     *         description="Ticket deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Ticket deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Ticket not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Ticket not found")
     *         )
     *     ),
     * )
     */

    public function deleteById(Request $request)
    {
        $request->validate([
            'id' => 'required'
        ]);

        $ticket = Ticket::find($request->input('id'));
        if ($ticket) {
            $ticket->delete();
            return response()->json(['message' => 'Ticket deleted'], 200);
        }
        return response()->json(['message' => 'Ticket not found'], 404);
    }

    /**
     * @OA\Post(
     *     path="/restoreTicketById",
     *     summary="Restaurar um ticket excluído",
     *     description="Restaura um ticket excluído com base no ID.",
     *     tags={"Ticket"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="ID do ticket a ser restaurado",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Sucesso ao restaurar o ticket",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Service restored successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Ticket não encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Service not found")
     *         )
     *     ),
     *     security={{"bearerAuth": {}}}
     * )
     */

    public function restoreById(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:tickets,id',
        ]);

        $ticket = Ticket::withTrashed()->find($request->id);

        if ($ticket) {
            $ticket->restore();

            return response()->json(['message' => 'Service restored successfully'], 200);
        }

        return response()->json(['message' => 'Service not found'], 404);
    }
}
