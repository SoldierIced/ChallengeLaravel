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

        ```bash
            git clone <repository-url>
            cd <repository-folder>
            ```

        ### 2. Install Composer Dependencies

        ```bash
            composer install
            ```

        ### 3. Install NPM Dependencies

        ```bash
            npm install && npm run dev
            ```

        ### 4. Environment Configuration

        Copy the `.env.example` file to create your `.env` file:

        ```bash
            cp .env.example .env
            ```

        ### 5. Set Up Database

        Configure your database settings in the `.env` file:

        ```env
            DB_CONNECTION=mysql
            DB_HOST=127.0.0.1
            DB_PORT=3306
            DB_DATABASE=your_database_name
            DB_USERNAME=your_database_user
            DB_PASSWORD=your_database_password
            ```

        Additionally, set up your Google API key for PageSpeed Insights in the `.env` file:

        ```env
            GOOGLE_API_KEY=your_google_api_key
            ```

        If you don’t have a Google API key, follow these steps to obtain one:
        1. Go to [Google Cloud Console](https://console.cloud.google.com/).
        2. Create or select a project.
        3. Enable the **PageSpeed Insights API**.
        4. Create an API key and add it to your `.env` file.

        ### 6. Migrate and Seed Database

        Run the following command to create the tables and seed initial data:

        ```bash
            php artisan migrate --seed
            ```

        ### 7. Run the Development Server

        Start the Laravel server:

        ```bash
            php artisan serve
            ```

        Your application should now be running at `http://127.0.0.1:8000`.

        ## Usage

        ### Get Metrics
        - Navigate to the "Get Metrics" page.
        - Enter a URL, select the desired categories and strategy, and click "Get Metrics".
        - The application will fetch the metrics from Google PageSpeed Insights and display the results.

        ### Save Metrics
        - After fetching the metrics, click "Save Metrics" to store the results in the database for future reference.

        ### View Metrics History
        - Go to the "Metrics History" page to view all saved metrics records, including URL, performance metrics, and strategy.

        ## Project Structure

        - **Controllers**: `MetricController` handles the main functionality for fetching and saving metrics.
        - **Services**: `PageSpeedService` is responsible for communicating with the Google PageSpeed Insights API.
        - **Views**: Blade templates are used for displaying forms, results, and history.

        ## Credits

        - **Template**: This project uses a Creative Tim template to enhance the frontend design.
        - **SweetAlert2**: For displaying alerts in the user interface.

        ## Notes

        - Ensure you have a stable internet connection for fetching metrics from the Google API.
        - The project relies on `.env` configuration for sensitive data such as database credentials and API keys. Make sure to keep this file secure.

        ## License

        This project is for educational purposes only.
