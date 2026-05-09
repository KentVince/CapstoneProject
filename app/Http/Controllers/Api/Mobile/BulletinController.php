<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Bulletin;
use App\Models\Farm;
use App\Models\Farmer;
use Illuminate\Http\Request;

class BulletinController extends Controller
{
    /**
     * 📱 Fetch all bulletins for the CofSys mobile app
     * Supports optional ?category=Event|Notice|Announcement
     * Optional ?app_no=COF-... — when provided, filters out targeted bulletins not addressed to this user.
     */
    public function index(Request $request)
    {
        $query = Bulletin::query()->orderBy('date_posted', 'desc');

        // 🔎 Optional filtering by category
        if ($request->has('category') && strtolower($request->category) !== 'all') {
            $query->where('category', $request->category);
        }

        // 🎯 Per-user visibility: include broadcasts (target_farm_ids null/empty)
        // plus bulletins whose target_farm_ids contain a farm owned by this user.
        if ($request->filled('app_no')) {
            $farmIds = self::resolveFarmIdsForAppNo($request->input('app_no'));

            $query->where(function ($q) use ($farmIds) {
                $q->whereNull('target_farm_ids')
                  ->orWhereJsonLength('target_farm_ids', 0);

                foreach ($farmIds as $fid) {
                    $q->orWhereJsonContains('target_farm_ids', $fid)
                      ->orWhereJsonContains('target_farm_ids', (string) $fid);
                }
            });
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

    /**
     * Resolve the farm IDs owned by the farmer behind the given app_no.
     * Returns an empty array if the app_no doesn't belong to a farmer (e.g. expert).
     */
    public static function resolveFarmIdsForAppNo(string $appNo): array
    {
        $farmer = Farmer::where('app_no', $appNo)->first();
        if (!$farmer) {
            return [];
        }

        return Farm::where('farmer_id', $farmer->id)->pluck('id')->all();
    }
}
