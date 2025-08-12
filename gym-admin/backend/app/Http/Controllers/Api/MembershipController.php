<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\ClientMembership;
use App\Models\MembershipType;
use App\Models\PaymentHistory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MembershipController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $memberships = ClientMembership::with(['client', 'membershipType'])
                ->orderBy('created_at', 'desc')
                ->paginate(15);

            return response()->json([
                'success' => true,
                'data' => $memberships,
                'message' => 'Membresías obtenidas exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener membresías: ' . $e->getMessage()
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
                'client_id' => 'required|exists:clients,id',
                'membership_type_id' => 'required|exists:membership_types,id',
                'start_date' => 'required|date',
                'amount_paid' => 'required|numeric|min:0',
                'payment_status' => 'in:paid,pending,partial',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            $membershipType = MembershipType::findOrFail($request->membership_type_id);
            $startDate = Carbon::parse($request->start_date);
            $endDate = $startDate->copy()->addDays($membershipType->duration_days);

            // Crear la membresía
            $membership = ClientMembership::create([
                'client_id' => $request->client_id,
                'membership_type_id' => $request->membership_type_id,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'status' => 'active',
                'payment_status' => $request->payment_status ?? 'paid',
                'amount_paid' => $request->amount_paid,
            ]);

            // Registrar el pago
            PaymentHistory::create([
                'client_id' => $request->client_id,
                'client_membership_id' => $membership->id,
                'amount' => $request->amount_paid,
                'payment_date' => now(),
                'payment_type' => 'membership',
                'notes' => 'Pago de membresía: ' . $membershipType->name,
            ]);

            // Agregar puntos de lealtad
            $client = Client::findOrFail($request->client_id);
            $loyaltyPoints = $this->calculateLoyaltyPoints($request->amount_paid);
            $client->addLoyaltyPoints($loyaltyPoints);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $membership->load(['client', 'membershipType']),
                'message' => 'Membresía creada exitosamente'
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al crear membresía: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $membership = ClientMembership::with([
                'client',
                'membershipType',
                'client.payments'
            ])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $membership,
                'message' => 'Membresía obtenida exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Membresía no encontrada'
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $membership = ClientMembership::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'status' => 'in:active,expired,cancelled',
                'payment_status' => 'in:paid,pending,partial',
                'amount_paid' => 'numeric|min:0',
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
                'data' => $membership->load(['client', 'membershipType']),
                'message' => 'Membresía actualizada exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar membresía: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $membership = ClientMembership::findOrFail($id);

            if ($membership->isActive()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar una membresía activa'
                ], 400);
            }

            $membership->delete();

            return response()->json([
                'success' => true,
                'message' => 'Membresía eliminada exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar membresía: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener membresías activas
     */
    public function active(): JsonResponse
    {
        try {
            $memberships = ClientMembership::with(['client', 'membershipType'])
                ->active()
                ->orderBy('end_date')
                ->paginate(15);

            return response()->json([
                'success' => true,
                'data' => $memberships,
                'message' => 'Membresías activas obtenidas exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener membresías activas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener membresías próximas a vencer
     */
    public function expiringSoon(Request $request): JsonResponse
    {
        try {
            $days = $request->get('days', 7);
            
            $memberships = ClientMembership::with(['client', 'membershipType'])
                ->expiringSoon($days)
                ->orderBy('end_date')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $memberships,
                'message' => "Membresías próximas a vencer en {$days} días obtenidas exitosamente"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener membresías próximas a vencer: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener membresías expiradas
     */
    public function expired(): JsonResponse
    {
        try {
            $memberships = ClientMembership::with(['client', 'membershipType'])
                ->expired()
                ->orderBy('end_date', 'desc')
                ->paginate(15);

            return response()->json([
                'success' => true,
                'data' => $memberships,
                'message' => 'Membresías expiradas obtenidas exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener membresías expiradas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Renovar membresía
     */
    public function renew(Request $request, string $id): JsonResponse
    {
        try {
            $membership = ClientMembership::findOrFail($id);
            
            if (!$membership->isExpired()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Solo se pueden renovar membresías expiradas'
                ], 400);
            }

            $validator = Validator::make($request->all(), [
                'amount_paid' => 'required|numeric|min:0',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            // Calcular nueva fecha de vencimiento
            $newEndDate = now()->addDays($membership->membershipType->duration_days);

            // Actualizar membresía
            $membership->update([
                'start_date' => now(),
                'end_date' => $newEndDate,
                'status' => 'active',
                'payment_status' => 'paid',
                'amount_paid' => $request->amount_paid,
            ]);

            // Registrar el pago
            PaymentHistory::create([
                'client_id' => $membership->client_id,
                'client_membership_id' => $membership->id,
                'amount' => $request->amount_paid,
                'payment_date' => now(),
                'payment_type' => 'membership',
                'notes' => 'Renovación de membresía: ' . $membership->membershipType->name,
            ]);

            // Agregar puntos de lealtad
            $loyaltyPoints = $this->calculateLoyaltyPoints($request->amount_paid);
            $membership->client->addLoyaltyPoints($loyaltyPoints);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $membership->load(['client', 'membershipType']),
                'message' => 'Membresía renovada exitosamente'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al renovar membresía: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calcular puntos de lealtad basados en el monto pagado
     */
    private function calculateLoyaltyPoints(float $amount): int
    {
        // 1 punto por cada $100 pagados
        return (int) ($amount / 100);
    }
}
