import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\AdminAuditLogController::index
* @see app/Http/Controllers/Admin/AdminAuditLogController.php:12
* @route '/admin/audit-log'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/audit-log',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\AdminAuditLogController::index
* @see app/Http/Controllers/Admin/AdminAuditLogController.php:12
* @route '/admin/audit-log'
*/
index.url = (options?: RouteQueryOptions) => {




    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AdminAuditLogController::index
* @see app/Http/Controllers/Admin/AdminAuditLogController.php:12
* @route '/admin/audit-log'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminAuditLogController::index
* @see app/Http/Controllers/Admin/AdminAuditLogController.php:12
* @route '/admin/audit-log'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\AdminAuditLogController::index
* @see app/Http/Controllers/Admin/AdminAuditLogController.php:12
* @route '/admin/audit-log'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminAuditLogController::index
* @see app/Http/Controllers/Admin/AdminAuditLogController.php:12
* @route '/admin/audit-log'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminAuditLogController::index
* @see app/Http/Controllers/Admin/AdminAuditLogController.php:12
* @route '/admin/audit-log'
*/
indexForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

index.form = indexForm

const auditLog = {
    index: Object.assign(index, index),
}

export default auditLog