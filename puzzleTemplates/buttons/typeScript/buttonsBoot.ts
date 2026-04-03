import {ButtonsPuzzle} from "./components/ButtonsPuzzle";


(()=> {
    document.addEventListener('DOMContentLoaded', () => {
        const customElementRegistry: CustomElementRegistry = window.customElements;
        customElementRegistry.define('buttons-puzzle', ButtonsPuzzle);
        const container: HTMLElement = document.getElementById('puzzle-container');

        let templateSlug: string|undefined = container.dataset.templateslug;
        let instanceCode: string|undefined = container.dataset.instancecode;

        if (templateSlug === undefined) {
            throw new Error("Template Slug is missing. aborting");
        }
        if (instanceCode === undefined) {
            throw new Error("Instance Code is missing. aborting");
        }



        const puzzle = document.createElement('buttons-puzzle');

        puzzle.dataset.templateslug = templateSlug;
        puzzle.dataset.instancecode = instanceCode;
        container.appendChild(puzzle);
    });
})();
