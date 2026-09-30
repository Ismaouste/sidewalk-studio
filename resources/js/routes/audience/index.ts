import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
/**
* @see \App\Http\Controllers\AudiencePingController::__invoke
* @see app/Http/Controllers/AudiencePingController.php:23
* @route '/audience'
*/
export const ping = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: ping.url(options),
    method: 'post',
})

ping.definition = {
    methods: ["post"],
    url: '/audience',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\AudiencePingController::__invoke
* @see app/Http/Controllers/AudiencePingController.php:23
* @route '/audience'
*/
ping.url = (options?: RouteQueryOptions) => {




    return ping.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AudiencePingController::__invoke
* @see app/Http/Controllers/AudiencePingController.php:23
* @route '/audience'
*/
ping.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: ping.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\AudiencePingController::__invoke
* @see app/Http/Controllers/AudiencePingController.php:23
* @route '/audience'
*/
const pingForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: ping.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\AudiencePingController::__invoke
* @see app/Http/Controllers/AudiencePingController.php:23
* @route '/audience'
*/
pingForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: ping.url(options),
    method: 'post',
})

ping.form = pingForm

const audience = {
    ping: Object.assign(ping, ping),
}

export default audience