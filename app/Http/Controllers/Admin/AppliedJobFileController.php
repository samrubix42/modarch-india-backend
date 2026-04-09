<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppliedJobs;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AppliedJobFileController extends Controller
{
    public function download(AppliedJobs $appliedJob, string $type): BinaryFileResponse
    {
        $path = match ($type) {
            'resume' => $appliedJob->resume_path,
            'portfolio' => $appliedJob->portfolio_path,
            default => null,
        };

        abort_unless($path, 404, 'Requested file was not found.');

        $absolutePath = storage_path('app/public/' . $path);
        abort_unless(file_exists($absolutePath), 404, 'Requested file was not found.');

        return response()->download($absolutePath, basename($absolutePath));
    }
}
