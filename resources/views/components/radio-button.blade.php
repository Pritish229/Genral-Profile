<style>
    .radio-option {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 14px;
        border: 1px solid #ccc;
        border-radius: 6px;
        cursor: pointer;
        font-family: sans-serif;
        transition: all 0.2s ease;
        background: white;
    }


    [data-bs-theme=dark] .radio-option {
        background: transparent !important;
    }
    [data-bs-theme=dark] .radio-option span {
        color: gray !important;
    }

    /* Hide default radio */
    .radio-option input {
        display: none;
    }

    /* Circle icon area */
    .radio-option .icon {
        width: 22px;
        height: 22px;
        border-radius: 50%;
        border: 2px solid #ccc;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        background: white;
        color: transparent;
        transition: all 0.2s ease;
    }

    /* Text */
    .radio-option span {
        font-size: 14px;
        font-weight: 500;
        color: #333;
    }

    /* Change border + colors when checked */
    .radio-option:has(input:checked) {
        border-color: #4f46e5;
    }

    .radio-option input:checked+.icon {
        background: #4f46e5;
        border-color: #4f46e5;
        color: white;
        font-family: 'Font Awesome 5 Free';
        font-weight: 900;
        content: "\f00c";
        /* FA check icon */
    }

    .radio-option input:checked~span {
        color: #4f46e5;
    }

    /* Responsive */
    @media (max-width: 480px) {
        .radio-option {
            padding: 6px 10px;
            gap: 6px;
        }

        .radio-option .icon {
            width: 18px;
            height: 18px;
            font-size: 10px;
        }

        .radio-option span {
            font-size: 12px;
        }
    }
</style>

<label class="radio-option" >
    <input
        type="radio"
        name="{{ $name }}"
        value="{{ $value }}"
        @if($checked) checked @endif>
    <div class="icon">&#xf00c;</div>
    <span>{{ $label }}</span>
</label>