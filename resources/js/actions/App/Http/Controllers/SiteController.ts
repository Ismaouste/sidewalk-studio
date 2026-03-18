import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\SiteController::home
* @see app/Http/Controllers/SiteController.php:23
* @route '/'
*/
export const home = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: home.url(options),
    method: 'get',
})

home.definition = {
    methods: ["get","head"],
    url: '/',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SiteController::home
* @see app/Http/Controllers/SiteController.php:23
* @route '/'
*/
home.url = (options?: RouteQueryOptions) => {




    return home.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\SiteController::home
* @see app/Http/Controllers/SiteController.php:23
* @route '/'
*/
home.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: home.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SiteController::home
* @see app/Http/Controllers/SiteController.php:23
* @route '/'
*/
home.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: home.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\SiteController::home
* @see app/Http/Controllers/SiteController.php:23
* @route '/'
*/
const homeForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: home.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SiteController::home
* @see app/Http/Controllers/SiteController.php:23
* @route '/'
*/
homeForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: home.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SiteController::home
* @see app/Http/Controllers/SiteController.php:23
* @route '/'
*/
homeForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: home.url({
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
* @see app/Http/Controllers/SiteController.php:61
* @route '/experience'
*/
export const experience = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: experience.url(options),
    method: 'get',
})

experience.definition = {
    methods: ["get","head"],
    url: '/experience',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SiteController::experience
* @see app/Http/Controllers/SiteController.php:61
* @route '/experience'
*/
experience.url = (options?: RouteQueryOptions) => {




    return experience.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\SiteController::experience
* @see app/Http/Controllers/SiteController.php:61
* @route '/experience'
*/
experience.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: experience.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SiteController::experience
* @see app/Http/Controllers/SiteController.php:61
* @route '/experience'
*/
experience.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: experience.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\SiteController::experience
* @see app/Http/Controllers/SiteController.php:61
* @route '/experience'
*/
const experienceForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: experience.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SiteController::experience
* @see app/Http/Controllers/SiteController.php:61
* @route '/experience'
*/
experienceForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: experience.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SiteController::experience
* @see app/Http/Controllers/SiteController.php:61
* @route '/experience'
*/
experienceForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: experience.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

experience.form = experienceForm

/**
* @see \App\Http\Controllers\SiteController::local
* @see app/Http/Controllers/SiteController.php:66
* @route '/local'
*/
export const local = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: local.url(options),
    method: 'get',
})

local.definition = {
    methods: ["get","head"],
    url: '/local',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SiteController::local
* @see app/Http/Controllers/SiteController.php:66
* @route '/local'
*/
local.url = (options?: RouteQueryOptions) => {




    return local.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\SiteController::local
* @see app/Http/Controllers/SiteController.php:66
* @route '/local'
*/
local.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: local.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SiteController::local
* @see app/Http/Controllers/SiteController.php:66
* @route '/local'
*/
local.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: local.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\SiteController::local
* @see app/Http/Controllers/SiteController.php:66
* @route '/local'
*/
const localForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: local.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SiteController::local
* @see app/Http/Controllers/SiteController.php:66
* @route '/local'
*/
localForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: local.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SiteController::local
* @see app/Http/Controllers/SiteController.php:66
* @route '/local'
*/
localForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: local.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

local.form = localForm

/**
* @see \App\Http\Controllers\SiteController::projects
* @see app/Http/Controllers/SiteController.php:101
* @route '/projects'
*/
export const projects = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: projects.url(options),
    method: 'get',
})

projects.definition = {
    methods: ["get","head"],
    url: '/projects',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SiteController::projects
* @see app/Http/Controllers/SiteController.php:101
* @route '/projects'
*/
projects.url = (options?: RouteQueryOptions) => {




    return projects.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\SiteController::projects
* @see app/Http/Controllers/SiteController.php:101
* @route '/projects'
*/
projects.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: projects.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SiteController::projects
* @see app/Http/Controllers/SiteController.php:101
* @route '/projects'
*/
projects.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: projects.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\SiteController::projects
* @see app/Http/Controllers/SiteController.php:101
* @route '/projects'
*/
const projectsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: projects.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SiteController::projects
* @see app/Http/Controllers/SiteController.php:101
* @route '/projects'
*/
projectsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: projects.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SiteController::projects
* @see app/Http/Controllers/SiteController.php:101
* @route '/projects'
*/
projectsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: projects.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

projects.form = projectsForm

/**
* @see \App\Http\Controllers\SiteController::labs
* @see app/Http/Controllers/SiteController.php:197
* @route '/labs'
*/
export const labs = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: labs.url(options),
    method: 'get',
})

labs.definition = {
    methods: ["get","head"],
    url: '/labs',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SiteController::labs
* @see app/Http/Controllers/SiteController.php:197
* @route '/labs'
*/
labs.url = (options?: RouteQueryOptions) => {




    return labs.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\SiteController::labs
* @see app/Http/Controllers/SiteController.php:197
* @route '/labs'
*/
labs.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: labs.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SiteController::labs
* @see app/Http/Controllers/SiteController.php:197
* @route '/labs'
*/
labs.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: labs.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\SiteController::labs
* @see app/Http/Controllers/SiteController.php:197
* @route '/labs'
*/
const labsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: labs.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SiteController::labs
* @see app/Http/Controllers/SiteController.php:197
* @route '/labs'
*/
labsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: labs.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SiteController::labs
* @see app/Http/Controllers/SiteController.php:197
* @route '/labs'
*/
labsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: labs.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

labs.form = labsForm

/**
* @see \App\Http\Controllers\SiteController::contact
* @see app/Http/Controllers/SiteController.php:223
* @route '/contact'
*/
export const contact = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: contact.url(options),
    method: 'get',
})

contact.definition = {
    methods: ["get","head"],
    url: '/contact',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SiteController::contact
* @see app/Http/Controllers/SiteController.php:223
* @route '/contact'
*/
contact.url = (options?: RouteQueryOptions) => {




    return contact.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\SiteController::contact
* @see app/Http/Controllers/SiteController.php:223
* @route '/contact'
*/
contact.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: contact.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SiteController::contact
* @see app/Http/Controllers/SiteController.php:223
* @route '/contact'
*/
contact.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: contact.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\SiteController::contact
* @see app/Http/Controllers/SiteController.php:223
* @route '/contact'
*/
const contactForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: contact.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SiteController::contact
* @see app/Http/Controllers/SiteController.php:223
* @route '/contact'
*/
contactForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: contact.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SiteController::contact
* @see app/Http/Controllers/SiteController.php:223
* @route '/contact'
*/
contactForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: contact.url({
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
* @see app/Http/Controllers/SiteController.php:251
* @route '/data-processing'
*/
export const dataProcessing = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: dataProcessing.url(options),
    method: 'get',
})

dataProcessing.definition = {
    methods: ["get","head"],
    url: '/data-processing',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SiteController::dataProcessing
* @see app/Http/Controllers/SiteController.php:251
* @route '/data-processing'
*/
dataProcessing.url = (options?: RouteQueryOptions) => {




    return dataProcessing.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\SiteController::dataProcessing
* @see app/Http/Controllers/SiteController.php:251
* @route '/data-processing'
*/
dataProcessing.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: dataProcessing.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SiteController::dataProcessing
* @see app/Http/Controllers/SiteController.php:251
* @route '/data-processing'
*/
dataProcessing.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: dataProcessing.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\SiteController::dataProcessing
* @see app/Http/Controllers/SiteController.php:251
* @route '/data-processing'
*/
const dataProcessingForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: dataProcessing.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SiteController::dataProcessing
* @see app/Http/Controllers/SiteController.php:251
* @route '/data-processing'
*/
dataProcessingForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: dataProcessing.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SiteController::dataProcessing
* @see app/Http/Controllers/SiteController.php:251
* @route '/data-processing'
*/
dataProcessingForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: dataProcessing.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

dataProcessing.form = dataProcessingForm

/**
* @see \App\Http\Controllers\SiteController::downloadCv
* @see app/Http/Controllers/SiteController.php:276
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
* @see app/Http/Controllers/SiteController.php:276
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
* @see app/Http/Controllers/SiteController.php:276
* @route '/cv/{locale}'
*/
downloadCv.get = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: downloadCv.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SiteController::downloadCv
* @see app/Http/Controllers/SiteController.php:276
* @route '/cv/{locale}'
*/
downloadCv.head = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: downloadCv.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\SiteController::downloadCv
* @see app/Http/Controllers/SiteController.php:276
* @route '/cv/{locale}'
*/
const downloadCvForm = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: downloadCv.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SiteController::downloadCv
* @see app/Http/Controllers/SiteController.php:276
* @route '/cv/{locale}'
*/
downloadCvForm.get = (args: { locale: string | number } | [locale: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: downloadCv.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SiteController::downloadCv
* @see app/Http/Controllers/SiteController.php:276
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

const SiteController = { home, experience, local, projects, labs, contact, dataProcessing, downloadCv }

export default SiteController