import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\AdminBrandingController::edit
* @see app/Http/Controllers/Admin/AdminBrandingController.php:22
* @route '/admin/branding'
*/
export const edit = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/admin/branding',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\AdminBrandingController::edit
* @see app/Http/Controllers/Admin/AdminBrandingController.php:22
* @route '/admin/branding'
*/
edit.url = (options?: RouteQueryOptions) => {




    return edit.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AdminBrandingController::edit
* @see app/Http/Controllers/Admin/AdminBrandingController.php:22
* @route '/admin/branding'
*/
edit.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminBrandingController::edit
* @see app/Http/Controllers/Admin/AdminBrandingController.php:22
* @route '/admin/branding'
*/
edit.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\AdminBrandingController::edit
* @see app/Http/Controllers/Admin/AdminBrandingController.php:22
* @route '/admin/branding'
*/
const editForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminBrandingController::edit
* @see app/Http/Controllers/Admin/AdminBrandingController.php:22
* @route '/admin/branding'
*/
editForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminBrandingController::edit
* @see app/Http/Controllers/Admin/AdminBrandingController.php:22
* @route '/admin/branding'
*/
editForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

edit.form = editForm

/**
* @see \App\Http\Controllers\Admin\AdminBrandingController::update
* @see app/Http/Controllers/Admin/AdminBrandingController.php:35
* @route '/admin/branding'
*/
export const update = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: update.url(options),
    method: 'post',
})

update.definition = {
    methods: ["post"],
    url: '/admin/branding',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\AdminBrandingController::update
* @see app/Http/Controllers/Admin/AdminBrandingController.php:35
* @route '/admin/branding'
*/
update.url = (options?: RouteQueryOptions) => {




    return update.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AdminBrandingController::update
* @see app/Http/Controllers/Admin/AdminBrandingController.php:35
* @route '/admin/branding'
*/
update.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: update.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\AdminBrandingController::update
* @see app/Http/Controllers/Admin/AdminBrandingController.php:35
* @route '/admin/branding'
*/
const updateForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\AdminBrandingController::update
* @see app/Http/Controllers/Admin/AdminBrandingController.php:35
* @route '/admin/branding'
*/
updateForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(options),
    method: 'post',
})

update.form = updateForm

const AdminBrandingController = { edit, update }

export default AdminBrandingController