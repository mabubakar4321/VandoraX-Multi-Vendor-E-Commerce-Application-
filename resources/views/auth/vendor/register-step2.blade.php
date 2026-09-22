@extends('layouts.auth')
@section('title', 'Sell on VandoraX — Step 2')
@section('width', 'max-w-lg')

@section('panel')
    <div class="max-w-sm">
        <h2 class="text-[26px] leading-tight text-white">Where should we send your money?</h2>
        <p class="mt-3 text-[15px] leading-relaxed text-forest-100/80">
            Payouts are transferred to this account every Friday for orders delivered that week.
        </p>
        <div class="mt-10">
            @include('auth.vendor.partials.progress', ['current' => 2])
        </div>
    </div>
@endsection

@section('content')
<div class="panel p-7 sm:p-9">

    <p class="text-[13px] font-medium text-graphite lg:hidden">Step 2 of 3</p>
    <h1 class="mt-1 text-[28px] leading-tight">Payout account</h1>
    <p class="mt-1 text-[15px] text-graphite">The account must be in your own name.</p>

    <hr class="my-7 border-mist">

    <form method="POST" action="{{ route('vendor.register.step2.store') }}" class="space-y-5">
        @csrf

        <div>
            <label for="account_holder_name" class="field-label">Account holder name</label>
            <input id="account_holder_name" name="account_holder_name" type="text"
                   value="{{ old('account_holder_name') }}" required autofocus
                   class="field @error('account_holder_name') field-invalid @enderror">
            <p class="field-hint">Must match your CNIC exactly, or payouts will bounce.</p>
            @error('account_holder_name')<p class="field-error">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="bank_name" class="field-label">Bank</label>
            <select id="bank_name" name="bank_name" required
                    class="field @error('bank_name') field-invalid @enderror">
                <option value="">Choose your bank</option>
                @foreach ([
                    'hbl' => 'HBL',
                    'ubl' => 'UBL',
                    'mcb' => 'MCB',
                    'allied' => 'Allied Bank',
                    'alfalah' => 'Bank Alfalah',
                    'meezan' => 'Meezan Bank',
                    'standard_chartered' => 'Standard Chartered',
                    'faysal' => 'Faysal Bank',
                    'js' => 'JS Bank',
                    'askari' => 'Askari Bank',
                    'bank_of_punjab' => 'Bank of Punjab',
                ] as $value => $label)
                    <option value="{{ $value }}" @selected(old('bank_name') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            @error('bank_name')<p class="field-error">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="account_number" class="field-label">Account number</label>
            <input id="account_number" name="account_number" type="text" value="{{ old('account_number') }}"
                   inputmode="numeric" required
                   class="field tabular @error('account_number') field-invalid @enderror">
            <p class="field-hint">
                Stored encrypted. Our staff only ever see the last four digits.
            </p>
            @error('account_number')<p class="field-error">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="iban" class="field-label">
                IBAN <span class="font-normal text-graphite/60">— optional</span>
            </label>
            <input id="iban" name="iban" type="text" value="{{ old('iban') }}"
                   placeholder="PK36SCBL0000001123456702"
                   class="field tabular uppercase @error('iban') field-invalid @enderror">
            <p class="field-hint">Adding it means fewer failed transfers.</p>
            @error('iban')<p class="field-error">{{ $message }}</p>@enderror
        </div>

        <fieldset>
    <legend class="field-label">Account type</legend>
    <div class="grid grid-cols-3 gap-3">
        @foreach (['current' => 'Current', 'savings' => 'Savings', 'business' => 'Business'] as $value => $label)
            <label class="flex cursor-pointer items-center gap-2.5 border border-mist px-4 py-3 text-[15px] text-forest transition-colors hover:border-forest has-[:checked]:border-forest has-[:checked]:bg-forest-100/40"
                   style="border-radius: 2px;">
                <input type="radio" name="account_type" value="{{ $value }}" required
                       @checked(old('account_type') === $value)
                       class="h-4 w-4 border-mist text-forest focus:ring-marigold">
                {{ $label }}
            </label>
        @endforeach
    </div>
    @error('account_type')<p class="field-error">{{ $message }}</p>@enderror
</fieldset>

        <button type="submit" class="btn-primary">Continue</button>
    </form>
</div>
@endsection