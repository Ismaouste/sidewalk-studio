import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../wayfinder'
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
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/about'
*/
export const about = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: about.url(options),
    method: 'get',
})

about.definition = {
    methods: ["get","head","post","put","patch","delete","options"],
    url: '/about',
} satisfies RouteDefinition<["get","head","post","put","patch","delete","options"]>

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/about'
*/
about.url = (options?: RouteQueryOptions) => {




    return about.definition.url + queryParams(options)
}

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/about'
*/
about.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: about.url(options),
    method: 'get',
})

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/about'
*/
about.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: about.url(options),
    method: 'head',
})

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/about'
*/
about.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: about.url(options),
    method: 'post',
})

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/about'
*/
about.put = (options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: about.url(options),
    method: 'put',
})

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/about'
*/
about.patch = (options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: about.url(options),
    method: 'patch',
})

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/about'
*/
about.delete = (options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: about.url(options),
    method: 'delete',
})

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/about'
*/
about.options = (options?: RouteQueryOptions): RouteDefinition<'options'> => ({
    url: about.url(options),
    method: 'options',
})

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/about'
*/
const aboutForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: about.url(options),
    method: 'get',
})

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/about'
*/
aboutForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: about.url(options),
    method: 'get',
})

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/about'
*/
aboutForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: about.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/about'
*/
aboutForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: about.url(options),
    method: 'post',
})

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/about'
*/
aboutForm.put = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: about.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/about'
*/
aboutForm.patch = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: about.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/about'
*/
aboutForm.delete = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: about.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/about'
*/
aboutForm.options = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: about.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'OPTIONS',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

about.form = aboutForm

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
* @see \App\Http\Controllers\SitemapController::__invoke
* @see app/Http/Controllers/SitemapController.php:10
* @route '/sitemap.xml'
*/
export const sitemap = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: sitemap.url(options),
    method: 'get',
})

sitemap.definition = {
    methods: ["get","head"],
    url: '/sitemap.xml',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SitemapController::__invoke
* @see app/Http/Controllers/SitemapController.php:10
* @route '/sitemap.xml'
*/
sitemap.url = (options?: RouteQueryOptions) => {




    return sitemap.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\SitemapController::__invoke
* @see app/Http/Controllers/SitemapController.php:10
* @route '/sitemap.xml'
*/
sitemap.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: sitemap.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SitemapController::__invoke
* @see app/Http/Controllers/SitemapController.php:10
* @route '/sitemap.xml'
*/
sitemap.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: sitemap.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\SitemapController::__invoke
* @see app/Http/Controllers/SitemapController.php:10
* @route '/sitemap.xml'
*/
const sitemapForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: sitemap.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SitemapController::__invoke
* @see app/Http/Controllers/SitemapController.php:10
* @route '/sitemap.xml'
*/
sitemapForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: sitemap.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SitemapController::__invoke
* @see app/Http/Controllers/SitemapController.php:10
* @route '/sitemap.xml'
*/
sitemapForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: sitemap.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

sitemap.form = sitemapForm

/**
* @see [serialized-closure]:2
* @route '/robots.txt'
*/
export const robots = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: robots.url(options),
    method: 'get',
})

robots.definition = {
    methods: ["get","head"],
    url: '/robots.txt',
} satisfies RouteDefinition<["get","head"]>

/**
* @see [serialized-closure]:2
* @route '/robots.txt'
*/
robots.url = (options?: RouteQueryOptions) => {




    return robots.definition.url + queryParams(options)
}

/**
* @see [serialized-closure]:2
* @route '/robots.txt'
*/
robots.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: robots.url(options),
    method: 'get',
})

/**
* @see [serialized-closure]:2
* @route '/robots.txt'
*/
robots.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: robots.url(options),
    method: 'head',
})

/**
* @see [serialized-closure]:2
* @route '/robots.txt'
*/
const robotsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: robots.url(options),
    method: 'get',
})

/**
* @see [serialized-closure]:2
* @route '/robots.txt'
*/
robotsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: robots.url(options),
    method: 'get',
})

/**
* @see [serialized-closure]:2
* @route '/robots.txt'
*/
robotsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: robots.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

robots.form = robotsForm
