@extends('layouts.app')

@section('title', 'Get Metrics')

@section('header', 'Get Google PageSpeed Insights Metrics')

@section('content')

    <form id="metricsForm" class="text-start">

        <div class="ms-md-auto pe-md-3 d-flex align-items-center">
            <div class="input-group input-group-outline">
                <label class="form-label">URL:</label>
                <input type="text" class="form-control" value="http://broobe.com" name="url" required>

            </div>
        </div>
        <div class="card-body p-3">
            <h6 class="text-uppercase text-body text-xs font-weight-bolder">Categories</h6>
            <ul class="list-group">
                @foreach ($categories as $category)
                    <li class="list-group-item border-0 px-0">
                        <div class="form-check form-switch ps-0">
                            <input class="form-check-input ms-auto" type="checkbox" name="categories[]"
                                value="{{ $category->name }}">
                            <label class="form-check-label text-body ms-3 text-truncate w-80 mb-0"
                                for="flexSwitchCheckDefault">{{ $category->name }}</label>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="form-group mt-3">
            <label for="strategy">Strategy:</label>
            <select name="strategy" class="form-control" required>
                @foreach ($strategies as $strategy)
                    <option value="{{ $strategy->name }}">{{ $strategy->name }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary mt-3" id="submitButton">Get Metrics</button>
    </form>

    <div id="results" class="row">

    </div>
    <button id="saveMetrics" class="btn btn-success mt-3" style="display:none;">Save Metrics</button>
@endsection

@section('scripts')
    <script>
        let dataMetrics = {};

        document.getElementById('metricsForm').addEventListener('submit', function(event) {
            event.preventDefault();
            $("#submitButton").attr("disabled", "disabled")
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
                    let output = '<h3>Metrics Results:</h3>';
                    if (data.status == "error") {


                        Swal.fire({
                            icon: 'error',
                            title: 'Transaction Failed',
                            text: data.response.error,
                            confirmButtonText: 'OK'
                        });
                    } else {
                        dataMetrics = (data.response?.lighthouseResult?.categories);

                        Object.entries(dataMetrics).forEach((metric) => {
                            output += `<div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                        <div class="card">
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
                    </div>
                    `


                        });

                        document.getElementById('saveMetrics').style.display = 'block';
                    }
                    document.getElementById('results').innerHTML =
                        `
                        <div class="col-12">
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
                    alert(data.message || 'Metrics saved successfully');
                });
        });
    </script>
@endsection
