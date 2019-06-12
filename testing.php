<?php
// require the class definition file
require_once( 'server/classes/webpage.class.php' );
$page = new Webpage( "Testing", array("" ) );
$page->addToBody("<nav>
    <ul>
        <li><a href=\"/cm0665-assignment/#/\">Home</a></li>
        <li><a href=\"testing.php\">Testing Page</a></li>
    </ul>

</nav>");
$page->addToBody("<h1>Testing</h1>");
$page->addToBody("<a href='server/index.php?action=list&subject=albums'><h2>Show Albums</h2></a>");
$page->addToBody("<a href='server/index.php?action=list&subject=genre'><h2>Show Genres</h2></a>");
$page->addToBody("<a href='server/index.php?action=list&subject=tracks&id=4'><h2>Show Tracks</h2></a>");
$page->addToBody("<a href='server/index.php?action=search&subject=genre&id=5'><h2>Show Albums by Genre</h2></a>");
$page->addToBody("<a href='server/index.php?action=search&subject=albums&data=car'><h2>Show Search Results</h2></a>");
$page->addToBody("<a href='server/index.php?action=show&subject=notes&id=1'><h2>Show Notes</h2></a>");
$page->addToBody("<h2>Login</h2>");
$page->addToBody(" <form action='server/index.php?action=login&subject=user' method=\"post\">
                            <input id=\"username\" name=\"username\" type=\"text\" placeholder=\"username\">
                            <input id=\"password\" name=\"password\" type=\"password\" placeholder=\"password\">
                            <input type=\"submit\" name=\"Login\">
                          </form>");
$page->addToBody("<a href='server/index.php?action=logout&subject=user'><h2>Logout</h2></a>");
$page->addToBody("<h2>Add Note to Album ID: 55</h2>");
$page->addToBody("<form action='server/index.php?action=add&subject=note' method=\"post\">
                            <textarea name=\"notes\" id=\"notes\"></textarea>
                            <input type=\"submit\" name=\"Submit Note\">
                            <input type=\"hidden\" value=\"55\" id=\"album_id\" name=\"album_id\">
                          </form>");
$page->addToBody("<h2>Edit Note of Album ID: 1</h2>");
$page->addToBody("<form action='server/index.php?action=update&subject=notes' method=\"post\">
                            <textarea name=\"notes\" id=\"notes\"></textarea>
                            <input type=\"submit\" name=\"Update Note\">
                            <input type=\"hidden\" value=\"1\" id=\"album_id\" name=\"album_id\">
                          </form>");
// echo the page contents back to the browser
echo $page->getPage();
