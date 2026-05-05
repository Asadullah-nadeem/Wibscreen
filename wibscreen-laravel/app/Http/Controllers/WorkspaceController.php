<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Collection;
use App\Models\WorkspaceTab;
use Illuminate\Support\Facades\Auth;

class WorkspaceController extends Controller
{
    /**
     * Create a new collection (folder)
     */
    public function createCollection(Request $request)
    {
        $user = Auth::user();

        if ($user->isPlanExpired()) {
            return response()->json(['error' => 'Your plan has expired. Please renew to continue using the workspace.'], 403);
        }
        
        // Enforce Plan Limit
        if (!$user->canCreateWorkspace()) {
            return response()->json(['error' => 'Plan limit reached. Please upgrade.'], 403);
        }

        $collection = Collection::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'icon' => $request->icon ?? 'fas fa-folder',
        ]);

        return response()->json($collection);
    }

    /**
     * Rename a collection
     */
    public function renameCollection(Request $request, $id)
    {
        if (Auth::user()->isPlanExpired()) {
            return response()->json(['error' => 'Your plan has expired.'], 403);
        }

        $collection = Collection::where('user_id', Auth::id())->findOrFail($id);
        $collection->update(['name' => $request->name]);
        return response()->json($collection);
    }

    /**
     * Delete a collection and its tabs
     */
    public function deleteCollection($id)
    {
        if (Auth::user()->isPlanExpired()) {
            return response()->json(['error' => 'Your plan has expired.'], 403);
        }

        $collection = Collection::where('user_id', Auth::id())->findOrFail($id);
        $collection->tabs()->delete();
        $collection->delete();
        return response()->json(['success' => true]);
    }

    /**
     * Create a new tab
     */
    public function createTab(Request $request)
    {
        $user = Auth::user();

        if ($user->isPlanExpired()) {
            return response()->json(['error' => 'Your plan has expired.'], 403);
        }

        $collectionId = $request->collection_id;

        // Ensure collection ID is numeric (ignore old string IDs from localStorage)
        if (!is_numeric($collectionId)) {
            return response()->json(['error' => 'Invalid Workspace selection. Please refresh your page.'], 400);
        }

        $collection = Collection::where('user_id', $user->id)->findOrFail($collectionId);

        // Enforce Tab Limit
        $plan = $user->plan;
        $tabLimit = ($plan === 'pro' || ($plan === 'business' && $user->plan_status === 'active')) ? 99999 : 10;
        $currentTabCount = WorkspaceTab::where('collection_id', $collection->id)->count();

        if ($currentTabCount >= $tabLimit) {
            return response()->json(['error' => 'Tab limit reached for this workspace.'], 403);
        }

        $tab = WorkspaceTab::create([
            'user_id' => $user->id,
            'collection_id' => $collection->id,
            'title' => $request->title,
            'url' => $request->url,
        ]);

        return response()->json($tab);
    }

    /**
     * Track user usage time (called every minute)
     */
    public function trackUsage()
    {
        $user = Auth::user();
        $user->increment('monthly_usage_minutes');

        $limitMinutes = 150 * 60; // 150 hours
        $isExceeded = ($user->plan === 'free' && $user->monthly_usage_minutes >= $limitMinutes);

        return response()->json([
            'minutes' => $user->monthly_usage_minutes,
            'exceeded' => $isExceeded,
            'limit' => $limitMinutes
        ]);
    }

    /**
     * Delete a tab
     */
    public function deleteTab($id)
    {
        $tab = WorkspaceTab::where('user_id', Auth::id())->findOrFail($id);
        $tab->delete();
        return response()->json(['success' => true]);
    }
}
