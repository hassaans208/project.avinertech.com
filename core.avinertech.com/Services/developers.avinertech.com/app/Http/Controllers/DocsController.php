<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DocsController extends Controller
{
    public function index()
    {

        $services = [
            'storage' => [
                'name' => 'Cloud Storage',
                'url' => 'storage.avinertech.com',
                'apis' => [
                    'store-file' => [
                        'endpoint' => '/upload',
                        'method' => 'POST',
                        'body_params' => [
                            'param_1' => [
                                'name' => 'file',
                                'required' => true,
                                'description' => 'The file to upload.',
                                'datatype' => 'file',
                                'example' => 'sample.jpg'
                            ]
                        ],
                        'query_params' => [],
                        'path_params' => []
                    ]
                ]
            ]
        ];

        return view('welcome', compact('services'));
    }
}
