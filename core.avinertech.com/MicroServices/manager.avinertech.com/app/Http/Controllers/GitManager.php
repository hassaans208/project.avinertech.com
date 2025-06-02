<?php

namespace App\Http\Controllers;

use App\Services\SshService;
use Illuminate\Http\Request;
use Symfony\Component\Process\Process;

class GitManager extends Controller
{
    public function showTags()
    {
        $tags = SshService::getGitTags();
        dd($tags);
        // return view('git.tags', compact('tags'));
    }
}
