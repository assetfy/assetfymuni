<div class="px-3 py-1">
    @if ($value)
        <div class="w-20 h-20 overflow-hidden rounded border border-gray-300 mx-auto">
            <img src="{{ Storage::disk('s3')->temporaryUrl(trim(explode(',', $value)[0]), now()->addMinutes(10)) }}"
                alt="{{ $column->label() }}" class="w-full h-full object-cover object-center" />
        </div>
    @endif
</div>
