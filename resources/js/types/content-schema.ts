/**
 * The content declaration, as the server hands it to the admin.
 *
 * This mirrors `App\Content\Schema\ContentSchema::toArray()` exactly, and the
 * mirroring is the point: the editor is generated from the same declaration
 * that the save path validates against, so a form cannot offer a field the
 * server will reject, and a field cannot be added to the model without the
 * form growing an input for it.
 */
export type ContentFieldType =
    'line' | 'text' | 'markdown' | 'slug' | 'date' | 'url' | 'choice' | 'group';

export type ContentField = {
    name: string;
    type: ContentFieldType;
    label?: string;
    help?: string;
    required: boolean;
    repeats: boolean;
    /**
     * For a repeating group: which child field names an item. It is what the
     * editor puts in each `<summary>`, so eighteen inputs read as three named
     * rows the operator can scan and open one of.
     */
    itemLabel?: string;
    choices?: string[];
    children?: ContentField[];
};

export type ContentSchema = {
    key: string;
    label: string;
    fields: ContentField[];
};

/**
 * A page's metadata lives in columns and its content in a JSON payload, so
 * the editor binds the two halves to different objects. The server splits on
 * the same list when it reassembles them for validation.
 */
export type PageSchemaPayload = {
    schema: ContentSchema;
    metaFields: string[];
};
