@if (session('success'))
    <div x-data="{ show: true }" x-show="show" class="mb-5 rounded-lg bg-emerald-50 border border-emerald-200 p-3.5 text-xs text-emerald-900 flex items-center justify-between">
        <div>
            <span class="font-semibold">{{ session('success') }}</span>
            @if(session('transaction_reference'))
                <span class="ms-2 font-mono text-[11px] text-emerald-700">({{ __('Ref') }}: {{ session('transaction_reference') }})</span>
            @endif
        </div>
        <button type="button" @click="show = false" class="text-emerald-500 hover:text-emerald-800 p-1">
            &times;
        </button>
    </div>
@endif

@if (session('error'))
    <div x-data="{ show: true }" x-show="show" class="mb-5 rounded-lg bg-rose-50 border border-rose-200 p-3.5 text-xs text-rose-900 flex items-center justify-between">
        <div>
            <span class="font-semibold">{{ session('error') }}</span>
        </div>
        <button type="button" @click="show = false" class="text-rose-500 hover:text-rose-800 p-1">
            &times;
        </button>
    </div>
@endif

@if (isset($errors) && $errors->any())
    <div x-data="{ show: true }" x-show="show" class="mb-5 rounded-lg bg-rose-50 border border-rose-200 p-3.5 text-xs text-rose-900">
        <div class="font-semibold mb-1">{{ __('Please correct the following errors') }}:</div>
        <ul class="list-disc list-inside space-y-0.5 text-rose-800">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
