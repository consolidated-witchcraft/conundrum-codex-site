import {createApiClient} from "@shared/helpers/CreateApiClient";
import type {components, paths} from "../../../../resources/typescript/schema/schema";
import {Client} from "openapi-fetch";
import {ButtonPuzzleButton} from "./ButtonPuzzleButton";

interface ButtonsPuzzleConfig {
    buttons: string[];
    clue: string;
    failureMessage: string;
    failureRoll: string;
    solution: string[];
    successMessage: string;
}

interface DiceRollPayload {
    total?: number;
}

interface DieRollValue {
    d: number;
    p?: number | null;
    x: number;
}

export class ButtonsPuzzle extends HTMLElement {
    private client: Client<paths>;

    private buttonsContainer: HTMLDivElement | null = null;

    private buttonsWrapper: HTMLDivElement | null = null;

    private clueElement: HTMLParagraphElement | null = null;

    private hasBoundPressedListener = false;

    private instanceCode: string | null = null;

    private isLocked = false;

    private hasPostedInstanceStart = false;

    private hasPostedInstanceSuccess = false;

    private messageElement: HTMLParagraphElement | null = null;

    private progressIndex = 0;

    private resetButton: HTMLButtonElement | null = null;

    private solution: string[] = [];

    private templateSlug: string | null = null;

    private userId: string | null = null;

    constructor() {
        super();
        this.client = createApiClient();
    }

    connectedCallback(): void {
        const templateSlug = this.dataset.templateslug;
        const instanceCode = this.dataset.instancecode;
        const userId = this.dataset.userid;

        if (templateSlug === undefined) {
            throw new Error("Template Slug is missing. Aborting");
        }

        if (instanceCode === undefined) {
            throw new Error("Instance code is missing. Aborting");
        }

        if (userId === undefined) {
            throw new Error("User ID is missing. Aborting");
        }

        const parsedUserId = Number.parseInt(userId, 10);

        if (Number.isNaN(parsedUserId)) {
            throw new Error("User ID is invalid. Aborting");
        }

        if (!this.hasBoundPressedListener) {
            this.addEventListener("pressed", this.handlePressedEvent as EventListener);
            this.hasBoundPressedListener = true;
        }

        this.instanceCode = instanceCode;
        this.templateSlug = templateSlug;
        this.userId = String(parsedUserId);

        void this.buildPuzzle();
    }

    disconnectedCallback(): void {
        if (!this.hasBoundPressedListener) {
            return;
        }

        this.removeEventListener("pressed", this.handlePressedEvent as EventListener);
        this.hasBoundPressedListener = false;
    }

    private async buildPuzzle(): Promise<void> {
        const config = await this.getPuzzleConfig();

        await this.postPlayEvent("instance_start");

        this.progressIndex = 0;
        this.isLocked = false;
        this.solution = config.solution;

        this.classList.add("buttons-puzzle");
        this.replaceChildren();

        const clueElement = document.createElement("p");
        clueElement.className = "buttons-puzzle__clue";
        clueElement.textContent = config.clue;

        const buttonsWrapper = document.createElement("div");
        buttonsWrapper.className = "buttons-puzzle__buttons-wrapper";

        const buttonsContainer = document.createElement("div");
        buttonsContainer.className = "buttons-puzzle__buttons";

        for (const buttonValue of config.buttons) {
            const buttonElement = document.createElement("button", {is: "button-puzzle-button"}) as ButtonPuzzleButton;
            buttonElement.dataset.buttonValue = buttonValue;
            buttonElement.classList.add('btn', 'btn-primary');
            buttonElement.textContent = buttonValue;
            buttonsContainer.appendChild(buttonElement);
        }

        buttonsWrapper.appendChild(buttonsContainer);

        const messageElement = document.createElement("p");
        messageElement.className = "buttons-puzzle__message";
        messageElement.classList.add("hide");

        const resetButton = document.createElement("button");
        resetButton.className = "buttons-puzzle__reset";
        resetButton.type = "button";
        resetButton.textContent = "Reset";
        resetButton.hidden = true;
        resetButton.classList.add('btn', 'btn-danger');
        resetButton.addEventListener("click", this.handleReset);

        this.clueElement = clueElement;
        this.buttonsWrapper = buttonsWrapper;
        this.buttonsContainer = buttonsContainer;
        this.messageElement = messageElement;
        this.resetButton = resetButton;
        this.append(clueElement, buttonsWrapper, messageElement, resetButton);
        this.dataset.successMessage = config.successMessage;
        this.dataset.failureMessage = config.failureMessage;
        this.dataset.failureRoll = config.failureRoll;
    }

