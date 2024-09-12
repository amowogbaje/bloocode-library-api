<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;


use App\Models\BorrowRecord;
use Illuminate\Http\Request;
use App\Http\Resources\BorrowRecordResource;
use Illuminate\Support\Facades\Auth;

class BorrowRecordController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', BorrowRecord::class);
        $borrowRecords = BorrowRecord::all();
        return response()->json([
            'message' => 'Borrow records retrieved successfully',
            'data' => BorrowRecordResource::collection($borrowRecords)
        ], 200);
    }

    public function show($id)
    {
        $borrowRecord = BorrowRecord::findOrFail($id);
        $this->authorize('view', $borrowRecord);
        return response()->json([
            'message' => 'Borrow record retrieved successfully',
            'data' => new BorrowRecordResource($borrowRecord)
        ], 200);
    }

   
}
