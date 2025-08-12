<?php

namespace App\Http\Controllers;

use App\Models\Membership;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class MembershipController extends Controller
{
    /**
     * Obtener todas las membresías con paginación y filtros
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Membership::query();

            // Aplicar filtros
            if ($request->filled('search')) {
                $query->search($request->search);
            }

            if ($request->filled('type')) {
                $query->byType($request->type);
            }

            if ($request->filled('status')) {
                $query->byStatus($request->status);
            }

            if ($request->filled('is_active')) {
                $query->where('is_active', $request->boolean('is_active'));
            }

            // Ordenamiento
            $sortBy = $request->get('sort_by', 'name');
            $sortDirection = $request->get('sort_direction', 'asc');
            $query->orderBy($sortBy, $sortDirection);

            // Paginación
            $perPage = $request->get('per_page', 15);
            $memberships = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $memberships,
                'message' => 'Membresías obtenidas correctamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las membresías',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener una membresía específica
     */
    public function show(Membership $membership): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'data' => $membership,
                'message' => 'Membresía obtenida correctamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener la membresía',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear una nueva membresía
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'type' => 'required|string|in:' . implode(',', array_keys(Membership::TYPES)),
                'duration' => 'required|integer|min:1',
                'price' => 'required|numeric|min:0.01',
                'status' => 'required|string|in:' . implode(',', array_keys(Membership::STATUSES)),
                'description' => 'nullable|string|max:1000',
                'max_visits' => 'required|integer|min:1',
                'is_active' => 'boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            $membership = Membership::create($request->all());

            return response()->json([
                'success' => true,
                'data' => $membership,
                'message' => 'Membresía creada correctamente'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la membresía',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar una membresía existente
     */
    public function update(Request $request, Membership $membership): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:255',
                'type' => 'sometimes|required|string|in:' . implode(',', array_keys(Membership::TYPES)),
                'duration' => 'sometimes|required|integer|min:1',
                'price' => 'sometimes|required|numeric|min:0.01',
                'status' => 'sometimes|required|string|in:' . implode(',', array_keys(Membership::STATUSES)),
                'description' => 'nullable|string|max:1000',
                'max_visits' => 'sometimes|required|integer|min:1',
                'is_active' => 'sometimes|boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            $membership->update($request->all());

            return response()->json([
                'success' => true,
                'data' => $membership->fresh(),
                'message' => 'Membresía actualizada correctamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la membresía',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar una membresía
     */
    public function destroy(Membership $membership): JsonResponse
    {
        try {
            // Verificar si hay clientes asociados
            if ($membership->clients()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar la membresía porque tiene clientes asociados'
                ], 422);
            }

            $membership->delete();

            return response()->json([
                'success' => true,
                'message' => 'Membresía eliminada correctamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la membresía',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Buscar membresías
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $query = $request->get('query');
            
            if (empty($query)) {
                return response()->json([
                    'success' => false,
                    'message' => 'El término de búsqueda es requerido'
                ], 400);
            }

            $memberships = Membership::search($query)
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $memberships,
                'message' => 'Búsqueda completada'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error en la búsqueda',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener estadísticas de membresías
     */
    public function stats(): JsonResponse
    {
        try {
            $stats = [
                'total' => Membership::count(),
                'active' => Membership::active()->count(),
                'inactive' => Membership::inactive()->count(),
                'totalRevenue' => Membership::sum('price'),
                'avgPrice' => Membership::avg('price'),
                'typeStats' => [
                    'Semanal' => Membership::byType('Semanal')->count(),
                    'Mensual' => Membership::byType('Mensual')->count(),
                    'Visita' => Membership::byType('Visita')->count(),
                ],
                'statusStats' => [
                    'Activa' => Membership::byStatus('Activa')->count(),
                    'Inactiva' => Membership::byStatus('Inactiva')->count(),
                    'Suspendida' => Membership::byStatus('Suspendida')->count(),
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $stats,
                'message' => 'Estadísticas obtenidas correctamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estadísticas',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener tipos de membresía disponibles
     */
    public function types(): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'data' => Membership::TYPES,
                'message' => 'Tipos obtenidos correctamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener tipos',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener estados de membresía disponibles
     */
    public function statuses(): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'data' => Membership::STATUSES,
                'message' => 'Estados obtenidos correctamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estados',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
