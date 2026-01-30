import createClient, {Client} from "openapi-fetch";
import type {components, paths} from "../../schema/schema";

import {ButtonPuzzleButton} from "./ButtonPuzzleButton";
import {ButtonsConfig} from "./types/ButtonsConfig";
import {ButtonClickDetail} from "./types/ButtonClickDetail";

export class ButtonsElement extends HTMLDivElement {
    private client: Client<paths>;

    private instanceCode: string;

    private templateSlug: string;

    constructor() {
        super();
        this.classList.add('buttons-element');
        let env: string = 'process.env.APP_ENV';
        let apiBaseUrl = (env == '"dev"') ? 'http://localhost:8089' : 'https://conundrumcodex.com';
        let instanceCode: string | undefined = this.dataset.instancecode;
        let templateSlug: string | undefined = this.dataset.templateslug;

        if (instanceCode === undefined) {
            throw new Error("No instance code set.");
        }
        this.instanceCode = instanceCode;
        if (templateSlug === undefined) {
            throw new Error("No template slug set.");
        }
        this.templateSlug = templateSlug;
        this.client = createClient<paths>({baseUrl: apiBaseUrl});

        this.buildContent()



    }

    connectedCallback() {
    }

    async buildContent(): Promise<void> {
        const {data, error} = await this.client.GET(`/api/puzzles/instances/dynamic/{templateSlug}/{instanceCode}`,
            {
                params: {
                    path: {
                        instanceCode: this.instanceCode,
                        templateSlug: this.templateSlug,
                    },
                },
            }
        );
        if (error !== undefined) {
            throw new Error('Could not retrieve data from API');
        }

        let buttonsConfig: ButtonsConfig = this.unpackConfig(data);

        let clueElement = document.createElement("div");
        clueElement.className = 'buttons-clue';
        clueElement.innerHTML = buttonsConfig.clue;

        this.appendChild(clueElement);

        buttonsConfig.buttons.forEach((buttonValue: string) => {
            let buttonElement: ButtonPuzzleButton = new ButtonPuzzleButton();
            buttonElement.dataset.buttonvalue = buttonValue;
            buttonElement.innerHTML = buttonValue;

            this.append(buttonElement);
        })

        let delta = 0;
        let nextCorrectValue: string|undefined = buttonsConfig.solution[delta];
        console.log(nextCorrectValue);


        this.addEventListener('puzzle-button-click', (e:CustomEventInit<ButtonClickDetail>) => {
            if (e.detail?.value === nextCorrectValue) {
                e.detail?.target.dispatchEvent(new CustomEvent('successful-click-attempt'));
                delta++;
            }
            nextCorrectValue = buttonsConfig.solution[delta];
            if (nextCorrectValue === undefined) {
                this.handleSuccess();
            }
        })
    }

    private handleSuccess(): void {
        let allButtons: NodeListOf<ButtonPuzzleButton> = this.querySelectorAll('.puzzle-button');
        allButtons.forEach((button: ButtonPuzzleButton) => {
            button.setAttribute('disabled', 'disabled');
        })
    }


    private unpackConfig(data: { "@context"?: string | ({ "@vocab": string; hydra: "http://www.w3.org/ns/hydra/core#"; } & { [key: string]: unknown; }); "@id": string; "@type": string; } & { instanceCode?: string; name?: string; description?: string; config?: components["schemas"]["DynamicPuzzleFieldDto.jsonld"][]; publicationDate?: string | null; }): ButtonsConfig {
        let clue: string | null = null;

        let failure: string | null = null;

        let buttons: string[] | null = null;

        let solution: string[] | null = null;

        if (data.config === undefined) {
            throw new Error("Malformed configuration payload");
        }
        data.config.forEach((config: any) => {

            if (config.name === 'clue') {
                clue = config.value;
            }

            if (config.name === 'failure') {
                failure = `${config.value.x}d${config.value.d}p${config.value.p}`;
            }

            if (config.name === 'buttons') {
                buttons = config.value;
            }

            if (config.name === 'solution') {
                solution = config.value;
            }
        });

        if (clue === null) {
            throw new Error("Malformed configuration payload: Missing Clue");
        }
        if (failure === null) {
            throw new Error("Malformed configuration payload: Missing Failure Roll");
        }

        if (buttons === null) {
            throw new Error("Malformed configuration payload: Missing Buttons Config");
        }

        if (solution === null) {
            throw new Error("Malformed configuration payload: Missing Solution Config");
        }
        return {
            clue: clue,
            failure: failure,
            buttons: buttons,
            solution: solution,
        };
    }
}
