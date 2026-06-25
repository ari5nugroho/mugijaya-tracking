<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use App\Models\User;
use Carbon\Carbon;

class AuditLogController extends Controller
{
    /**
     * Display a listing of system activity logs.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $causer_id = $request->input('user_id');
        $module = $request->input('module');
        $date_start = $request->input('date_start');
        $date_end = $request->input('date_end');

        $query = Activity::with(['causer.roles']);

        // Filter: User
        if ($causer_id && $causer_id !== 'all') {
            $query->where('causer_id', $causer_id)
                  ->where('causer_type', User::class);
        }

        // Filter: Module (log_name)
        if ($module && $module !== 'all') {
            $query->where('log_name', $module);
        }

        // Filter: Date Start
        if ($date_start) {
            $query->where('created_at', '>=', Carbon::parse($date_start)->startOfDay());
        }

        // Filter: Date End
        if ($date_end) {
            $query->where('created_at', '<=', Carbon::parse($date_end)->endOfDay());
        }

        // Search: description, log_name, properties, and username
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('log_name', 'like', "%{$search}%")
                  ->orWhere('properties', 'like', "%{$search}%")
                  ->orWhereHasMorph('causer', [User::class], function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Paginate by 20 items
        $activities = $query->latest('id')->paginate(20)->withQueryString();

        // Get all users for the filter dropdown
        $users = User::orderBy('name')->get();

        // Define available modules for filtering
        $modules = [
            'auth' => 'Authentication',
            'warehouse' => 'Warehouse',
            'category' => 'Category',
            'product' => 'Product',
            'inventory' => 'Inventory',
            'user_management' => 'User Management',
        ];

        return view('audit.index', compact('activities', 'users', 'modules', 'search', 'causer_id', 'module', 'date_start', 'date_end'));
    }
}
