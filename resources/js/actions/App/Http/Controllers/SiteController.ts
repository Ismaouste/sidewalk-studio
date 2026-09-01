import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\SiteController::home
* @see app/Http/Controllers/SiteController.php:103
* @route '/{locale}'
*/
export const home = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: home.url(args, options),
    method: 'get',
})

home.definition = {
    methods: ["get","head"],
    url: '/{locale}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SiteController::home
* @see app/Http/Controllers/SiteController.php:103
* @route '/{locale}'
*/
home.url = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return home.definition.url
            .replace('{locale}', parsedArgs.locale.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\SiteController::home
* @see app/Http/Controllers/SiteController.php:103
* @route '/{locale}'
*/
home.get = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: home.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SiteController::home
* @see app/Http/Controllers/SiteController.php:103
* @route '/{locale}'
*/
home.head = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: home.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\SiteController::home
* @see app/Http/Controllers/SiteController.php:103
* @route '/{locale}'
*/
const homeForm = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: home.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SiteController::home
* @see app/Http/Controllers/SiteController.php:103
* @route '/{locale}'
*/
homeForm.get = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: home.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SiteController::home
* @see app/Http/Controllers/SiteController.php:103
* @route '/{locale}'
*/
homeForm.head = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: home.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

home.form = homeForm

/**
* @see \App\Http\Controllers\SiteController::experience
* @see app/Http/Controllers/SiteController.php:222
* @route '/{locale}/experience'
*/
export const experience = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: experience.url(args, options),
    method: 'get',
})

experience.definition = {
    methods: ["get","head"],
    url: '/{locale}/experience',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SiteController::experience
* @see app/Http/Controllers/SiteController.php:222
* @route '/{locale}/experience'
*/
experience.url = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return experience.definition.url
            .replace('{locale}', parsedArgs.locale.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\SiteController::experience
* @see app/Http/Controllers/SiteController.php:222
* @route '/{locale}/experience'
*/
experience.get = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: experience.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SiteController::experience
* @see app/Http/Controllers/SiteController.php:222
* @route '/{locale}/experience'
*/
experience.head = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: experience.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\SiteController::experience
* @see app/Http/Controllers/SiteController.php:222
* @route '/{locale}/experience'
*/
const experienceForm = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: experience.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SiteController::experience
* @see app/Http/Controllers/SiteController.php:222
* @route '/{locale}/experience'
*/
experienceForm.get = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: experience.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SiteController::experience
* @see app/Http/Controllers/SiteController.php:222
* @route '/{locale}/experience'
*/
experienceForm.head = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: experience.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

experience.form = experienceForm

/**
* @see \App\Http\Controllers\SiteController::projectsLegacy
* @see app/Http/Controllers/SiteController.php:153
* @route '/{locale}/projects'
*/
export const projectsLegacy = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: projectsLegacy.url(args, options),
    method: 'get',
})

projectsLegacy.definition = {
    methods: ["get","head"],
    url: '/{locale}/projects',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SiteController::projectsLegacy
* @see app/Http/Controllers/SiteController.php:153
* @route '/{locale}/projects'
*/
projectsLegacy.url = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return projectsLegacy.definition.url
            .replace('{locale}', parsedArgs.locale.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\SiteController::projectsLegacy
* @see app/Http/Controllers/SiteController.php:153
* @route '/{locale}/projects'
*/
projectsLegacy.get = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: projectsLegacy.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SiteController::projectsLegacy
* @see app/Http/Controllers/SiteController.php:153
* @route '/{locale}/projects'
*/
projectsLegacy.head = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: projectsLegacy.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\SiteController::projectsLegacy
* @see app/Http/Controllers/SiteController.php:153
* @route '/{locale}/projects'
*/
const projectsLegacyForm = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: projectsLegacy.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SiteController::projectsLegacy
* @see app/Http/Controllers/SiteController.php:153
* @route '/{locale}/projects'
*/
projectsLegacyForm.get = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: projectsLegacy.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SiteController::projectsLegacy
* @see app/Http/Controllers/SiteController.php:153
* @route '/{locale}/projects'
*/
projectsLegacyForm.head = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: projectsLegacy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

projectsLegacy.form = projectsLegacyForm

/**
* @see \App\Http\Controllers\SiteController::sparkle
* @see app/Http/Controllers/SiteController.php:193
* @route '/{locale}/sparkle'
*/
export const sparkle = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: sparkle.url(args, options),
    method: 'get',
})

