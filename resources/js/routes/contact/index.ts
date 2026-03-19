import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \App\Http\Controllers\ContactSubmissionController::store
* @see app/Http/Controllers/ContactSubmissionController.php:12
* @route '/{locale}/contact'
*/
export const store = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/{locale}/contact',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\ContactSubmissionController::store
* @see app/Http/Controllers/ContactSubmissionController.php:12
* @route '/{locale}/contact'
*/
store.url = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return store.definition.url
            .replace('{locale}', parsedArgs.locale.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ContactSubmissionController::store
* @see app/Http/Controllers/ContactSubmissionController.php:12
* @route '/{locale}/contact'
*/
store.post = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ContactSubmissionController::store
* @see app/Http/Controllers/ContactSubmissionController.php:12
* @route '/{locale}/contact'
*/
const storeForm = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ContactSubmissionController::store
* @see app/Http/Controllers/ContactSubmissionController.php:12
* @route '/{locale}/contact'
*/
storeForm.post = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

store.form = storeForm



const contact = {
    store: Object.assign(store, store),
}

export default contact