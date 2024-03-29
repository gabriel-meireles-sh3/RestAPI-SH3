<?php

namespace App\Http\Controllers;

use App\Models\Support;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Models\User;

class AuthController extends Controller
{

    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="Login",
     *     description="Authenticate a user and return a JWT token",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful login",
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string", description="JWT Token")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", description="Error message")
     *         )
     *     ),
     * )
     */

    public function signIn(Request $request) // Login
    {
        // Validando o input da requisição
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');
        if ($token = auth()->attempt($credentials)) {
            // Sucesso na autenticação
            return response()->json(
                [
                    'success' => true,
                    'token' => $token,
                ],
                200
            );
        }

        return response()->json(
            [
                'success' => false,
                'error' => 'Invalid credentials, email or password incorrect.'
            ],
            401
        );
    }

    /**
     * @OA\Post(
     *     path="/api/register",
     *     summary="Register a new user",
     *     tags={"Authentication"},
     *     description="Create a new user and, if the user has the role 'support', associate a service area.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password","role"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123"),
     *             @OA\Property(property="role", type="integer", example=3, description="User role (1=admin, 2=attendant, 3=support, 4=user)"),
     *             @OA\Property(property="service_area", type="string", nullable=true, description="Service area for users with the 'support' role"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User registered successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="object", description="User details")
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

    public function signUp(Request $request) // Register
    {
        DB::beginTransaction();

        // Validando o input da requisição
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'role' => 'required|integer|between:1,4',
            'service_area' => 'nullable',
        ]);

        // Criando um novo usuário
        $user = User::create([
            "name" => $request->input("name"),
            "email" => $request->input("email"),
            "password" => $request->input("password"),
            "role" => $request->input("role"),
        ]);

        if ($request->input("role") == User::ROLE_SUPPORT) {

            if ($request->input("service_area") === null) {
                // Retornar erro, pois service_area não pode ser nulo para role de suporte
                DB::rollBack();
                return response()->json(['error' => 'O campo service_area é obrigatório para usuários de suporte.'], 400);
            }

            // Criando a área de atendimento do analista de suporte
            $newServiceArea = Support::create([
                'user_id' => $user->id,
                'service_area' => $request->input("service_area"),
            ]);

            if ($user && $newServiceArea) {
                // Sucesso
                DB::commit();
                return response()->json(
                    [
                        'success' => true,
                        'data' => $user
                    ],
                    200
                );
            } else {
                // Falhou, desfaz as alterações no banco de dados
                DB::rollBack();
                return response()->json(
                    [
                        'success' => false,
                        'message' => "Create Error"
                    ],
                    400
                );
            }
        } else if ($user) { // usuário não analista de suporte
            // Criado, então successo
            DB::commit();
            return response()->json(
                [
                    'success' => true,
                    'data' => $user
                ],
                201
            );
        } else {
            // Qualquer outro, erro de criação
            return response()->json(
                [
                    'success' => false,
                    'message' => "Create Error"
                ],
                400
            );
        }
    }

    /**
     * @OA\Post(
     *      path="/api/logout",
     *      summary="Logout the current authenticated user",
     *      description="Invalidate the user session and log out.",
     *      tags={"Authentication"},
     *      security={{"bearer_token":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="Redirects to the home page after successful logout",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="message",
     *                  type="string",
     *                  example="User logged out successfully."
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="error",
     *                  type="string",
     *                  example="Unauthenticated."
     *              )
     *          )
     *      ),
     * )
     */

    public function logout()
    { // logout
        // Retirando a autenticação
        Auth::logout();

        return response()->json(
            [
                'success' => true,
                'message' => "Logout Sucess"
            ],
            200
        );
    }

    /**
     * @OA\Get(
     *     path="/api/getSupportList",
     *     summary="Get all support users with their associated ticket services",
     *     tags={"Users"},
     *     security={{"bearer_token":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
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
     *         response=404,
     *         description="Support users not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", description="Error message")
     *         )
     *     )
     * )
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function findAllSupport()
    {
        // Recuperando os usuários analistas de suporte
        $users = User::where('role', User::ROLE_SUPPORT)->get();

        if ($users && count($users) > 0) {
            // Carregando seus serviços
            $users->load('support.ticket_services');
            return response()->json(
                [
                    'success' => true,
                    'data' => $users,
                ],
                200
            );
        }

        // Mensagem de Erro
        return response()->json(
            [
                'success' => false,
                'message' => 'Support users not found'
            ],
            404
        );
    }

    /**
     * @OA\Get(
     *     path="/api/getAvailableSupport",
     *     summary="Get all available support users",
     *     tags={"Users"},
     *     security={{"bearer_token":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
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
     *         response=404,
     *         description="No available support users found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", description="Error message")
     *         )
     *     )
     * )
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function findAvailableSupport(Request $request)
    {
        // Recuperando os usuários analistas de suporte
        $users = User::where('role', User::ROLE_SUPPORT)->get();

        if ($users && count($users) > 0) {
            $users->load('support.ticket_services');

            // Criando a função para excluir usuários que possuam serviços com status = false (em andamento).
            $availableSupportUsers = $users->reject(function ($user) {
                return $user->support->contains(function ($support) {
                    return $support->ticket_services->contains('status', false);
                });
            });

            // Retornando a lista se não vazia
            if ($availableSupportUsers->isNotEmpty()) {
                return response()->json(
                    [
                        'success' => true,
                        'data' => $availableSupportUsers,
                    ],
                    200
                );
            }
        }

        // Mensagem de erro
        return response()->json(
            [
                'success' => false,
                'message' => 'No available support analyst'
            ],
            404
        );
    }
}
