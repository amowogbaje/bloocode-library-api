<?php

namespace App\Services;

use App\Models\BorrowRecord;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Log;

class BorrowRecordService
{
    /**
     * Retrieve a borrow record by ID.
     *
     * @param int $id
     * @return BorrowRecord
     * @throws AuthorizationException
     */
    public function getBorrowRecordById($id)
    {
        try {
            $borrowRecord = BorrowRecord::findOrFail($id);
            return $borrowRecord->load('book');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Borrow record not found: ' . $e->getMessage());
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException('Borrow record not found');
        } catch (\Throwable $e) {
            Log::error('Error retrieving borrow record: ' . $e->getMessage());
            throw new \Exception('An error occurred while retrieving the borrow record');
        }
    }

    /**
     * Retrieve all borrow records.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     * @throws AuthorizationException
     */
    public function getAllBorrowRecords()
    {
        try {
            return BorrowRecord::with('book')->get();
        } catch (\Throwable $e) {
            Log::error('Error retrieving borrow records: ' . $e->getMessage());
            throw new \Exception('An error occurred while retrieving borrow records');
        }
    }
}
