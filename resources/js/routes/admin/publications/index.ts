import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
import typeSettings from './type-settings'
/**
* @see \App\Http\Controllers\Admin\AdminPublicationController::index
* @see app/Http/Controllers/Admin/AdminPublicationController.php:23
* @route '/admin/publications'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/publications',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\AdminPublicationController::index
* @see app/Http/Controllers/Admin/AdminPublicationController.php:23
* @route '/admin/publications'
*/
index.url = (options?: RouteQueryOptions) => {




    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AdminPublicationController::index
* @see app/Http/Controllers/Admin/AdminPublicationController.php:23
* @route '/admin/publications'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminPublicationController::index
* @see app/Http/Controllers/Admin/AdminPublicationController.php:23
* @route '/admin/publications'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\AdminPublicationController::index
* @see app/Http/Controllers/Admin/AdminPublicationController.php:23
* @route '/admin/publications'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminPublicationController::index
* @see app/Http/Controllers/Admin/AdminPublicationController.php:23
* @route '/admin/publications'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminPublicationController::index
* @see app/Http/Controllers/Admin/AdminPublicationController.php:23
* @route '/admin/publications'
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
* @see \App\Http\Controllers\Admin\AdminPublicationController::create
* @see app/Http/Controllers/Admin/AdminPublicationController.php:31
* @route '/admin/publications/create/{type}'
*/
export const create = (args: { type: string | number } | [type: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(args, options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/admin/publications/create/{type}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\AdminPublicationController::create
* @see app/Http/Controllers/Admin/AdminPublicationController.php:31
* @route '/admin/publications/create/{type}'
*/
create.url = (args: { type: string | number } | [type: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { type: args }
    }


    if (Array.isArray(args)) {
        args = {
            type: args[0],
        }
    }

    args = applyUrlDefaults(args)


    const parsedArgs = {
        type: args.type,
    }

    return create.definition.url
            .replace('{type}', parsedArgs.type.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AdminPublicationController::create
* @see app/Http/Controllers/Admin/AdminPublicationController.php:31
* @route '/admin/publications/create/{type}'
*/
create.get = (args: { type: string | number } | [type: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminPublicationController::create
* @see app/Http/Controllers/Admin/AdminPublicationController.php:31
* @route '/admin/publications/create/{type}'
*/
create.head = (args: { type: string | number } | [type: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\AdminPublicationController::create
* @see app/Http/Controllers/Admin/AdminPublicationController.php:31
* @route '/admin/publications/create/{type}'
*/
const createForm = (args: { type: string | number } | [type: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminPublicationController::create
* @see app/Http/Controllers/Admin/AdminPublicationController.php:31
* @route '/admin/publications/create/{type}'
*/
createForm.get = (args: { type: string | number } | [type: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminPublicationController::create
* @see app/Http/Controllers/Admin/AdminPublicationController.php:31
* @route '/admin/publications/create/{type}'
*/
createForm.head = (args: { type: string | number } | [type: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

create.form = createForm

/**
* @see \App\Http\Controllers\Admin\AdminPublicationController::store
* @see app/Http/Controllers/Admin/AdminPublicationController.php:86
* @route '/admin/publications'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/admin/publications',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\AdminPublicationController::store
* @see app/Http/Controllers/Admin/AdminPublicationController.php:86
* @route '/admin/publications'
*/
store.url = (options?: RouteQueryOptions) => {




    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AdminPublicationController::store
* @see app/Http/Controllers/Admin/AdminPublicationController.php:86
* @route '/admin/publications'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\AdminPublicationController::store
* @see app/Http/Controllers/Admin/AdminPublicationController.php:86
* @route '/admin/publications'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\AdminPublicationController::store
* @see app/Http/Controllers/Admin/AdminPublicationController.php:86
* @route '/admin/publications'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\Admin\AdminPublicationController::edit
* @see app/Http/Controllers/Admin/AdminPublicationController.php:73
* @route '/admin/publications/{type}/{locale}/{slug}'
*/
export const edit = (args: { type: string | number, locale: string | number, slug: string | number } | [type: string | number, locale: string | number, slug: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/admin/publications/{type}/{locale}/{slug}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\AdminPublicationController::edit
* @see app/Http/Controllers/Admin/AdminPublicationController.php:73
* @route '/admin/publications/{type}/{locale}/{slug}'
*/
edit.url = (args: { type: string | number, locale: string | number, slug: string | number } | [type: string | number, locale: string | number, slug: string | number ], options?: RouteQueryOptions) => {

    if (Array.isArray(args)) {
        args = {
            type: args[0],
            locale: args[1],
            slug: args[2],
        }
    }

    args = applyUrlDefaults(args)


    const parsedArgs = {
        type: args.type,
        locale: args.locale,
        slug: args.slug,
    }

    return edit.definition.url
            .replace('{type}', parsedArgs.type.toString())
            .replace('{locale}', parsedArgs.locale.toString())
            .replace('{slug}', parsedArgs.slug.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AdminPublicationController::edit
* @see app/Http/Controllers/Admin/AdminPublicationController.php:73
* @route '/admin/publications/{type}/{locale}/{slug}'
*/
edit.get = (args: { type: string | number, locale: string | number, slug: string | number } | [type: string | number, locale: string | number, slug: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminPublicationController::edit
* @see app/Http/Controllers/Admin/AdminPublicationController.php:73
* @route '/admin/publications/{type}/{locale}/{slug}'
*/
edit.head = (args: { type: string | number, locale: string | number, slug: string | number } | [type: string | number, locale: string | number, slug: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\AdminPublicationController::edit
* @see app/Http/Controllers/Admin/AdminPublicationController.php:73
* @route '/admin/publications/{type}/{locale}/{slug}'
*/
const editForm = (args: { type: string | number, locale: string | number, slug: string | number } | [type: string | number, locale: string | number, slug: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminPublicationController::edit
* @see app/Http/Controllers/Admin/AdminPublicationController.php:73
* @route '/admin/publications/{type}/{locale}/{slug}'
*/
editForm.get = (args: { type: string | number, locale: string | number, slug: string | number } | [type: string | number, locale: string | number, slug: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminPublicationController::edit
* @see app/Http/Controllers/Admin/AdminPublicationController.php:73
* @route '/admin/publications/{type}/{locale}/{slug}'
*/
editForm.head = (args: { type: string | number, locale: string | number, slug: string | number } | [type: string | number, locale: string | number, slug: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Http\Controllers\Admin\AdminPublicationController::update
* @see app/Http/Controllers/Admin/AdminPublicationController.php:91
* @route '/admin/publications/{type}/{locale}/{slug}'
*/
export const update = (args: { type: string | number, locale: string | number, slug: string | number } | [type: string | number, locale: string | number, slug: string | number ], options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/admin/publications/{type}/{locale}/{slug}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\Admin\AdminPublicationController::update
* @see app/Http/Controllers/Admin/AdminPublicationController.php:91
* @route '/admin/publications/{type}/{locale}/{slug}'
*/
update.url = (args: { type: string | number, locale: string | number, slug: string | number } | [type: string | number, locale: string | number, slug: string | number ], options?: RouteQueryOptions) => {

    if (Array.isArray(args)) {
        args = {
            type: args[0],
            locale: args[1],
            slug: args[2],
        }
    }

    args = applyUrlDefaults(args)


    const parsedArgs = {
        type: args.type,
        locale: args.locale,
        slug: args.slug,
    }

    return update.definition.url
            .replace('{type}', parsedArgs.type.toString())
            .replace('{locale}', parsedArgs.locale.toString())
            .replace('{slug}', parsedArgs.slug.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AdminPublicationController::update
* @see app/Http/Controllers/Admin/AdminPublicationController.php:91
* @route '/admin/publications/{type}/{locale}/{slug}'
*/
update.put = (args: { type: string | number, locale: string | number, slug: string | number } | [type: string | number, locale: string | number, slug: string | number ], options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Admin\AdminPublicationController::update
* @see app/Http/Controllers/Admin/AdminPublicationController.php:91
* @route '/admin/publications/{type}/{locale}/{slug}'
*/
const updateForm = (args: { type: string | number, locale: string | number, slug: string | number } | [type: string | number, locale: string | number, slug: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\AdminPublicationController::update
* @see app/Http/Controllers/Admin/AdminPublicationController.php:91
* @route '/admin/publications/{type}/{locale}/{slug}'
*/
updateForm.put = (args: { type: string | number, locale: string | number, slug: string | number } | [type: string | number, locale: string | number, slug: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

update.form = updateForm

const publications = {
    index: Object.assign(index, index),
create: Object.assign(create, create),
store: Object.assign(store, store),
edit: Object.assign(edit, edit),
update: Object.assign(update, update),
typeSettings: Object.assign(typeSettings, typeSettings),
}

export default publications