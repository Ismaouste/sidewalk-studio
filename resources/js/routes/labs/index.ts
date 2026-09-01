import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \App\Http\Controllers\SiteController::audit
* @see app/Http/Controllers/SiteController.php:345
* @route '/{locale}/labs/audit'
*/
export const audit = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: audit.url(args, options),
    method: 'get',
})

audit.definition = {
    methods: ["get","head"],
    url: '/{locale}/labs/audit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SiteController::audit
* @see app/Http/Controllers/SiteController.php:345
* @route '/{locale}/labs/audit'
*/
audit.url = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return audit.definition.url
            .replace('{locale}', parsedArgs.locale.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\SiteController::audit
* @see app/Http/Controllers/SiteController.php:345
* @route '/{locale}/labs/audit'
*/
audit.get = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: audit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SiteController::audit
* @see app/Http/Controllers/SiteController.php:345
* @route '/{locale}/labs/audit'
*/
audit.head = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: audit.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\SiteController::audit
* @see app/Http/Controllers/SiteController.php:345
* @route '/{locale}/labs/audit'
*/
const auditForm = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: audit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SiteController::audit
* @see app/Http/Controllers/SiteController.php:345
* @route '/{locale}/labs/audit'
*/
auditForm.get = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: audit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SiteController::audit
* @see app/Http/Controllers/SiteController.php:345
* @route '/{locale}/labs/audit'
*/
auditForm.head = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: audit.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

audit.form = auditForm

const labs = {
    audit: Object.assign(audit, audit),
}

export default labs