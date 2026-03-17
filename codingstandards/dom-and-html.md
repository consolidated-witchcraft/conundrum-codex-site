## Document Object Model (DOM) and HTML standards
* All DOM elements that are unique within a page MUST be given a unique ID.
* You SHOULD Semantic HTML over divs.
* You MUST use CSS stylesheets over inline styles.
* You MUST use descriptive and meaningful IDs for DOM elements.
* You MUST NOT use IDs that are too generic or easily confused with other elements.
* You MUST use IDs consistently across similar elements to improve maintainability.
* All ```<html>``` tags MUST have a lang attribute.
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
* All scripts and styles MUST be linked, rather than being written in the HTML itself. It is permissible to inline such a file, but the file's content must be separate in the codebase.
* DOM MUST NOT use elements that are not supported by all modern browsers.
  * Modern browser are defined as Firefox, Chrome, Edge, Safari, and Chromium-based browsers.
