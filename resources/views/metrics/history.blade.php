@extends('layouts.app')

@section('title', 'Metrics History')

@section('header', 'Saved Metrics History')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-dark shadow-dark border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">@yield('header')</h6>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0" id="TableHistory">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        URL</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                        ACCESIBILITY</th>
                                    <th
                                        class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        PWA</th>
                                    <th
                                        class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        SEO</th>
                                    <th
                                        class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        PERFOMANCE</th>
                                    <th
                                        class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        BEST PRACTICES</th>
                                    <th
                                        class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        STRATEGY</th>
                                    <th
                                        class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        DATETIME</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (count($metricsHistory) <= 0)
                                    <tr class="text-center">
                                        <td colspan="8">
                                            <p class="text-xs font-weight-bold mb-0 ">No information saved
                                                yet.</p>
                                        </td>
                                    </tr>
                                @endif
                                @foreach ($metricsHistory as $key => $metric)
                                    <tr class="text-center">
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $metric->url }}</p>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $metric->accessibility_metric }}</p>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0 ">
                                                {{ $metric->pwa_metric ?? 0 }}</p>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $metric->seo_metric }}</p>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $metric->performance_metric }}</p>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $metric->best_practices_metric }}
                                            </p>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $metric->strategy->name }}</p>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $metric->created_at }}</p>
                                        </td>


                                    </tr>
                                @endforeach


                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')



@endsection
