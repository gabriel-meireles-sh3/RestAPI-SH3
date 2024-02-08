<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use App\Models\Service;

class ServiceController extends Controller
{

    /**
     * @OA\Post(
     *     path="/api/postService",
     *     summary="Create a new service",
     *     tags={"Service"},
     *     security={{"bearer_token":{}}},
     *     description="Create a new service with the specified details.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"requester_name","client_id","service_area","support_id"},
     *             @OA\Property(property="requester_name", type="string", example="John Doe", description="Name of the requester"),
     *             @OA\Property(property="client_id", type="integer", example=1, description="ID of the client or ticket associated with the service"),
     *             @OA\Property(property="service_area", type="string", example="Support", description="Service area description"),
     *             @OA\Property(property="support_id", type="integer", example=2, description="ID of the support user or analyst assigned to the service"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Service created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="requester_name", type="string", description="Name of the requester"),
     *             @OA\Property(property="client_id", type="integer", description="ID of the client or ticket associated with the service"),
     *             @OA\Property(property="service_area", type="string", description="Service area description"),
     *             @OA\Property(property="support_id", type="integer", description="ID of the support user or analyst assigned to the service"),
     *             @OA\Property(property="created_at", type="string", format="date-time", description="Timestamp of when the service was created"),
     *             @OA\Property(property="updated_at", type="string", format="date-time", description="Timestamp of when the service was last updated"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error or missing required fields",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", description="Error message")
     *         )
     *     ),
     * )
     */

    public function create(Request $request)
    { // create service
        $request->validate([
            'requester_name' => 'required',
            'client_id' => 'required',
            'service_area' => 'required',
            'support_id' => 'nullable',
        ]);

        $service = Service::create([
            'requester_name' => $request->input('requester_name'),
            'client_id' => $request->input('client_id'),
            'service_area' => $request->input('service_area'),
            'support_id' => $request->input('support_id'),
        ]);

        return $service;
    }

    /**
     * @OA\Put(
     *     path="/api/putService",
     *     summary="Update an existing service",
     *     tags={"Service"},
     *     security={{"bearer_token":{}}},
     *     description="Update an existing service with the specified details.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"service_id","requester_name","client_id","service_area","support_id"},
     *             @OA\Property(property="service_id", type="integer", example=1, description="ID of the service to be updated"),
     *             @OA\Property(property="requester_name", type="string", example="John Doe", description="Updated name of the requester"),
     *             @OA\Property(property="client_id", type="integer", example=1, description="Updated ID of the client or ticket associated with the service"),
     *             @OA\Property(property="service_area", type="string", example="Support", description="Updated service area description"),
     *             @OA\Property(property="support_id", type="integer", example=2, description="Updated ID of the support user or analyst assigned to the service"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Service updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="requester_name", type="string", description="Updated name of the requester"),
     *             @OA\Property(property="client_id", type="integer", description="Updated ID of the client or ticket associated with the service"),
     *             @OA\Property(property="service_area", type="string", description="Updated service area description"),
     *             @OA\Property(property="support_id", type="integer", description="Updated ID of the support user or analyst assigned to the service"),
     *             @OA\Property(property="updated_at", type="string", format="date-time", description="Timestamp of when the service was last updated"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Service not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", description="Error message")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error or missing required fields",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", description="Error message")
     *         )
     *     ),
     * )
     */

    public function update(Request $request)
    {
        $request->validate([
            'service_id' => 'required',
            'requester_name' => 'required',
            'client_id' => 'required',
            'service_area' => 'required',
            'support_id' => 'nullable',
        ]);

        $service = Service::find($request->input('service_id'));

        if ($service && !$service->deleted_at !== NULL) {
            $service->requester_name = $request->input('requester_name');
            $service->client_id = $request->input('client_id');
            $service->service_area = $request->input('service_area');
            $service->support_id = $request->input('support_id');

            $service->save();
            return $service;
        }

        return response()->json(['message' => 'Service not found'], 404);
    }

