/**
 * Names for the regions and grouped controls that assistive technology
 * announces on the public surface. Nothing here is drawn on screen, which is
 * why these six strings stayed English in both locales: a sighted reviewer
 * comparing the two languages side by side has nothing to compare, and the
 * constitution calls accessibility non-negotiable.
 *
 * They are grouped under `layout` rather than with the components that carry
 * them, because they name parts of the page shell — a breadcrumb is a
 * breadcrumb wherever it is rendered.
 *
 * English copy. This is the reference shape the French file is checked
 * against. Keys are sorted; `sort-keys` enforces it in lint.
 */
export default {
    breadcrumb: 'Breadcrumb',
    colorTheme: 'Color theme',
    contentMeta: 'Content metadata',
    nextStep: 'Next step',
    relatedItems: 'Related items',
};
