@props(['items'])

<div {{ $attributes->merge(['class' => 'divide-y divide-ink-200 rounded-3xl border border-ink-200 bg-white']) }}>
    @foreach ($items as $item)
        <details class="group px-6 py-5 [&_summary::-webkit-details-marker]:hidden" data-faq-item>
            <summary class="flex cursor-pointer list-none items-center justify-between gap-6 font-title font-semibold text-ink-900">
                <span data-faq-question>{{ $item['question'] }}</span>
                <span class="flex size-8 shrink-0 items-center justify-center rounded-full bg-lilac-50 text-brand-40 transition group-open:rotate-45">
                    @svg('phosphor-plus-bold', 'size-4')
                </span>
            </summary>
            <div class="mt-3 text-ink-600" data-faq-answer>{!! $item['answer'] !!}</div>
        </details>
    @endforeach
</div>
