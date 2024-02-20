<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
    { // Criando o Serviço
        // Validando o input da requisição
        $request->validate([
            'requester_name' => 'required',
            'client_id' => 'required',
            'service_area' => 'required',
            'support_id' => 'nullable',
        ]);

        // criando o novo serviço
        $service = Service::create([
            'requester_name' => $request->input('requester_name'),
            'client_id' => $request->input('client_id'),
            'service_area' => $request->input('service_area'),
            'support_id' => $request->input('support_id'),
        ]);

        // retornando o serviço
        return response()->json(
            [
                'success' => true,
                'data' => $service,
            ],
            201
        );
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
    { // Editando o Serviço
        // Validando o input da requisição
        $request->validate([
            'service_id' => 'required',
            'requester_name' => 'required',
            'client_id' => 'required',
            'service_area' => 'required',
            'support_id' => 'nullable',
        ]);

        // Procurando o serviço a ser odificado
        $service = Service::find($request->input('service_id'));

        // Certificando que o serviço foi achado e não está em estado de SoftDelete
        if ($service && !$service->deleted_at !== NULL) {
            $service->requester_name = $request->input('requester_name');
            $service->client_id = $request->input('client_id');
            $service->service_area = $request->input('service_area');
            $service->support_id = $request->input('support_id');

            // Após as modificações, salvando o serviço
            $service->save();
            return response()->json(
                [
                    'success' => true,
                    'data' => $service,
                ],
                200
            );
        }

        //mensagem de erro
        return response()->json(
            [
                'success' => false,
                'message' => 'Service not found'
            ],
            404
        );
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
    { // Retornar todos os serviços
        // Recuperando todos os serviços
        $services = Service::all();

        // Certificando que existem serviços
        if ($services) {
            return response()->json(
                [
                    'success' => true,
                    'data' => $services,
                ],
                200
            );
        }

        return response()->json(
            [
                'success' => false,
                'message' => 'Services not found'
            ],
            404
        );
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
    { // Recuperando o servuço pelo ID
        // Validando o input da requisição
        $request->validate([
            'id' => 'required'
        ]);

        // Recuperando o serviço solicitado
        $service = Service::find($request->input('id'));

        return response()->json(
            [
                'success' => true,
                'data' => $service,
            ],
            200
        );
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
    { // SoftDelete do serviço pelo ID
        // Validando o input da requisição
        $request->validate([
            'id' => 'required'
        ]);

        // Recuperando o serviço solicitado
        $service = Service::find($request->input('id'));

        // Certificando que o serviço existe e deletando
        if ($service) {
            $service->delete();
            return response()->json(
                [
                    'success' => true,
                    'message' => 'Service deleted'
                ],
                200
            );
        }

        // Mesangem de erro
        return response()->json(
            [
                'success' => false,
                'message' => 'Service not found'
            ],
            404
        );
    }

    /**
     * @OA\Post(
     *     path="/restoreServiceById",
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
    { // Restaurando o serviço SoftDelete pelo id
        // Validando o input da requisição
        $request->validate([
            'id' => 'required|exists:services,id',
        ]);

        // Recuperando o serviço deletado
        $service = Service::withTrashed()->find($request->id);

        // Certificando que o serviço existem e restaurando
        if ($service) {
            $service->restore();

            return response()->json(
                [
                    'success' => true,
                    'message' => 'Service restored successfully'
                ],
                200
            );
        }

        // Mensagem de erro
        return response()->json(
            [
                'success' => false,
                'message' => 'Service not found'
            ],
            404
        );
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
    { // Recuperand o serviço pelo ID de analista de suporte
        // Validando o input da requisição
        $request->validate([
            'support_id' => 'required'
        ]);

        // Recuperando os serviços onde o ID do analista de suporte seja o solicitado
        $service = Service::where('support_id', $request->input('support_id'))->get();

        // retornando a lista
        return response()->json(
            [
                'success' => true,
                'data' => $service,
            ],
            200
        );
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
    { // Recuperando os serviços pelo ID da solicitação
        // Validando o input da requisição
        $request->validate([
            'ticket_id' => 'required'
        ]);

        // Recuperando os serviços onde o ID da solicitação seja o solicitado
        $service = Service::where('client_id', $request->input('ticket_id'))->get();

        return response()->json(
            [
                'success' => true,
                'data' => $service,
            ],
            200
        );
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
    { // Associando o Serviço à um analista de suporte
        $request->validate([
            'id' => 'required'
        ]);

        // Recuperando o serviço e o usuário solicitante
        $service = Service::find($request->input('id'));
        $user = $request->user();

        // Recuperando o perfil suporte do usuário
        $supports = $user->support;

        // Função para verificar a ocorrencia de área de atuação do analista de suporte ser igual à
        // área de atendimento solicitada no serviço
        $matchingSupport = $supports->first(function ($support) use ($service) {
            return $support->service_area === $service->service_area;
        });

        // Se esse analista atender à área então ele é associado ao serviço para responde-lo
        if ($matchingSupport) {
            $service->support_id = $matchingSupport->id;
            $service->save();

            return response()->json(
                [
                    'success' => true,
                    'data' => $service,
                ],
                200
            );
        }

        // Mensagem de erro
        return response()->json(
            [
                'success' => false,
                'message' => 'There is already an analyst responding to this service or the service area does not match any support.'
            ],
            400
        );
    }

    /**
     * @OA\Get(
     *     path="/api/getServicesAreas",
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

    public function servicesArea()
    { // Recuperando todas as áreas de serviço
        $services_areas = Service::select('service_area')->get();

        // Certificando que existem
        if ($services_areas) {
            return response()->json(
                [
                    'success' => true,
                    'data' => $services_areas,
                ],
                200
            );
        }

        // Mensagem de erro
        return response()->json(
            [
                'success' => false,
                'message' => 'Services areas not found'
            ],
            404
        );
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


    public function servicesTypes()
    { // Recuperando os tipos de atencimentos realizados
        $services_type = Service::select('service')->get();

        if ($services_type && !empty($services_type)) {
            return response()->json(
                [
                    'success' => true,
                    'data' => $services_type,
                ],
                200
            );
        }

        // Mensagem de erro
        return response()->json(
            [
                'success' => false,
                'message' => 'Services types not found'
            ],
            404
        );
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

    public function unassociateServices()
    { // Recuperando os Serviços sem atendimento
        $services = Service::whereNull('support_id')->get();

        if ($services->isEmpty()) {
            // mensagem de erro
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Services not found'
                ],
                404
            );
        }

        return response()->json(
            [
                'success' => true,
                'data' => $services,
            ],
            200
        );
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
    { // Concluir o atendimento e fechar a ordem de serviço
        // Validando o input da requisição
        $request->validate([
            'status' => 'required',
            'service' => 'required',
        ]);

        // Recuperando o Serviço e o Usuário solicitante
        $service = Service::find($request->input('id'));
        $supports = $request->user()->support;

        // Verificando se esse usuário é o responsável pelo serviço
        $matchingSupport = $supports->first(function ($support) use ($service) {
            return $support->id === $service->support_id;
        });

        // Fechando o Serviço
        if ($service && $matchingSupport) {
            $service->status = $request->input('status');
            $service->service = $request->input('service');

            $service->save();
            return response()->json(
                [
                    'success' => true,
                    'data' => $service,
                ],
                200
            );
        }

        // Mensagem de erro
        return response()->json(
            [
                'success' => false,
                'message' => 'Service not found or not belonging to the user'
            ],
            404
        );
    }

    /**
     * @OA\Get(
     *     path="/api/getIncompletedServices",
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

    public function incompletedServices()
    { // Recuperando os Serviços em aberto
        $services = Service::where('status', false)->get();

        if ($services->isEmpty()) {
            // Mensagem de erro
            return response()->json([
                'success' => false,
                'message' => 'No incomplete Services found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $services,
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/getCompletedServices",
     *     summary="Get complete services",
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
     *         description="No complete services found",
     *         @OA\JsonContent(type="object", @OA\Property(property="message", type="string"))
     *     ),
     * )
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function completedServices()
    { // Recuperando os Serviços respondidos
        $services = Service::where('status', true)->get();

        if ($services && count($services) > 0) {
            return response()->json(
                [
                    'success' => true,
                    'data' => $services,
                ],
                200
            );
        }

        return response()->json(
            [
                'success' => false,
                'message' => 'No completed Services found'
            ],
            404
        );
    }
}
