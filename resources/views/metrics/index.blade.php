@extends('layouts.app')

@section('title', 'Get Metrics')

@section('header', 'Get Google PageSpeed Insights Metrics')

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-dark shadow-dark border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">@yield('header')</h6>
                    </div>
                </div>
                <div class="card-body px-0 pb-2 my-2">
                    <form id="metricsForm" class="text-start container">
                        <div class="ms-md-auto align-items-center">
                            <div class="input-group input-group-outline">
                                <label class="form-label">URL:</label>
                                <input type="text" class="form-control" name="url" required>

                            </div>
                        </div>
                        <div class="my-2">
                            <h6 class="text-uppercase text-body text-xs font-weight-bolder">Categories</h6>
                            <ul class=" row">
                                @foreach ($categories as $category)
                                    <li class="list-group-item col-12 col-md-4 border-0 px-0">
                                        <div class="form-check form-switch ps-2">
                                            <input class="form-check-input ms-auto" type="checkbox" name="categories[]"
                                                value="{{ $category->name }}">
                                            <label class="form-check-label text-body ms-3 text-truncate w-80 mb-0"
                                                for="flexSwitchCheckDefault">{{ $category->name }}</label>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <div class="form-group">
                            <label for="strategy">Strategy:</label>
                            <select name="strategy" class="form-control" id="StrategyId" required>
                                @foreach ($strategies as $strategy)
                                    <option value="{{ $strategy->name }}">{{ $strategy->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary mt-3" id="submitButton">Get Metrics</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-12" style="display:none;" id="colMetrics">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-dark shadow-dark border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">Metrics</h6>
                    </div>
                </div>
                <div class="container px-0 pb-2">
                    <div id="results" class="row gx-3">
                    </div>
                    <button id="saveMetrics" class="btn btn-success mt-3" style="display:none;">Save Metrics</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        let dataMetrics = {};

        function isValidURL(string) {
            try {
                new URL(string);
                return true;
            } catch (error) {
                return false;
            }
        }
        $("#StrategyId").select2();
        document.getElementById('metricsForm').addEventListener('submit', function(event) {
            event.preventDefault();
            if (!isValidURL(this.url.value)) {
                Swal.fire({
                    icon: 'error',
                    title: 'URL not valid',
                    text: "",
                    confirmButtonText: 'OK'
                });
                return;
            }
            $("#submitButton").attr("disabled", "disabled")
            document.getElementById('colMetrics').style.display = 'none';

            Swal.fire({
                title: 'Please wait, the transaction is being processed',
                showConfirmButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            fetch("{{ route('metrics.get') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        url: this.url.value,
                        categories: Array.from(document.querySelectorAll(
                            "input[name='categories[]']:checked")).map(c => c.value),
                        strategy: this.strategy.value
                    })
                })
                .then(response => response.json())
                .then(data => {
                    Swal.close();
                    $("#submitButton").attr("disabled", false)

                    console.log(data, "DATA?")
                    let output = '';
                    if (data.status == "error") {


                        document.getElementById('saveMetrics').style.display = 'none';
                        Swal.fire({
                            icon: 'error',
                            title: 'Transaction Failed',
                            text: data.response.error,
                            confirmButtonText: 'OK'
                        });

                    } else {
                        dataMetrics = (data.response?.lighthouseResult?.categories);

                        Object.entries(dataMetrics).forEach((metric) => {
                            output +=
                                `<div class="col-xl-6 col-sm-6 my-3 ">
                                        <div class="card-header p-2 ps-3">
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    <p class="text-sm mb-0 text-capitalize">${metric[0]}</p>
                                                    <h4 class="mb-0">${metric[1].score} </h4>
                                                </div>
                                                <div
                                                    class="icon icon-md icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-lg">
                                                <i class="material-symbols-rounded opacity-10">leaderboard</i>
                                                </div>
                                            </div>
                                        </div>
                                        <hr class="dark horizontal my-0">
                                        <div class="card-footer p-2 ps-3">
                                        </div>
                                </div>
                    `


                        });

                        document.getElementById('saveMetrics').style.display = 'block';
                        document.getElementById('colMetrics').style.display = 'block';

                    }
                    document.getElementById('results').innerHTML =
                        `
                        <div class="col-12 my-3">
                          <div class="card">
                            <div class="card-body">
                              <h6 class="mb-0 ">Metrics</h6>
                              <p class="text-sm "></p>
                              <div class="pe-2">
                                <div class="chart">
                                  <canvas id="chart-bars" class="chart-canvas" height="170"></canvas>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                ` + output;
                    var ctx = document.getElementById("chart-bars").getContext("2d");

                    new Chart(ctx, {
                        type: "bar",
                        data: {
                            labels: Object.entries(dataMetrics).map(x => x[0]),
                            datasets: [{
                                label: "Views",
                                tension: 0.4,
                                borderWidth: 0,
                                borderRadius: 4,
                                borderSkipped: false,
                                backgroundColor: "#43A047",
                                data: Object.entries(dataMetrics).map(x => x[1].score),
                                barThickness: 'flex'
                            }, ],
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false,
                                }
                            },
                            interaction: {
                                intersect: false,
                                mode: 'index',
                            },
                            scales: {
                                y: {
                                    grid: {
                                        drawBorder: false,
                                        display: true,
                                        drawOnChartArea: true,
                                        drawTicks: false,
                                        borderDash: [5, 5],
                                        color: '#e5e5e5'
                                    },
                                    ticks: {
                                        suggestedMin: 0,
                                        suggestedMax: 500,
                                        beginAtZero: true,
                                        padding: 10,
                                        font: {
                                            size: 14,
                                            lineHeight: 2
                                        },
                                        color: "#737373"
                                    },
                                },
                                x: {
                                    grid: {
                                        drawBorder: false,
                                        display: false,
                                        drawOnChartArea: false,
                                        drawTicks: false,
                                        borderDash: [5, 5]
                                    },
                                    ticks: {
                                        display: true,
                                        color: '#737373',
                                        padding: 10,
                                        font: {
                                            size: 14,
                                            lineHeight: 2
                                        },
                                    }
                                },
                            },
                        },
                    });
                });
        });

        document.getElementById('saveMetrics').addEventListener('click', function() {
            fetch("{{ route('metrics.save') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        url: document.querySelector("input[name='url']").value,
                        strategy: document.querySelector("select[name='strategy']").value,
                        metrics: {
                            accessibility: dataMetrics?.accessibility?.score ?? null,
                            pwa: dataMetrics?.pwa?.score ?? null,
                            performance: dataMetrics?.performance?.score ?? null,
                            seo: dataMetrics?.seo?.score ?? null,
                            best_practices: dataMetrics["best-practices"]?.score ?? null,
                        }
                    })
                })
                .then(response => response.json())
                .then(data => {
                    Swal.fire({
                        title: 'Metrics saved successfully',
                        showConfirmButton: true,
                    });
                });
        });
    </script>
@endsection
