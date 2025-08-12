<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $products = Product::orderBy('name')
                ->paginate(15);

            return response()->json([
                'success' => true,
                'data' => $products,
                'message' => 'Productos obtenidos exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener productos: ' . $e->getMessage()
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
                'description' => 'nullable|string|max:1000',
                'price' => 'required|numeric|min:0',
                'stock' => 'required|integer|min:0',
                'category' => 'required|string|max:100',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            $product = Product::create($request->all());

            return response()->json([
                'success' => true,
                'data' => $product,
                'message' => 'Producto creado exitosamente'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear producto: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $product = Product::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $product,
                'message' => 'Producto obtenido exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Producto no encontrado'
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $product = Product::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
                'price' => 'required|numeric|min:0',
                'stock' => 'required|integer|min:0',
                'category' => 'required|string|max:100',
                'is_active' => 'boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            $product->update($request->all());

            return response()->json([
                'success' => true,
                'data' => $product,
                'message' => 'Producto actualizado exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar producto: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $product = Product::findOrFail($id);

            // Verificar si el producto tiene ventas
            if ($product->saleItems()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar un producto que tiene ventas registradas'
                ], 400);
            }

            $product->delete();

            return response()->json([
                'success' => true,
                'message' => 'Producto eliminado exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar producto: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener productos activos
     */
    public function active(): JsonResponse
    {
        try {
            $products = Product::active()
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $products,
                'message' => 'Productos activos obtenidos exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener productos activos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener productos en stock
     */
    public function inStock(): JsonResponse
    {
        try {
            $products = Product::inStock()
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $products,
                'message' => 'Productos en stock obtenidos exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener productos en stock: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener productos con stock bajo
     */
    public function lowStock(Request $request): JsonResponse
    {
        try {
            $threshold = $request->get('threshold', 5);
            
            $products = Product::lowStock($threshold)
                ->orderBy('stock')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $products,
                'message' => "Productos con stock bajo (≤{$threshold}) obtenidos exitosamente"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener productos con stock bajo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener productos por categoría
     */
    public function byCategory(string $category): JsonResponse
    {
        try {
            $products = Product::byCategory($category)
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $products,
                'message' => "Productos de la categoría '{$category}' obtenidos exitosamente"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener productos por categoría: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Buscar productos
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

            $products = Product::where('name', 'like', "%{$query}%")
                ->orWhere('description', 'like', "%{$query}%")
                ->orWhere('category', 'like', "%{$query}%")
                ->orderBy('name')
                ->limit(10)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $products,
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
     * Actualizar stock de producto
     */
    public function updateStock(Request $request, string $id): JsonResponse
    {
        try {
            $product = Product::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'quantity' => 'required|integer',
                'operation' => 'required|in:add,subtract,set',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            $quantity = $request->quantity;
            $operation = $request->operation;

            switch ($operation) {
                case 'add':
                    $product->addStock($quantity);
                    break;
                case 'subtract':
                    if ($product->stock < $quantity) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Stock insuficiente para realizar la operación'
                        ], 400);
                    }
                    $product->updateStock($quantity);
                    break;
                case 'set':
                    if ($quantity < 0) {
                        return response()->json([
                            'success' => false,
                            'message' => 'El stock no puede ser negativo'
                        ], 400);
                    }
                    $product->update(['stock' => $quantity]);
                    break;
            }

            $product->refresh();

            return response()->json([
                'success' => true,
                'data' => $product,
                'message' => 'Stock actualizado exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar stock: ' . $e->getMessage()
            ], 500);
        }
    }
}
