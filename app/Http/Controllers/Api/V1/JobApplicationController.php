<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreAppliedJobRequest;
use App\Models\AppliedJobs;
use App\Models\JobProfile;
use Illuminate\Http\JsonResponse;

class JobApplicationController extends Controller
{
    public function store(StoreAppliedJobRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $resumePath = $request->hasFile('resume')
            ? $request->file('resume')->store('job-applications/resumes', 'public')
            : null;

        $portfolioPath = $request->hasFile('portfolio_file')
            ? $request->file('portfolio_file')->store('job-applications/portfolios', 'public')
            : null;

        $jobTitle = $validated['job_title'] ?? null;

        if (! $jobTitle && isset($validated['job_profile_id'])) {
            $jobTitle = JobProfile::query()
                ->whereKey($validated['job_profile_id'])
                ->value('job_title');
        }

        $application = AppliedJobs::query()->create([
            'job_profile_id' => $validated['job_profile_id'] ?? null,
            'job_title' => $jobTitle,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'city' => $validated['city'] ?? null,
            'portfolio_url' => $validated['portfolio_url'] ?? null,
            'resume_path' => $resumePath,
            'portfolio_path' => $portfolioPath,
            'message' => $validated['message'] ?? null,
            'status' => 'new',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Application submitted successfully.',
            'data' => [
                'id' => $application->id,
                'status' => $application->status,
                'submitted_at' => $application->created_at?->toIso8601String(),
            ],
        ], 201);
    }
}
