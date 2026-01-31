<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subject;
use App\Models\Topic;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('q');

        if (!$query || strlen($query) < 2) {
            return response()->json([]);
        }

        // 1. Keresés a Tantárgyak között
        $subjects = Subject::where('name', 'LIKE', "%{$query}%")
            ->select('id', 'name as title', 'slug', 'icon') // Egységesítjük a mezőneveket
            ->take(3)
            ->get()
            ->map(function ($item) {
                $item->type = 'subject';
                $item->url = "/tantargyak/{$item->slug}";
                return $item;
            });

        // 2. Keresés a Leckék között
        $topics = Topic::with('subject') // Kell a subject, hogy tudjuk a slugját
            ->where('title', 'LIKE', "%{$query}%")
            ->select('id', 'title', 'slug', 'subject_id', 'type')
            ->take(5)
            ->get()
            ->map(function ($item) {
                $item->type = $item->type === 'test' ? 'test' : 'lesson';
                $item->icon = $item->type === 'test' ? '📝' : '📚';
                $item->url = "/tantargyak/{$item->subject->slug}/{$item->slug}";
                return $item;
            });

        // Összefésüljük az eredményeket
        return response()->json([
            'results' => $subjects->concat($topics)
        ]);
    }
}