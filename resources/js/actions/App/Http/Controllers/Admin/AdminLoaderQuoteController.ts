import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\AdminLoaderQuoteController::index
* @see app/Http/Controllers/Admin/AdminLoaderQuoteController.php:23
* @route '/admin/loader-quotes'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/loader-quotes',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\AdminLoaderQuoteController::index
* @see app/Http/Controllers/Admin/AdminLoaderQuoteController.php:23
* @route '/admin/loader-quotes'
*/
index.url = (options?: RouteQueryOptions) => {




    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AdminLoaderQuoteController::index
* @see app/Http/Controllers/Admin/AdminLoaderQuoteController.php:23
* @route '/admin/loader-quotes'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminLoaderQuoteController::index
* @see app/Http/Controllers/Admin/AdminLoaderQuoteController.php:23
* @route '/admin/loader-quotes'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\AdminLoaderQuoteController::index
* @see app/Http/Controllers/Admin/AdminLoaderQuoteController.php:23
* @route '/admin/loader-quotes'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminLoaderQuoteController::index
* @see app/Http/Controllers/Admin/AdminLoaderQuoteController.php:23
* @route '/admin/loader-quotes'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminLoaderQuoteController::index
* @see app/Http/Controllers/Admin/AdminLoaderQuoteController.php:23
* @route '/admin/loader-quotes'
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

/**
* @see \App\Http\Controllers\Admin\AdminLoaderQuoteController::update
* @see app/Http/Controllers/Admin/AdminLoaderQuoteController.php:30
* @route '/admin/loader-quotes'
*/
export const update = (options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/admin/loader-quotes',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\Admin\AdminLoaderQuoteController::update
* @see app/Http/Controllers/Admin/AdminLoaderQuoteController.php:30
* @route '/admin/loader-quotes'
*/
update.url = (options?: RouteQueryOptions) => {




    return update.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AdminLoaderQuoteController::update
* @see app/Http/Controllers/Admin/AdminLoaderQuoteController.php:30
* @route '/admin/loader-quotes'
*/
update.put = (options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Admin\AdminLoaderQuoteController::update
* @see app/Http/Controllers/Admin/AdminLoaderQuoteController.php:30
* @route '/admin/loader-quotes'
*/
const updateForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\AdminLoaderQuoteController::update
* @see app/Http/Controllers/Admin/AdminLoaderQuoteController.php:30
* @route '/admin/loader-quotes'
*/
updateForm.put = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

update.form = updateForm

const AdminLoaderQuoteController = { index, update }

export default AdminLoaderQuoteController