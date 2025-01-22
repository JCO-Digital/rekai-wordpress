import hljs from 'highlight.js/lib/core';
import javascript from 'highlight.js/lib/languages/javascript';
import php from 'highlight.js/lib/languages/php';

// Then register the languages you need
hljs.registerLanguage('javascript', javascript);
hljs.registerLanguage('php', php);

hljs.highlightAll();