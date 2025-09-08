<!doctype html>
<html lang="uk">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" crossorigin="anonymous"></script>
    <title>DataForSEO Form</title>
    <style>
        #location-suggest {
            position: absolute;
            z-index: 1000;
            width: 100%;
            display: none;
            max-height: 260px;
            overflow: auto;
        }

        #location-suggest .list-group-item {
            cursor: pointer
        }

        #location-suggest .list-group-item.active {
            background: #0d6efd;
            color: #fff;
        }
    </style>
</head>
<body class="bg-dark min-vh-100 d-flex justify-content-center align-items-center flex-column">
<h1 class="text-white mb-3">DataForSEO Form</h1>
<div class="d-flex justify-content-around align-items-start flex-wrap gap-3 w-100 p-5">
    <div class="card p-5 w-50">
        <form method="POST" action="{{ route('search') }}">
            @csrf
            <div class="mb-3">
                <div class="form-group mb-3">
                    <label for="keyword">Keyword</label>
                    <input type="text" class="form-control" id="keyword" name="keyword"
                           value="{{old('keyword') ?: ''}}">
                    @error('keyword') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <div class="form-group mb-3">
                    <label for="site">Website (domain to check)</label>
                    <input type="text" class="form-control" id="site" name="site"
                           value="{{ old('site', $data['site'] ?? '') }}" placeholder="example.com">
                    @error('site') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <div class="form-group mb-3 position-relative">
                    <label for="location">Location</label>
                    <input id="location" name="location_name" type="text" class="form-control"
                           placeholder="Почніть вводити місто/країну…" autocomplete="off"
                           value="{{old('location_name') ?: ''}}">
                    <input id="location_code" name="location" type="hidden" value="{{old('location') ?: ''}}">

                    <ul id="location-suggest" class="list-group mt-2"
                        style="position:absolute; z-index:1000; width:100%; display:none; max-height:260px; overflow:auto;"></ul>

                    @error('location')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="domain">Search Engine</label>
                    <select class="form-select" aria-label="Default select example" id="domain" name="domain">
                        <option {{old('domain') ?? ''}}>Choose the domain</option>
                        <option {{old('domain') == 'google' ? 'selected' : ''}} value="google">Google</option>
                        <option {{old('domain') == 'bing' ? 'selected' : ''}} value="bing">Bing</option>
                        <option {{old('domain') == 'yahoo' ? 'selected' : ''}} value="yahoo">Yahoo</option>
                    </select>
                    @error('domain') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="language">Language</label>
                    <input type="text" class="form-control" id="language" name="language"
                           value="{{old('language') ?: ''}}">
                    @error('language') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>

    <div class="card response flex-fill p-3 overflow-y-scroll" style="max-height: 447px;">
        <h4>Response from API</h4>
        <hr>

        @if(isset($response['error']))
            <div class="alert alert-danger">{{ $response['error'] }}</div>

        @elseif(isset($response)) {{-- Є відповідь від API (навіть якщо rank = null) --}}
        {{-- Блок з рангом --}}
        @if(!is_null($rank))
            <p>
                <strong>{{ $data['site'] ?? '' }}</strong> found:
                <span class="badge bg-success">#{{ $rank['rank_absolute'] }}</span>
                @if(!empty($rank['rank_group']))
                    <small class="text-muted">(organic position: {{ $rank['rank_group'] }})</small>
                @endif
            </p>
            @if(!empty($rank['title']) && !empty($rank['url']))
                <p><a href="{{ $rank['url'] }}" target="_blank" rel="noopener">{{ $rank['title'] }}</a></p>
            @endif
        @else
            <div class="alert alert-warning">
                {{ $data['site'] ?? 'Site' }} not found in top results for “{{ $data['keyword'] ?? '' }}”.
            </div>
        @endif

        <hr>

        {{-- Список органічних результатів (показуємо завжди, якщо є response) --}}
        @foreach(($response['items'] ?? []) as $result)
            @if(($result['type'] ?? null) === 'organic')
                <div class="mb-2">
                    <a href="{{ $result['url'] ?? '#' }}" target="_blank" rel="noopener">
                        {{ $result['title'] ?? $result['url'] ?? '' }}
                    </a>
                    <small class="text-muted"> (#{{ $result['rank_absolute'] ?? '?' }})</small>
                </div>
            @endif
        @endforeach

        @else
            <span class="alert alert-info">First you should make a request</span>
        @endif
    </div>

</div>

<script>
    $(function () {
        const $input = $('#location');
        const $hidden = $('#location_code');
        const $list = $('#location-suggest');
        const $language = $('#language')

        // 1) Підвантажуємо підказки
        $input.on('input', function () {
            const q = $input.val().trim();
            if (q.length < 2) {
                $list.hide().empty();
                return;
            }

            $.post("{{ route('locations-suggest') }}",
                {_token: "{{ csrf_token() }}", query: q},
                function (resp) {
                    $list.empty();
                    if (!Array.isArray(resp) || !resp.length) {
                        $list.hide();
                        return;
                    }

                    resp.forEach(item => {
                        $list.append(
                            `<li class="list-group-item" data-code="${item.location_code}" data-language="${item.language_code || ''}">
               ${item.location_name} <small class="text-muted">${item.language_code || ''}</small>
             </li>`
                        );
                    });
                    $list.show();
                }
            ).fail(function () {
                $list.hide().empty();
            });
        });

        // 2) Клік по елементу списку — підставляємо значення і ховаємо список
        $list.on('click', 'li', function () {
            const code = $(this).data('code');
            const text = $(this).contents().first().text().trim();
            $hidden.val(code);
            $input.val(text);
            $list.hide().empty();
            $('#language').val($(this).data('language') || '');
        });

        // 3) Клік поза — ховаємо
        $(document).on('click', function (e) {
            if (!$(e.target).closest('#location, #location-suggest').length) $list.hide();
        });
    });
</script>
</body>
</html>
