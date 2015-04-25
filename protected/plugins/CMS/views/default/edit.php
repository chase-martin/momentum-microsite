<?php
?>
<style>
    #cmsEditOverlay {
        position: fixed;
        display:none;
        height: 300px;
        top:0;
        width:100%;
        background: black;
        opacity: 0.7;
        z-index: 10000000;
        }
    .showCmsEditOverlay {
        position: fixed;
        height: 40px;
        top:0;
        left:0;
        width:40px;
        background: black;
        opacity: 0.7;
        z-index: 10000001;
        }

</style>
<div class="showCmsEditOverlay">

</div>
<div id="cmsEditOverlay">
  <div class="myblock">

<input id="addVariantInput"/>
<button id="addVariant" docId="<?php echo $cms->getId() ?>">
      Add Variant
</button>
  </div>
</div>



<style>


#cmsEditOverlay .myblock {
    padding : 50px;

    }

#cmsEditOverlay {
    font-size: 100%; /* Fixes exaggerated text resizing in IE6 and IE7 */
    }

#cmsEditOverlay body,  #cmsEditOverlay caption,  #cmsEditOverlay th,   #cmsEditOverlay td,  #cmsEditOverlay input,  #cmsEditOverlay textarea,  #cmsEditOverlay select,  #cmsEditOverlay option,  #cmsEditOverlay legend,   #cmsEditOverlay fieldset {
    font-family: AvenirBook, Verdana, Tahoma, "DejaVu Sans", sans-serif;
    }
#cmsEditOverlay pre,  #cmsEditOverlay code,  #cmsEditOverlay kbd,  #cmsEditOverlay samp,  #cmsEditOverlay tt,  #cmsEditOverlay var {
    font-family: "Courier New", "DejaVu Sans Mono", monospace;
    }
#cmsEditOverlay h1,  #cmsEditOverlay h2,  #cmsEditOverlay h3,  #cmsEditOverlay h4,  #cmsEditOverlay h5,  #cmsEditOverlay h6,  #cmsEditOverlay p,  #cmsEditOverlay blockquote,  #cmsEditOverlay pre,  #cmsEditOverlay ul,  #cmsEditOverlay ol,  #cmsEditOverlay dl,  #cmsEditOverlay hr,  #cmsEditOverlay table,  #cmsEditOverlay fieldset {
    margin: 1.5em 0;
    }
#cmsEditOverlay  input  { width : 200px  }
#cmsEditOverlay h1  {
    font-family: AvenirHeavy, Verdana, Tahoma, "DejaVu Sans", sans-serif;
    font-size: 36px;
    line-height: 38px;
    color: #1E4CA1;
    font-weight: 100;
    margin: 0;
    }
#cmsEditOverlay h2 {
    font-family: AvenirBook, Verdana, Tahoma, "DejaVu Sans", sans-serif;
    font-size: 22px;
    line-height: 1.2em;
    color: #000000;
    font-weight: 100;
    margin: 4px 0
    }

/*panels-flexible-region panels-flexible-region-edition_panel_layout-center */

#cmsEditOverlay h3 {
    font-family: AvenirHeavy, Verdana, Tahoma, "DejaVu Sans", sans-serif;
    text-transform: uppercase;
    font-size: 14px;
    line-height: 1.333em;
    color: #000000;
    margin: 0
    }
#cmsEditOverlay h3 + p {
    margin-top: 0
    }
#cmsEditOverlay h4,  #cmsEditOverlay h5,  #cmsEditOverlay h6 {
    font-size: 1.1em;
    margin: 1.364em 0; /* Equivalent to 1.5em in the page's base font: 1.5 / 1.1 = 1.364 */
    }
/*Hide H1 in sidebars*/

/* Other block-level elements */
#cmsEditOverlay p {
    font-family: AvenirBook, Verdana, Tahoma, "DejaVu Sans", sans-serif;
    font-size: 14px;
    line-height: 1.333em;
    color: #303030;
    }

