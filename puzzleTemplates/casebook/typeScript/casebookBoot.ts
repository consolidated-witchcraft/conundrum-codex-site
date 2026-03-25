import {CasebookSubject} from "./CasebookSubject";
import {CasebookSubjectClue} from "./CasebookSubjectClue";

document.addEventListener('DOMContentLoaded', () => {

    let customElementRegistry = window.customElements;
    customElementRegistry.define('casebook-clue', CasebookSubjectClue, { extends: 'div'});
    customElementRegistry.define('casebook-subject', CasebookSubject, { extends: 'article'});

});
