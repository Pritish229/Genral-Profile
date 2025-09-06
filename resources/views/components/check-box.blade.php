@props([
'name' => 'option[]',
'value' => '',
'label' => '',
'checked' => false,
'id' => null,
])

@php
// generate a stable id if none provided
$inputId = $id ?? 'chk_' . substr(md5($name . '|' . $value), 0, 8);
@endphp

<style>
    .check-option {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 14px;
        border: 1px solid #ccc;
        border-radius: 4px;
        cursor: pointer;
        font-family: sans-serif;
        transition: all 0.15s ease;
        background: white;
        user-select: none;
    }


    [data-bs-theme=dark] .check-option {
        background: transparent !important;
    }

    [data-bs-theme=dark] .check-option span {
        color: gray !important;
    }

    /* Hide default checkbox visually but keep it accessible */
    .check-option input {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
        pointer-events: none;
    }

    /* Circle icon area */
    .check-option .icon {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        border: 2px solid #ccc;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        background: white;
        color: transparent;
        transition: all 0.15s ease;
        line-height: 1;
    }

    /* Text */
    .check-option span {
        font-size: 14px;
        font-weight: 500;
        color: #333;
        transition: color 0.15s ease;
    }

    /* Change border + colors when checked (use sibling selectors) */
    .check-option input:checked+.icon {
        background: #4f46e5;
        border-color: #4f46e5;
        color: white;
        font-family: 'Font Awesome 5 Free', sans-serif;
        font-weight: 900;
    }

    .check-option input:checked~span {
        color: #4f46e5;
    }

    /* Focus styles */
    .check-option input:focus+.icon {
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.12);
    }

    /* Responsive */
    @media (max-width: 480px) {
        .check-option {
            padding: 6px 10px;
            gap: 6px;
        }

        .check-option .icon {
            width: 18px;
            height: 18px;
            font-size: 10px;
        }

        .check-option span {
            font-size: 12px;
        }
    }
</style>

<label class="check-option" for="{{ $inputId }}">
    <input
        id="{{ $inputId }}"
        type="checkbox"
        name="{{ $name }}"
        value="{{ $value }}"
        @if(!empty($checked)) checked @endif
        aria-label="{{ $label }}">
    <div class="icon">&#xf00c;</div>
    <span>{{ $label }}</span>
</label>