#cmsEditOverlay blockquote {
    font-family: AvenirMediumOblique, Verdana, Tahoma, "DejaVu Sans", sans-serif;
    font-size: 16px;
    line-height: 1.65em;
    color: #00aeef;
    }
#cmsEditOverlay pre {
    font-size: 1.1em; /* Monospace fonts can be hard to read */
    margin: 1.364em 0; /* Equivalent to 1.5em in the page's base font: 1.5 / 1.1 = 1.364 */
    }
#cmsEditOverlay hr {
    height: 1px;
    border: 1px solid #666;
    }

#cmsEditOverlay ul, ol {
    margin-left: 0; /* LTR */
    padding-left: 2em; /* LTR */
    }

#cmsEditOverlay ul ul,  #cmsEditOverlay ul ol,  #cmsEditOverlay ol ol,   #cmsEditOverlay ol ul,  #cmsEditOverlay .item-list ul ul,  #cmsEditOverlay .item-list ul ol,  #cmsEditOverlay .item-list ol ol,  #cmsEditOverlay .item-list ol ul {
    margin: 0;
    }
li {
    margin: 0;
    padding: 0;
    }

#cmsEditOverlay ul {
    list-style-type: disc;
    }
#cmsEditOverlay ul ul {
    list-style-type: circle;
    }
#cmsEditOverlay ul ul ul {
    list-style-type: square;
    }
#cmsEditOverlay ul ul ul ul {
    list-style-type: circle;
    }
#cmsEditOverlay ol {
    list-style-type: decimal;
    }
#cmsEditOverlay ol ol {
    list-style-type: lower-alpha;
    }
#cmsEditOverlay ol ol ol {
    list-style-type: decimal;
    }
#cmsEditOverlay dl {
    }
#cmsEditOverlay dt {
    margin: 0;
    padding: 0;
    }
#cmsEditOverlay dd {
    margin: 0 0 0 2em; /* LTR */
    padding: 0;
    }
#cmsEditOverlay table {
    border-collapse: collapse;
    /* width: 100%; */ /* Prevent cramped-looking tables */
    }
#cmsEditOverlay th {
    text-align: left; /* LTR */
    padding: 0;
    border-bottom: none;
    }
#cmsEditOverlay tbody {
    border-top: none;
    }
/*
 * Forms
 */
#cmsEditOverlay form {
    margin: 0;
    padding: 0;
    }
#cmsEditOverlay fieldset {
    padding: 0.5em;
    }
/*links*/
#cmsEditOverlay a:link {
    font-family: AvenirMedium, Verdana, Tahoma, "DejaVu Sans", sans-serif;
    color: #1e4ca1;
    text-decoration: none
    }
#cmsEditOverlay a:visited {
    color: #1e4ca1;
    }
#cmsEditOverlay a:hover, a:focus {
    color: #1e4ca1;
    text-decoration: underline;
    }
#cmsEditOverlay a:active {
    text-decoration: none
    }
/*
 * Other inline elements
 */
#cmsEditOverlay img {
    border: 0;
    /* vertical-align: bottom; */ /* Suppress the space beneath the baseline */
    }
#cmsEditOverlay abbr, /* Abbreviations */
#cmsEditOverlay acronym {
    border-bottom: 1px dotted #666;
    cursor: help;
    white-space: nowrap;
    }
#cmsEditOverlay sup {
    line-height: 0;
    }

#cmsEditOverlay .bold {
    font-weight: 900
    }
#cmsEditOverlay .italic {
    font-style: italic
    }
#cmsEditOverlay .ave-book-18 {
    font-family: AvenirBook, Verdana, Tahoma, "DejaVu Sans", sans-serif;
    font-size: 18px
    }
#cmsEditOverlay .font-black {
    color: #000000
    }
/*Block Buttons*/
#cmsEditOverlay .quote, .quote-author {
    color: #00aeef;
    font-size: 14px;
    padding-left: 14px
    }
#cmsEditOverlay .quote {
    margin: 0
    }
#cmsEditOverlay .quote-author {
    margin: 32px 0 0
    }
</style>