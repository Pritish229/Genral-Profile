<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">{{ $title ?? 'Dashboard' }}</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        @foreach ($links as $label => $route)
                        @if ($loop->last)
                        <li class="breadcrumb-item active">{{ $label }}</li>
                        @else
                        <li class="breadcrumb-item">
                            <a href="{{ $route ? route($route) : '#' }}">{{ $label }}</a>
                        </li>
                        @endif
                        @endforeach
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>