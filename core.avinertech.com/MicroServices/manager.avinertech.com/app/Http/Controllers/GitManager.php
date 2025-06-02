<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Process\Process;

class GitManager extends Controller
{
    public function showTags()
    {
        $tags = $this->sshService->getGitTags();
        dd($tags);
        // return view('git.tags', compact('tags'));
    }
}
