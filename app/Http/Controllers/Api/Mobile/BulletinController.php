<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Bulletin;

class BulletinController extends Controller
{
    /**
     * 📱 Fetch all bulletins for the CofSys mobile app
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
            ->select('bulletin_id', 'title', 'content', 'category', 'date_posted', 'attachments')
            ->get()
            ->map(function ($item) {
                $attachments = collect($item->attachments ?? [])
                    ->map(fn($path) => [
                        'url'      => url('storage/' . $path),
                        'path'     => $path,
                        'is_image' => preg_match('/\.(jpg|jpeg|png|webp|gif)$/i', $path) === 1,
                    ])
                    ->values()
                    ->all();

                return [
                    'id'          => $item->bulletin_id,
                    'title'       => $item->title ?? 'Untitled',
                    'content'     => $item->content ?? '',
                    'category'    => $item->category ?? 'Announcement',
                    'date_posted' => $item->date_posted
                        ? $item->date_posted->format('Y-m-d')
                        : null,
                    'attachments' => $attachments,
                ];
            });

        return response()->json([
            'status' => 'success',
            'count' => $bulletins->count(),
            'data' => $bulletins,
        ], 200);
    }
}
