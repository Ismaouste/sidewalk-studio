import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../wayfinder'
/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/about'
*/
const RedirectController535fd093ca1d5254af5dc12ac208e8d5 = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: RedirectController535fd093ca1d5254af5dc12ac208e8d5.url(options),
    method: 'get',
})

RedirectController535fd093ca1d5254af5dc12ac208e8d5.definition = {
    methods: ["get","head","post","put","patch","delete","options"],
    url: '/about',
} satisfies RouteDefinition<["get","head","post","put","patch","delete","options"]>

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/about'
*/
RedirectController535fd093ca1d5254af5dc12ac208e8d5.url = (options?: RouteQueryOptions) => {




    return RedirectController535fd093ca1d5254af5dc12ac208e8d5.definition.url + queryParams(options)
}

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/about'
*/
RedirectController535fd093ca1d5254af5dc12ac208e8d5.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: RedirectController535fd093ca1d5254af5dc12ac208e8d5.url(options),
    method: 'get',
})

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/about'
*/
RedirectController535fd093ca1d5254af5dc12ac208e8d5.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: RedirectController535fd093ca1d5254af5dc12ac208e8d5.url(options),
    method: 'head',
})

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/about'
*/
RedirectController535fd093ca1d5254af5dc12ac208e8d5.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: RedirectController535fd093ca1d5254af5dc12ac208e8d5.url(options),
    method: 'post',
})

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/about'
*/
RedirectController535fd093ca1d5254af5dc12ac208e8d5.put = (options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: RedirectController535fd093ca1d5254af5dc12ac208e8d5.url(options),
    method: 'put',
})

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/about'
*/
RedirectController535fd093ca1d5254af5dc12ac208e8d5.patch = (options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: RedirectController535fd093ca1d5254af5dc12ac208e8d5.url(options),
    method: 'patch',
})

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/about'
*/
RedirectController535fd093ca1d5254af5dc12ac208e8d5.delete = (options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: RedirectController535fd093ca1d5254af5dc12ac208e8d5.url(options),
    method: 'delete',
})

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/about'
*/
RedirectController535fd093ca1d5254af5dc12ac208e8d5.options = (options?: RouteQueryOptions): RouteDefinition<'options'> => ({
    url: RedirectController535fd093ca1d5254af5dc12ac208e8d5.url(options),
    method: 'options',
})

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/about'
*/
const RedirectController535fd093ca1d5254af5dc12ac208e8d5Form = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: RedirectController535fd093ca1d5254af5dc12ac208e8d5.url(options),
    method: 'get',
})

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/about'
*/
RedirectController535fd093ca1d5254af5dc12ac208e8d5Form.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: RedirectController535fd093ca1d5254af5dc12ac208e8d5.url(options),
    method: 'get',
})

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/about'
*/
RedirectController535fd093ca1d5254af5dc12ac208e8d5Form.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: RedirectController535fd093ca1d5254af5dc12ac208e8d5.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/about'
*/
RedirectController535fd093ca1d5254af5dc12ac208e8d5Form.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: RedirectController535fd093ca1d5254af5dc12ac208e8d5.url(options),
    method: 'post',
})

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/about'
*/
RedirectController535fd093ca1d5254af5dc12ac208e8d5Form.put = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: RedirectController535fd093ca1d5254af5dc12ac208e8d5.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/about'
*/
RedirectController535fd093ca1d5254af5dc12ac208e8d5Form.patch = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: RedirectController535fd093ca1d5254af5dc12ac208e8d5.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/about'
*/
RedirectController535fd093ca1d5254af5dc12ac208e8d5Form.delete = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: RedirectController535fd093ca1d5254af5dc12ac208e8d5.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/about'
*/
RedirectController535fd093ca1d5254af5dc12ac208e8d5Form.options = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: RedirectController535fd093ca1d5254af5dc12ac208e8d5.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'OPTIONS',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

RedirectController535fd093ca1d5254af5dc12ac208e8d5.form = RedirectController535fd093ca1d5254af5dc12ac208e8d5Form
/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/writing'
*/
const RedirectControllerc362ca439221c918105b1554ff4e7e95 = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: RedirectControllerc362ca439221c918105b1554ff4e7e95.url(options),
    method: 'get',
})

