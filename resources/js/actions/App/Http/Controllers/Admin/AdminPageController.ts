import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\AdminPageController::index
* @see app/Http/Controllers/Admin/AdminPageController.php:24
* @route '/admin/pages'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/pages',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\AdminPageController::index
* @see app/Http/Controllers/Admin/AdminPageController.php:24
* @route '/admin/pages'
*/
index.url = (options?: RouteQueryOptions) => {




    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AdminPageController::index
* @see app/Http/Controllers/Admin/AdminPageController.php:24
* @route '/admin/pages'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminPageController::index
* @see app/Http/Controllers/Admin/AdminPageController.php:24
* @route '/admin/pages'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\AdminPageController::index
* @see app/Http/Controllers/Admin/AdminPageController.php:24
* @route '/admin/pages'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminPageController::index
* @see app/Http/Controllers/Admin/AdminPageController.php:24
* @route '/admin/pages'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminPageController::index
* @see app/Http/Controllers/Admin/AdminPageController.php:24
* @route '/admin/pages'
*/
indexForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

index.form = indexForm

/**
* @see \App\Http\Controllers\Admin\AdminPageController::edit
* @see app/Http/Controllers/Admin/AdminPageController.php:31
* @route '/admin/pages/{page}/{locale}'
*/
export const edit = (args: { page: string | number, locale: string | number } | [page: string | number, locale: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/admin/pages/{page}/{locale}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\AdminPageController::edit
* @see app/Http/Controllers/Admin/AdminPageController.php:31
* @route '/admin/pages/{page}/{locale}'
*/
edit.url = (args: { page: string | number, locale: string | number } | [page: string | number, locale: string | number ], options?: RouteQueryOptions) => {

    if (Array.isArray(args)) {
        args = {
            page: args[0],
            locale: args[1],
        }
    }

    args = applyUrlDefaults(args)


    const parsedArgs = {
        page: args.page,
        locale: args.locale,
    }

    return edit.definition.url
            .replace('{page}', parsedArgs.page.toString())
            .replace('{locale}', parsedArgs.locale.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AdminPageController::edit
* @see app/Http/Controllers/Admin/AdminPageController.php:31
* @route '/admin/pages/{page}/{locale}'
*/
edit.get = (args: { page: string | number, locale: string | number } | [page: string | number, locale: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminPageController::edit
* @see app/Http/Controllers/Admin/AdminPageController.php:31
* @route '/admin/pages/{page}/{locale}'
*/
edit.head = (args: { page: string | number, locale: string | number } | [page: string | number, locale: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\AdminPageController::edit
* @see app/Http/Controllers/Admin/AdminPageController.php:31
* @route '/admin/pages/{page}/{locale}'
*/
const editForm = (args: { page: string | number, locale: string | number } | [page: string | number, locale: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminPageController::edit
* @see app/Http/Controllers/Admin/AdminPageController.php:31
* @route '/admin/pages/{page}/{locale}'
*/
editForm.get = (args: { page: string | number, locale: string | number } | [page: string | number, locale: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminPageController::edit
* @see app/Http/Controllers/Admin/AdminPageController.php:31
* @route '/admin/pages/{page}/{locale}'
*/
editForm.head = (args: { page: string | number, locale: string | number } | [page: string | number, locale: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

edit.form = editForm

/**
* @see \App\Http\Controllers\Admin\AdminPageController::update
* @see app/Http/Controllers/Admin/AdminPageController.php:79
* @route '/admin/pages/{page}/{locale}'
*/
export const update = (args: { page: string | number, locale: string | number } | [page: string | number, locale: string | number ], options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/admin/pages/{page}/{locale}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\Admin\AdminPageController::update
* @see app/Http/Controllers/Admin/AdminPageController.php:79
* @route '/admin/pages/{page}/{locale}'
*/
update.url = (args: { page: string | number, locale: string | number } | [page: string | number, locale: string | number ], options?: RouteQueryOptions) => {

    if (Array.isArray(args)) {
        args = {
            page: args[0],
            locale: args[1],
        }
    }

    args = applyUrlDefaults(args)


    const parsedArgs = {
        page: args.page,
        locale: args.locale,
    }

    return update.definition.url
            .replace('{page}', parsedArgs.page.toString())
            .replace('{locale}', parsedArgs.locale.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AdminPageController::update
* @see app/Http/Controllers/Admin/AdminPageController.php:79
* @route '/admin/pages/{page}/{locale}'
*/
update.put = (args: { page: string | number, locale: string | number } | [page: string | number, locale: string | number ], options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Admin\AdminPageController::update
* @see app/Http/Controllers/Admin/AdminPageController.php:79
* @route '/admin/pages/{page}/{locale}'
*/
const updateForm = (args: { page: string | number, locale: string | number } | [page: string | number, locale: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\AdminPageController::update
* @see app/Http/Controllers/Admin/AdminPageController.php:79
* @route '/admin/pages/{page}/{locale}'
*/
updateForm.put = (args: { page: string | number, locale: string | number } | [page: string | number, locale: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

update.form = updateForm

/**
* @see \App\Http\Controllers\Admin\AdminPageController::revert
* @see app/Http/Controllers/Admin/AdminPageController.php:51
* @route '/admin/pages/{page}/{locale}/revert'
*/
export const revert = (args: { page: string | number, locale: string | number } | [page: string | number, locale: string | number ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: revert.url(args, options),
    method: 'post',
})

revert.definition = {
    methods: ["post"],
    url: '/admin/pages/{page}/{locale}/revert',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\AdminPageController::revert
* @see app/Http/Controllers/Admin/AdminPageController.php:51
* @route '/admin/pages/{page}/{locale}/revert'
*/
revert.url = (args: { page: string | number, locale: string | number } | [page: string | number, locale: string | number ], options?: RouteQueryOptions) => {

    if (Array.isArray(args)) {
        args = {
            page: args[0],
            locale: args[1],
        }
    }

    args = applyUrlDefaults(args)


    const parsedArgs = {
        page: args.page,
        locale: args.locale,
    }

    return revert.definition.url
            .replace('{page}', parsedArgs.page.toString())
            .replace('{locale}', parsedArgs.locale.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AdminPageController::revert
* @see app/Http/Controllers/Admin/AdminPageController.php:51
* @route '/admin/pages/{page}/{locale}/revert'
*/
revert.post = (args: { page: string | number, locale: string | number } | [page: string | number, locale: string | number ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: revert.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\AdminPageController::revert
* @see app/Http/Controllers/Admin/AdminPageController.php:51
* @route '/admin/pages/{page}/{locale}/revert'
*/
const revertForm = (args: { page: string | number, locale: string | number } | [page: string | number, locale: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: revert.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\AdminPageController::revert
* @see app/Http/Controllers/Admin/AdminPageController.php:51
* @route '/admin/pages/{page}/{locale}/revert'
*/
revertForm.post = (args: { page: string | number, locale: string | number } | [page: string | number, locale: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: revert.url(args, options),
    method: 'post',
})

revert.form = revertForm

const AdminPageController = { index, edit, update, revert }

export default AdminPageController