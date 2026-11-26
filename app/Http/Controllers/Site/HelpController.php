<?php

namespace App\Http\Controllers\Site;

use App\Enums\ContactSubject;
use App\Enums\FaqCategory;
use App\Http\Controllers\Controller;
use App\Http\Requests\Site\ContactRequest;
use App\Models\ContactMessage;
use App\Models\Faq;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class HelpController extends Controller
{
    public function index(): View
    {
        $faqs = Faq::active()->orderBy('position')->orderBy('id')->get()->groupBy(fn (Faq $faq) => $faq->category->value);

        return view('site.pages.help', [
            'groups' => collect(FaqCategory::cases())
                ->filter(fn (FaqCategory $category) => $faqs->has($category->value))
                ->map(fn (FaqCategory $category) => [
                    'category' => $category,
                    'items' => $faqs[$category->value]->map(fn (Faq $faq) => [
                        'question' => $faq->question,
                        'answer' => nl2br(e($faq->answer)),
                    ])->all(),
                ])
                ->values(),
            'subjects' => ContactSubject::cases(),
        ]);
    }

    public function contact(ContactRequest $request): RedirectResponse
    {
        ContactMessage::create($request->safe()->except('website'));

        return redirect()->to(route('site.help').'#contato')
            ->with('contact_sent', 'Mensagem recebida! A gente responde no seu e-mail em até 1 dia útil.');
    }
}
