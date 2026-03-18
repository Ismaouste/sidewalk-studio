import AdminOnboardingController from './AdminOnboardingController'
import AuthenticatedSessionController from './AuthenticatedSessionController'


const Auth = {
    AdminOnboardingController: Object.assign(AdminOnboardingController, AdminOnboardingController),
    AuthenticatedSessionController: Object.assign(AuthenticatedSessionController, AuthenticatedSessionController),
}

export default Auth