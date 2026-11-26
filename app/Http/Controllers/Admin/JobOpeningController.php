<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobOpening;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class JobOpeningController extends Controller
{
    public const SENIORITIES = ['Estágio', 'Júnior', 'Pleno', 'Sênior', 'Especialista', 'Liderança'];

    public const CONTRACTS = ['CLT', 'PJ', 'Estágio'];

    public function index(): Response
    {
        return Inertia::render('admin/Jobs', [
            'jobs' => JobOpening::orderByDesc('active')->orderBy('team')->orderBy('title')->get()
                ->map(fn (JobOpening $job) => $job->only(['id', 'title', 'team', 'location', 'seniority', 'contract', 'description', 'active'])),
            'seniorities' => self::SENIORITIES,
            'contracts' => self::CONTRACTS,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        JobOpening::create($this->validated($request));

        return back()->with('success', 'Vaga criada.');
    }

    public function update(Request $request, JobOpening $job): RedirectResponse
    {
        $job->update($this->validated($request));

        return back()->with('success', 'Vaga atualizada.');
    }

    public function destroy(JobOpening $job): RedirectResponse
    {
        $job->delete();

        return back()->with('success', 'Vaga removida.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'team' => ['required', 'string', 'max:60'],
            'location' => ['required', 'string', 'max:80'],
            'seniority' => ['required', Rule::in(self::SENIORITIES)],
            'contract' => ['required', Rule::in(self::CONTRACTS)],
            'description' => ['required', 'string', 'max:5000'],
            'active' => ['boolean'],
        ]);
    }
}
