<div class="mb-2">
    <label for="{{ $id }}" class="mb-2 labeltxt">{{ $label }}</label>
    <input type="{{ $type }}" id="{{ $id }}" class="form-control" placeholder="{{ $placeholder }}"
        name="{{ $name }}" value="{{ $value }}" @if ($required) required @endif>
    <small class="mb-3 pt-1 helpertxt"> {{ $helpertxt }}</small>
</div>

<style>
    .form-control {
        display: block;
        width: 100%;
        padding: .47rem .75rem;
        font-size: .875rem;
        font-weight: 400;
        line-height: 1.5;
        color: var(--bs-body-color);
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        background-color: transparent !important;
        background-clip: padding-box;
        border: 1px solid #ced4da !important;
        border-radius: var(--bs-border-radius);
        -webkit-transition: border-color .15s ease-in-out, -webkit-box-shadow .15s ease-in-out;
        transition: border-color .15s ease-in-out, -webkit-box-shadow .15s ease-in-out;
        transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
        transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out, -webkit-box-shadow .15s ease-in-out;
    }

    [data-bs-theme=dark] .helpertxt {
        color: white !important;
    }

    [data-bs-theme=dark] .labeltxt {
        color: white !important;
    }


    .form-control:focus {
        color: var(--bs-body-color);
        background-color: var(--bs-tertiary-bg);
        border-color: #a8abdf;
        outline: 0;
        -webkit-box-shadow:  none !important;
        box-shadow:none !important;
    }
</style>
