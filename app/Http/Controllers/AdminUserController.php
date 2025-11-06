<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Post;
use App\Models\Report;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

/**
 * AdminUserController
 *
 * Admin-facing user management: list, view details, suspend, ban, restore, delete.
 */
class AdminUserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(Request $request)
    {
        $perPage = (int) $request->query('per_page', 15);
        $q = trim((string) $request->query('q', ''));
        $status = $request->query('status', null);
        $role = $request->query('role', null);

        $query = User::query();

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($role) {
            $query->where('role', $role);
        }

        $users = $query->withCount('posts')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->appends($request->query());

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $users,
            ]);
        }

        return view('admin.users', compact('users', 'q', 'status', 'role'));
    }

    public function show(User $user, Request $request)
    {
        $user->load(['posts' => function ($q) {
            $q->latest()->limit(10);
        }]);

        $reportsCount = Report::where('reported_user_id', $user->id)->count();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'user' => $user,
                    'posts' => $user->posts->take(10),
                    'reports_count' => $reportsCount,
                ],
            ]);
        }

        return view('admin.user_show', compact('user', 'reportsCount'));
    }

    public function suspend(User $user, Request $request)
    {
        if (!Auth::guard('admin')->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized (admin session expired).'
            ], 401);
        }

        $this->validateActionInput($request);

        if ($this->isCurrentAdminUser($user)) {
            return $this->errorResponse($request, 'You cannot suspend your own admin account.', 403);
        }

        try {
            $user->status = 'suspended';
            $user->suspended_at = now();
            $user->save();

            // Defensive notification creation: do not allow notification failure to break the action
            if (class_exists(Notification::class)) {
                try {
                    Notification::create([
                        'user_id'  => $user->id,
                        // supply actor_id (admin id) when possible
                        'actor_id' => Auth::guard('admin')->id() ?? null,
                        'post_id'  => null, // explicit null to signal no post association
                        'type'     => 'account_suspended',
                        'data'     => json_encode([
                            'by_admin_id' => Auth::guard('admin')->id(),
                            'reason'      => $request->input('reason'),
                        ]),
                    ]);
                } catch (\Throwable $notifEx) {
                    // Log a warning but do NOT fail the suspend action
                    Log::warning('Notification create failed (suspend)', [
                        'user_id' => $user->id,
                        'admin_id'=> Auth::guard('admin')->id(),
                        'error'   => $notifEx->getMessage(),
                    ]);
                }
            }

            return $this->successResponse($request, 'User suspended successfully.');
        } catch (\Throwable $ex) {
            Log::error('Failed to suspend user', ['user_id' => $user->id, 'error' => $ex->getMessage()]);
            return $this->errorResponse($request, 'Failed to suspend user.', 500);
        }
    }

    public function ban(User $user, Request $request)
    {
        if (!Auth::guard('admin')->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized (admin session expired).'
            ], 401);
        }

        $this->validateActionInput($request);

        if ($this->isCurrentAdminUser($user)) {
            return $this->errorResponse($request, 'You cannot ban your own admin account.', 403);
        }

        try {
            $user->status = 'banned';
            $user->banned_at = now();
            $user->save();

            if (class_exists(Notification::class)) {
                try {
                    Notification::create([
                        'user_id'  => $user->id,
                        'actor_id' => Auth::guard('admin')->id() ?? null,
                        'post_id'  => null,
                        'type'     => 'account_banned',
                        'data'     => json_encode([
                            'by_admin_id' => Auth::guard('admin')->id(),
                            'reason'      => $request->input('reason'),
                        ]),
                    ]);
                } catch (\Throwable $notifEx) {
                    Log::warning('Notification create failed (ban)', [
                        'user_id' => $user->id,
                        'admin_id'=> Auth::guard('admin')->id(),
                        'error'   => $notifEx->getMessage(),
                    ]);
                }
            }

            return $this->successResponse($request, 'User banned successfully.');
        } catch (\Throwable $ex) {
            Log::error('Failed to ban user', ['user_id' => $user->id, 'error' => $ex->getMessage()]);
            return $this->errorResponse($request, 'Failed to ban user.', 500);
        }
    }

    public function restore(User $user, Request $request)
    {
        if (!Auth::guard('admin')->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized (admin session expired).'
            ], 401);
        }

        $this->validateActionInput($request, false);

        try {
            $user->status = 'active';
            $user->suspended_at = null;
            $user->banned_at = null;
            $user->save();

            if (class_exists(Notification::class)) {
                try {
                    Notification::create([
                        'user_id'  => $user->id,
                        'actor_id' => Auth::guard('admin')->id() ?? null,
                        'post_id'  => null,
                        'type'     => 'account_restored',
                        'data'     => json_encode([
                            'by_admin_id' => Auth::guard('admin')->id(),
                            'note'        => $request->input('reason'),
                        ]),
                    ]);
                } catch (\Throwable $notifEx) {
                    Log::warning('Notification create failed (restore)', [
                        'user_id' => $user->id,
                        'admin_id'=> Auth::guard('admin')->id(),
                        'error'   => $notifEx->getMessage(),
                    ]);
                }
            }

            return $this->successResponse($request, 'User restored to active status.');
        } catch (\Throwable $ex) {
            Log::error('Failed to restore user', ['user_id' => $user->id, 'error' => $ex->getMessage()]);
            return $this->errorResponse($request, 'Failed to restore user.', 500);
        }
    }

    public function destroy(User $user, Request $request)
    {
        if (!Auth::guard('admin')->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized (admin session expired).'
            ], 401);
        }

        if ($this->isCurrentAdminUser($user)) {
            return $this->errorResponse($request, 'You cannot delete your own admin account.', 403);
        }

        $force = $request->boolean('force', false);

        DB::beginTransaction();
        try {
            if (method_exists($user, 'trashed') && $force) {
                $user->forceDelete();
            } else {
                $user->delete();
            }

            DB::commit();

            return $this->successResponse($request, 'User deleted successfully.');
        } catch (\Throwable $ex) {
            DB::rollBack();
            Log::error('Failed to delete user', ['user_id' => $user->id, 'error' => $ex->getMessage()]);
            return $this->errorResponse($request, 'Failed to delete user.', 500);
        }
    }

    /* helpers (unchanged) */
    protected function validateActionInput(Request $request, bool $requireReason = false)
    {
        $rules = ['reason' => 'nullable|string|max:1000'];
        if ($requireReason) $rules['reason'] = 'required|string|max:1000';
        Validator::make($request->all(), $rules)->validate();
    }

    protected function successResponse(Request $request, string $message = 'OK', $data = [])
    {
        if ($request->wantsJson()) {
            return response()->json(array_merge(['success' => true, 'message' => $message], $data ? ['data' => $data] : []));
        }
        return back()->with('success', $message);
    }

    protected function errorResponse(Request $request, string $message = 'Error', int $status = 400)
    {
        if ($request->wantsJson()) {
            return response()->json(['success' => false, 'message' => $message], $status);
        }
        return back()->withErrors(['error' => $message]);
    }

    protected function isCurrentAdminUser(User $user): bool
    {
        $admin = Auth::guard('admin')->user();
        if (!$admin) return false;
        return property_exists($admin, 'id') && $admin->id === $user->id;
    }
}
