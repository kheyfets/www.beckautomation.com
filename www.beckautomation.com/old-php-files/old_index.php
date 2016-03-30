<?php
ob_start();

$no_template = (isset($_GET['noTemplate']) ? $_GET['noTemplate'] : false);
/****
 * pages hash exists to validate the data, do not shortcut this hash.
 * If you shortcut this hash and just do $input . '.html' you will
 * get in trouble when people put in values like '../../../../someImportantFile
 ***/
$pages =  array
(
    'accessories'           => Array( 'file' => 'accessories.html',           'title' => 'Accessories'),
    'contactus'             => Array( 'file' => 'contactus.html',             'title' => 'Contact Us'),
    'sales'                 => Array( 'file' => 'sales.php',                  'title' => 'Let\'s talk'),
    'home'                  => Array( 'file' => 'home.html',                  'title' => 'Home'),
    'rollforming'           => Array( 'file' => 'rollforming.html',           'title' => 'Rollforming Controls'),
    'folding'               => Array( 'file' => 'folding.html',               'title' => 'Folder Controls'),
    'interactiveDirections' => Array( 'file' => 'interactiveDirections.html', 'title' => 'Interactive Directions'),
    'directions'            => Array( 'file' => 'directions.html',            'title' => 'Get Directions'),
    'networking'            => Array( 'file' => 'smartcomm.html',             'title' => 'SmartComm'),
    'turnkey'               => Array( 'file' => 'turnkey.html',               'title' => 'Turnkey Solutions'),
    'downloadCenter'        => Array( 'file' => 'downloadCenter.php',         'title' => 'Download Center'),
    'cobalt'                => Array( 'file' => 'cobalt.html',                'title' => 'Cobalt'),
    'sierra'                => Array( 'file' => 'sierra.html',                'title' => 'Sierra'),
    'sii'                   => Array( 'file' => 'sii.html',                   'title' => 'SII')    
);
$file = '';
$title= '';
if($pages[$_GET['page']])
{
    $file  = $pages[ $_GET['page'] ][ 'file' ];
    $title = $pages[ $_GET['page'] ][ 'title' ];
}
else
{
    $file  = $pages[ 'home' ][ 'file' ];
    $title = $pages[ 'home' ][ 'title' ];
}
if(!$no_template)
{
    print('<?xml version="1.0" encoding="UTF-8"?>');
?>
   <!---------- main page begins -------------->
   <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
   <html xmlns="http://www.w3.org/1999/xhtml">
   <head>
   <meta http-equiv="content-type" content="text/html; charset=utf-8" />
   <title>Beck Automation, LLC.  St. Louis, MO</title>
   <meta name="keywords" content="" />
   <meta name="description" content="" />
   <link href="styles.css" rel="stylesheet" type="text/css" />
   </head>
   <body>

   <div id="main_bg">
   <div id="main">
   
   <!-- header begins -->
   <div id="header">
    <div id="logo">
    </div>
      <div id="buttons">
         <ul>
            <li class="first"><a href="index.php?page=home"  title="">Home</a></li>
            <li><a href="index.php?page=rollforming" title="">Rollforming</a></li>
            <li><a href="index.php?page=folding" title="">Folding</a></li>
            <li><a href="index.php?page=networking" title="">Networking</a></li>
            <li><a href="index.php?page=accessories" class="last_b" title="">Accessories</a></li>
            <li ><a href="index.php?page=turnkey" title="" class="last_b">Turn-Key</a></li>
            <li ><a href="index.php?page=downloadCenter" title="" class="last_b">Downloads</a></li>
            <li ><a href="index.php?page=contactus" title="" class="last_b">Contact Us</a></li>
         </ul>
      </div>
   </div>
   <!-- header ends -->

<!-- content begins -->
   <div id="content">
     
     <div id="left">
       <h1>Company News</h1>
       <div class="tit_bot">
        <?php
            include('news1.html');
            include('news2.html');
            include('news3.html');
            include('news4.html');
            include('news5.html');
        ?>
      </div>   
     </div>  

     <div id="right" class = "right_all">
       <div class="right_top">
       </div>

       <div class="right_s">          
          <div>
             <?php
} // end of if ! noTemplate
                include($file);
                ob_end_flush();
                if(!$no_template)
                {
             ?>
             </div>
             <div style="clear: both"><img src="images/spaser.gif" alt="" width="1" height="1" />
             </div>
          </div>

          <div class="right_bot">          
          </div>

       </div>        
       <div style="clear: both"><img src="images/spaser.gif" alt="" width="1" height="1" />
       </div>       
     </div> 
<!-- content ends -->
      <!-- footer begins -->
      <div id="footer">
		    814 Fee Fee Rd.  &nbsp&nbsp St. Louis, MO 63043 <br>
            Phone: 314.576.9736  &nbsp&nbsp sales@beckautomation.com &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp Copyright  2010 
      </div>     
          
<!-- footer ends -->
   </div>
   </div>
   </body>
   </html>

<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("UA-7290317-1");
pageTracker._trackPageview();
} catch(err) {}</script>
</body>
</html>
<?php } //end of second ! noTemplate?>
