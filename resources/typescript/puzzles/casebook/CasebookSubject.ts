import {Client} from "openapi-fetch";
import type { paths } from "../../schema/schema";
import {CasebookSubjectClue} from "./CasebookSubjectClue";
import {Clue} from "./clue";
import {createApiClient} from "../../helpers/CreateApiClient";

export class CasebookSubject extends HTMLElement {

    private readonly subjectId: string;

    private readonly instanceCode: string;

    private client: Client<paths>;

    private subjectWrapper: HTMLDivElement;

    private cluesList: HTMLDivElement;

    private timer: number | undefined;

    constructor() {
        super();

        let subjectId: string|undefined = this.dataset.subjectid,
            instanceCode: string|undefined = this.dataset.instancecode,
            template: HTMLElement|null = document.getElementById("casebook-subject-template");

        if (subjectId === undefined) {
            throw new Error(`No subject found with id ${subjectId}`);
        }


        if (instanceCode === undefined) {
            throw new Error(`No instance found with id ${instanceCode}`);
        }

        if (null === template) {
            throw new Error(`No Template found`);
        }

        this.subjectId = subjectId;
        this.instanceCode = instanceCode;

        this.client = createApiClient();

        // @ts-ignore
        let templateContent = template.content;

        const shadowRoot = this.attachShadow({ mode: "open" });

        shadowRoot.appendChild(templateContent.cloneNode(true));

        const subjectWrapper: HTMLDivElement|null = shadowRoot.querySelector('div.subject-wrapper');

        if (null === subjectWrapper) {
            throw new Error('No subject wrapper element found.');
        }
        this.subjectWrapper = subjectWrapper;

        const cluesList: HTMLDivElement|null = shadowRoot.querySelector('div.clues-list');

        if (null === cluesList) {
            throw new Error('No subject wrapper element found.');
        }
        this.cluesList = cluesList;
        this.buildContent();

    }

    connectedCallback() {
        this.timer = setInterval(()=> this.buildContent(), 5000);
    }

    disconnectedCallback() {
        clearInterval(this.timer);
        this.timer = undefined;
    }

    private async buildContent() {

        const { data, error } = await this.client.GET("/api/puzzles/instances/static/casebook/{instanceCode}/subjects/{subject}",
            {
                params: {
                    path: {
                        instanceCode: this.instanceCode,
                        subject: this.subjectId,
                    },
                },
            }
        );
        if (error !== undefined) {
            throw new Error();
        }
        if (data.name && data.description) {

            const titleElement: HTMLHeadingElement|null|undefined = this.shadowRoot?.querySelector('slot[name="subject-name"]');
            const cardBodyElement: HTMLDivElement|null|undefined = this.shadowRoot?.querySelector('slot[name="subject-description"]');

            if (titleElement === null || titleElement === undefined) {
                throw new Error('Missing title element');

            }

            if (cardBodyElement === null || cardBodyElement === undefined) {
                throw new Error('Missing body element');
            }

            if (data.imageUri) {
                const imageElement: HTMLImageElement|null|undefined = this.shadowRoot?.querySelector('img[slot="image"]');

                if (imageElement === null || imageElement === undefined) {
                    throw new Error("Image URL defined but image element missing.")
                }
                imageElement.src = `${data.imageUri}`;
                imageElement.alt = data.isRevealed ? data.name : 'Undiscovered';
            }


            titleElement.textContent = data.name;
            cardBodyElement.innerHTML = data.description;

            this.subjectWrapper.classList.add('loaded');

            if (!data.isRevealed) {
                titleElement.textContent = 'Undiscovered';
                cardBodyElement.innerHTML = 'Undiscovered';
            } else {
                this.subjectWrapper.classList.add('revealed');
                if (data.clues) {
                    await this.buildClues(data.clues);
                }
            }
        }
    }

    private async buildClues(clues: {
        id? : unknown,
        title?: string,
        body?: string,
        type?: string,
        typeLabel?: string,
        updatedAt?: string | null | undefined,
        revealedDate?: string | null | undefined
    }[]) {

        if (clues.length > 0) {
            let emptyCluesMessage: HTMLElement | null | undefined = this.shadowRoot?.querySelector('em.empty_clues');
            if (emptyCluesMessage !== undefined && emptyCluesMessage !== null) {
                emptyCluesMessage.remove();
            }
        }

        clues.forEach(clue => {
            if (clue !== null) {
                let clueObject= clue as unknown as Clue;
                let clueExists: boolean = true;
                let clueComponent: CasebookSubjectClue | null | undefined= this.shadowRoot?.querySelector(`#clue-${clueObject.id}`);
                if (clueComponent === null || clueComponent === undefined) {
                    this.dispatchEvent(new CustomEvent('child-element-resize', {bubbles: true, composed: true}));
                    clueExists = false;
                    clueComponent = new CasebookSubjectClue();
                    clueComponent.id = `clue-${clueObject.id}`;
                }
                if (!clueExists || clueComponent.dataset.updated !== clueObject.updatedAt) {
                    clueComponent.setAttribute('data-title', clueObject.title);
                    clueComponent.setAttribute('data-body', clueObject.body);
                    clueComponent.setAttribute('data-type', clueObject.type);
                    clueComponent.setAttribute('data-typelabel', clueObject.typeLabel);
                    clueComponent.setAttribute('data-updated', clueObject.updatedAt);
                    if (clueObject.revealedDate !== null && clueObject.revealedDate !== undefined) {
                        clueComponent.setAttribute('data-revealed', clueObject.revealedDate);
                    }

                    if (!clueExists) {
                        this.cluesList.appendChild(clueComponent);
                    }
                }

            }
        })
    }
}
