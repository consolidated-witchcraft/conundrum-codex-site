export class WikiEditor extends HTMLDivElement {

    private knownActions: string[] = [
        'bold',
        'italic',
        'unordered-list',
        'ordered-list',
        'quote',
        'wiki-link'
    ];

    private textarea: HTMLTextAreaElement;

    private toolbar: HTMLElement;

    connectedCallback(): void {
        console.log("Wikieditor Connected")
    }

    constructor() {
        super();
        let template: HTMLElement|null = document.getElementById("wikieditor-widget-template");


        if (null === template) {
            throw new Error(`No Template found`);
        }


        // @ts-ignore
        let templateContent = template.content;

        const shadowRoot = this.attachShadow({ mode: "open" });

        shadowRoot.appendChild(templateContent.cloneNode(true));

        let textarea: HTMLTextAreaElement|null = shadowRoot.querySelector("textarea.wikieditor-input");
        let toolbar: HTMLElement|null = shadowRoot.querySelector("nav.wiki-editor-toolbar");

        if (null === textarea) {
            throw new Error(`No textarea found for Wiki editor`);
        }
        if (null === toolbar) {
            throw new Error(`No toolbar found for Wiki editor`);
        }

        this.textarea = textarea;
        this.toolbar = toolbar;

    }
    //How this system will work?
    //The widget's HTML has been built from template. When an Item is wrapped in the editor surface and a button is clicked, we take the HTML from the surface, convert it to markDown and copy it to the target input.
    //This is presuming we're in edit mode. If we're in view mode, we need to do the opposite.
}
