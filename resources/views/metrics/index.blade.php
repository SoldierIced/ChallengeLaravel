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
        let readme = `
        Broobe Challenge - Metrics Analysis with Laravel

        This project is a Laravel application designed to fetch, store, and display Google PageSpeed Insights metrics for various categories and strategies. Users can input a URL and select specific metrics (e.g., Accessibility, Performance, SEO) and strategies (Desktop or Mobile) to get insights into the webpage’s performance.

        ## Requirements

        - **Laravel**: 10
        - **PHP**: >= 8.0
        - **Composer**
        - **Node.js & NPM** (for frontend dependencies)

        ## Installation

        Follow these steps to set up and run the project locally.

        ### 1. Clone the repository

        \`\`\`bash
            git clone <repository-url>
            cd <repository-folder>
            \`\`\`

        ### 2. Install Composer Dependencies

        \`\`\`bash
            composer install
            \`\`\`

        ### 3. Install NPM Dependencies

        \`\`\`bash
            npm install && npm run dev
            \`\`\`

        ### 4. Environment Configuration

        Copy the \`.env.example\` file to create your \`.env\` file:

        \`\`\`bash
            cp .env.example .env
            \`\`\`

        ### 5. Set Up Database

        Configure your database settings in the \`.env\` file:

        \`\`\`env
            DB_CONNECTION=mysql
            DB_HOST=127.0.0.1
            DB_PORT=3306
            DB_DATABASE=your_database_name
            DB_USERNAME=your_database_user
            DB_PASSWORD=your_database_password
            \`\`\`

        Additionally, set up your Google API key for PageSpeed Insights in the \`.env\` file:

        \`\`\`env
            GOOGLE_API_KEY=your_google_api_key
            \`\`\`

        If you don’t have a Google API key, follow these steps to obtain one:
        1. Go to [Google Cloud Console](https://console.cloud.google.com/).
        2. Create or select a project.
        3. Enable the **PageSpeed Insights API**.
        4. Create an API key and add it to your \`.env\` file.

        ### 6. Migrate and Seed Database

        Run the following command to create the tables and seed initial data:

        \`\`\`bash
            php artisan migrate --seed
            \`\`\`

        ### 7. Run the Development Server

        Start the Laravel server:

        \`\`\`bash
            php artisan serve
            \`\`\`

        Your application should now be running at \`http://127.0.0.1:8000\`.

        ## Usage

        ### Get Metrics
        - Navigate to the "Get Metrics" page.
        - Enter a URL, select the desired categories and strategy, and click "Get Metrics".
        - The application will fetch the metrics from Google PageSpeed Insights and display the results.

        ### Save Metrics
        - After fetching the metrics, click "Save Metrics" to store the results in the database for future reference.

        ### View Metrics History
        - Go to the "Metrics History" page to view all saved metrics records, including URL, performance metrics, and strategy.

        ## Features

        - **Google PageSpeed Insights Integration**: Fetches metrics using Google’s API, with categories such as Accessibility, Best Practices, Performance, PWA, and SEO.
        - **Database Storage**: Saves metrics history for later review.
        - **Frontend**: Styled with a Creative Tim template for a clean and professional user interface.

        ## Project Structure

        - **Controllers**: \`MetricController\` handles the main functionality for fetching and saving metrics.
        - **Services**: \`PageSpeedService\` is responsible for communicating with the Google PageSpeed Insights API.
        - **Views**: Blade templates are used for displaying forms, results, and history.

        ## Credits

        - **Template**: This project uses a Creative Tim template to enhance the frontend design.
        - **SweetAlert2**: For displaying alerts in the user interface.

        ## Notes

        - Ensure you have a stable internet connection for fetching metrics from the Google API.
        - The project relies on \`.env\` configuration for sensitive data such as database credentials and API keys. Make sure to keep this file secure.

        ## License

        This project is for educational purposes only.
        `;
        console.log(readme)

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
                            <p class="mb-0 text-sm">${metric[1]?.manualDescription?? ""}</p>
                            </div>
                        </div>
                    </div>
                    `

                        });
                        document.getElementById('saveMetrics').style.display = 'block';
                    }
                    document.getElementById('results').innerHTML = output;
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