    private async getPuzzleConfig(): Promise<ButtonsPuzzleConfig> {
        if (this.instanceCode === null || this.templateSlug === null) {
            throw new Error("Puzzle identifiers are not set.");
        }

        const {data, error} = await this.client.GET("/api/puzzles/instances/dynamic/{templateSlug}/{instanceCode}", {
            params: {
                path: {
                    instanceCode: this.instanceCode,
                    templateSlug: this.templateSlug,
                },
            },
        });

        if (error !== undefined || data === undefined) {
            throw new Error("Unable to retrieve puzzle data.");
        }

        const configFields = this.getConfigMap(data.config);

        return {
            buttons: this.getRequiredStringArray(configFields, "buttons"),
            clue: this.getRequiredString(configFields, "clue"),
            failureMessage: this.getRequiredString(configFields, "failure_message"),
            failureRoll: this.getRequiredDieRoll(configFields, "failure"),
            solution: this.getRequiredStringArray(configFields, "solution"),
            successMessage: this.getRequiredString(configFields, "success_message"),
        };
    }

    private getConfigMap(
        config: components["schemas"]["ApiDynamicPuzzleInstanceDto.jsonld"]["config"] | undefined,
    ): Record<string, unknown> {
        if (config === undefined || config === null || typeof config !== "object" || Array.isArray(config)) {
            throw new Error("Puzzle config payload is malformed.");
        }

        return config as Record<string, unknown>;
    }

    private getFieldValue(config: Record<string, unknown>, fieldName: string): unknown {
        const field = config[fieldName];

        if (field === undefined || field === null || typeof field !== "object" || Array.isArray(field)) {
            throw new Error(`Puzzle config field ${fieldName} is missing or invalid.`);
        }

        return (field as {value?: unknown}).value;
    }

    private getRequiredString(config: Record<string, unknown>, fieldName: string): string {
        const value = this.getFieldValue(config, fieldName);

        if (typeof value !== "string") {
            throw new Error(`Puzzle config field ${fieldName} must be a string.`);
        }

        return value;
    }

    private getRequiredStringArray(config: Record<string, unknown>, fieldName: string): string[] {
        const value = this.getFieldValue(config, fieldName);

        if (!Array.isArray(value) || value.some((item) => typeof item !== "string")) {
            throw new Error(`Puzzle config field ${fieldName} must be a string array.`);
        }

        return value;
    }

    private getRequiredDieRoll(config: Record<string, unknown>, fieldName: string): string {
        const value = this.getFieldValue(config, fieldName);

        if (value === null || typeof value !== "object" || Array.isArray(value)) {
            throw new Error(`Puzzle config field ${fieldName} must be a die roll.`);
        }

        const dieRoll = value as DieRollValue;

        if (typeof dieRoll.x !== "number" || typeof dieRoll.d !== "number") {
            throw new Error(`Puzzle config field ${fieldName} must contain die roll values.`);
        }

        const bonus = dieRoll.p ?? 0;

        if (typeof bonus !== "number") {
            throw new Error(`Puzzle config field ${fieldName} has an invalid bonus value.`);
        }

        const baseRoll = `${dieRoll.x}d${dieRoll.d}`;

        if (bonus === 0) {
            return baseRoll;
        }

        return `${baseRoll}p${bonus}`;
    }

    private readonly handlePressedEvent = async (event: CustomEvent<{value: string}>): Promise<void> => {
        if (this.isLocked) {
            return;
        }

        const nextExpectedValue = this.solution[this.progressIndex];
        const buttonTarget = event.target;

        if (event.detail.value !== nextExpectedValue) {
            this.isLocked = true;
            this.disableButtons();
            await this.handleFailure();
            return;
        }

        if (buttonTarget instanceof ButtonPuzzleButton) {
            buttonTarget.dispatchEvent(new CustomEvent("successful-press"));
        }

        this.progressIndex++;

        if (this.progressIndex >= this.solution.length) {
            this.isLocked = true;
            this.disableButtons();
            this.handleSuccess();
        }
    };

    private handleSuccess(): void {
        const successMessage = this.dataset.successMessage;

        if (successMessage === undefined || this.messageElement === null) {
            throw new Error("Success message element is not configured.");
        }

        this.messageElement.textContent = successMessage;
        this.showMessage();
        void this.postPlayEvent("instance_win");
    }

