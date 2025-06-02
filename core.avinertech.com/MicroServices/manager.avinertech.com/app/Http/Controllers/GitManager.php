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

            $tags = array_filter(function ($tag) {
                return !empty($tag);
            }, $tags);

            dd($tags);
            // return view('git.tags', compact('tags'));
        }

        return redirect()->back()->with('error', 'No tags found');
    }
}
