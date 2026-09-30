import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\Auth\AdminOnboardingController::create
* @see app/Http/Controllers/Admin/Auth/AdminOnboardingController.php:22
* @route '/admin/onboarding'
*/
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/admin/onboarding',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\Auth\AdminOnboardingController::create
* @see app/Http/Controllers/Admin/Auth/AdminOnboardingController.php:22
* @route '/admin/onboarding'
*/
create.url = (options?: RouteQueryOptions) => {




    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\Auth\AdminOnboardingController::create
* @see app/Http/Controllers/Admin/Auth/AdminOnboardingController.php:22
* @route '/admin/onboarding'
*/
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\Auth\AdminOnboardingController::create
* @see app/Http/Controllers/Admin/Auth/AdminOnboardingController.php:22
* @route '/admin/onboarding'
*/
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\Auth\AdminOnboardingController::create
* @see app/Http/Controllers/Admin/Auth/AdminOnboardingController.php:22
* @route '/admin/onboarding'
*/
const createForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\Auth\AdminOnboardingController::create
* @see app/Http/Controllers/Admin/Auth/AdminOnboardingController.php:22
* @route '/admin/onboarding'
*/
createForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\Auth\AdminOnboardingController::create
* @see app/Http/Controllers/Admin/Auth/AdminOnboardingController.php:22
* @route '/admin/onboarding'
*/
createForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

create.form = createForm

/**
* @see \App\Http\Controllers\Admin\Auth\AdminOnboardingController::store
* @see app/Http/Controllers/Admin/Auth/AdminOnboardingController.php:33
* @route '/admin/onboarding'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/admin/onboarding',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\Auth\AdminOnboardingController::store
* @see app/Http/Controllers/Admin/Auth/AdminOnboardingController.php:33
* @route '/admin/onboarding'
*/
store.url = (options?: RouteQueryOptions) => {




    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\Auth\AdminOnboardingController::store
* @see app/Http/Controllers/Admin/Auth/AdminOnboardingController.php:33
* @route '/admin/onboarding'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\Auth\AdminOnboardingController::store
* @see app/Http/Controllers/Admin/Auth/AdminOnboardingController.php:33
* @route '/admin/onboarding'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\Auth\AdminOnboardingController::store
* @see app/Http/Controllers/Admin/Auth/AdminOnboardingController.php:33
* @route '/admin/onboarding'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

const AdminOnboardingController = { create, store }

export default AdminOnboardingController