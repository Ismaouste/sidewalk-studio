import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \App\Http\Controllers\CaseStudyController::index
* @see app/Http/Controllers/CaseStudyController.php:18
* @route '/{locale}/case-studies'
*/
export const index = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/{locale}/case-studies',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\CaseStudyController::index
* @see app/Http/Controllers/CaseStudyController.php:18
* @route '/{locale}/case-studies'
*/
index.url = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return index.definition.url
            .replace('{locale}', parsedArgs.locale.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\CaseStudyController::index
* @see app/Http/Controllers/CaseStudyController.php:18
* @route '/{locale}/case-studies'
*/
index.get = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CaseStudyController::index
* @see app/Http/Controllers/CaseStudyController.php:18
* @route '/{locale}/case-studies'
*/
index.head = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\CaseStudyController::index
* @see app/Http/Controllers/CaseStudyController.php:18
* @route '/{locale}/case-studies'
*/
const indexForm = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CaseStudyController::index
* @see app/Http/Controllers/CaseStudyController.php:18
* @route '/{locale}/case-studies'
*/
indexForm.get = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CaseStudyController::index
* @see app/Http/Controllers/CaseStudyController.php:18
* @route '/{locale}/case-studies'
*/
indexForm.head = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

index.form = indexForm

/**
* @see \App\Http\Controllers\CaseStudyController::show
* @see app/Http/Controllers/CaseStudyController.php:36
* @route '/{locale}/case-studies/{slug}'
*/
export const show = (args: { locale: string | number, slug: string | number } | [locale: string | number, slug: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/{locale}/case-studies/{slug}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\CaseStudyController::show
* @see app/Http/Controllers/CaseStudyController.php:36
* @route '/{locale}/case-studies/{slug}'
*/
show.url = (args: { locale: string | number, slug: string | number } | [locale: string | number, slug: string | number ], options?: RouteQueryOptions) => {

    if (Array.isArray(args)) {
        args = {
            locale: args[0],
            slug: args[1],
        }
    }

    args = applyUrlDefaults(args)


    const parsedArgs = {
        locale: args.locale,
        slug: args.slug,
    }

    return show.definition.url
            .replace('{locale}', parsedArgs.locale.toString())
            .replace('{slug}', parsedArgs.slug.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\CaseStudyController::show
* @see app/Http/Controllers/CaseStudyController.php:36
* @route '/{locale}/case-studies/{slug}'
*/
show.get = (args: { locale: string | number, slug: string | number } | [locale: string | number, slug: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CaseStudyController::show
* @see app/Http/Controllers/CaseStudyController.php:36
* @route '/{locale}/case-studies/{slug}'
*/
show.head = (args: { locale: string | number, slug: string | number } | [locale: string | number, slug: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\CaseStudyController::show
* @see app/Http/Controllers/CaseStudyController.php:36
* @route '/{locale}/case-studies/{slug}'
*/
const showForm = (args: { locale: string | number, slug: string | number } | [locale: string | number, slug: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CaseStudyController::show
* @see app/Http/Controllers/CaseStudyController.php:36
* @route '/{locale}/case-studies/{slug}'
*/
showForm.get = (args: { locale: string | number, slug: string | number } | [locale: string | number, slug: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CaseStudyController::show
* @see app/Http/Controllers/CaseStudyController.php:36
* @route '/{locale}/case-studies/{slug}'
*/
showForm.head = (args: { locale: string | number, slug: string | number } | [locale: string | number, slug: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

show.form = showForm

const caseStudies = {
    index: Object.assign(index, index),
show: Object.assign(show, show),
}

export default caseStudies