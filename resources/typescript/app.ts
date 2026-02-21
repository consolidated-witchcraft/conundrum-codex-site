import ProgressBar from "./components/ProgressBar";
import NavToggle from "./components/NavToggle";
import ToolTip from "./components/Tooltip";
import {FlashAlert} from "./components/FlashAlert";
import {initialiseDropCaps} from "./behaviours/dropCap";
import {SlideShow} from "./components/SlideShow";
import AccordionList from "./components/Accordion/AccordionList";
import {AccordionEntry} from "./components/Accordion/AccordionEntry";
import {Header} from "./components/Header";
import {MultiaddWrapper} from "./components/MultiaddWrapper";
import {TabsElement} from "./components/Tabs/TabsElement";
import {PostButton} from "./components/PostButton";
import RandomQuotation from "./components/RandomQuotation";
import {HeadingElement} from "./components/HeadingElement";
import {LazyImage} from "./components/LazyImage";
import {SubmenuTrigger} from "./components/SubmenuTrigger";
import {NumberSpinner} from "./components/NumberSpinner";
import {CartLinePreview} from "./components/CartLinePreview";

import {UserCartProductLine} from "./components/UserCart/UserCartProductLine";
import {UserCart} from "./components/UserCart/UserCart";

(()=> {
    document.addEventListener('DOMContentLoaded', () => {
        const body: HTMLBodyElement|null = document.querySelector('body');

        initialiseCustomElements();
        initialiseDropCaps();

        if (null === body) {
            throw new Error('Body Element not found');
        }
        body.classList.add('loaded');
    });

    function initialiseCustomElements(): void {
        const customElementRegistry: CustomElementRegistry = window.customElements;
        customElementRegistry.define('header-element', Header, {extends: 'header'});
        customElementRegistry.define('progress-bar', ProgressBar);
        customElementRegistry.define('nav-toggle', NavToggle, { extends: 'button'});
        customElementRegistry.define('tooltip-element', ToolTip, {extends: 'span'});
        customElementRegistry.define('flash-alert', FlashAlert, {extends: 'span'});
        customElementRegistry.define('slideshow-element', SlideShow, {extends: 'div'});
        customElementRegistry.define('accordion-list', AccordionList, {extends: 'div'});
        customElementRegistry.define('accordion-entry', AccordionEntry, {extends: 'article'});
        customElementRegistry.define('multiadd-wrapper', MultiaddWrapper);
        customElementRegistry.define('tabs-element', TabsElement, {extends: 'div'});
        customElementRegistry.define('post-button', PostButton, {extends: 'button'});
        customElementRegistry.define('random-quotation', RandomQuotation, {extends: 'blockquote'});
        customElementRegistry.define('heading-element', HeadingElement);
        customElementRegistry.define('lazy-image', LazyImage, {extends: 'img'});
        customElementRegistry.define('submenu-trigger', SubmenuTrigger, {extends: 'a'});
        customElementRegistry.define('number-spinner', NumberSpinner, {extends: 'input'});
        customElementRegistry.define('cartline-preview', CartLinePreview, {extends: 'dl'});
        customElementRegistry.define('user-cart-product-line', UserCartProductLine, {extends: 'div'});
        customElementRegistry.define('user-cart', UserCart, {extends: 'div'});

    }
})();



