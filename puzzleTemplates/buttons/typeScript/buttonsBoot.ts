import {ButtonPuzzleButton} from "./components/ButtonPuzzleButton";
import {ButtonsPuzzle} from "./components/ButtonsPuzzle";


(()=> {
    document.addEventListener('DOMContentLoaded', () => {
        const customElementRegistry: CustomElementRegistry = window.customElements;
        customElementRegistry.define('button-puzzle-button', ButtonPuzzleButton, {extends: 'button'});
        customElementRegistry.define('buttons-puzzle', ButtonsPuzzle);
        const container = document.getElementById('puzzle-container');

        if (!(container instanceof HTMLElement)) {
            throw new Error("Puzzle container is missing.");
        }

        const templateSlug: string | undefined = container.dataset.templateslug;
        const instanceCode: string | undefined = container.dataset.instancecode;
        const userId: string | undefined = container.dataset.userid;

        if (templateSlug === undefined) {
            throw new Error("Template Slug is missing. Aborting");
        }

        if (instanceCode === undefined) {
            throw new Error("Instance Code is missing. Aborting");
        }

        if (userId === undefined) {
            throw new Error("User ID is missing. Aborting");
        }

        const puzzle = document.createElement('buttons-puzzle');

        puzzle.dataset.templateslug = templateSlug;
        puzzle.dataset.instancecode = instanceCode;
        puzzle.dataset.userid = userId;
        container.appendChild(puzzle);
    });
})();
