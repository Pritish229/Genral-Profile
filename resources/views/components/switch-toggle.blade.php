<div class="mb-2">
    @if($label)
        <label for="{{ $id }}" class=" d-block">{{ $label }}</label>
    @endif

    <label class="switch-toggle">
        <input type="checkbox"
               id="{{ $id }}"
               name="{{ $name }}"
               value="1"
               {{ $checked ? 'checked' : '' }}>
        <span></span>
    </label>
</div>

<style>
.switch-toggle {
    position: relative;
    margin-top:4px;
    width: 55px;
    height: 26px;
    display: inline-block;
}
.switch-toggle input { display: none; }
.switch-toggle span {
    position: absolute;
    cursor: pointer;
    background-color: #d1d1d1;
    border-radius: 25px;
    inset: 0;
    transition: .4s;
}
.switch-toggle span:before {
    position: absolute;
    content: "";
    height: 20px;
    width: 20px;
    left: 3px;
    bottom: 3px;
    background: white;
    border-radius: 50%;
    transition: .4s;
}
.switch-toggle input:checked + span { background-color: #28a745; }
.switch-toggle input:checked + span:before { transform: translateX(28px); }
</style>