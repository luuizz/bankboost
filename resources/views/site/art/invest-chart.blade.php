<div class="relative mx-auto max-w-md">
    <div class="rounded-[2rem] bg-white p-6 text-ink-900 shadow-2xl shadow-brand-10/30">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm text-ink-500">Total investido</p>
                <p class="font-title text-3xl font-bold">R$ 18.540,22</p>
            </div>
            <span class="flex items-center gap-1 rounded-full bg-emerald-50 px-3 py-1 text-sm font-semibold text-emerald-700">@svg('phosphor-trend-up-bold', 'size-4') 12,8% a.a.</span>
        </div>

        <svg viewBox="0 0 320 140" class="mt-6 w-full" aria-hidden="true">
            <defs>
                <linearGradient id="area" x1="0" x2="0" y1="0" y2="1">
                    <stop offset="0%" stop-color="#653df5" stop-opacity=".35" />
                    <stop offset="100%" stop-color="#653df5" stop-opacity="0" />
                </linearGradient>
            </defs>
            @foreach ([35, 70, 105] as $y)
                <line x1="0" x2="320" y1="{{ $y }}" y2="{{ $y }}" stroke="#dfe1e8" stroke-dasharray="4 6" />
            @endforeach
            <path d="M0 120 C40 112 60 104 90 98 S150 80 180 70 S240 46 270 34 S305 18 320 12 L320 140 L0 140 Z" fill="url(#area)" />
            <path d="M0 120 C40 112 60 104 90 98 S150 80 180 70 S240 46 270 34 S305 18 320 12" fill="none" stroke="#4d20ef" stroke-width="3" stroke-linecap="round" />
            <path d="M0 126 C60 124 120 120 180 116 S280 108 320 104" fill="none" stroke="#abafba" stroke-width="2" stroke-dasharray="5 6" />
            <circle cx="270" cy="34" r="6" fill="#4d20ef" stroke="white" stroke-width="3" />
        </svg>
        <div class="mt-3 flex gap-5 text-xs text-ink-600">
            <span class="flex items-center gap-2"><span class="h-1 w-5 rounded bg-brand-40"></span>Boost Mais</span>
            <span class="flex items-center gap-2"><span class="h-1 w-5 rounded bg-ink-400"></span>Poupança</span>
        </div>

        <div class="mt-6 grid grid-cols-3 gap-3 text-center text-sm">
            @foreach ([['CDB', '62%', 'bg-brand-40'], ['Tesouro', '25%', 'bg-accent'], ['Fundos', '13%', 'bg-emerald-500']] as [$label, $share, $color])
                <div class="rounded-2xl bg-ink-100 p-3">
                    <span class="mx-auto block h-1.5 w-8 rounded-full {{ $color }}"></span>
                    <p class="mt-2 font-semibold">{{ $share }}</p>
                    <p class="text-xs text-ink-500">{{ $label }}</p>
                </div>
            @endforeach
        </div>
    </div>
    <div class="animate-float absolute -top-6 -right-3 rounded-2xl bg-accent px-4 py-3 font-title text-sm font-bold text-brand-10 shadow-xl">+ R$ 6,41 hoje</div>
</div>
