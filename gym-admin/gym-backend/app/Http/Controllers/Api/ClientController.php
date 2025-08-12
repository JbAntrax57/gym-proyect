<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $clients = Client::with(['memberships', 'loyaltyDiscounts'])
                ->orderBy('name')
                ->paginate(15);

            return response()->json([
                'success' => true,
                'data' => $clients,
                'message' => 'Clientes obtenidos exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener clientes: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'nullable|email|unique:clients,email',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:500',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            $client = Client::create($request->all());

            return response()->json([
                'success' => true,
                'data' => $client->load(['memberships', 'loyaltyDiscounts']),
                'message' => 'Cliente creado exitosamente'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear cliente: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $client = Client::with([
                'memberships.membershipType',
                'sales.saleItems',
                'payments',
                'notifications',
                'loyaltyDiscounts'
            ])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $client,
                'message' => 'Cliente obtenido exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cliente no encontrado'
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $client = Client::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'nullable|email|unique:clients,email,' . $id,
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:500',
                'is_active' => 'boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            $client->update($request->all());

            return response()->json([
                'success' => true,
                'data' => $client->load(['memberships', 'loyaltyDiscounts']),
                'message' => 'Cliente actualizado exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar cliente: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $client = Client::findOrFail($id);

            // Verificar si tiene membresías activas
            if ($client->hasActiveMembership()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar un cliente con membresía activa'
                ], 400);
            }

            $client->delete();

            return response()->json([
                'success' => true,
                'message' => 'Cliente eliminado exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar cliente: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Buscar clientes por nombre o email
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $query = $request->get('query', '');
            
            if (empty($query)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Query de búsqueda requerida'
                ], 400);
            }

            $clients = Client::where('name', 'like', "%{$query}%")
                ->orWhere('email', 'like', "%{$query}%")
                ->orWhere('phone', 'like', "%{$query}%")
                ->with(['memberships', 'loyaltyDiscounts'])
                ->limit(10)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $clients,
                'message' => 'Búsqueda completada'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error en la búsqueda: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener estadísticas del cliente
     */
    public function stats(string $id): JsonResponse
    {
        try {
            $client = Client::findOrFail($id);

            $stats = [
                'total_memberships' => $client->memberships()->count(),
                'active_memberships' => $client->memberships()->active()->count(),
                'total_sales' => $client->sales()->count(),
                'total_spent' => $client->sales()->sum('final_amount'),
                'loyalty_points' => $client->loyalty_points,
                'active_discounts' => $client->loyaltyDiscounts()->valid()->count(),
                'unread_notifications' => $client->notifications()->unread()->count(),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats,
                'message' => 'Estadísticas obtenidas exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estadísticas: ' . $e->getMessage()
            ], 500);
        }
    }
}
