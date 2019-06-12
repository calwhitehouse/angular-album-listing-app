<?php

/**
 *
 * This is a skeleton class for a Webpage class.  We have given you the method names
 * and a very brief comment on what those methods need to do.  You will need to implement the class yourself.
 *
 * Also, included with this file is a file that is a 'client' or 'user' of the class.
 * If you look at the way the client is using the class it'll give you some more clues about how
 * you might need to implement the methods that the client calls.
 *
 */

/**
 * The class should be called: Webpage
 * you will need attributes to hold at least the main sections of a page: the head, body and footer
 */

/**
 *
 */
class Webpage {
    protected $head;
    protected $body;
    protected $footer;
    protected $css;
    protected $title;

    /* Methods:
    * A constructor
    * The constructor for the class should accept at least two arguments: the title of the page, and an array of
    * css filenames that it will use to create the appropriate code in the head section to link to those stylesheets
    * The constructor should create the head section and footer section and give a default value for the body section
      */
    public function __construct($title = null, array $css = null) {
        $this->head = $this->createHead($title, $css);

    }

    private function createHead($title = "My Page", array $css = null) {
        $pageHead = <<<PAGESTART
<!doctype html>

<html lang="en">
<head>
    <meta charset="utf-8">

    <title>$title</title>
PAGESTART;
        if ($css) {
            foreach ($css as $link) {
                $pageHead .= "\n<link rel='stylesheet' type='text/css' href='$link'/>";
            }
            $pageHead .= "\n</head>\n<body>\n";
        }
        return $pageHead;
    }

    /*
    * addToBody
    * a method called 'addToBody' that will add text to the body attribute of the webpage.  See the client to see how this method
    * will be used - it'll give you a clue as to how to implement it.*/
    public function addToBody($content) {
        $this->body .= $content;
    }

    /*
    * getPage
    * a getPage method which has as a return value the various sections, head, body and footer, of the webpage concatenated together.
    */
    public function getPage() {
        return $this->head . $this->body . "\n</body>\n</html>";
    }
}


/*
* Consider carefully the scope of all attributes and methods/functions.  Remember to make scope as restrictive
* as possible while still being consonant with the class working.
*
*/

?>
