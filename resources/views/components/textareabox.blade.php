<div class="form-group mt-2">
    <label for="{{ $id }}" class="mb-2 labeltxt">{{ $label }}</label>
    <textarea class="form-control " placeholder="{{ $placeholder }}" id="{{ $id }}" rows="5"
        name="{{ $name }}">{{ $value }}</textarea>
    <p class="mb-3 pt-1 helpertxt"> {{ $helpertxt }}</p>
</div>

<style>
    [data-bs-theme=dark] .helpertxt {
        color: white !important;
    }
    [data-bs-theme=dark] .labeltxt {
        color: white !important;
    }

</style>
<style>
    .form-control {
        display: block;
        width: 100%;
        padding: .47rem .75rem;
        font-size: .875rem;
        font-weight: 400;
        line-height: 1.5;
        color: var(--bs-body-color);
        background-color: transparent !important;
        border: 1px solid #ced4da !important;
        border-radius: var(--bs-border-radius);
        transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
    }

    .form-control:focus {
        color: var(--bs-body-color);
        background-color: var(--bs-tertiary-bg);
        border-color: #a8abdf;
        outline: 0;
    }
</style>