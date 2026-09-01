import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \App\Http\Controllers\SiteController::legacy
* @see app/Http/Controllers/SiteController.php:153
* @route '/{locale}/projects'
*/
export const legacy = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: legacy.url(args, options),
    method: 'get',
})

legacy.definition = {
    methods: ["get","head"],
    url: '/{locale}/projects',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SiteController::legacy
* @see app/Http/Controllers/SiteController.php:153
* @route '/{locale}/projects'
*/
legacy.url = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { locale: args }
    }


    if (Array.isArray(args)) {
        args = {
            locale: args[0],
        }
    }

    args = applyUrlDefaults(args)


    const parsedArgs = {
        locale: args.locale,
    }

    return legacy.definition.url
            .replace('{locale}', parsedArgs.locale.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\SiteController::legacy
* @see app/Http/Controllers/SiteController.php:153
* @route '/{locale}/projects'
*/
legacy.get = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: legacy.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SiteController::legacy
* @see app/Http/Controllers/SiteController.php:153
* @route '/{locale}/projects'
*/
legacy.head = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: legacy.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\SiteController::legacy
* @see app/Http/Controllers/SiteController.php:153
* @route '/{locale}/projects'
*/
const legacyForm = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: legacy.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SiteController::legacy
* @see app/Http/Controllers/SiteController.php:153
* @route '/{locale}/projects'
*/
legacyForm.get = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: legacy.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SiteController::legacy
* @see app/Http/Controllers/SiteController.php:153
* @route '/{locale}/projects'
*/
legacyForm.head = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: legacy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

legacy.form = legacyForm

const projects = {
    legacy: Object.assign(legacy, legacy),
}

export default projects