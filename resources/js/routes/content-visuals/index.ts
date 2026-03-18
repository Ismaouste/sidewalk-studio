import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \App\Http\Controllers\ContentVisualController::__invoke
* @see app/Http/Controllers/ContentVisualController.php:13
* @route '/content-visuals/{section}/{slug}.svg'
*/
export const show = (args: { section: string | number, slug: string | number } | [section: string | number, slug: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/content-visuals/{section}/{slug}.svg',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ContentVisualController::__invoke
* @see app/Http/Controllers/ContentVisualController.php:13
* @route '/content-visuals/{section}/{slug}.svg'
*/
show.url = (args: { section: string | number, slug: string | number } | [section: string | number, slug: string | number ], options?: RouteQueryOptions) => {

    if (Array.isArray(args)) {
        args = {
            section: args[0],
            slug: args[1],
        }
    }

    args = applyUrlDefaults(args)


    const parsedArgs = {
        section: args.section,
        slug: args.slug,
    }

    return show.definition.url
            .replace('{section}', parsedArgs.section.toString())
            .replace('{slug}', parsedArgs.slug.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ContentVisualController::__invoke
* @see app/Http/Controllers/ContentVisualController.php:13
* @route '/content-visuals/{section}/{slug}.svg'
*/
show.get = (args: { section: string | number, slug: string | number } | [section: string | number, slug: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ContentVisualController::__invoke
* @see app/Http/Controllers/ContentVisualController.php:13
* @route '/content-visuals/{section}/{slug}.svg'
*/
show.head = (args: { section: string | number, slug: string | number } | [section: string | number, slug: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ContentVisualController::__invoke
* @see app/Http/Controllers/ContentVisualController.php:13
* @route '/content-visuals/{section}/{slug}.svg'
*/
const showForm = (args: { section: string | number, slug: string | number } | [section: string | number, slug: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ContentVisualController::__invoke
* @see app/Http/Controllers/ContentVisualController.php:13
* @route '/content-visuals/{section}/{slug}.svg'
*/
showForm.get = (args: { section: string | number, slug: string | number } | [section: string | number, slug: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ContentVisualController::__invoke
* @see app/Http/Controllers/ContentVisualController.php:13
* @route '/content-visuals/{section}/{slug}.svg'
*/
showForm.head = (args: { section: string | number, slug: string | number } | [section: string | number, slug: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

show.form = showForm



const contentVisuals = {
    show: Object.assign(show, show),
}

export default contentVisuals