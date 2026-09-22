@php
    $steps = [
        1 => ['Your details', 'Name, email and a password'],
        2 => ['Payout account', 'Where your earnings are sent'],
        3 => ['Store and documents', 'CNIC, bank proof, store name'],
    ];
@endphp

<ol class="space-y-6">
    @foreach ($steps as $number => [$label, $caption])
        @php
            $state = $number < $current ? 'done' : ($number === $current ? 'current' : 'todo');
        @endphp

        <li class="flex gap-4">
            <div class="flex flex-col items-center">
                <span @class([
                    'flex h-7 w-7 shrink-0 items-center justify-center text-[13px] font-semibold',
                    'bg-marigold text-forest' => $state === 'done',
                    'bg-white text-forest' => $state === 'current',
                    'border border-forest-100/25 text-forest-100/40' => $state === 'todo',
                ])>{{ $number }}</span>

                @unless ($loop->last)
                    <span @class([
                        'mt-1 h-full min-h-[24px] w-px',
                        'bg-marigold' => $state === 'done',
                        'bg-forest-100/20' => $state !== 'done',
                    ])></span>
                @endunless
            </div>

            <div class="pb-1">
                <p @class([
                    'text-[15px] font-medium',
                    'text-white' => $state === 'current',
                    'text-forest-100/70' => $state === 'done',
                    'text-forest-100/40' => $state === 'todo',
                ])>{{ $label }}</p>
                <p class="mt-0.5 text-[13px] {{ $state === 'todo' ? 'text-forest-100/30' : 'text-forest-100/60' }}">
                    {{ $caption }}
                </p>
            </div>
        </li>
    @endforeach
</ol>