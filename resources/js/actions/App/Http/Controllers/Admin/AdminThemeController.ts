import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\AdminThemeController::edit
* @see app/Http/Controllers/Admin/AdminThemeController.php:23
* @route '/admin/theme'
*/
export const edit = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/admin/theme',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\AdminThemeController::edit
* @see app/Http/Controllers/Admin/AdminThemeController.php:23
* @route '/admin/theme'
*/
edit.url = (options?: RouteQueryOptions) => {




    return edit.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AdminThemeController::edit
* @see app/Http/Controllers/Admin/AdminThemeController.php:23
* @route '/admin/theme'
*/
edit.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminThemeController::edit
* @see app/Http/Controllers/Admin/AdminThemeController.php:23
* @route '/admin/theme'
*/
edit.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\AdminThemeController::edit
* @see app/Http/Controllers/Admin/AdminThemeController.php:23
* @route '/admin/theme'
*/
const editForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminThemeController::edit
* @see app/Http/Controllers/Admin/AdminThemeController.php:23
* @route '/admin/theme'
*/
editForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminThemeController::edit
* @see app/Http/Controllers/Admin/AdminThemeController.php:23
* @route '/admin/theme'
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
* @see \App\Http\Controllers\Admin\AdminThemeController::update
* @see app/Http/Controllers/Admin/AdminThemeController.php:34
* @route '/admin/theme'
*/
export const update = (options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/admin/theme',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\Admin\AdminThemeController::update
* @see app/Http/Controllers/Admin/AdminThemeController.php:34
* @route '/admin/theme'
*/
update.url = (options?: RouteQueryOptions) => {




    return update.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AdminThemeController::update
* @see app/Http/Controllers/Admin/AdminThemeController.php:34
* @route '/admin/theme'
*/
update.put = (options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Admin\AdminThemeController::update
* @see app/Http/Controllers/Admin/AdminThemeController.php:34
* @route '/admin/theme'
*/
const updateForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\AdminThemeController::update
* @see app/Http/Controllers/Admin/AdminThemeController.php:34
* @route '/admin/theme'
*/
updateForm.put = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

update.form = updateForm

/**
* @see \App\Http\Controllers\Admin\AdminThemeController::rebuild
* @see app/Http/Controllers/Admin/AdminThemeController.php:70
* @route '/admin/theme/rebuild'
*/
export const rebuild = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: rebuild.url(options),
    method: 'post',
})

rebuild.definition = {
    methods: ["post"],
    url: '/admin/theme/rebuild',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\AdminThemeController::rebuild
* @see app/Http/Controllers/Admin/AdminThemeController.php:70
* @route '/admin/theme/rebuild'
*/
rebuild.url = (options?: RouteQueryOptions) => {




    return rebuild.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AdminThemeController::rebuild
* @see app/Http/Controllers/Admin/AdminThemeController.php:70
* @route '/admin/theme/rebuild'
*/
rebuild.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: rebuild.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\AdminThemeController::rebuild
* @see app/Http/Controllers/Admin/AdminThemeController.php:70
* @route '/admin/theme/rebuild'
*/
const rebuildForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: rebuild.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\AdminThemeController::rebuild
* @see app/Http/Controllers/Admin/AdminThemeController.php:70
* @route '/admin/theme/rebuild'
*/
rebuildForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: rebuild.url(options),
    method: 'post',
})

rebuild.form = rebuildForm

const AdminThemeController = { edit, update, rebuild }

export default AdminThemeController