import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\LlmsController::__invoke
 * @see app/Http/Controllers/LlmsController.php:16
 * @route '/llms.txt'
 */
const LlmsController = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: LlmsController.url(options),
    method: 'get',
})

LlmsController.definition = {
    methods: ["get","head"],
    url: '/llms.txt',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\LlmsController::__invoke
 * @see app/Http/Controllers/LlmsController.php:16
 * @route '/llms.txt'
 */
LlmsController.url = (options?: RouteQueryOptions) => {
    return LlmsController.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\LlmsController::__invoke
 * @see app/Http/Controllers/LlmsController.php:16
 * @route '/llms.txt'
 */
LlmsController.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: LlmsController.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\LlmsController::__invoke
 * @see app/Http/Controllers/LlmsController.php:16
 * @route '/llms.txt'
 */
LlmsController.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: LlmsController.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\LlmsController::__invoke
 * @see app/Http/Controllers/LlmsController.php:16
 * @route '/llms.txt'
 */
    const LlmsControllerForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: LlmsController.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\LlmsController::__invoke
 * @see app/Http/Controllers/LlmsController.php:16
 * @route '/llms.txt'
 */
        LlmsControllerForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: LlmsController.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\LlmsController::__invoke
 * @see app/Http/Controllers/LlmsController.php:16
 * @route '/llms.txt'
 */
        LlmsControllerForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: LlmsController.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    LlmsController.form = LlmsControllerForm
export default LlmsController