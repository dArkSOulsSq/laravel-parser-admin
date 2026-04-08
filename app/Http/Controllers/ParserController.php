<?php namespace App\Http\Controllers;
use App\Services\NetworkAnalyzerService;
use Illuminate\Http\Request;

class ParserController extends Controller {
    public function run(NetworkAnalyzerService $analyzer) {
        $urls = [
            'https://jsonplaceholder.typicode.com/posts/1',
            'https://httpbin.org/html',
            'https://example.com'
        ];

        $results = [];
        foreach ($urls as $url) {
            $item = $analyzer->analyzeAndSave($url);
            $results[] = $item ? "✅ $url -> OK" : "❌ $url -> Error";
        }

        return back()->with('status', implode("\n", $results));
    }
}