sparkle.definition = {
    methods: ["get","head"],
    url: '/{locale}/sparkle',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SiteController::sparkle
* @see app/Http/Controllers/SiteController.php:193
* @route '/{locale}/sparkle'
*/
sparkle.url = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return sparkle.definition.url
            .replace('{locale}', parsedArgs.locale.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\SiteController::sparkle
* @see app/Http/Controllers/SiteController.php:193
* @route '/{locale}/sparkle'
*/
sparkle.get = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: sparkle.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SiteController::sparkle
* @see app/Http/Controllers/SiteController.php:193
* @route '/{locale}/sparkle'
*/
sparkle.head = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: sparkle.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\SiteController::sparkle
* @see app/Http/Controllers/SiteController.php:193
* @route '/{locale}/sparkle'
*/
const sparkleForm = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: sparkle.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SiteController::sparkle
* @see app/Http/Controllers/SiteController.php:193
* @route '/{locale}/sparkle'
*/
sparkleForm.get = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: sparkle.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SiteController::sparkle
* @see app/Http/Controllers/SiteController.php:193
* @route '/{locale}/sparkle'
*/
sparkleForm.head = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: sparkle.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

sparkle.form = sparkleForm

/**
* @see \App\Http\Controllers\SiteController::local
* @see app/Http/Controllers/SiteController.php:158
* @route '/{locale}/local'
*/
export const local = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: local.url(args, options),
    method: 'get',
})

local.definition = {
    methods: ["get","head"],
    url: '/{locale}/local',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SiteController::local
* @see app/Http/Controllers/SiteController.php:158
* @route '/{locale}/local'
*/
local.url = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return local.definition.url
            .replace('{locale}', parsedArgs.locale.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\SiteController::local
* @see app/Http/Controllers/SiteController.php:158
* @route '/{locale}/local'
*/
local.get = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: local.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SiteController::local
* @see app/Http/Controllers/SiteController.php:158
* @route '/{locale}/local'
*/
local.head = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: local.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\SiteController::local
* @see app/Http/Controllers/SiteController.php:158
* @route '/{locale}/local'
*/
const localForm = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: local.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SiteController::local
* @see app/Http/Controllers/SiteController.php:158
* @route '/{locale}/local'
*/
localForm.get = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: local.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SiteController::local
* @see app/Http/Controllers/SiteController.php:158
* @route '/{locale}/local'
*/
localForm.head = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: local.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

local.form = localForm

/**
* @see \App\Http\Controllers\SiteController::labs
* @see app/Http/Controllers/SiteController.php:309
* @route '/{locale}/labs'
*/
export const labs = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: labs.url(args, options),
    method: 'get',
})

labs.definition = {
    methods: ["get","head"],
    url: '/{locale}/labs',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SiteController::labs
* @see app/Http/Controllers/SiteController.php:309
* @route '/{locale}/labs'
*/
labs.url = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return labs.definition.url
            .replace('{locale}', parsedArgs.locale.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\SiteController::labs
* @see app/Http/Controllers/SiteController.php:309
* @route '/{locale}/labs'
*/
labs.get = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: labs.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SiteController::labs
* @see app/Http/Controllers/SiteController.php:309
* @route '/{locale}/labs'
*/
labs.head = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: labs.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\SiteController::labs
* @see app/Http/Controllers/SiteController.php:309
* @route '/{locale}/labs'
*/
const labsForm = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: labs.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SiteController::labs
* @see app/Http/Controllers/SiteController.php:309
* @route '/{locale}/labs'
*/
labsForm.get = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: labs.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SiteController::labs
* @see app/Http/Controllers/SiteController.php:309
* @route '/{locale}/labs'
*/
labsForm.head = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: labs.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

labs.form = labsForm

/**
* @see \App\Http\Controllers\SiteController::services
* @see app/Http/Controllers/SiteController.php:335
* @route '/{locale}/services'
*/
export const services = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: services.url(args, options),
    method: 'get',
})

services.definition = {
    methods: ["get","head"],
    url: '/{locale}/services',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SiteController::services
* @see app/Http/Controllers/SiteController.php:335
* @route '/{locale}/services'
*/
services.url = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return services.definition.url
            .replace('{locale}', parsedArgs.locale.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\SiteController::services
* @see app/Http/Controllers/SiteController.php:335
* @route '/{locale}/services'
*/
services.get = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: services.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SiteController::services
* @see app/Http/Controllers/SiteController.php:335
* @route '/{locale}/services'
*/
services.head = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: services.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\SiteController::services
* @see app/Http/Controllers/SiteController.php:335
* @route '/{locale}/services'
*/
const servicesForm = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: services.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SiteController::services
* @see app/Http/Controllers/SiteController.php:335
* @route '/{locale}/services'
*/
servicesForm.get = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: services.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SiteController::services
* @see app/Http/Controllers/SiteController.php:335
* @route '/{locale}/services'
*/
servicesForm.head = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: services.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

services.form = servicesForm

/**
* @see \App\Http\Controllers\SiteController::contact
* @see app/Http/Controllers/SiteController.php:362
* @route '/{locale}/contact'
*/
export const contact = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: contact.url(args, options),
    method: 'get',
})

