<?php

namespace App\Http\Controllers\Admin;

use App\Enums\FaqCategory;
use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class FaqController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('admin/Faqs', [
            'faqs' => Faq::orderBy('category')->orderBy('position')->orderBy('id')->get()
                ->map(fn (Faq $faq) => [...$faq->only(['id', 'question', 'answer', 'position', 'active']), 'category' => $faq->category->value]),
            'categories' => array_map(fn (FaqCategory $category) => ['value' => $category->value, 'label' => $category->label()], FaqCategory::cases()),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Faq::create($this->validated($request));

        return back()->with('success', 'Pergunta adicionada.');
    }

    public function update(Request $request, Faq $faq): RedirectResponse
    {
        $faq->update($this->validated($request));

        return back()->with('success', 'Pergunta atualizada.');
    }

    public function destroy(Faq $faq): RedirectResponse
    {
        $faq->delete();

        return back()->with('success', 'Pergunta removida.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        return $request->validate([
            'category' => ['required', Rule::enum(FaqCategory::class)],
            'question' => ['required', 'string', 'max:255'],
            'answer' => ['required', 'string', 'max:3000'],
            'position' => ['required', 'integer', 'min:0', 'max:999'],
            'active' => ['boolean'],
        ]);
    }
}
