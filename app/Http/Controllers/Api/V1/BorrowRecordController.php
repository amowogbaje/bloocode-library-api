<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

use App\Http\Resources\BorrowRecordResource;
use App\Models\BorrowRecord;
use App\Services\BorrowRecordService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class BorrowRecordController extends Controller
{
    protected $borrowRecordService;

    public function __construct(BorrowRecordService $borrowRecordService)
    {
        $this->borrowRecordService = $borrowRecordService;
    }

    public function index()
    {
        try {
            $this->authorize('viewAny', BorrowRecord::class);
            $borrowRecords = $this->borrowRecordService->getAllBorrowRecords();

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


    public function show($id)
    {
        try {
            $borrowRecord = $this->borrowRecordService->getBorrowRecordById($id);
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
