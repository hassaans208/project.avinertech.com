<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StorageController extends Controller
{
    public function index(Request $request)
    {
//        dd($request->file('file'));

        $validated = validator()->make($request->all(), [
            'file' => 'required|file|max:50000', // Max 20MB
        ]);

        if($validated->fails()){
            return response()->json([
                'status' => false,
                'message' => $validated->errors()->first(),
            ]);
        }

        try {
            // Store the file on the mounted disk
            $path = $request->file('file')->store('upload', 'sftp');

            return response()->json([
                'status' => true,
                'message' => 'File uploaded successfully!',
                'path' => $path
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'error' => 'File upload failed!',
                'message' => $e->getMessage()
            ], 200);
        }
    }

    public function show(Request $request, $filename)
    {
        $get = Storage::disk('sftp')->download("upload/$filename");

        return Storage::disk('sftp')->download("upload/$filename");
    }
}
