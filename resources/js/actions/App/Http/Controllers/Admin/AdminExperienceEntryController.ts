import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\AdminExperienceEntryController::index
* @see app/Http/Controllers/Admin/AdminExperienceEntryController.php:39
* @route '/admin/experience'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/experience',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\AdminExperienceEntryController::index
* @see app/Http/Controllers/Admin/AdminExperienceEntryController.php:39
* @route '/admin/experience'
*/
index.url = (options?: RouteQueryOptions) => {




    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AdminExperienceEntryController::index
* @see app/Http/Controllers/Admin/AdminExperienceEntryController.php:39
* @route '/admin/experience'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminExperienceEntryController::index
* @see app/Http/Controllers/Admin/AdminExperienceEntryController.php:39
* @route '/admin/experience'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\AdminExperienceEntryController::index
* @see app/Http/Controllers/Admin/AdminExperienceEntryController.php:39
* @route '/admin/experience'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminExperienceEntryController::index
* @see app/Http/Controllers/Admin/AdminExperienceEntryController.php:39
* @route '/admin/experience'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminExperienceEntryController::index
* @see app/Http/Controllers/Admin/AdminExperienceEntryController.php:39
* @route '/admin/experience'
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
* @see \App\Http\Controllers\Admin\AdminExperienceEntryController::create
* @see app/Http/Controllers/Admin/AdminExperienceEntryController.php:66
* @route '/admin/experience/create'
*/
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/admin/experience/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\AdminExperienceEntryController::create
* @see app/Http/Controllers/Admin/AdminExperienceEntryController.php:66
* @route '/admin/experience/create'
*/
create.url = (options?: RouteQueryOptions) => {




    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AdminExperienceEntryController::create
* @see app/Http/Controllers/Admin/AdminExperienceEntryController.php:66
* @route '/admin/experience/create'
*/
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminExperienceEntryController::create
* @see app/Http/Controllers/Admin/AdminExperienceEntryController.php:66
* @route '/admin/experience/create'
*/
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\AdminExperienceEntryController::create
* @see app/Http/Controllers/Admin/AdminExperienceEntryController.php:66
* @route '/admin/experience/create'
*/
const createForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminExperienceEntryController::create
* @see app/Http/Controllers/Admin/AdminExperienceEntryController.php:66
* @route '/admin/experience/create'
*/
createForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminExperienceEntryController::create
* @see app/Http/Controllers/Admin/AdminExperienceEntryController.php:66
* @route '/admin/experience/create'
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
* @see \App\Http\Controllers\Admin\AdminExperienceEntryController::store
* @see app/Http/Controllers/Admin/AdminExperienceEntryController.php:90
* @route '/admin/experience'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/admin/experience',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\AdminExperienceEntryController::store
* @see app/Http/Controllers/Admin/AdminExperienceEntryController.php:90
* @route '/admin/experience'
*/
store.url = (options?: RouteQueryOptions) => {




    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AdminExperienceEntryController::store
* @see app/Http/Controllers/Admin/AdminExperienceEntryController.php:90
* @route '/admin/experience'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\AdminExperienceEntryController::store
* @see app/Http/Controllers/Admin/AdminExperienceEntryController.php:90
* @route '/admin/experience'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\AdminExperienceEntryController::store
* @see app/Http/Controllers/Admin/AdminExperienceEntryController.php:90
* @route '/admin/experience'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\Admin\AdminExperienceEntryController::edit
* @see app/Http/Controllers/Admin/AdminExperienceEntryController.php:79
* @route '/admin/experience/{entry}'
*/
export const edit = (args: { entry: number | { id: number } } | [entry: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/admin/experience/{entry}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\AdminExperienceEntryController::edit
* @see app/Http/Controllers/Admin/AdminExperienceEntryController.php:79
* @route '/admin/experience/{entry}'
*/
edit.url = (args: { entry: number | { id: number } } | [entry: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { entry: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { entry: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            entry: args[0],
        }
    }

    args = applyUrlDefaults(args)


    const parsedArgs = {
        entry: typeof args.entry === 'object'
        ? args.entry.id
        : args.entry,
    }

    return edit.definition.url
            .replace('{entry}', parsedArgs.entry.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AdminExperienceEntryController::edit
* @see app/Http/Controllers/Admin/AdminExperienceEntryController.php:79
* @route '/admin/experience/{entry}'
*/
edit.get = (args: { entry: number | { id: number } } | [entry: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminExperienceEntryController::edit
* @see app/Http/Controllers/Admin/AdminExperienceEntryController.php:79
* @route '/admin/experience/{entry}'
*/
edit.head = (args: { entry: number | { id: number } } | [entry: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\AdminExperienceEntryController::edit
* @see app/Http/Controllers/Admin/AdminExperienceEntryController.php:79
* @route '/admin/experience/{entry}'
*/
const editForm = (args: { entry: number | { id: number } } | [entry: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminExperienceEntryController::edit
* @see app/Http/Controllers/Admin/AdminExperienceEntryController.php:79
* @route '/admin/experience/{entry}'
*/
editForm.get = (args: { entry: number | { id: number } } | [entry: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminExperienceEntryController::edit
* @see app/Http/Controllers/Admin/AdminExperienceEntryController.php:79
* @route '/admin/experience/{entry}'
*/
editForm.head = (args: { entry: number | { id: number } } | [entry: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Http\Controllers\Admin\AdminExperienceEntryController::update
* @see app/Http/Controllers/Admin/AdminExperienceEntryController.php:118
* @route '/admin/experience/{entry}'
*/
export const update = (args: { entry: number | { id: number } } | [entry: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/admin/experience/{entry}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\Admin\AdminExperienceEntryController::update
* @see app/Http/Controllers/Admin/AdminExperienceEntryController.php:118
* @route '/admin/experience/{entry}'
*/
update.url = (args: { entry: number | { id: number } } | [entry: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { entry: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { entry: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            entry: args[0],
        }
    }

    args = applyUrlDefaults(args)


    const parsedArgs = {
        entry: typeof args.entry === 'object'
        ? args.entry.id
        : args.entry,
    }

    return update.definition.url
            .replace('{entry}', parsedArgs.entry.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AdminExperienceEntryController::update
* @see app/Http/Controllers/Admin/AdminExperienceEntryController.php:118
* @route '/admin/experience/{entry}'
*/
update.put = (args: { entry: number | { id: number } } | [entry: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Admin\AdminExperienceEntryController::update
* @see app/Http/Controllers/Admin/AdminExperienceEntryController.php:118
* @route '/admin/experience/{entry}'
*/
const updateForm = (args: { entry: number | { id: number } } | [entry: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\AdminExperienceEntryController::update
* @see app/Http/Controllers/Admin/AdminExperienceEntryController.php:118
* @route '/admin/experience/{entry}'
*/
updateForm.put = (args: { entry: number | { id: number } } | [entry: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Http\Controllers\Admin\AdminExperienceEntryController::destroy
* @see app/Http/Controllers/Admin/AdminExperienceEntryController.php:135
* @route '/admin/experience/{entry}'
*/
export const destroy = (args: { entry: number | { id: number } } | [entry: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/admin/experience/{entry}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Admin\AdminExperienceEntryController::destroy
* @see app/Http/Controllers/Admin/AdminExperienceEntryController.php:135
* @route '/admin/experience/{entry}'
*/
destroy.url = (args: { entry: number | { id: number } } | [entry: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { entry: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { entry: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            entry: args[0],
        }
    }

    args = applyUrlDefaults(args)


    const parsedArgs = {
        entry: typeof args.entry === 'object'
        ? args.entry.id
        : args.entry,
    }

    return destroy.definition.url
            .replace('{entry}', parsedArgs.entry.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AdminExperienceEntryController::destroy
* @see app/Http/Controllers/Admin/AdminExperienceEntryController.php:135
* @route '/admin/experience/{entry}'
*/
destroy.delete = (args: { entry: number | { id: number } } | [entry: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Admin\AdminExperienceEntryController::destroy
* @see app/Http/Controllers/Admin/AdminExperienceEntryController.php:135
* @route '/admin/experience/{entry}'
*/
const destroyForm = (args: { entry: number | { id: number } } | [entry: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\AdminExperienceEntryController::destroy
* @see app/Http/Controllers/Admin/AdminExperienceEntryController.php:135
* @route '/admin/experience/{entry}'
*/
destroyForm.delete = (args: { entry: number | { id: number } } | [entry: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const AdminExperienceEntryController = { index, create, store, edit, update, destroy }

export default AdminExperienceEntryController