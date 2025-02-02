<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class ManagerController extends Controller
{
    public function createApplication(Request $request)
    {
        $validator = validator()->make($request->all(), [
            'module' => 'required|string',
            'submodule' => 'required|string',
            'appName' => 'required|string',
        ]);

        // Handle Validation Errors
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
                'message' => 'Validation failed. Please enter all required fields correctly.'
            ], 422);
        }

        // Execute Artisan Command
        try {
            $exitCode = Artisan::call('app:manage-call', [
                'module' => $request->module,
                'submodule' => $request->submodule,
                'appName' => $request->appName
            ]);

            // Retrieve Command Output
            $output = Artisan::output();

            return response()->json([
                'status' => true,
                'message' => trim($output),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while executing the command.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
