import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see routes/web.php:128
* @route '/{locale}/writing/{slug}'
*/
export const show = (args: { locale: string | number, slug: string | number } | [locale: string | number, slug: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/{locale}/writing/{slug}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/web.php:128
* @route '/{locale}/writing/{slug}'
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
* @see routes/web.php:128
* @route '/{locale}/writing/{slug}'
*/
show.get = (args: { locale: string | number, slug: string | number } | [locale: string | number, slug: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see routes/web.php:128
* @route '/{locale}/writing/{slug}'
*/
show.head = (args: { locale: string | number, slug: string | number } | [locale: string | number, slug: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see routes/web.php:128
* @route '/{locale}/writing/{slug}'
*/
const showForm = (args: { locale: string | number, slug: string | number } | [locale: string | number, slug: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see routes/web.php:128
* @route '/{locale}/writing/{slug}'
*/
showForm.get = (args: { locale: string | number, slug: string | number } | [locale: string | number, slug: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see routes/web.php:128
* @route '/{locale}/writing/{slug}'
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

const legacy = {
    show: Object.assign(show, show),
}

export default legacy