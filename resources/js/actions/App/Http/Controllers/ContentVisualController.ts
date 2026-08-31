import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\ContentVisualController::__invoke
* @see app/Http/Controllers/ContentVisualController.php:13
* @route '/content-visuals/{section}/{slug}.svg'
*/
const ContentVisualController = (args: { section: string | number, slug: string | number } | [section: string | number, slug: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ContentVisualController.url(args, options),
    method: 'get',
})

ContentVisualController.definition = {
    methods: ["get","head"],
    url: '/content-visuals/{section}/{slug}.svg',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ContentVisualController::__invoke
* @see app/Http/Controllers/ContentVisualController.php:13
* @route '/content-visuals/{section}/{slug}.svg'
*/
ContentVisualController.url = (args: { section: string | number, slug: string | number } | [section: string | number, slug: string | number ], options?: RouteQueryOptions) => {

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

    return ContentVisualController.definition.url
            .replace('{section}', parsedArgs.section.toString())
            .replace('{slug}', parsedArgs.slug.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ContentVisualController::__invoke
* @see app/Http/Controllers/ContentVisualController.php:13
* @route '/content-visuals/{section}/{slug}.svg'
*/
ContentVisualController.get = (args: { section: string | number, slug: string | number } | [section: string | number, slug: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ContentVisualController.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ContentVisualController::__invoke
* @see app/Http/Controllers/ContentVisualController.php:13
* @route '/content-visuals/{section}/{slug}.svg'
*/
ContentVisualController.head = (args: { section: string | number, slug: string | number } | [section: string | number, slug: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: ContentVisualController.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ContentVisualController::__invoke
* @see app/Http/Controllers/ContentVisualController.php:13
* @route '/content-visuals/{section}/{slug}.svg'
*/
const ContentVisualControllerForm = (args: { section: string | number, slug: string | number } | [section: string | number, slug: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: ContentVisualController.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ContentVisualController::__invoke
* @see app/Http/Controllers/ContentVisualController.php:13
* @route '/content-visuals/{section}/{slug}.svg'
*/
ContentVisualControllerForm.get = (args: { section: string | number, slug: string | number } | [section: string | number, slug: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: ContentVisualController.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ContentVisualController::__invoke
* @see app/Http/Controllers/ContentVisualController.php:13
* @route '/content-visuals/{section}/{slug}.svg'
*/
ContentVisualControllerForm.head = (args: { section: string | number, slug: string | number } | [section: string | number, slug: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: ContentVisualController.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

ContentVisualController.form = ContentVisualControllerForm

export default ContentVisualController