RedirectControllerc362ca439221c918105b1554ff4e7e95.definition = {
    methods: ["get","head","post","put","patch","delete","options"],
    url: '/writing',
} satisfies RouteDefinition<["get","head","post","put","patch","delete","options"]>

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/writing'
*/
RedirectControllerc362ca439221c918105b1554ff4e7e95.url = (options?: RouteQueryOptions) => {




    return RedirectControllerc362ca439221c918105b1554ff4e7e95.definition.url + queryParams(options)
}

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/writing'
*/
RedirectControllerc362ca439221c918105b1554ff4e7e95.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: RedirectControllerc362ca439221c918105b1554ff4e7e95.url(options),
    method: 'get',
})

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/writing'
*/
RedirectControllerc362ca439221c918105b1554ff4e7e95.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: RedirectControllerc362ca439221c918105b1554ff4e7e95.url(options),
    method: 'head',
})

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/writing'
*/
RedirectControllerc362ca439221c918105b1554ff4e7e95.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: RedirectControllerc362ca439221c918105b1554ff4e7e95.url(options),
    method: 'post',
})

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/writing'
*/
RedirectControllerc362ca439221c918105b1554ff4e7e95.put = (options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: RedirectControllerc362ca439221c918105b1554ff4e7e95.url(options),
    method: 'put',
})

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/writing'
*/
RedirectControllerc362ca439221c918105b1554ff4e7e95.patch = (options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: RedirectControllerc362ca439221c918105b1554ff4e7e95.url(options),
    method: 'patch',
})

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/writing'
*/
RedirectControllerc362ca439221c918105b1554ff4e7e95.delete = (options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: RedirectControllerc362ca439221c918105b1554ff4e7e95.url(options),
    method: 'delete',
})

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/writing'
*/
RedirectControllerc362ca439221c918105b1554ff4e7e95.options = (options?: RouteQueryOptions): RouteDefinition<'options'> => ({
    url: RedirectControllerc362ca439221c918105b1554ff4e7e95.url(options),
    method: 'options',
})

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/writing'
*/
const RedirectControllerc362ca439221c918105b1554ff4e7e95Form = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: RedirectControllerc362ca439221c918105b1554ff4e7e95.url(options),
    method: 'get',
})

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/writing'
*/
RedirectControllerc362ca439221c918105b1554ff4e7e95Form.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: RedirectControllerc362ca439221c918105b1554ff4e7e95.url(options),
    method: 'get',
})

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/writing'
*/
RedirectControllerc362ca439221c918105b1554ff4e7e95Form.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: RedirectControllerc362ca439221c918105b1554ff4e7e95.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/writing'
*/
RedirectControllerc362ca439221c918105b1554ff4e7e95Form.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: RedirectControllerc362ca439221c918105b1554ff4e7e95.url(options),
    method: 'post',
})

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/writing'
*/
RedirectControllerc362ca439221c918105b1554ff4e7e95Form.put = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: RedirectControllerc362ca439221c918105b1554ff4e7e95.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/writing'
*/
RedirectControllerc362ca439221c918105b1554ff4e7e95Form.patch = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: RedirectControllerc362ca439221c918105b1554ff4e7e95.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/writing'
*/
RedirectControllerc362ca439221c918105b1554ff4e7e95Form.delete = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: RedirectControllerc362ca439221c918105b1554ff4e7e95.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/writing'
*/
RedirectControllerc362ca439221c918105b1554ff4e7e95Form.options = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: RedirectControllerc362ca439221c918105b1554ff4e7e95.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'OPTIONS',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

RedirectControllerc362ca439221c918105b1554ff4e7e95.form = RedirectControllerc362ca439221c918105b1554ff4e7e95Form

const RedirectController = {
    '/about': RedirectController535fd093ca1d5254af5dc12ac208e8d5,
    '/writing': RedirectControllerc362ca439221c918105b1554ff4e7e95,
}




export default RedirectController