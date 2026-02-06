<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Bulletin;

class BulletinController extends Controller
{
    /**
     * 📱 Fetch all bulletins for the CAFARM mobile app
     * Supports optional ?category=Event|Notice|Announcement
     */
    public function index(Request $request)
    {
        $query = Bulletin::query()->orderBy('date_posted', 'desc');

        // 🔎 Optional filtering by category
        if ($request->has('category') && strtolower($request->category) !== 'all') {
            $query->where('category', $request->category);
        }

        $bulletins = $query
            ->select('bulletin_id', 'title', 'content', 'category', 'date_posted')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->bulletin_id,
                    'title' => $item->title ?? 'Untitled',
                    'content' => strip_tags($item->content ?? ''),
                    'category' => $item->category ?? 'Announcement',
                    'date_posted' => $item->date_posted
                        ? $item->date_posted->format('Y-m-d')
                        : null,
                ];
            });

        return response()->json([
            'status' => 'success',
            'count' => $bulletins->count(),
            'data' => $bulletins,
        ], 200);
    }
}