    private async postPlayEvent(type: "instance_start" | "instance_win"): Promise<void> {
        if (this.userId === null || this.instanceCode === null || this.templateSlug === null) {
            throw new Error("Play event identifiers are not set.");
        }

        if (type === "instance_start" && this.hasPostedInstanceStart) {
            return;
        }

        if (type === "instance_win" && this.hasPostedInstanceSuccess) {
            return;
        }

        const {error} = await this.client.POST("/api/user/{userId}/playEvents", {
            headers: {
                Accept: "application/ld+json",
                "Content-Type": "application/ld+json",
            },
            params: {
                path: {
                    userId: this.userId,
                },
            },
            body: {
                puzzleInstanceCode: this.instanceCode,
                puzzleTemplateSlug: this.templateSlug,
                type,
            },
        });

        if (error !== undefined) {
            const apiError = typeof error === "object" && error !== null && "detail" in error && typeof error.detail === "string"
                ? error.detail
                : "Unknown API error.";
            throw new Error(`Unable to record play event ${type}: ${apiError}`);
        }

        if (type === "instance_start") {
            this.hasPostedInstanceStart = true;
            return;
        }

        this.hasPostedInstanceSuccess = true;
    }

    private async handleFailure(): Promise<void> {
        const failureMessage = this.dataset.failureMessage;
        const failureRoll = this.dataset.failureRoll;

        if (failureMessage === undefined || failureRoll === undefined || this.messageElement === null) {
            throw new Error("Failure state is not configured.");
        }

        const damage = await this.rollFailureDamage(failureRoll);
        this.messageElement.textContent = `${failureMessage} You take ${damage} damage.`;
        this.showMessage();
        this.showResetButton();
    }

    private async rollFailureDamage(rollState: string): Promise<number> {
        const {data, error} = await this.client.GET("/api/dice/{rollState}", {
            params: {
                path: {
                    rollState,
                },
            },
        });

        if (error !== undefined || data === undefined) {
            throw new Error("Unable to retrieve failure roll.");
        }

        const payload = this.normaliseDiceRollPayload(data);

        if (payload.total === undefined) {
            throw new Error("Dice roll payload is missing a total.");
        }

        return payload.total;
    }

    private normaliseDiceRollPayload(data: unknown): DiceRollPayload {
        const matchingPayload = this.findDiceRollPayload(data);

        if (matchingPayload !== null) {
            return matchingPayload;
        }

        throw new Error("Dice roll payload is malformed.");
    }

    private findDiceRollPayload(data: unknown): DiceRollPayload | null {
        if (Array.isArray(data)) {
            for (const item of data) {
                const matchingPayload = this.findDiceRollPayload(item);

                if (matchingPayload !== null) {
                    return matchingPayload;
                }
            }

            return null;
        }

        if (data !== null && typeof data === "object" && !Array.isArray(data)) {
            if ("total" in data && typeof data.total === "number") {
                return data as DiceRollPayload;
            }

            if ("hydra:member" in data && Array.isArray(data["hydra:member"])) {
                const matchingPayload = this.findDiceRollPayload(data["hydra:member"]);

                if (matchingPayload !== null) {
                    return matchingPayload;
                }
            }

            for (const value of Object.values(data)) {
                const matchingPayload = this.findDiceRollPayload(value);

                if (matchingPayload !== null) {
                    return matchingPayload;
                }
            }
        }

        return null;
    }

    private disableButtons(): void {
        if (this.buttonsContainer === null) {
            return;
        }

        const allButtons = this.buttonsContainer.querySelectorAll("button");

        allButtons.forEach((buttonElement) => {
            buttonElement.setAttribute("disabled", "disabled");
        });
    }

    private enableButtons(): void {
        if (this.buttonsContainer === null) {
            return;
        }

        const allButtons = this.buttonsContainer.querySelectorAll("button");

        allButtons.forEach((buttonElement) => {
            buttonElement.removeAttribute("disabled");
            buttonElement.classList.remove("btn-success");
            buttonElement.classList.add("btn-primary");
        });
    }

    private readonly handleReset = (): void => {
        this.progressIndex = 0;
        this.isLocked = false;
        this.enableButtons();
        this.hideResetButton();

        if (this.messageElement !== null) {
            this.messageElement.textContent = "";
        }

        this.hideMessage();
    };

    private showResetButton(): void {
        if (this.resetButton === null) {
            return;
        }

        this.resetButton.hidden = false;
    }

    private hideResetButton(): void {
        if (this.resetButton === null) {
            return;
        }

        this.resetButton.hidden = true;
    }

    private showMessage(): void {
        if (this.messageElement === null) {
            return;
        }

        this.messageElement.classList.remove("hide");
        this.messageElement.classList.add("show");
    }

    private hideMessage(): void {
        if (this.messageElement === null) {
            return;
        }

        this.messageElement.classList.remove("show");
        this.messageElement.classList.add("hide");
    }
}
