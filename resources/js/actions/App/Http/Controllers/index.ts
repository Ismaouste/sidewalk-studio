import AudiencePingController from './AudiencePingController'
import NewsletterSubscriptionController from './NewsletterSubscriptionController'
import AuditRequestController from './AuditRequestController'
import SiteController from './SiteController'
import ContactSubmissionController from './ContactSubmissionController'
import WritingController from './WritingController'
import CaseStudyController from './CaseStudyController'
import ContentVisualController from './ContentVisualController'
import Admin from './Admin'
import SitemapController from './SitemapController'
const Controllers = {
    AudiencePingController: Object.assign(AudiencePingController, AudiencePingController),
NewsletterSubscriptionController: Object.assign(NewsletterSubscriptionController, NewsletterSubscriptionController),
AuditRequestController: Object.assign(AuditRequestController, AuditRequestController),
SiteController: Object.assign(SiteController, SiteController),
ContactSubmissionController: Object.assign(ContactSubmissionController, ContactSubmissionController),
WritingController: Object.assign(WritingController, WritingController),
CaseStudyController: Object.assign(CaseStudyController, CaseStudyController),
ContentVisualController: Object.assign(ContentVisualController, ContentVisualController),
Admin: Object.assign(Admin, Admin),
SitemapController: Object.assign(SitemapController, SitemapController),
}

export default Controllers