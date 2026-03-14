# Document Object Model (DOM) standards
* All DOM standards are in the [DOM Level 3](http://www.w3.org/TR/DOM-Level-3-Core/) specification.
* All DOM elements that are unique within a page MUST be given a unique ID.
* Prefer Semantic HTML over divs.
* Prefer CSS over inline styles.
* Use descriptive and meaningful IDs for DOM elements.
* Avoid using IDs that are too generic or easily confused with other elements.
* Use IDs consistently across similar elements to improve maintainability.
* All html tags MUST have a lang attribute.
* There MUST be a single root element in the DOM.
* If an H1 element is present, there MUST be only one H1 element in the DOM.
* All documents MUST have the following metatags:
  * viewport
  * charset
  * description
  * keywords
  * author
  * robots
  * title
* DOM MUST NOT use elements that are not supported by all modern browsers.
  * Modern browser are defined as Firefox, Chrome, Edge, Safari, and Chromium-based browsers.
