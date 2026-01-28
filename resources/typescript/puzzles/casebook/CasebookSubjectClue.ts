export class CasebookSubjectClue extends HTMLDivElement {

    static observedAttributes = ["data-title", 'data-body', 'data-type', 'data-typelabel', 'data-updated', 'data-revealed'];

    private titleElement: HTMLElement;

    private bodyElement: HTMLElement;

    private typeElement: HTMLElement;

    private revealedElement: HTMLElement;

    constructor() {
        super();
        this.classList.add('casebook-subject-clue');
        this.classList.remove('casebook-subject-clue-template');
        let template: HTMLElement | null = document.getElementById("casebook-subject-clue-template");
        if (null === template) {
            throw new Error(`No Clue Template found`);
        }
        //@ts-ignore
        let templateContent = template.content;

        const shadowRoot = this.attachShadow({mode: "open"});

        shadowRoot.appendChild(templateContent.cloneNode(true));

        let titleElement: HTMLElement | null = shadowRoot.querySelector('[slot="clue-title"]');
        if (null === titleElement) {
            throw new Error(`No Clue Title found`);
        }

        let bodyElement: HTMLElement | null = shadowRoot.querySelector('[slot="clue-body"]');
        if (null === bodyElement) {
            throw new Error(`No Clue Body found`);
        }
        let typeElement: HTMLElement | null = shadowRoot.querySelector('[slot="clue-type"]');
        if (null === typeElement) {
            throw new Error(`No Clue Type found`);
        }

        let revealedElement: HTMLElement | null = shadowRoot.querySelector('[slot="revealed-at"]');
        if (null === revealedElement) {
            throw new Error(`No revealed element found`);
        }

        this.titleElement = titleElement;
        this.bodyElement = bodyElement;
        this.typeElement = typeElement;
        this.revealedElement = revealedElement;
    }

    connectedCallback() {
        this.guardDataset();
    }

    guardDataset(): void {
        if (this.id === undefined) {
            throw new Error('No clueid was found.');
        }
        if (this.dataset.title === undefined) {
            throw new Error('No title was found.');
        }
        if (this.dataset.body === undefined) {
            throw new Error('No body was found.');
        }
        if (this.dataset.type === undefined) {
            throw new Error('No type was found.');
        }
        if (this.dataset.typelabel === undefined) {
            throw new Error('No type label was found.');
        }
        if (this.dataset.updated === undefined) {
            throw new Error('No updated was found.');
        }
        if (this.dataset.revealed === undefined) {
            throw new Error('No revealed was found.');
        }
    }

    attributeChangedCallback(name: string, oldValue: string, newValue: string) {
        if (oldValue !== newValue) {
            switch (name) {
                case 'data-title':
                    this.titleElement.innerHTML = newValue;
                    break;
                case 'data-body':
                    this.bodyElement.innerHTML = newValue;
                    break;
                case 'data-typelabel':
                    this.typeElement.innerHTML = newValue;
                    break;
                case 'data-type':
                    this.typeElement.removeAttribute('class');
                    this.typeElement.classList.add('clue-type');
                    this.typeElement.classList.add(newValue);
                    break;
                case 'data-revealed':
                    let revealedDate = new Date(newValue);
                    this.revealedElement.innerHTML = `Revealed ${revealedDate.toDateString()} ${revealedDate.getHours()}:${revealedDate.getMinutes()}`;
            }
        }
    }
}
