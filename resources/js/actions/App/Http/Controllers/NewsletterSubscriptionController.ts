import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\NewsletterSubscriptionController::__invoke
* @see app/Http/Controllers/NewsletterSubscriptionController.php:12
* @route '/newsletter'
*/
const NewsletterSubscriptionController = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: NewsletterSubscriptionController.url(options),
    method: 'post',
})

NewsletterSubscriptionController.definition = {
    methods: ["post"],
    url: '/newsletter',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\NewsletterSubscriptionController::__invoke
* @see app/Http/Controllers/NewsletterSubscriptionController.php:12
* @route '/newsletter'
*/
NewsletterSubscriptionController.url = (options?: RouteQueryOptions) => {




    return NewsletterSubscriptionController.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\NewsletterSubscriptionController::__invoke
* @see app/Http/Controllers/NewsletterSubscriptionController.php:12
* @route '/newsletter'
*/
NewsletterSubscriptionController.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: NewsletterSubscriptionController.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\NewsletterSubscriptionController::__invoke
* @see app/Http/Controllers/NewsletterSubscriptionController.php:12
* @route '/newsletter'
*/
const NewsletterSubscriptionControllerForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: NewsletterSubscriptionController.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\NewsletterSubscriptionController::__invoke
* @see app/Http/Controllers/NewsletterSubscriptionController.php:12
* @route '/newsletter'
*/
NewsletterSubscriptionControllerForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: NewsletterSubscriptionController.url(options),
    method: 'post',
})

NewsletterSubscriptionController.form = NewsletterSubscriptionControllerForm

export default NewsletterSubscriptionController