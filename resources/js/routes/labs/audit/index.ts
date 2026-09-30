import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\AuditRequestController::__invoke
* @see app/Http/Controllers/AuditRequestController.php:15
* @route '/labs/audit'
*/
export const request = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: request.url(options),
    method: 'post',
})

request.definition = {
    methods: ["post"],
    url: '/labs/audit',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\AuditRequestController::__invoke
* @see app/Http/Controllers/AuditRequestController.php:15
* @route '/labs/audit'
*/
request.url = (options?: RouteQueryOptions) => {




    return request.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AuditRequestController::__invoke
* @see app/Http/Controllers/AuditRequestController.php:15
* @route '/labs/audit'
*/
request.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: request.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\AuditRequestController::__invoke
* @see app/Http/Controllers/AuditRequestController.php:15
* @route '/labs/audit'
*/
const requestForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: request.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\AuditRequestController::__invoke
* @see app/Http/Controllers/AuditRequestController.php:15
* @route '/labs/audit'
*/
requestForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: request.url(options),
    method: 'post',
})

request.form = requestForm