contact.definition = {
    methods: ["get","head"],
    url: '/{locale}/contact',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SiteController::contact
* @see app/Http/Controllers/SiteController.php:362
* @route '/{locale}/contact'
*/
contact.url = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return contact.definition.url
            .replace('{locale}', parsedArgs.locale.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\SiteController::contact
* @see app/Http/Controllers/SiteController.php:362
* @route '/{locale}/contact'
*/
contact.get = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: contact.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SiteController::contact
* @see app/Http/Controllers/SiteController.php:362
* @route '/{locale}/contact'
*/
contact.head = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: contact.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\SiteController::contact
* @see app/Http/Controllers/SiteController.php:362
* @route '/{locale}/contact'
*/
const contactForm = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: contact.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SiteController::contact
* @see app/Http/Controllers/SiteController.php:362
* @route '/{locale}/contact'
*/
contactForm.get = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: contact.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SiteController::contact
* @see app/Http/Controllers/SiteController.php:362
* @route '/{locale}/contact'
*/
contactForm.head = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: contact.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

contact.form = contactForm

/**
* @see \App\Http\Controllers\SiteController::dataProcessing
* @see app/Http/Controllers/SiteController.php:391
* @route '/{locale}/data-processing'
*/
export const dataProcessing = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: dataProcessing.url(args, options),
    method: 'get',
})

dataProcessing.definition = {
    methods: ["get","head"],
    url: '/{locale}/data-processing',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SiteController::dataProcessing
* @see app/Http/Controllers/SiteController.php:391
* @route '/{locale}/data-processing'
*/
dataProcessing.url = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return dataProcessing.definition.url
            .replace('{locale}', parsedArgs.locale.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\SiteController::dataProcessing
* @see app/Http/Controllers/SiteController.php:391
* @route '/{locale}/data-processing'
*/
dataProcessing.get = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: dataProcessing.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SiteController::dataProcessing
* @see app/Http/Controllers/SiteController.php:391
* @route '/{locale}/data-processing'
*/
dataProcessing.head = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: dataProcessing.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\SiteController::dataProcessing
* @see app/Http/Controllers/SiteController.php:391
* @route '/{locale}/data-processing'
*/
const dataProcessingForm = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: dataProcessing.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SiteController::dataProcessing
* @see app/Http/Controllers/SiteController.php:391
* @route '/{locale}/data-processing'
*/
dataProcessingForm.get = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: dataProcessing.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SiteController::dataProcessing
* @see app/Http/Controllers/SiteController.php:391
* @route '/{locale}/data-processing'
*/
dataProcessingForm.head = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: dataProcessing.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

dataProcessing.form = dataProcessingForm

/**
* @see \App\Http\Controllers\SiteController::colophon
* @see app/Http/Controllers/SiteController.php:416
* @route '/{locale}/colophon'
*/
export const colophon = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: colophon.url(args, options),
    method: 'get',
})

colophon.definition = {
    methods: ["get","head"],
    url: '/{locale}/colophon',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SiteController::colophon
* @see app/Http/Controllers/SiteController.php:416
* @route '/{locale}/colophon'
*/
colophon.url = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return colophon.definition.url
            .replace('{locale}', parsedArgs.locale.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\SiteController::colophon
* @see app/Http/Controllers/SiteController.php:416
* @route '/{locale}/colophon'
*/
colophon.get = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: colophon.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SiteController::colophon
* @see app/Http/Controllers/SiteController.php:416
* @route '/{locale}/colophon'
*/
colophon.head = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: colophon.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\SiteController::colophon
* @see app/Http/Controllers/SiteController.php:416
* @route '/{locale}/colophon'
*/
const colophonForm = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: colophon.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SiteController::colophon
* @see app/Http/Controllers/SiteController.php:416
* @route '/{locale}/colophon'
*/
colophonForm.get = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: colophon.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SiteController::colophon
* @see app/Http/Controllers/SiteController.php:416
* @route '/{locale}/colophon'
*/
colophonForm.head = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: colophon.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

colophon.form = colophonForm

/**
* @see \App\Http\Controllers\SiteController::downloadCv
* @see app/Http/Controllers/SiteController.php:434
* @route '/cv/{locale}'
*/
export const downloadCv = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: downloadCv.url(args, options),
    method: 'get',
})

downloadCv.definition = {
    methods: ["get","head"],
    url: '/cv/{locale}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SiteController::downloadCv
* @see app/Http/Controllers/SiteController.php:434
* @route '/cv/{locale}'
*/
downloadCv.url = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return downloadCv.definition.url
            .replace('{locale}', parsedArgs.locale.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\SiteController::downloadCv
* @see app/Http/Controllers/SiteController.php:434
* @route '/cv/{locale}'
*/
downloadCv.get = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: downloadCv.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SiteController::downloadCv
* @see app/Http/Controllers/SiteController.php:434
* @route '/cv/{locale}'
*/
downloadCv.head = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: downloadCv.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\SiteController::downloadCv
* @see app/Http/Controllers/SiteController.php:434
* @route '/cv/{locale}'
*/
const downloadCvForm = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: downloadCv.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SiteController::downloadCv
* @see app/Http/Controllers/SiteController.php:434
* @route '/cv/{locale}'
*/
downloadCvForm.get = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: downloadCv.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SiteController::downloadCv
* @see app/Http/Controllers/SiteController.php:434
* @route '/cv/{locale}'
*/
downloadCvForm.head = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: downloadCv.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

downloadCv.form = downloadCvForm

const SiteController = { home, experience, projectsLegacy, sparkle, local, labs, services, contact, dataProcessing, colophon, downloadCv }

export default SiteController