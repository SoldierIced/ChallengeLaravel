<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\MetricHistoryRun;
use App\Models\Strategy;
use App\Services\PageSpeedService;
use Illuminate\Http\Request;

class MetricController extends Controller
{
    protected $pageSpeedService;

    public function __construct(PageSpeedService $pageSpeedService)
    {
        $this->pageSpeedService = $pageSpeedService;
    }

    /**
     * Display the form for entering the URL and selecting categories and strategy.
     */
    public function index()
    {
        $categories = Category::all();
        $strategies = Strategy::all();

        return view('metrics.index', compact('categories', 'strategies'));
    }

    /**
     * Fetch metrics from the Google PageSpeed Insights API.
     */
    public function getMetrics(Request $request)
    {
        // Validate input data
        try {
            $request->validate([
                'url' => 'required|url',
                'categories' => 'required|array',
                'strategy' => 'required|exists:strategies,name',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $error = collect($e->errors())->flatten()->join(' ');
            return response()->json([
                'status' => 'error',
                'error' => $error
            ], 422); // 422 Unprocessable Entity
        }


        $url = $request->input('url');
        $categories = $request->input('categories');
        $strategy = $request->input('strategy');

        try {
            // Call the service to get the metrics
            $metrics = $this->pageSpeedService->getMetrics($url, $categories, $strategy);

            // Return success response using the response helper
            return $this->response($metrics, 'success');
        } catch (\Exception $e) {
            // Handle errors and return error response
            return $this->response(['error' => 'Failed to fetch metrics: ' . $e->getMessage()], 'error');
        }
    }

    /**
     * Save metrics results to the database.
     */
    public function saveMetrics(Request $request)
    {
        $request->validate([
            'url' => 'required|url',
            'strategy' => 'required|exists:strategies,name',
            'metrics.accessibility' => 'nullable|numeric',
            'metrics.pwa' => 'nullable|numeric',
            'metrics.performance' => 'nullable|numeric',
            'metrics.seo' => 'nullable|numeric',
            'metrics.best_practices' => 'nullable|numeric',
        ]);

        try {
            $strategy = Strategy::where('name', $request->input('strategy'))->first();

            $metricHistoryRun = new MetricHistoryRun([
                'url' => $request->input('url'),
                'accessibility_metric' => $request->input('metrics.accessibility'),
                'pwa_metric' => $request->input('metrics.pwa'),
                'performance_metric' => $request->input('metrics.performance'),
                'seo_metric' => $request->input('metrics.seo'),
                'best_practices_metric' => $request->input('metrics.best_practices'),
                'strategy_id' => $strategy->id,
            ]);

            $metricHistoryRun->save();

            // Return success response using the response helper
            return $this->response(['message' => 'Metrics saved successfully!'], 'success');
        } catch (\Exception $e) {
            // Handle errors and return error response
            return $this->response(['error' => 'Error saving metrics: ' . $e->getMessage()], 'error');
        }
    }

    /**
     * Display the history of saved metrics.
     */
    public function history()
    {
        $metricsHistory = MetricHistoryRun::with('strategy')->get();
        // dd($metricsHistory);
        return view('metrics.history', compact('metricsHistory'));
    }
}
