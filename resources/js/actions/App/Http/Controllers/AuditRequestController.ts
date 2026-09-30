import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\AuditRequestController::__invoke
* @see app/Http/Controllers/AuditRequestController.php:15
* @route '/labs/audit'
*/
const AuditRequestController = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: AuditRequestController.url(options),
    method: 'post',
})

AuditRequestController.definition = {
    methods: ["post"],
    url: '/labs/audit',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\AuditRequestController::__invoke
* @see app/Http/Controllers/AuditRequestController.php:15
* @route '/labs/audit'
*/
AuditRequestController.url = (options?: RouteQueryOptions) => {




    return AuditRequestController.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AuditRequestController::__invoke
* @see app/Http/Controllers/AuditRequestController.php:15
* @route '/labs/audit'
*/
AuditRequestController.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: AuditRequestController.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\AuditRequestController::__invoke
* @see app/Http/Controllers/AuditRequestController.php:15
* @route '/labs/audit'
*/
const AuditRequestControllerForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: AuditRequestController.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\AuditRequestController::__invoke
* @see app/Http/Controllers/AuditRequestController.php:15
* @route '/labs/audit'
*/
AuditRequestControllerForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: AuditRequestController.url(options),
    method: 'post',
})

AuditRequestController.form = AuditRequestControllerForm

export default AuditRequestController