    /**
     * @OA\Get(
     *     path="/api/getServices",
     *     summary="Get all services",
     *     tags={"Service"},
     *     security={{"bearer_token":{}}},
     *     description="Retrieve a list of all services.",
     *     @OA\Response(
     *         response=200,
     *         description="List of services",
     *         @OA\JsonContent(
     *             @OA\Property(property="requester_name", type="string", description="Updated name of the requester"),
     *             @OA\Property(property="client_id", type="integer", description="Updated ID of the client or ticket associated with the service"),
     *             @OA\Property(property="service_area", type="string", description="Updated service area description"),
     *             @OA\Property(property="support_id", type="integer", description="Updated ID of the support user or analyst assigned to the service"),
     *             @OA\Property(property="updated_at", type="string", format="date-time", description="Timestamp of when the service was last updated"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Services not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", description="Error message")
     *         )
     *     ),
     * )
     */

    public function findAll()
    {
        $services = Service::all();

        if ($services) {
            return $services;
        }

        return response()->json(['message' => 'Services not found'], 404);
    }

    /**
     * @OA\Get(
     *     path="/api/getService",
     *     summary="Get service by ID",
     *     tags={"Service"},
     *     security={{"bearer_token":{}}},
     *     description="Retrieve a service by its ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID of the service to retrieve."
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Service found",
     *         @OA\JsonContent(
     * @OA\Property(property="requester_name", type="string", description="Updated name of the requester"),
     *             @OA\Property(property="client_id", type="integer", description="Updated ID of the client or ticket associated with the service"),
     *             @OA\Property(property="service_area", type="string", description="Updated service area description"),
     *             @OA\Property(property="support_id", type="integer", description="Updated ID of the support user or analyst assigned to the service"),
     *             @OA\Property(property="updated_at", type="string", format="date-time", description="Timestamp of when the service was last updated"),
     * ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Service not found",
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

        $service = Service::find($request->input('id'));

        return $service;
    }

    /**
     * @OA\Delete(
     *     path="/api/deleteService",
     *     summary="Delete service by ID",
     *     tags={"Service"},
     *     security={{"bearer_token":{}}},
     *     description="Delete a service by its ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID of the service to delete."
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Service deleted successfully",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Service not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", description="Error message")
     *         )
     *     ),
     * )
     */


    public function deleteById(Request $request)
    {
        $request->validate([
            'id' => 'required'
        ]);

        $service = Service::find($request->input('id'));
        if ($service) {
            $service->delete();
            return response()->json(['message' => 'Service deleted'], 200);
        }

        return response()->json(['message' => 'Service not found'], 404);
    }

    /**
     * @OA\Post(
     *     path="/restoreById",
     *     summary="Restaurar um serviço excluído",
     *     description="Restaura um serviço excluído com base no ID.",
     *     tags={"Service"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="ID do serviço a ser restaurado",
     *         @OA\JsonContent(
     *             required={"id"},
     *             @OA\Property(property="id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Sucesso ao restaurar o serviço",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Service restored successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Serviço não encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Service not found")
     *         )
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function restoreById(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:services,id',
        ]);

        $service = Service::withTrashed()->find($request->id);

        if ($service) {
            $service->restore();

            return response()->json(['message' => 'Service restored successfully'], 200);
        }

        return response()->json(['message' => 'Service not found'], 404);
    }


    /**
     * @OA\Get(
     *     path="/api/getServiceSupport",
     *     summary="Find services by support ID",
     *     tags={"Service"},
     *     security={{"bearer_token":{}}},
     *     description="Find services associated with a specific support user by their support ID.",
     *     @OA\Parameter(
     *         name="support_id",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID of the support user."
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Services found successfully",
     *         @OA\JsonContent(
     *              @OA\Property(property="requester_name", type="string", description="Updated name of the requester"),
     *             @OA\Property(property="client_id", type="integer", description="Updated ID of the client or ticket associated with the service"),
     *             @OA\Property(property="service_area", type="string", description="Updated service area description"),
     *             @OA\Property(property="support_id", type="integer", description="Updated ID of the support user or analyst assigned to the service"),
     *             @OA\Property(property="updated_at", type="string", format="date-time", description="Timestamp of when the service was last updated"),
     *          ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No services found for the specified support ID",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", description="Error message")
     *         )
     *     ),
     * )
     */

