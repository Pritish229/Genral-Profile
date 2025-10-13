@props(['title' => 'Dashboard', 'links' => []])

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">{{ $title }}</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        @foreach ($links as $label => $route)
                            @if ($loop->last)
                                <li class="breadcrumb-item active">{{ $label }}</li>
                            @else
                                @php
                                    $url = '#';

                                    // If route is array
                                    if (is_array($route)) {
                                        $routeName = $route[0] ?? null;
                                        $params = $route[1] ?? [];

                                        // Ensure params is array
                                        if (!is_array($params)) {
                                            $params = [$params];
                                        }

                                        // Only generate route if it exists
                                        if ($routeName && Route::has($routeName)) {
                                            try {
                                                $url = route($routeName, $params);
                                            } catch (\Exception $e) {
                                                $url = '#';
                                            }
                                        }
                                    } elseif ($route && Route::has($route)) {
                                        $url = route($route);
                                    }
                                @endphp
                                <li class="breadcrumb-item">
                                    <a href="{{ $url }}">{{ $label }}</a>
                                </li>
                            @endif
                        @endforeach
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
