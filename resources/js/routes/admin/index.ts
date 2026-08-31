import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
import onboarding from './onboarding'
import contactSubmissions from './contact-submissions'
import auditLog from './audit-log'
import settings from './settings'
import theme from './theme'
import branding from './branding'
import loaderQuotes from './loader-quotes'
import pages from './pages'
import publications from './publications'
import languageFiles from './language-files'
/**
* @see \App\Http\Controllers\Admin\AdminEntryController::__invoke
* @see app/Http/Controllers/Admin/AdminEntryController.php:12
* @route '/admin'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\AdminEntryController::__invoke
* @see app/Http/Controllers/Admin/AdminEntryController.php:12
* @route '/admin'
*/
index.url = (options?: RouteQueryOptions) => {




    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AdminEntryController::__invoke
* @see app/Http/Controllers/Admin/AdminEntryController.php:12
* @route '/admin'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminEntryController::__invoke
* @see app/Http/Controllers/Admin/AdminEntryController.php:12
* @route '/admin'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\AdminEntryController::__invoke
* @see app/Http/Controllers/Admin/AdminEntryController.php:12
* @route '/admin'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminEntryController::__invoke
* @see app/Http/Controllers/Admin/AdminEntryController.php:12
* @route '/admin'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminEntryController::__invoke
* @see app/Http/Controllers/Admin/AdminEntryController.php:12
* @route '/admin'
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
* @see \App\Http\Controllers\Admin\Auth\AuthenticatedSessionController::login
* @see app/Http/Controllers/Admin/Auth/AuthenticatedSessionController.php:15
* @route '/admin/login'
*/
export const login = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: login.url(options),
    method: 'get',
})

login.definition = {
    methods: ["get","head"],
    url: '/admin/login',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\Auth\AuthenticatedSessionController::login
* @see app/Http/Controllers/Admin/Auth/AuthenticatedSessionController.php:15
* @route '/admin/login'
*/
login.url = (options?: RouteQueryOptions) => {




    return login.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\Auth\AuthenticatedSessionController::login
* @see app/Http/Controllers/Admin/Auth/AuthenticatedSessionController.php:15
* @route '/admin/login'
*/
login.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: login.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\Auth\AuthenticatedSessionController::login
* @see app/Http/Controllers/Admin/Auth/AuthenticatedSessionController.php:15
* @route '/admin/login'
*/
login.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: login.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\Auth\AuthenticatedSessionController::login
* @see app/Http/Controllers/Admin/Auth/AuthenticatedSessionController.php:15
* @route '/admin/login'
*/
const loginForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: login.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\Auth\AuthenticatedSessionController::login
* @see app/Http/Controllers/Admin/Auth/AuthenticatedSessionController.php:15
* @route '/admin/login'
*/
loginForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: login.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\Auth\AuthenticatedSessionController::login
* @see app/Http/Controllers/Admin/Auth/AuthenticatedSessionController.php:15
* @route '/admin/login'
*/
loginForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: login.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

login.form = loginForm

/**
* @see \App\Http\Controllers\Admin\Auth\AuthenticatedSessionController::authenticate
* @see app/Http/Controllers/Admin/Auth/AuthenticatedSessionController.php:28
* @route '/admin/login'
*/
export const authenticate = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: authenticate.url(options),
    method: 'post',
})

authenticate.definition = {
    methods: ["post"],
    url: '/admin/login',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\Auth\AuthenticatedSessionController::authenticate
* @see app/Http/Controllers/Admin/Auth/AuthenticatedSessionController.php:28
* @route '/admin/login'
*/
authenticate.url = (options?: RouteQueryOptions) => {




    return authenticate.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\Auth\AuthenticatedSessionController::authenticate
* @see app/Http/Controllers/Admin/Auth/AuthenticatedSessionController.php:28
* @route '/admin/login'
*/
authenticate.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: authenticate.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\Auth\AuthenticatedSessionController::authenticate
* @see app/Http/Controllers/Admin/Auth/AuthenticatedSessionController.php:28
* @route '/admin/login'
*/
const authenticateForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: authenticate.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\Auth\AuthenticatedSessionController::authenticate
* @see app/Http/Controllers/Admin/Auth/AuthenticatedSessionController.php:28
* @route '/admin/login'
*/
authenticateForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: authenticate.url(options),
    method: 'post',
})

authenticate.form = authenticateForm

/**
* @see \App\Http\Controllers\Admin\Auth\AuthenticatedSessionController::logout
* @see app/Http/Controllers/Admin/Auth/AuthenticatedSessionController.php:56
* @route '/admin/logout'
*/
export const logout = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: logout.url(options),
    method: 'post',
})

logout.definition = {
    methods: ["post"],
    url: '/admin/logout',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\Auth\AuthenticatedSessionController::logout
* @see app/Http/Controllers/Admin/Auth/AuthenticatedSessionController.php:56
* @route '/admin/logout'
*/
logout.url = (options?: RouteQueryOptions) => {




    return logout.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\Auth\AuthenticatedSessionController::logout
* @see app/Http/Controllers/Admin/Auth/AuthenticatedSessionController.php:56
* @route '/admin/logout'
*/
logout.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: logout.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\Auth\AuthenticatedSessionController::logout
* @see app/Http/Controllers/Admin/Auth/AuthenticatedSessionController.php:56
* @route '/admin/logout'
*/
const logoutForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: logout.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\Auth\AuthenticatedSessionController::logout
* @see app/Http/Controllers/Admin/Auth/AuthenticatedSessionController.php:56
* @route '/admin/logout'
*/
logoutForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: logout.url(options),
    method: 'post',
})

logout.form = logoutForm

const admin = {
    index: Object.assign(index, index),
onboarding: Object.assign(onboarding, onboarding),
login: Object.assign(login, login),
authenticate: Object.assign(authenticate, authenticate),
contactSubmissions: Object.assign(contactSubmissions, contactSubmissions),
auditLog: Object.assign(auditLog, auditLog),
settings: Object.assign(settings, settings),
theme: Object.assign(theme, theme),
branding: Object.assign(branding, branding),
loaderQuotes: Object.assign(loaderQuotes, loaderQuotes),
pages: Object.assign(pages, pages),
publications: Object.assign(publications, publications),
languageFiles: Object.assign(languageFiles, languageFiles),
logout: Object.assign(logout, logout),
}

export default admin