    public function findBySupportId(Request $request)
    {
        $request->validate([
            'support_id' => 'required'
        ]);

        $service = Service::where('support_id', $request->input('support_id'))->get();

        return $service;
    }

    /**
     * @OA\Get(
     *     path="/api/getServiceTicket",
     *     summary="Find services by ticket ID",
     *     tags={"Service"},
     *     security={{"bearer_token":{}}},
     *     description="Find services associated with a specific ticket by its ID.",
     *     @OA\Parameter(
     *         name="ticket_id",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID of the ticket."
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Services found successfully",
     *         @OA\JsonContent(
     *              @OA\Property(property="requester_name", type="string", description="Updated name of the requester"),
     *             @OA\Property(property="client_id", type="integer", description="Updated ID of the client or ticket associated with the service"),
     *             @OA\Property(property="service_area", type="string", description="Updated service area description"),
     *             @OA\Property(property="support_id", type="integer", description="Updated ID of the support user or analyst assigned to the service"),
     *             @OA\Property(property="updated_at", type="string", format="date-time", description="Timestamp of when the service was last updated"),
     *          )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No services found for the specified ticket ID",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", description="Error message")
     *         )
     *     ),
     * )
     */

    public function findByTicketId(Request $request)
    {
        $request->validate([
            'ticket_id' => 'required'
        ]);

        $service = Service::where('client_id', $request->input('ticket_id'))->get();

        return $service;
    }

    /**
     * @OA\Put(
     *     path="/api/putAssociateService",
     *     summary="Associate a service with a support analyst",
     *     tags={"Service"},
     *     security={{"bearer_token":{}}},
     *     description="Associate a service with a support analyst by setting the support_id. Only analysts with the matching service area can associate with the service.",
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID of the service."
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Service associated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="requester_name", type="string", description="Updated name of the requester"),
     *             @OA\Property(property="client_id", type="integer", description="Updated ID of the client or ticket associated with the service"),
     *             @OA\Property(property="service_area", type="string", description="Updated service area description"),
     *             @OA\Property(property="support_id", type="integer", description="Updated ID of the support user or analyst assigned to the service"),
     *             @OA\Property(property="updated_at", type="string", format="date-time", description="Timestamp of when the service was last updated"),)
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Service already has an analyst or service area mismatch",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", description="Error message")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Service not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", description="Error message")
     *         )
     *     ),
     * )
     */

