<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

use App\Http\Resources\BorrowRecordResource;
use App\Models\BorrowRecord;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class BorrowRecordController extends Controller
{
    
    /**
     * @OA\Get(
     *     path="/api/v1/borrow-records",
     *     summary="Retrieve all borrow records",
     *     description="Retrieve a list of all borrow records.",
     *     operationId="getBorrowRecords",
     *     tags={"Borrow Records"},
     *     @OA\Response(
     *         response=200,
     *         description="Borrow records retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Borrow records retrieved successfully"
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(
     *                         property="id",
     *                         type="integer",
     *                         example=1
     *                     ),
     *                     @OA\Property(
     *                         property="user_id",
     *                         type="integer",
     *                         example=1
     *                     ),
     *                     @OA\Property(
     *                         property="book_id",
     *                         type="integer",
     *                         example=1
     *                     ),
     *                     @OA\Property(
     *                         property="book",
     *                         type="object",
     *                         @OA\Property(
     *                             property="id",
     *                             type="integer",
     *                             example=1
     *                         ),
     *                         @OA\Property(
     *                             property="title",
     *                             type="string",
     *                             example="Sample Book Title"
     *                         ),
     *                         @OA\Property(
     *                             property="isbn",
     *                             type="string",
     *                             example="978-3-16-148410-0"
     *                         ),
     *                         @OA\Property(
     *                             property="published_date",
     *                             type="string",
     *                             format="date",
     *                             example="2023-01-01"
     *                         ),
     *                         @OA\Property(
     *                             property="author_id",
     *                             type="integer",
     *                             example=1
     *                         ),
     *                         @OA\Property(
     *                             property="status",
     *                             type="string",
     *                             example="available"
     *                         ),
     *                         @OA\Property(
     *                             property="created_at",
     *                             type="string",
     *                             format="date-time",
     *                             example="2023-01-01T00:00:00.000000Z"
     *                         ),
     *                         @OA\Property(
     *                             property="updated_at",
     *                             type="string",
     *                             format="date-time",
     *                             example="2023-01-01T00:00:00.000000Z"
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Unauthorized"
     *             ),
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="You do not have permission to perform this action"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="An error occurred while retrieving borrow records. Please try again later."
     *             ),
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="Internal server error"
     *             )
     *         )
     *     )
     * )
     */
    
    public function index()
    {
        try {
            $this->authorize('viewAny', BorrowRecord::class);
            $borrowRecords = BorrowRecord::all();

            return response()->json([
                'message' => 'Borrow records retrieved successfully',
                'data' => BorrowRecordResource::collection($borrowRecords)
            ], Response::HTTP_OK);
        } catch (AuthorizationException $e) {
            return response()->json([
                'message' => 'Unauthorized',
                'error' => $e->getMessage()
            ], Response::HTTP_FORBIDDEN);
        } catch (\Throwable $e) {
            Log::error('Error retrieving borrow records: ' . $e->getMessage());
            return response()->json([
                'message' => 'An error occurred while retrieving borrow records. Please try again later.',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * @OA\Get(
     *     path="/api/v1/borrow-records/{id}",
     *     summary="Retrieve a borrow record",
     *     description="Retrieve a specific borrow record by ID.",
     *     operationId="getBorrowRecordById",
     *     tags={"Borrow Records"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the borrow record",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Borrow record retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Borrow record retrieved successfully"
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="id",
     *                     type="integer",
     *                     example=1
     *                 ),
     *                 @OA\Property(
     *                     property="user_id",
     *                     type="integer",
     *                     example=1
     *                 ),
     *                 @OA\Property(
     *                     property="book_id",
     *                     type="integer",
     *                     example=1
     *                 ),
     *                 @OA\Property(
     *                     property="book",
     *                     type="object",
     *                     @OA\Property(
     *                         property="id",
     *                         type="integer",
     *                         example=1
     *                     ),
     *                     @OA\Property(
     *                         property="title",
     *                         type="string",
     *                         example="Sample Book Title"
     *                     ),
     *                     @OA\Property(
     *                         property="isbn",
     *                         type="string",
     *                         example="978-3-16-148410-0"
     *                     ),
     *                     @OA\Property(
     *                         property="published_date",
     *                         type="string",
     *                         format="date",
     *                         example="2023-01-01"
     *                     ),
     *                     @OA\Property(
     *                         property="author_id",
     *                         type="integer",
     *                         example=1
     *                     ),
     *                     @OA\Property(
     *                         property="status",
     *                         type="string",
     *                         example="available"
     *                     ),
     *                     @OA\Property(
     *                         property="created_at",
     *                         type="string",
     *                         format="date-time",
     *                         example="2023-01-01T00:00:00.000000Z"
     *                     ),
     *                     @OA\Property(
     *                         property="updated_at",
     *                         type="string",
     *                         format="date-time",
     *                         example="2023-01-01T00:00:00.000000Z"
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="borrowed_at",
     *                     type="string",
     *                     format="date-time",
     *                     example="2023-01-01T00:00:00.000000Z"
     *                 ),
     *                 @OA\Property(
     *                     property="due_at",
     *                     type="string",
     *                     format="date-time",
     *                     example="2023-01-15T00:00:00.000000Z"
     *                 ),
     *                 @OA\Property(
     *                     property="returned_at",
     *                     type="string",
     *                     format="date-time",
     *                     example="2023-01-10T00:00:00.000000Z"
     *                 ),
     *                 @OA\Property(
     *                     property="created_at",
     *                     type="string",
     *                     format="date-time",
     *                     example="2023-01-01T00:00:00.000000Z"
     *                 ),
     *                 @OA\Property(
     *                     property="updated_at",
     *                     type="string",
     *                     format="date-time",
     *                     example="2023-01-01T00:00:00.000000Z"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Unauthorized"
     *             ),
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="You do not have permission to perform this action"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Borrow record not found",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Borrow record not found"
     *             ),
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="No query results for model [App\\Models\\BorrowRecord] 1"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="An error occurred while retrieving the borrow record. Please try again later."
     *             ),
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="Internal server error"
     *             )
     *         )
     *     )
     * )
     */
    
    public function show($id)
    {
        try {
            $borrowRecord = BorrowRecord::findOrFail($id);
            $this->authorize('view', $borrowRecord);

            return response()->json([
                'message' => 'Borrow record retrieved successfully',
                'data' => new BorrowRecordResource($borrowRecord)
            ], Response::HTTP_OK);
        } catch (AuthorizationException $e) {
            return response()->json([
                'message' => 'Unauthorized',
                'error' => $e->getMessage()
            ], Response::HTTP_FORBIDDEN);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Borrow record not found',
                'error' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        } catch (\Throwable $e) {
            Log::error('Error retrieving borrow record: ' . $e->getMessage());
            return response()->json([
                'message' => 'An error occurred while retrieving the borrow record. Please try again later.',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

   
}
