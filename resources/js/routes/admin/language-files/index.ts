import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\AdminLanguageFileController::index
* @see app/Http/Controllers/Admin/AdminLanguageFileController.php:23
* @route '/admin/language-files'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/language-files',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\AdminLanguageFileController::index
* @see app/Http/Controllers/Admin/AdminLanguageFileController.php:23
* @route '/admin/language-files'
*/
index.url = (options?: RouteQueryOptions) => {




    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AdminLanguageFileController::index
* @see app/Http/Controllers/Admin/AdminLanguageFileController.php:23
* @route '/admin/language-files'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminLanguageFileController::index
* @see app/Http/Controllers/Admin/AdminLanguageFileController.php:23
* @route '/admin/language-files'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\AdminLanguageFileController::index
* @see app/Http/Controllers/Admin/AdminLanguageFileController.php:23
* @route '/admin/language-files'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminLanguageFileController::index
* @see app/Http/Controllers/Admin/AdminLanguageFileController.php:23
* @route '/admin/language-files'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminLanguageFileController::index
* @see app/Http/Controllers/Admin/AdminLanguageFileController.php:23
* @route '/admin/language-files'
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
* @see \App\Http\Controllers\Admin\AdminLanguageFileController::update
* @see app/Http/Controllers/Admin/AdminLanguageFileController.php:30
* @route '/admin/language-files/{key}'
*/
export const update = (args: { key: string | number } | [key: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/admin/language-files/{key}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\Admin\AdminLanguageFileController::update
* @see app/Http/Controllers/Admin/AdminLanguageFileController.php:30
* @route '/admin/language-files/{key}'
*/
update.url = (args: { key: string | number } | [key: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { key: args }
    }


    if (Array.isArray(args)) {
        args = {
            key: args[0],
        }
    }

    args = applyUrlDefaults(args)


    const parsedArgs = {
        key: args.key,
    }

    return update.definition.url
            .replace('{key}', parsedArgs.key.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AdminLanguageFileController::update
* @see app/Http/Controllers/Admin/AdminLanguageFileController.php:30
* @route '/admin/language-files/{key}'
*/
update.put = (args: { key: string | number } | [key: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Admin\AdminLanguageFileController::update
* @see app/Http/Controllers/Admin/AdminLanguageFileController.php:30
* @route '/admin/language-files/{key}'
*/
const updateForm = (args: { key: string | number } | [key: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\AdminLanguageFileController::update
* @see app/Http/Controllers/Admin/AdminLanguageFileController.php:30
* @route '/admin/language-files/{key}'
*/
updateForm.put = (args: { key: string | number } | [key: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

update.form = updateForm



const languageFiles = {
    index: Object.assign(index, index),
    update: Object.assign(update, update),
}

export default languageFiles