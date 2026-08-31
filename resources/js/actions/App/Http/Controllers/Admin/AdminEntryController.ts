import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\AdminEntryController::__invoke
* @see app/Http/Controllers/Admin/AdminEntryController.php:12
* @route '/admin'
*/
const AdminEntryController = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: AdminEntryController.url(options),
    method: 'get',
})

AdminEntryController.definition = {
    methods: ["get","head"],
    url: '/admin',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\AdminEntryController::__invoke
* @see app/Http/Controllers/Admin/AdminEntryController.php:12
* @route '/admin'
*/
AdminEntryController.url = (options?: RouteQueryOptions) => {




    return AdminEntryController.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AdminEntryController::__invoke
* @see app/Http/Controllers/Admin/AdminEntryController.php:12
* @route '/admin'
*/
AdminEntryController.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: AdminEntryController.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminEntryController::__invoke
* @see app/Http/Controllers/Admin/AdminEntryController.php:12
* @route '/admin'
*/
AdminEntryController.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: AdminEntryController.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\AdminEntryController::__invoke
* @see app/Http/Controllers/Admin/AdminEntryController.php:12
* @route '/admin'
*/
const AdminEntryControllerForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: AdminEntryController.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminEntryController::__invoke
* @see app/Http/Controllers/Admin/AdminEntryController.php:12
* @route '/admin'
*/
AdminEntryControllerForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: AdminEntryController.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminEntryController::__invoke
* @see app/Http/Controllers/Admin/AdminEntryController.php:12
* @route '/admin'
*/
AdminEntryControllerForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: AdminEntryController.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

AdminEntryController.form = AdminEntryControllerForm

export default AdminEntryController