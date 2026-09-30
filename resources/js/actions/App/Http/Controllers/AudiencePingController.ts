import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\AudiencePingController::__invoke
* @see app/Http/Controllers/AudiencePingController.php:23
* @route '/audience'
*/
const AudiencePingController = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: AudiencePingController.url(options),
    method: 'post',
})

AudiencePingController.definition = {
    methods: ["post"],
    url: '/audience',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\AudiencePingController::__invoke
* @see app/Http/Controllers/AudiencePingController.php:23
* @route '/audience'
*/
AudiencePingController.url = (options?: RouteQueryOptions) => {




    return AudiencePingController.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AudiencePingController::__invoke
* @see app/Http/Controllers/AudiencePingController.php:23
* @route '/audience'
*/
AudiencePingController.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: AudiencePingController.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\AudiencePingController::__invoke
* @see app/Http/Controllers/AudiencePingController.php:23
* @route '/audience'
*/
const AudiencePingControllerForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: AudiencePingController.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\AudiencePingController::__invoke
* @see app/Http/Controllers/AudiencePingController.php:23
* @route '/audience'
*/
AudiencePingControllerForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: AudiencePingController.url(options),
    method: 'post',
})

AudiencePingController.form = AudiencePingControllerForm

export default AudiencePingController