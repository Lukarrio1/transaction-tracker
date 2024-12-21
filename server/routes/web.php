<?php

use App\Models\Setting;
use App\Models\Node\Node;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckSuperAdmin;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Node\NodeController;
use App\Http\Controllers\ReferenceController;
use App\Http\Controllers\Role\RoleController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Cache\CacheController;
use App\Http\Controllers\Export\ExportController;
use App\Http\Controllers\Import\ImportController;
use App\Http\Controllers\Tenant\TenantController;
use App\Http\Controllers\Setting\SettingController;
use App\Http\Controllers\Redirect\RedirectController;
use App\Http\Controllers\Permission\PermissionController;

Auth::routes();




Route::middleware(['auth', CheckSuperAdmin::class])->group(function () {
    Route::get('/nodes', [NodeController::class, 'index'])->name('viewNodes');
    Route::get('/node/databus', [NodeController::class, 'databusData']);
    Route::get('/node/databus/tableData', [NodeController::class, 'databusTableData']);


    Route::post('/node', [NodeController::class, 'save'])->name('saveNode');
    Route::get('/node/{node}', [NodeController::class, 'node'])->name('viewNode');
    Route::delete('/node/delete/{node}', [NodeController::class, 'delete'])->name('deleteNode');


    Route::get('/roles', [RoleController::class, 'index'])->name('viewRoles');
    Route::get('/role/{role}', [RoleController::class, 'edit'])->name('editRole');
    Route::post('/role', [RoleController::class, 'save'])->name('saveRole');
    Route::delete('/role/{role}', [RoleController::class, 'delete'])->name('deleteRole');
    // Route::get('/node/types', [NodeTypeController::class,'index']);

    Route::get('/permissions', [PermissionController::class, 'index'])->name('viewPermissions');
    Route::get('/permission/{permission}', [PermissionController::class, 'edit'])->name('editPermission');
    Route::post('/permission', [PermissionController::class, 'save'])->name('savePermission');
    Route::delete('/permission/{permission}', [PermissionController::class, 'delete'])->name('deletePermission');

    Route::get('/caches', [CacheController::class, 'index'])->name('viewCache');
    Route::get('/clear/caches', [CacheController::class, 'clearCache'])->name('clearCache');

    Route::post('/update/user/{user}', [UserController::class, 'update'])->name('updateUser');
    Route::get('/users', [UserController::class, 'index'])->name('viewUsers');
    Route::post('/assign/role/{user}', [UserController::class, 'assignRole'])->name('assignRole');
    Route::delete('/delete/user/{user}', [UserController::class, 'delete'])->name('deleteUser');

    Route::get('/settings', [SettingController::class, 'index'])->name('viewSettings');
    Route::post('/save/setting', [SettingController::class, 'save'])->name('saveSetting');
    Route::delete('/delete/setting/{setting_key}', [SettingController::class, 'delete'])->name('deleteSetting');

    Route::get('/exports', [ExportController::class, 'index'])->name('exportData');
    Route::get('/export/data', [ExportController::class, 'export'])->name('exportDataNow');

    Route::get('/import', [ImportController::class, 'index'])->name('importView');
    Route::get('/import/ajax', [ImportController::class, 'index_ajax']);
    Route::post('/import/data', [ImportController::class, 'import'])->name('importData');

    Route::get('/references', [ReferenceController::class, 'index'])->name('viewReferences');
    Route::post('/reference', [ReferenceController::class, 'save'])->name('saveReference');
    Route::get('/references_ajax', [ReferenceController::class, 'index2']);
    Route::delete('/reference/{reference}', [ReferenceController::class, 'delete']);
    $multi_tenancy = (int) optional(collect(Cache::get('settings'))->where('key', 'multi_tenancy')->first())
                    ->getSettingValue('first');
    if ($multi_tenancy == 1) {
        Route::get('/tenants', [TenantController::class, 'index'])->name('viewTenants');
        Route::get('/tenant/{tenant}', [TenantController::class, 'index'])->name('editTenant');
        Route::post('/tenant', [TenantController::class, 'save'])->name('updateOrCreateTenant');
        Route::delete('/tenant/{tenant}/delete', [TenantController::class, 'delete'])->name('deleteTenant');
    }

    Route::get('/redirects', [RedirectController::class, 'index'])->name('roleRedirects');

    Route::post('/redirect/save', [RedirectController::class, 'save'])->name('saveRedirects');

    Route::get('/redirect/{redirect}', [RedirectController::class, 'edit'])->name('editRedirect');

    Route::delete('/delete/redirect/{redirect}', [RedirectController::class, 'delete'])->name('deleteRedirect');




    Route::get('/', [DashboardController::class, 'index'])->name('home');
});
