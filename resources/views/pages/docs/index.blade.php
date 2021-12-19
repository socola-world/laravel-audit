<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <!-- Latest compiled and minified CSS & JS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
    <script src="//code.jquery.com/jquery.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
    <style>
        h1, h2, h3, h4, h5, h6 {
            max-width: 100%;
        }
        .sss {
            word-break: break-all;
        }
        .t1 th{
            width: 1px;
            /*word-break: keep-all;*/
            white-space: nowrap;
        }
        .text-red {
            color: red;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                @foreach($items as $item)
                    {{--            @if (empty($item['rules']) || $item['controller'] != 'App\Http\Controllers\Admin\StoreController')--}}
                    {{--                @continue--}}
                    {{--            @endif--}}
                    <a href="#{{ $item['action'] }}">
                        <h3 id="{{ $item['action'] }}" class="sss">{{ $item['action'] }}</h3>
                    </a>
                    <div class="table-responsive">
                        <table class="table table-bordered t1">
                            <tr>
                                <th>クラス名 (ClassName)</th>
                                <td>{{ $item['controller'] }}</td>
                            </tr>
                            <tr>
                                <th>メソッド名 (Method name)</th>
                                <td>{{ $item['method'] }}</td>
                            </tr>
                            <tr>
                                <th>(Middleware)</th>
                                <td>
                                    @foreach($item['middlewares'] as $middleware)
                                        <span class="badge">{{ $middleware }}</span>
                                    @endforeach
                                </td>
                            </tr>
                            <tr>
                                <th>エンティティー名 (Entity Name)</th>
                                <td></td>
                            </tr>
                            <tr>
                                <th>リクエスト名 (Request Name)</th>
                                <td>{{ $item['name'] }}</td>
                            </tr>
                            <tr>
                                <th>リクエストURL (Request URL)</th>
                                <td>
                                    <a href="{{ $item['url'] }}" target="_blank">
                                        {!! preg_replace('/({[^}]+})/', '<strong class="text-red">$1</strong>', $item['url']) !!}
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <th>リクエストメソッド (Request Method)</th>
                                <td>
                                    @foreach($item['methods'] as $method)
                                        <span class="label label-default {{ [
                                            'DELETE' => 'label-danger',
                                            'GET' => 'label-success',
                                            'PUT' => 'label-primary',
                                            'PATCH' => 'label-primary',
                                            'POST' => 'label-warning',
                                        ][$method] ?? '' }}">{{ $method }}</span>
                                    @endforeach
                                </td>
                            </tr>
                            <tr>
                                <th>メソッド概要 (Method overview)</th>
                                <td></td>
                            </tr>
                        </table>
                    </div>

                    <h3 class="sss">{{ $item['request'] }}</h3>

                    @if($item['methods'][0] == 'GET')
                        <h3 class="sss">リクエストパラメータ(Request Parameter)</h3>
                    @else
                        <h3 class="sss">リクエストボディ(Request Body)</h3>
                    @endif

                    {{--            @php--}}
                    {{--                $method = new ReflectionMethod($controller, $item->getActionMethod());--}}
                    {{--                dump($method->getParameters());--}}
                    {{--            @endphp--}}
                    {{--            <table class="table table-bordered">--}}
                    {{--                <tr>--}}
                    {{--                    <th>項番<br>(Item number)</th>--}}
                    {{--                    <th>パラメター名<br>(Parameter name)</th>--}}
                    {{--                    <th>データタイプ<br>(Type)</th>--}}
                    {{--                    <th>フォーマット<br>(Format)</th>--}}
                    {{--                    <th>説明<br>(Explanation)</th>--}}
                    {{--                </tr>--}}
                    {{--            </table>--}}

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th>Attribute</th>
                                <th>Name</th>
                                {{--                    <th>Nullable</th>--}}
                                <th>Rule</th>
                                <th>Message</th>
                            </tr>
                            </thead>
                            @if(empty($item['rules']))
                                <tr>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                </tr>
                            @endif
                            @foreach($item['rules'] as $attributeName => $rules)
                                <tr>
                                    <td rowspan="{{ count($rules) + 1 }}">{{ $attributeName }}</td>
                                    <td rowspan="{{ count($rules) + 1 }}"
                                        class="{{ !empty($item['attributes'][$attributeName]) ?: 'bg-danger' }}">
                                        {{ $item['attributes'][$attributeName] ?? '' }}
                                    </td>
                                    {{--                        <td rowspan="{{ count($rules) + 1 }}">--}}
                                    {{--                            @if(array_intersect($rules, ['nullable', 'sometimes']))--}}
                                    {{--                                nullable--}}
                                    {{--                            @endif--}}
                                    {{--                        </td>--}}
                                </tr>
                                @foreach($rules as $rule)
                                    <tr>
                                        <td class="@if(empty($rule)) bg-danger @endif">
                                            {{ $rule }}
                                        </td>
                                        @php($noNeedMessages = ['sometimes', 'nullable', 'string', 'exclude_if'])
                                        @php($isNoNeedMessage = in_array($rule, $noNeedMessages))
                                        @php($message = data_get($item['messages'], [$attributeName, $rule], ''))
                                        <td class="@if(!$isNoNeedMessage && empty($message)) bg-danger @endif">
                                            {{ $message }}
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </table>
                    </div>

                    <hr>
                @endforeach
            </div>
        </div>
    </div>
</body>
</html>
