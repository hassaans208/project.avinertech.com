<?php

namespace App\Http\Controllers;

use App\Services\SshService;
use Illuminate\Http\Request;
use Symfony\Component\Process\Process;

class GitManager extends Controller
{
    public function showTags()
    {
        $sshService = new SshService();
        $tags = $sshService->getGitTags();

        if (!empty($tags['output'])) {
            $tags = explode("\n", $tags['output']);

            return response()->json([
                'status' => true,
                'data' => $tags
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'No tags found'
        ]);
    }
}
