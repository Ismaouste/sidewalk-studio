import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \App\Http\Controllers\NewsletterSubscriptionController::__invoke
* @see app/Http/Controllers/NewsletterSubscriptionController.php:12
* @route '/newsletter'
*/
export const subscribe = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: subscribe.url(options),
    method: 'post',
})

subscribe.definition = {
    methods: ["post"],
    url: '/newsletter',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\NewsletterSubscriptionController::__invoke
* @see app/Http/Controllers/NewsletterSubscriptionController.php:12
* @route '/newsletter'
*/
subscribe.url = (options?: RouteQueryOptions) => {




    return subscribe.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\NewsletterSubscriptionController::__invoke
* @see app/Http/Controllers/NewsletterSubscriptionController.php:12
* @route '/newsletter'
*/
subscribe.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: subscribe.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\NewsletterSubscriptionController::__invoke
* @see app/Http/Controllers/NewsletterSubscriptionController.php:12
* @route '/newsletter'
*/
const subscribeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: subscribe.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\NewsletterSubscriptionController::__invoke
* @see app/Http/Controllers/NewsletterSubscriptionController.php:12
* @route '/newsletter'
*/
subscribeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: subscribe.url(options),
    method: 'post',
})

subscribe.form = subscribeForm

/**
* @see \App\Http\Controllers\SiteController::confirmed
* @see app/Http/Controllers/SiteController.php:364
* @route '/{locale}/newsletter/confirmed'
*/
export const confirmed = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: confirmed.url(args, options),
    method: 'get',
})

confirmed.definition = {
    methods: ["get","head"],
    url: '/{locale}/newsletter/confirmed',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SiteController::confirmed
* @see app/Http/Controllers/SiteController.php:364
* @route '/{locale}/newsletter/confirmed'
*/
confirmed.url = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return confirmed.definition.url
            .replace('{locale}', parsedArgs.locale.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\SiteController::confirmed
* @see app/Http/Controllers/SiteController.php:364
* @route '/{locale}/newsletter/confirmed'
*/
confirmed.get = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: confirmed.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SiteController::confirmed
* @see app/Http/Controllers/SiteController.php:364
* @route '/{locale}/newsletter/confirmed'
*/
confirmed.head = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: confirmed.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\SiteController::confirmed
* @see app/Http/Controllers/SiteController.php:364
* @route '/{locale}/newsletter/confirmed'
*/
const confirmedForm = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: confirmed.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SiteController::confirmed
* @see app/Http/Controllers/SiteController.php:364
* @route '/{locale}/newsletter/confirmed'
*/
confirmedForm.get = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: confirmed.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SiteController::confirmed
* @see app/Http/Controllers/SiteController.php:364
* @route '/{locale}/newsletter/confirmed'
*/
confirmedForm.head = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: confirmed.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

confirmed.form = confirmedForm

const newsletter = {
    subscribe: Object.assign(subscribe, subscribe),
confirmed: Object.assign(confirmed, confirmed),
}

export default newsletter