import AdminEntryController from './AdminEntryController'
import Auth from './Auth'
import AdminContactSubmissionController from './AdminContactSubmissionController'
import AdminAuditLogController from './AdminAuditLogController'
import SiteSettingsController from './SiteSettingsController'
import AdminThemeController from './AdminThemeController'
import AdminBrandingController from './AdminBrandingController'
import AdminLoaderQuoteController from './AdminLoaderQuoteController'
import AdminPageController from './AdminPageController'
import AdminPublicationController from './AdminPublicationController'
import AdminLanguageFileController from './AdminLanguageFileController'
const Admin = {
    AdminEntryController: Object.assign(AdminEntryController, AdminEntryController),
Auth: Object.assign(Auth, Auth),
AdminContactSubmissionController: Object.assign(AdminContactSubmissionController, AdminContactSubmissionController),
AdminAuditLogController: Object.assign(AdminAuditLogController, AdminAuditLogController),
SiteSettingsController: Object.assign(SiteSettingsController, SiteSettingsController),
AdminThemeController: Object.assign(AdminThemeController, AdminThemeController),
AdminBrandingController: Object.assign(AdminBrandingController, AdminBrandingController),
AdminLoaderQuoteController: Object.assign(AdminLoaderQuoteController, AdminLoaderQuoteController),
AdminPageController: Object.assign(AdminPageController, AdminPageController),
AdminPublicationController: Object.assign(AdminPublicationController, AdminPublicationController),
AdminLanguageFileController: Object.assign(AdminLanguageFileController, AdminLanguageFileController),
}

export default Admin