    public function associateService(Request $request)
    {
        $request->validate([
            'id' => 'required'
        ]);

        $service = Service::find($request->input('id'));
        $support_area = $request->user()->service_areas->pluck('service_area');

        if ($service->support_id === NULL && $support_area->contains($service->service_area)) {
            $service->support_id = $request->user()->id;
            $service->save();

            return $service;
        }

        return response()->json([
            'message' => 'There is already an analyst responding to this service or the service area dont match'
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/getServicesArea",
     *     summary="Get unique service areas from all services",
     *     tags={"Service"},
     *     security={{"bearer_token":{}}},
     *     description="Retrieve a list of unique service areas from all services.",
     *     @OA\Response(
     *         response=200,
     *         description="List of unique service areas",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Service areas not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", description="Error message")
     *         )
     *     ),
     * )
     */

    public function servicesArea(Request $request)
    {
        $services_areas = Service::select('service_area')->get();

        if ($services_areas) {
            return $services_areas;
        }
        return response()->json(['message' => 'Services areas not found'], 404);
    }

    /**
     * @OA\Get(
     *     path="/api/getServicesTypes",
     *     summary="Get unique service types from all services",
     *     tags={"Service"},
     *     security={{"bearer_token":{}}},
     *     description="Retrieve a list of unique service types from all services.",
     *     @OA\Response(
     *         response=200,
     *         description="List of unique service types",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Service types not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", description="Error message")
     *         )
     *     ),
     * )
     */


    public function servicesTypes(Request $request)
    {
        $services_type = Service::select('service')->get();

        if ($services_type && !empty($services_type)) {
            return $services_type;
        }
        return response()->json(['message' => 'Services types not found'], 404);
    }

    /**
     * @OA\Get(
     *     path="/api/getUnassociateServices",
     *     summary="Get unassociated services",
     *     tags={"Service"},
     *     security={{"bearer_token":{}}}, 
     *     description="Retrieve a list of services with no assigned support",
     *     @OA\Response(
     *         response=200,
     *         description="List of unassociated services",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     * @OA\Property(property="requester_name", type="string", description="Updated name of the requester"),
     *             @OA\Property(property="client_id", type="integer", description="Updated ID of the client or ticket associated with the service"),
     *             @OA\Property(property="service_area", type="string", description="Updated service area description"),
     *             @OA\Property(property="support_id", type="integer", description="Updated ID of the support user or analyst assigned to the service"),
     *             @OA\Property(property="updated_at", type="string", format="date-time", description="Timestamp of when the service was last updated"),
     * )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Unassociated services not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", description="Error message")
     *         )
     *     ),
     * )
     */

    public function unassociateServices(Request $request)
    {
        $service = Service::where('support_id', NULL)->get();

        if ($service && count($service) > 0) {
            return $service;
        }

        return response()->json(['message' => 'Services not found'], 404);
    }

    /**
     * @OA\put(
     *     path="/api/putcompleteService",
     *     summary="Complete a service",
     *     tags={"Service"},
     *     security={{"bearer_token":{}}},
     *     description="Mark a service as completed by updating its status and service details",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"id", "status", "service"},
     *             @OA\Property(property="id", type="integer", description="Service ID"),
     *             @OA\Property(property="status", type="boolean", description="Service status (completed or not)"),
     *             @OA\Property(property="service", type="string", description="Service details or completion notes")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Service updated successfully",
     *         @OA\JsonContent(
     * @OA\Property(property="requester_name", type="string", description="Updated name of the requester"),
     *             @OA\Property(property="client_id", type="integer", description="Updated ID of the client or ticket associated with the service"),
     *             @OA\Property(property="service_area", type="string", description="Updated service area description"),
     *             @OA\Property(property="support_id", type="integer", description="Updated ID of the support user or analyst assigned to the service"),
     *             @OA\Property(property="updated_at", type="string", format="date-time", description="Timestamp of when the service was last updated"),
     * )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Service not found or not belonging to the user",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", description="Error message")
     *         )
     *     ),
     * )
     */

    public function completeService(Request $request)
    {
        $request->validate([
            'status' => 'required',
            'service' => 'required',
        ]);

        $service = Service::find($request->input('id'));

        if ($service && $service->support_id === $request->user()->id) {
            $service->status = $request->input('status');
            $service->service = $request->input('service');

            $service->save();
            return $service;
        }

        return response()->json(['message' => 'Service not found or not belonging to the user'], 404);
    }

    /**
     * @OA\Get(
     *     path="/api/getIncompleteServices",
     *     summary="Get incomplete services",
     *     tags={"Service"},
     *     security={{"bearer_token":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="requester_name", type="string", description="Updated name of the requester"),
     *             @OA\Property(property="client_id", type="integer", description="Updated ID of the client or ticket associated with the service"),
     *             @OA\Property(property="service_area", type="string", description="Updated service area description"),
     *             @OA\Property(property="support_id", type="integer", description="Updated ID of the support user or analyst assigned to the service"),
     *             @OA\Property(property="updated_at", type="string", format="date-time", description="Timestamp of when the service was last updated"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No incomplete services found",
     *         @OA\JsonContent(type="object", @OA\Property(property="message", type="string"))
     *     ),
     * )
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function incompleteServices(Request $request)
    {
        $services = Service::where('status', false)->get();

        if ($services && count($services) > 0) {
            return $services;
        }

        return response()->json([
            'message' => 'No incomplete Services found'
        ], 404);
    }
}
