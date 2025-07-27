<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Filepond;
use App\Http\Requests\V1\StoreFilepondRequest;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Exception;


class FilepondController extends Controller
{
    //

    public function store(StoreFilepondRequest $request)
    {
        try {
            // Retrieve uploaded file
            $file = $request->file('file');

            // Generate unique filename
            $uniqueName = Str::uuid()->toString() . '.' . $file->getClientOriginalExtension();

            // Store the file in the public disk
            $file->storeAs('uploads/images', $uniqueName, 'public');

            // Save filename to DB
            $filepond = Filepond::create([
                'name' => $uniqueName,
            ]);

            return response()->json([
                'message' => 'Image uploaded successfully',
                'data' => $filepond,
            ], 201);
        } catch (Exception $e) {
            // Log the error
            Log::error('File upload failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Failed to upload image.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function revoke(Request $request)
    {
        try {
            $names = $request->input('names', []);

            foreach ($names as $name) {
                $filePath = 'uploads/images/' . $name;

                // Delete from storage if exists
                if (Storage::disk('public')->exists($filePath)) {
                    Storage::disk('public')->delete($filePath);
                }

                // Delete from DB if exists
                Filepond::where('name', $name)->delete();
            }

            return response()->json([
                'message' => 'Files successfully revoked and deleted.',
            ], 200);
        } catch (Exception $e) {
            Log::error('Failed to revoke files', [
                'request' => $request->all(),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Failed to revoke files.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
