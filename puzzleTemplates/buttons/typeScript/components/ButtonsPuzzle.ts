import {createApiClient} from "@shared/helpers/CreateApiClient";
import type { paths } from "../../../../resources/typescript/schema/schema";
import {Client} from "openapi-fetch";
export class ButtonsPuzzle extends HTMLElement {

    private client: Client<paths>;

    private instanceCode: string|undefined;

    private templateSlug: string|undefined;

    constructor() {
        super();
        this.client = createApiClient();
    }

    connectedCallback(): void {
        let templateSlug: string|undefined = this.dataset.templateslug;
        let instanceCode: string|undefined = this.dataset.instancecode;
        if (templateSlug === undefined) {
            throw new Error("Template Slug is missing. Aborting");
        }
        if (instanceCode === undefined) {
            throw new Error("Instance code is missing. aborting");
        }
        this.instanceCode = instanceCode;
        this.templateSlug = templateSlug;
        this.getData();
    }

    async getData() {
        const { data, error } = await this.client.GET("/api/puzzles/instances/dynamic/{templateSlug}/{instanceCode}",
            {
                params: {
                    path: {
                        instanceCode: this.instanceCode,
                        templateSlug: this.templateSlug,
                    },
                },
            }
        );
        console.log(data);
    }
}
