import SiteController from './SiteController'
import ContactSubmissionController from './ContactSubmissionController'
import WritingController from './WritingController'
import CaseStudyController from './CaseStudyController'
import ContentVisualController from './ContentVisualController'
import Admin from './Admin'
import SitemapController from './SitemapController'


const Controllers = {
    SiteController: Object.assign(SiteController, SiteController),
    ContactSubmissionController: Object.assign(ContactSubmissionController, ContactSubmissionController),
    WritingController: Object.assign(WritingController, WritingController),
    CaseStudyController: Object.assign(CaseStudyController, CaseStudyController),
    ContentVisualController: Object.assign(ContentVisualController, ContentVisualController),
    Admin: Object.assign(Admin, Admin),
    SitemapController: Object.assign(SitemapController, SitemapController),
}

export default Controllers