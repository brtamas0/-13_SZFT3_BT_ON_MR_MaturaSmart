<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use Illuminate\Http\Request;

class TopicController extends Controller
{
    // app/Http/Controllers/TopicController.php

    public function show($slug) // <--- FONTOS: Itt $slug legyen a neve, NE $id!
    {
        // Debuggolás: Ha ezt látod a logban, akkor eljutott idáig a kód
        // \Log::info("Keresett slug: " . $slug); 

        $topic = Topic::where('slug', $slug)  // <--- Kifejezetten a 'slug' oszlopban keresünk
                ->with(['questions.answers', 'subject'])
                ->firstOrFail(); // Ez dobja a 404-et, ha nem találja

        return $topic;
    }
}