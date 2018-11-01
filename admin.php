<?php
require_once( dirname(__FILE__).'/form.lib.php' );

define( 'PHPFMG_USER', "careercatchjchs@gmail.com" ); // must be a email address. for sending password to you.
define( 'PHPFMG_PW', "888da9" );

?>
<?php
/**
 * GNU Library or Lesser General Public License version 2.0 (LGPLv2)
*/

# main
# ------------------------------------------------------
error_reporting( E_ERROR ) ;
phpfmg_admin_main();
# ------------------------------------------------------




function phpfmg_admin_main(){
    $mod  = isset($_REQUEST['mod'])  ? $_REQUEST['mod']  : '';
    $func = isset($_REQUEST['func']) ? $_REQUEST['func'] : '';
    $function = "phpfmg_{$mod}_{$func}";
    if( !function_exists($function) ){
        phpfmg_admin_default();
        exit;
    };

    // no login required modules
    $public_modules   = false !== strpos('|captcha||ajax|', "|{$mod}|");
    $public_functions = false !== strpos('|phpfmg_ajax_submit||phpfmg_mail_request_password||phpfmg_filman_download||phpfmg_image_processing||phpfmg_dd_lookup|', "|{$function}|") ;   
    if( $public_modules || $public_functions ) { 
        $function();
        exit;
    };
    
    return phpfmg_user_isLogin() ? $function() : phpfmg_admin_default();
}

function phpfmg_ajax_submit(){
    $phpfmg_send = phpfmg_sendmail( $GLOBALS['form_mail'] );
    $isHideForm  = isset($phpfmg_send['isHideForm']) ? $phpfmg_send['isHideForm'] : false;

    $response = array(
        'ok' => $isHideForm,
        'error_fields' => isset($phpfmg_send['error']) ? $phpfmg_send['error']['fields'] : '',
        'OneEntry' => isset($GLOBALS['OneEntry']) ? $GLOBALS['OneEntry'] : '',
    );
    
    @header("Content-Type:text/html; charset=$charset");
    echo "<html><body><script>
    var response = " . json_encode( $response ) . ";
    try{
        parent.fmgHandler.onResponse( response );
    }catch(E){};
    \n\n";
    echo "\n\n</script></body></html>";

}


function phpfmg_admin_default(){
    if( phpfmg_user_login() ){
        phpfmg_admin_panel();
    };
}



function phpfmg_admin_panel()
{    
    if( !phpfmg_user_isLogin() ){
        exit;
    };

    phpfmg_admin_header();
    phpfmg_writable_check();
?>    
<table cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td valign=top style="padding-left:280px;">

<style type="text/css">
    .fmg_title{
        font-size: 16px;
        font-weight: bold;
        padding: 10px;
    }
    
    .fmg_sep{
        width:32px;
    }
    
    .fmg_text{
        line-height: 150%;
        vertical-align: top;
        padding-left:28px;
    }

</style>

<script type="text/javascript">
    function deleteAll(n){
        if( confirm("Are you sure you want to delete?" ) ){
            location.href = "admin.php?mod=log&func=delete&file=" + n ;
        };
        return false ;
    }
</script>


<div class="fmg_title">
    1. Email Traffics
</div>
<div class="fmg_text">
    <a href="admin.php?mod=log&func=view&file=1">view</a> &nbsp;&nbsp;
    <a href="admin.php?mod=log&func=download&file=1">download</a> &nbsp;&nbsp;
    <?php 
        if( file_exists(PHPFMG_EMAILS_LOGFILE) ){
            echo '<a href="#" onclick="return deleteAll(1);">delete all</a>';
        };
    ?>
</div>


<div class="fmg_title">
    2. Form Data
</div>
<div class="fmg_text">
    <a href="admin.php?mod=log&func=view&file=2">view</a> &nbsp;&nbsp;
    <a href="admin.php?mod=log&func=download&file=2">download</a> &nbsp;&nbsp;
    <?php 
        if( file_exists(PHPFMG_SAVE_FILE) ){
            echo '<a href="#" onclick="return deleteAll(2);">delete all</a>';
        };
    ?>
</div>

<div class="fmg_title">
    3. Form Generator
</div>
<div class="fmg_text">
    <a href="http://www.formmail-maker.com/generator.php" onclick="document.frmFormMail.submit(); return false;" title="<?php echo htmlspecialchars(PHPFMG_SUBJECT);?>">Edit Form</a> &nbsp;&nbsp;
    <a href="http://www.formmail-maker.com/generator.php" >New Form</a>
</div>
    <form name="frmFormMail" action='http://www.formmail-maker.com/generator.php' method='post' enctype='multipart/form-data'>
    <input type="hidden" name="uuid" value="<?php echo PHPFMG_ID; ?>">
    <input type="hidden" name="external_ini" value="<?php echo function_exists('phpfmg_formini') ?  phpfmg_formini() : ""; ?>">
    </form>

		</td>
	</tr>
</table>

<?php
    phpfmg_admin_footer();
}



function phpfmg_admin_header( $title = '' ){
    header( "Content-Type: text/html; charset=" . PHPFMG_CHARSET );
?>
<html>
<head>
    <title><?php echo '' == $title ? '' : $title . ' | ' ; ?>PHP FormMail Admin Panel </title>
    <meta name="keywords" content="PHP FormMail Generator, PHP HTML form, send html email with attachment, PHP web form,  Free Form, Form Builder, Form Creator, phpFormMailGen, Customized Web Forms, phpFormMailGenerator,formmail.php, formmail.pl, formMail Generator, ASP Formmail, ASP form, PHP Form, Generator, phpFormGen, phpFormGenerator, anti-spam, web hosting">
    <meta name="description" content="PHP formMail Generator - A tool to ceate ready-to-use web forms in a flash. Validating form with CAPTCHA security image, send html email with attachments, send auto response email copy, log email traffics, save and download form data in Excel. ">
    <meta name="generator" content="PHP Mail Form Generator, phpfmg.sourceforge.net">

    <style type='text/css'>
    body, td, label, div, span{
        font-family : Verdana, Arial, Helvetica, sans-serif;
        font-size : 12px;
    }
    </style>
</head>
<body  marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">

<table cellspacing=0 cellpadding=0 border=0 width="100%">
    <td nowrap align=center style="background-color:#024e7b;padding:10px;font-size:18px;color:#ffffff;font-weight:bold;width:250px;" >
        Form Admin Panel
    </td>
    <td style="padding-left:30px;background-color:#86BC1B;width:100%;font-weight:bold;" >
        &nbsp;
<?php
    if( phpfmg_user_isLogin() ){
        echo '<a href="admin.php" style="color:#ffffff;">Main Menu</a> &nbsp;&nbsp;' ;
        echo '<a href="admin.php?mod=user&func=logout" style="color:#ffffff;">Logout</a>' ;
    }; 
?>
    </td>
</table>

<div style="padding-top:28px;">

<?php
    
}


function phpfmg_admin_footer(){
?>

</div>

<div style="color:#cccccc;text-decoration:none;padding:18px;font-weight:bold;">
	:: <a href="http://phpfmg.sourceforge.net" target="_blank" title="Free Mailform Maker: Create read-to-use Web Forms in a flash. Including validating form with CAPTCHA security image, send html email with attachments, send auto response email copy, log email traffics, save and download form data in Excel. " style="color:#cccccc;font-weight:bold;text-decoration:none;">PHP FormMail Generator</a> ::
</div>

</body>
</html>
<?php
}


function phpfmg_image_processing(){
    $img = new phpfmgImage();
    $img->out_processing_gif();
}


# phpfmg module : captcha
# ------------------------------------------------------
function phpfmg_captcha_get(){
    $img = new phpfmgImage();
    $img->out();
    //$_SESSION[PHPFMG_ID.'fmgCaptchCode'] = $img->text ;
    $_SESSION[ phpfmg_captcha_name() ] = $img->text ;
}



function phpfmg_captcha_generate_images(){
    for( $i = 0; $i < 50; $i ++ ){
        $file = "$i.png";
        $img = new phpfmgImage();
        $img->out($file);
        $data = base64_encode( file_get_contents($file) );
        echo "'{$img->text}' => '{$data}',\n" ;
        unlink( $file );
    };
}


function phpfmg_dd_lookup(){
    $paraOk = ( isset($_REQUEST['n']) && isset($_REQUEST['lookup']) && isset($_REQUEST['field_name']) );
    if( !$paraOk )
        return;
        
    $base64 = phpfmg_dependent_dropdown_data();
    $data = @unserialize( base64_decode($base64) );
    if( !is_array($data) ){
        return ;
    };
    
    
    foreach( $data as $field ){
        if( $field['name'] == $_REQUEST['field_name'] ){
            $nColumn = intval($_REQUEST['n']);
            $lookup  = $_REQUEST['lookup']; // $lookup is an array
            $dd      = new DependantDropdown(); 
            echo $dd->lookupFieldColumn( $field, $nColumn, $lookup );
            return;
        };
    };
    
    return;
}


function phpfmg_filman_download(){
    if( !isset($_REQUEST['filelink']) )
        return ;
        
    $filelink =  base64_decode($_REQUEST['filelink']);
    $file = PHPFMG_SAVE_ATTACHMENTS_DIR . basename($filelink);

    // 2016-12-05:  to prevent *LFD/LFI* attack. patch provided by Pouya Darabi, a security researcher in cert.org
    $real_basePath = realpath(PHPFMG_SAVE_ATTACHMENTS_DIR); 
    $real_requestPath = realpath($file);
    if ($real_requestPath === false || strpos($real_requestPath, $real_basePath) !== 0) { 
        return; 
    }; 

    if( !file_exists($file) ){
        return ;
    };
    
    phpfmg_util_download( $file, $filelink );
}


class phpfmgDataManager
{
    var $dataFile = '';
    var $columns = '';
    var $records = '';
    
    function __construct(){
        $this->dataFile = PHPFMG_SAVE_FILE; 
    }

    function phpfmgDataManager(){
        $this->dataFile = PHPFMG_SAVE_FILE; 
    }
    
    function parseFile(){
        $fp = @fopen($this->dataFile, 'rb');
        if( !$fp ) return false;
        
        $i = 0 ;
        $phpExitLine = 1; // first line is php code
        $colsLine = 2 ; // second line is column headers
        $this->columns = array();
        $this->records = array();
        $sep = chr(0x09);
        while( !feof($fp) ) { 
            $line = fgets($fp);
            $line = trim($line);
            if( empty($line) ) continue;
            $line = $this->line2display($line);
            $i ++ ;
            switch( $i ){
                case $phpExitLine:
                    continue;
                    break;
                case $colsLine :
                    $this->columns = explode($sep,$line);
                    break;
                default:
                    $this->records[] = explode( $sep, phpfmg_data2record( $line, false ) );
            };
        }; 
        fclose ($fp);
    }
    
    function displayRecords(){
        $this->parseFile();
        echo "<table border=1 style='width=95%;border-collapse: collapse;border-color:#cccccc;' >";
        echo "<tr><td>&nbsp;</td><td><b>" . join( "</b></td><td>&nbsp;<b>", $this->columns ) . "</b></td></tr>\n";
        $i = 1;
        foreach( $this->records as $r ){
            echo "<tr><td align=right>{$i}&nbsp;</td><td>" . join( "</td><td>&nbsp;", $r ) . "</td></tr>\n";
            $i++;
        };
        echo "</table>\n";
    }
    
    function line2display( $line ){
        $line = str_replace( array('"' . chr(0x09) . '"', '""'),  array(chr(0x09),'"'),  $line );
        $line = substr( $line, 1, -1 ); // chop first " and last "
        return $line;
    }
    
}
# end of class



# ------------------------------------------------------
class phpfmgImage
{
    var $im = null;
    var $width = 73 ;
    var $height = 33 ;
    var $text = '' ; 
    var $line_distance = 8;
    var $text_len = 4 ;

    function __construct( $text = '', $len = 4 ){
        $this->phpfmgImage( $text, $len );
    }

    function phpfmgImage( $text = '', $len = 4 ){
        $this->text_len = $len ;
        $this->text = '' == $text ? $this->uniqid( $this->text_len ) : $text ;
        $this->text = strtoupper( substr( $this->text, 0, $this->text_len ) );
    }
    
    function create(){
        $this->im = imagecreate( $this->width, $this->height );
        $bgcolor   = imagecolorallocate($this->im, 255, 255, 255);
        $textcolor = imagecolorallocate($this->im, 0, 0, 0);
        $this->drawLines();
        imagestring($this->im, 5, 20, 9, $this->text, $textcolor);
    }
    
    function drawLines(){
        $linecolor = imagecolorallocate($this->im, 210, 210, 210);
    
        //vertical lines
        for($x = 0; $x < $this->width; $x += $this->line_distance) {
          imageline($this->im, $x, 0, $x, $this->height, $linecolor);
        };
    
        //horizontal lines
        for($y = 0; $y < $this->height; $y += $this->line_distance) {
          imageline($this->im, 0, $y, $this->width, $y, $linecolor);
        };
    }
    
    function out( $filename = '' ){
        if( function_exists('imageline') ){
            $this->create();
            if( '' == $filename ) header("Content-type: image/png");
            ( '' == $filename ) ? imagepng( $this->im ) : imagepng( $this->im, $filename );
            imagedestroy( $this->im ); 
        }else{
            $this->out_predefined_image(); 
        };
    }

    function uniqid( $len = 0 ){
        $md5 = md5( uniqid(rand()) );
        return $len > 0 ? substr($md5,0,$len) : $md5 ;
    }
    
    function out_predefined_image(){
        header("Content-type: image/png");
        $data = $this->getImage(); 
        echo base64_decode($data);
    }
    
    // Use predefined captcha random images if web server doens't have GD graphics library installed  
    function getImage(){
        $images = array(
			'E957' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7QkMYQ1hDHUNDkMQCGlhbWYG0CIqYSKMrNrGpIBrhvtCopUtTM7NWZiG5L6CBMdChIaCVAUUvQyNQbAqqGAvQjoAABjS3MDo6OqC7mSGUEUVsoMKPihCL+wC3ks0ou+Yv1QAAAABJRU5ErkJggg==',
			'C7F4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7WENEQ11DAxoCkMREWhkaXRsYGpHFAhrBYq0oYkA+awPDlAAk90WtWjVtaeiqqCgk9wHlA1gbGB1Q9TI6AMVCQ1DsYG1ghahHcosIhhhrCKbYQIUfFSEW9wEANBbNo3uaTHkAAAAASUVORK5CYII=',
			'683A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7WAMYQxhDGVqRxUSmsLayNjpMdUASC2gRaXRoCAgIQBZrYG1laHR0EEFyX2TUyrBVU1dmTUNyX8gUFHUQva0g8wJDQzDFUNRB3IKqF+JmRhSxgQo/KkIs7gMALuHM3YVNU4gAAAAASUVORK5CYII=',
			'D59C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7QgNEQxlCGaYGIIkFTBFpYHR0CBBBFmsVaWBtCHRgQRULAYkhuy9q6dSlKzMjs5DdF9DK0OgQAleHEGtAFxNpdES3YwprK7pbQgMYQ9DdPFDhR0WIxX0AD9PM6muMtVgAAAAASUVORK5CYII=',
			'C2C2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7WEMYQxhCHaY6IImJtLK2MjoEBAQgiQU0ijS6Ngg6iCCLNTAAxYDqkdwXtWrV0qUQGu4+oLoprEC1Dqh6A4BirQwodjA6sDYITGFAdUsDyC2obhYNdQh1DA0ZBOFHRYjFfQDPpsyRypI5EgAAAABJRU5ErkJggg==',
			'A184' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7GB0YAhhCGRoCkMRYAxgDGB0dGpHFRKawBrA2BLQiiwW0MoDUTQlAcl/U0lVRq0JXRUUhuQ+iztEBWW9oKAPQvMDQEDTzgHY0YLEDTYw1FN3NAxV+VIRY3AcATV7L0oIXJMQAAAAASUVORK5CYII=',
			'BDEA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7QgNEQ1hDHVqRxQKmiLSyNjBMdUAWaxVpdG1gCAhAVQcUY3QQQXJfaNS0lamhK7OmIbkPTR2SeYyhIZhiqOrAbkEVg7jZEUVsoMKPihCL+wDEf81GSUU1TAAAAABJRU5ErkJggg==',
			'CCBE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWklEQVR4nGNYhQEaGAYTpIn7WEMYQ1lDGUMDkMREWlkbXRsdHZDVBTSKNLg2BKKKNYg0sCLUgZ0UtWraqqWhK0OzkNyHpg4hhm4eFjuwuQWbmwcq/KgIsbgPAMyAy+F+EmuTAAAAAElFTkSuQmCC',
			'FFC2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7QkNFQx1CHaY6IIkFNIg0MDoEBASgibE2CDqIYIgxNIgguS80amrYUiAdheQ+qLpGdDuAYq0MGGICU9DFQG5BF2MIdQwNGQThR0WIxX0A9OzNjQYgbX4AAAAASUVORK5CYII=',
			'C590' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7WENEQxlCGVqRxURaRRoYHR2mOiCJBTSKNLA2BAQEIIs1iISwNgQ6iCC5L2rV1KUrMyOzpiG5D6in0SEErg4h1oAm1ijS6Ihmh0grayu6W1hDGEPQ3TxQ4UdFiMV9ALZGzKptVcg4AAAAAElFTkSuQmCC',
			'2B53' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdklEQVR4nGNYhQEaGAYTpIn7WANEQ1hDHUIdkMREpoi0sjYwOgQgiQW0ijS6guSQdbcC1U0FyiG7b9rUsKWZWUuzkN0XINIKUoVsHqODSKMDUATZPNYGkB2oYiINIq2Mjo4obgkNFQ1hCGVAcfNAhR8VIRb3AQCdc8yP+8rJbwAAAABJRU5ErkJggg==',
			'E63E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAW0lEQVR4nGNYhQEaGAYTpIn7QkMYQxhDGUMDkMQCGlhbWRsdHRhQxEQaGRoC0cUaGBDqwE4KjZoWtmrqytAsJPcFNIi2MmAxzwHTPCximG7B5uaBCj8qQizuAwDSpMv3RzvfbQAAAABJRU5ErkJggg==',
			'AC18' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7GB0YQxmmMEx1QBJjDWBtdAhhCAhAEhOZItLgGMLoIIIkFtAK5E2BqwM7KWrptFWrpq2amoXkPjR1YBgaChLDNM8BQwzoFjS9Aa2MoYyhDihuHqjwoyLE4j4AwqLNAO0/HkcAAAAASUVORK5CYII=',
			'9373' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7WANYQ1hDA0IdkMREpoi0MjQEOgQgiQW0MjQ6NAQ0iKCKQUUR7ps2dVXYqqWrlmYhuY/VFahuCkMDsnlgnQEMKOYJAMUcHVDFQG5hbWBEcQvYzQ0MKG4eqPCjIsTiPgAQacyZbR/dhQAAAABJRU5ErkJggg==',
			'9194' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7WAMYAhhCGRoCkMREpjAGMDo6NCKLBbSyBrACSVQxBpDYlAAk902buipqZWZUVBSS+1hdgXaEBDog62UA6mVoCAwNQRITAIoxAl2C6hYGkFtQxIAuCUV380CFHxUhFvcBAO+GyyTMBN92AAAAAElFTkSuQmCC',
			'D50E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7QgNEQxmmMIYGIIkFTBFpYAhldEBWF9Aq0sDo6IguFsLaEAgTAzspaunUpUtXRYZmIbkvoJWh0RWhDo+YSKMjuh1TWFvR3RIawBiC7uaBCj8qQizuAwCY/8u+9xlneAAAAABJRU5ErkJggg==',
			'59C7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7QkMYQxhCHUNDkMQCGlhbGR0CGkRQxEQaXRsEUMQCA0BiIDmE+8KmLV2aumrVyixk97UyBgLVtaLY3MoA0jsFWSyglQVkRwCymMgUkFsCHZDFWAPAbkYRG6jwoyLE4j4ApH/MJSoPIRQAAAAASUVORK5CYII=',
			'D1B6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7QgMYAlhDGaY6IIkFTGEMYG10CAhAFmtlDWBtCHQQQBED6m10dEB2X9RSIApdmZqF5D6oOjTzGMDmiRASm8KA4ZZQoIvR3TxQ4UdFiMV9AM8fy+1JFSqUAAAAAElFTkSuQmCC',
			'C917' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7WEMYQximMIaGIImJtLK2MoQAaSSxgEaRRkd0sQaRRocpIBrhvqhVS5dmTVu1MgvJfQENjIFAda0MKHoZQHqnoIg1soDEAhjQ3TKF0QHdzYyhjihiAxV+VIRY3AcAQAXMEDLqFDMAAAAASUVORK5CYII=',
			'6B76' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7WANEQ1hDA6Y6IImJTBFpZWgICAhAEgtoEWl0aAh0EEAWawCqa3R0QHZfZNTUsFVLV6ZmIbkvBGTeFEZU81qB5gUwOoigiTk6oIqB3MLawICiF+zmBgYUNw9U+FERYnEfAOXszJaKxCpuAAAAAElFTkSuQmCC',
			'C18A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7WEMYAhhCGVqRxURaGQMYHR2mOiCJBTSyBrA2BAQEIIs1MADVOTqIILkvCoRCV2ZNQ3Ifmjq4GGtDYGgIih1gMRR1Iq2YellDWEMZQhlRxAYq/KgIsbgPAMKCyU/qbRemAAAAAElFTkSuQmCC',
			'53E4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7QkNYQ1hDHRoCkMQCGkRaWRsYGlHFGBpdGxhakcUCAxhA6qYEILkvbNqqsKWhq6KikN3XClLH6ICsFygGNI8xNATZDrAYA4pbRKaA3YIixhqA6eaBCj8qQizuAwDOAs1iR9DD5wAAAABJRU5ErkJggg==',
			'FC21' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7QkMZQxlCGVqRxQIaWBsdHR2mooqJNLg2BISiiwFJmF6wk0Kjpq1atTJrKbL7wOpa0e0Aik3BFHMIwOIWB3QxxlDW0IDQgEEQflSEWNwHANpWzYlUr3jKAAAAAElFTkSuQmCC',
			'79F7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7QkMZQ1hDA0NDkEVbWVtZgbQIiphIoyu62BSIWACy+6KWLk0NXbUyC8l9jA6MgUB1rcj2As0H6Z2CLCbSwAISC0AWC2gAuYXRAVUM6GY0sYEKPypCLO4DADLiyzHWD7QoAAAAAElFTkSuQmCC',
			'0B07' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7GB1EQximMIaGIImxBoi0MoQyNIggiYlMEWl0dHRAEQtoFWllbQgAQoT7opZODVu6KmplFpL7oOpaGVD1Nro2BExhwLQjgAHDLYwOWNyMIjZQ4UdFiMV9AJa2y4OL5oaVAAAAAElFTkSuQmCC',
			'767F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7QkMZQ1hDA0NDkEVbWVsZGgIdUFS2ijRiiE0RaWBodISJQdwUNS1s1dKVoVlI7mN0EG1lmMKIope1QaTRIQBVTAQo5uiAKhbQwNrK2oAuBnQzmthAhR8VIRb3AQDSb8lLd7JN9gAAAABJRU5ErkJggg==',
			'DFDE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAU0lEQVR4nGNYhQEaGAYTpIn7QgNEQ11DGUMDkMQCpog0sDY6OiCrC2gFijUE4hMDOylq6dSwpasiQ7OQ3EeEXtxiWNwSGgAUQ3PzQIUfFSEW9wEAwjXMnaXvIOQAAAAASUVORK5CYII=',
			'5387' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7QkNYQxhCGUNDkMQCGkRaGR0dGkRQxBgaXUEySGKBAQxgdQFI7gubtipsVeiqlVnI7msFq2tFsbkVbN4UZLEAiFgAspjIFJBbHB2QxVgDwG5GERuo8KMixOI+AO5oy6htsD9cAAAAAElFTkSuQmCC',
			'A8D6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7GB0YQ1hDGaY6IImxBrC2sjY6BAQgiYlMEWl0bQh0EEASC2gFqgOKIbsvaunKsKWrIlOzkNwHVYdiXmgoxDwRFPOwiWG6JaAV080DFX5UhFjcBwAovc1SygXQHgAAAABJRU5ErkJggg==',
			'7F5F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7QkNFQ11DHUNDkEVbRRpYGxgdGAiJTQGKTYWLQdwUNTVsaWZmaBaS+xgdRIBkIIpe1gZMMZEGkB2oYgFAMUZHRwwxhlA0twxQ+FERYnEfAIigyTiZ+xvNAAAAAElFTkSuQmCC',
			'CBCA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7WENEQxhCHVqRxURaRVoZHQKmOiCJBTSKNLo2CAQEIIsBVbI2MDqIILkvatXUsKWrVmZNQ3IfmjqYGNA8xtAQDDsEUdRB3BKIIgZxsyOK2ECFHxUhFvcBAGIDzC8ExB3WAAAAAElFTkSuQmCC',
			'F381' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAVUlEQVR4nGNYhQEaGAYTpIn7QkNZQxhCGVqRxQIaRFoZHR2moooxNLo2BISiiYHUwfSCnRQatSpsVeiqpcjuQ1OHbB4RYiJY9ILdHBowCMKPihCL+wA6v80rZj24kwAAAABJRU5ErkJggg==',
			'E584' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7QkNEQxlCGRoCkMQCGkQaGB0dGtHFWBsCWtHEQoDqpgQguS80aurSVaGroqKQ3AeUb3R0dHRA1cvQ6NoQGBqCah5QLADNLaytQDtQxEJDGEPQ3TxQ4UdFiMV9AI3yzvmI+7m4AAAAAElFTkSuQmCC',
			'990C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7WAMYQximMEwNQBITmcLayhDKECCCJBbQKtLo6OjowIIm5toQ6IDsvmlTly5NXRWZhew+VlfGQCR1ENjK0IguJtDKgmEHNrdgc/NAhR8VIRb3AQDIU8r4VHDpDgAAAABJRU5ErkJggg==',
			'B6E5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7QgMYQ1hDHUMDkMQCprC2sjYwOiCrC2gVacQQmyLSABRzdUByX2jUtLCloSujopDcFzBFFGgeQ4MImnmuWMUYHVDEwG5hCEB2H8TNDlMdBkH4URFicR8A8YHMOOdls88AAAAASUVORK5CYII=',
			'BC38' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAW0lEQVR4nGNYhQEaGAYTpIn7QgMYQxlDGaY6IIkFTGFtdG10CAhAFmsVaXBoCHQQQVEH5CHUgZ0UGjVt1aqpq6ZmIbkPTR3cPAZ087DagekWbG4eqPCjIsTiPgA7zM9qTwFRUwAAAABJRU5ErkJggg==',
			'4D21' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpI37poiGMIQytKKIhYi0Mjo6TEUWYwwRaXRtCAhFFmOdItLo0BAA0wt20rRp01Zmrcxaiuy+AJC6VlQ7QkOBYlPQ7AWpC8AQa2V0QBcTDWENDQgNGAzhRz2IxX0ANKPMTKQZvcUAAAAASUVORK5CYII=',
			'EEDB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAUUlEQVR4nGNYhQEaGAYTpIn7QkNEQ1lDGUMdkMQCGkQaWBsdHQLQxRoCHUSwiAUguS80amrY0lWRoVlI7kNTR9A8DDE0t2Bz80CFHxUhFvcBADeJzQYZt2SYAAAAAElFTkSuQmCC',
			'5405' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nGNYhQEaGAYTpIn7QkMYWhmmMIYGIIkB2VMZQhkdGFDFQhkdHVHEAgMYXVkbAl0dkNwXNm3p0qWrIqOikN3XKtLKCjRBBNnmVtFQVzSxgFaGVpAdyGIiU4DuC2UIQHYfawDIzQxTHQZB+FERYnEfAL7Tyx9WcfmvAAAAAElFTkSuQmCC',
			'EBA9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7QkNEQximMEx1QBILaBBpZQhlCAhAFWt0dHR0EEFTx9oQCBMDOyk0amrY0lVRUWFI7oOoC5iKprfRNRRIoos1BGCxIwDFLSA3g8xDdvNAhR8VIRb3AQCA1M5YVi2SZwAAAABJRU5ErkJggg==',
			'2D37' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7WANEQxhDGUNDkMREpoi0sjY6NIggiQW0igBFAlDEGEBiYFEk902btjJr6qqVWcjuCwCra0W2l9EBbN4UFLc0gMUCkMVEGkBucXRAFgsNBbsZRWygwo+KEIv7AJl7zRV7MigNAAAAAElFTkSuQmCC',
			'F6FA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7QkMZQ1hDA1qRxQIaWFtZGximOqCIiTQCxQICUMUaWBsYHUSQ3BcaNS1saejKrGlI7gtoEG1FUgc3z7WBMTQEUwxNHSsWvUA3o4kNVPhREWJxHwAUF8v9o+uFLQAAAABJRU5ErkJggg==',
			'EB42' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7QkNEQxgaHaY6IIkFNIi0MrQ6BASgigFVOTqIoKsLdGgQQXJfaNTUsJWZWauikNwHUsfa6NCIZkeja2hAKwO6HY0OUxjQ7Wh0CMB0s2NoyCAIPypCLO4DAKOVzu3USbLWAAAAAElFTkSuQmCC',
			'65B2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nM2QMQrAIAxF45AbpPdx6Z6CGeppIrQ3sEdw8ZTVLdKOLZgPGR6BPD7UxyjMlF/8kBdBgcsbRpkUk2c2jI/GdPNkmVJod0rGb49XKVJrNH4hQ1qTT/YHn431PTDqLMPggmd3GZ1dQHESJujvw7z43Ruuzb1LxkgXAAAAAElFTkSuQmCC',
			'8EFF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAATUlEQVR4nGNYhQEaGAYTpIn7WANEQ1lDA0NDkMREpog0sDYwOiCrC2jFFENTB3bS0qipYUtDV4ZmIbmPWPOIsAPhZjSxgQo/KkIs7gMAGwrIxOINRjIAAAAASUVORK5CYII=',
			'5671' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nM2QwQnAMAhF7cEN7D7pBhaSzGMObpBmg14yZaVQiLTHFuoHwQfiQ+i3EvhTPvFLcYqYWEfGgjbz5hkV62lkK5NACdfuqZRby323jH46K1RwN0CpBPaMjS3BM6qoKJ4hm7NA4h/878U8+B2aY8xDh5eaIQAAAABJRU5ErkJggg==',
			'AB7C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7GB1EQ1hDA6YGIImxBoi0MjQEBIggiYlMEWl0aAh0YEESC2gFqmt0dEB2X9TSqWGrlq7MQnYfWN0URgdke0NDgeYFoIoB1QFNY8Swg7WBAcUtAa1ANzcwoLh5oMKPihCL+wAI9swg889tXQAAAABJRU5ErkJggg==',
			'33C8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7RANYQxhCHaY6IIkFTBFpZXQICAhAVtnK0OjaIOgggiw2haGVtYEBpg7spJVRq8KWrlo1NQvZfajqkMxjRDUPix3Y3ILNzQMVflSEWNwHANz7y+4U76afAAAAAElFTkSuQmCC',
			'61DD' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7WAMYAlhDGUMdkMREpjAGsDY6OgQgiQW0sAawNgQ6iCCLNTAgi4GdFBm1KmrpqsisaUjuC5nCgKm3lTgxEZBeNLcAXRKK7uaBCj8qQizuAwCI78oa6JTHVwAAAABJRU5ErkJggg==',
			'7FB8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWUlEQVR4nGNYhQEaGAYTpIn7QkNFQ11DGaY6IIu2ijSwNjoEBKCLNQQ6iCCLTUFRB3FT1NSwpaGrpmYhuY/RAdM81gZM80SwiAU0YOoFi6G7eYDCj4oQi/sA65vM3f5nl/kAAAAASUVORK5CYII=',
			'8F35' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7WANEQx1DGUMDkMREpog0sDY6OiCrC2gVAZKBKGIgdQyNjq4OSO5bGjU1bNXUlVFRSO6DqHNoEMEwLwCLWKCDSAO6WxwCkN3HGiDSwBjKMNVhEIQfFSEW9wEAlqHMrstM+WgAAAAASUVORK5CYII=',
			'AC62' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7GB0YQxlCGaY6IImxBrA2Ojo6BAQgiYlMEWlwbXB0EEESC2gVaWAFySG5L2rptFVLpwJpJPeB1Tk6NCLbERoK0hvQyoBmnmtDwBRUMYhbUMVAbmYMDRkE4UdFiMV9AHuxzXn3lu4TAAAAAElFTkSuQmCC',
			'3A30' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7RAMYAhhDGVqRxQKmMIawNjpMdUBW2coKVBMQEIAsNkWk0aHR0UEEyX0ro6atzJq6MmsasvtQ1UHNEw11aAhEEwOqQ7MjAKjXFc0togEijY5obh6o8KMixOI+AJWSzZ3Rvg1CAAAAAElFTkSuQmCC',
			'4EAB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpI37poiGMkxhDHVAFgsRaWAIZXQIQBJjBIoxOjo6iCCJsU4RaWBtCISpAztp2rSpYUtXRYZmIbkvAFUdGIaGAsVCA1HMY4CqwyYWgCImGgoUQ3XzQIUf9SAW9wEA3JTLdmGmK2UAAAAASUVORK5CYII=',
			'8BC0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAW0lEQVR4nGNYhQEaGAYTpIn7WANEQxhCHVqRxUSmiLQyOgRMdUASC2gVaXRtEAgIQFPH2sDoIILkvqVRU8OWrlqZNQ3JfWjqkMzDJoZpB7pbsLl5oMKPihCL+wCzv8yjxmcV7wAAAABJRU5ErkJggg==',
			'86B7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7WAMYQ1hDGUNDkMREprC2sjY6NIggiQW0ijSyNgSgiIlMEWkAqQtAct/SqGlhS0NXrcxCcp/IFFGQea0MaOa5NgRMwSIWwIDhFkcHLG5GERuo8KMixOI+AAkFzLnm0/tbAAAAAElFTkSuQmCC',
			'FCF6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpIn7QkMZQ1lDA6Y6IIkFNLA2ujYwBASgiIk0uDYwOgigibECxZDdFxo1bdXS0JWpWUjug6rDMA+kVwSLHSIE3QJ0cwMDipsHKvyoCLG4DwA76s0Lmu30mwAAAABJRU5ErkJggg==',
			'B93F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7QgMYQxhDGUNDkMQCprC2sjY6OiCrC2gVaXRoCEQVmwIUQ6gDOyk0aunSrKkrQ7OQ3BcwhTHQAcM8BkzzWlmw2IHpFqibUcQGKvyoCLG4DwCZJ8xgDD+ZaAAAAABJRU5ErkJggg==',
			'0DBA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7GB1EQ1hDGVqRxVgDRFpZGx2mOiCJiUwRaXRtCAgIQBILaAWKNTo6iCC5L2rptJWpoSuzpiG5D00dQqwhMDQEw45AFHUQt6DqhbiZEUVsoMKPihCL+wD22cysdB/5/QAAAABJRU5ErkJggg==',
			'70D5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7QkMZAlhDGUMDkEVbGUNYGx0dUFS2srayNgSiik0RaXRtCHR1QHZf1LSVqasio6KQ3MfoAFIX0CCCpJe1AVNMpAFiB7JYQAPILQ4BAShiIDczTHUYBOFHRYjFfQBcjsvSG6PqnwAAAABJRU5ErkJggg==',
			'CFF3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWklEQVR4nGNYhQEaGAYTpIn7WENEQ11DA0IdkMREWkUaWBsYHQKQxAIaQWJAOWSxBohYAJL7olZNDVsaumppFpL70NShiIkQsAObW1hDwOpQ3DxQ4UdFiMV9ANfFzKcXEyCtAAAAAElFTkSuQmCC',
			'64E2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nM2QsQ2AMAwEn8IbhH1Ckd4FLsg0RiIbJCOkyZSQzghKkOLvTi/9yWiPU4yUX/yIkUh88Ya5jEIKZsP4gJBO3lmmU6DeN35brLVKa9H4rdmlq7fbDU6zBEXCjaH3Mu4unfHTeZF1gP99mBe/E5Chy7B6k/1fAAAAAElFTkSuQmCC',
			'FD90' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAW0lEQVR4nGNYhQEaGAYTpIn7QkNFQxhCGVqRxQIaRFoZHR2mOqCKNbo2BAQEYIgFOogguS80atrKzMzIrGlI7gOpcwiBq0OINWCKOWLagcUtmG4eqPCjIsTiPgC9W85L1fDWHgAAAABJRU5ErkJggg==',
			'DFCC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWElEQVR4nGNYhQEaGAYTpIn7QgNEQx1CHaYGIIkFTBFpYHQICBBBFmsVaWBtEHRgwRBjdEB2X9TSqWFLV63MQnYfmjoCYmh2YHFLKIiH5uaBCj8qQizuAwDZssyqWKh6QgAAAABJRU5ErkJggg==',
			'2C75' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7WAMYQ1lDA0MDkMREprA2OjQEOiCrC2gVaUAXYwCKMTQ6ujogu2/atFWrlq6MikJ2XwBQ3RSguUh6GR2AvABUMVYgz9EBLINwSwNroytQJbL7QkOBbm5gmOowCMKPihCL+wCOLcuYe+6HlAAAAABJRU5ErkJggg==',
			'54DF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7QkMYWllDGUNDkMQCGhimsjY6OjCgioWyNgSiiAUGMLoiiYGdFDZt6dKlqyJDs5Dd1yrSiq6XoVU01BVNLKCVAUOdyBSgGJpbWAPAbkY1b4DCj4oQi/sAGcLKUO30LWAAAAAASUVORK5CYII=',
			'6237' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nGNYhQEaGAYTpIn7WAMYQxhDGUNDkMREprC2sjY6NIggiQW0iABFAlDFGhgaHcCiCPdFRq1aumrqqpVZSO4LmcIwBaiyFdnegFaGACA5BVWM0QFIBjCguqWBtdHRAdXNoqGOoYwoYgMVflSEWNwHAICjzPlAc4GrAAAAAElFTkSuQmCC',
			'775B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7QkNFQ11DHUMdkEVbGRpdGxgdArCIiSCLTWFoZZ0KVwdxU9SqaUszM0OzkNzH6MAQwNAQiGIeK0gUKIZsnghQlBVNLAAoyujoiKIXJMYQyojq5gEKPypCLO4DAKqyyu9R3TQ9AAAAAElFTkSuQmCC',
			'6006' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7WAMYAhimMEx1QBITmcIYwhDKEBCAJBbQwtrK6OjoIIAs1iDS6NoQ6IDsvsioaStTV0WmZiG5L2QKWB2qea0QvSIoYhA7RAi4BZubByr8qAixuA8AYwvLnapSRmYAAAAASUVORK5CYII=',
			'1727' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7GB1EQx1CGUNDkMRYHRgaHR0dGkSQxESBYq4NAShijA4MrQxAsQAk963MWjUNSAAphPuA6gKAKltR7QWKTgFCFDHWBqDKAFQxEYhaZLeEiDSwhgaiiA1U+FERYnEfAMnOyENgvoMDAAAAAElFTkSuQmCC',
			'E58F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7QkNEQxlCGUNDkMQCGkQaGB0dHRjQxFgbAtHFQpDUgZ0UGjV16arQlaFZSO4LaGBodMQwj6HRFdM8LGKsrehuCQ1hDAG6GUVsoMKPihCL+wBiGsqwpgAY+AAAAABJRU5ErkJggg==',
			'9756' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAd0lEQVR4nGNYhQEaGAYTpIn7WANEQ11DHaY6IImJTGFodG1gCAhAEgtoBYkxOgigirWyTmV0QHbftKmrpi3NzEzNQnIfqytDAENDIIp5DK0gfYEOIkhiAq2sDaxoYiJTRBoYHR1Q9LIGAFWEMqC4eaDCj4oQi/sAQ5LLREX9dxkAAAAASUVORK5CYII=',
			'0140' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7GB0YAhgaHVqRxVgDGAMYWh2mOiCJiUxhDWCY6hAQgCQW0ArUG+joIILkvqilq6JWZmZmTUNyH0gdayNcHUIsNBBFTGQK2C0odgBtBYmhuIXRgTUU3c0DFX5UhFjcBwCqrco2VTqbWgAAAABJRU5ErkJggg==',
			'4243' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAeElEQVR4nGNYhQEaGAYTpI37pjCGMDQ6hDogi4WwtjK0OjoEIIkxhog0Okx1aBBBEmOdAtQZ6NAQgOS+adNWLV2ZmbU0C8l9AVMYprA2wtWBYWgoQwBraACKeUC3OABNRBNjbWBoRHULwxTRUAd0Nw9U+FEPYnEfALyjzYH/eoTgAAAAAElFTkSuQmCC',
			'7E73' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7QkNFQ1lDA0IdkEVbRYBkoEMAhlhAgwiy2BQgr9GhIQDZfVFTw1YtXbU0C8l9jA5AdVMYGpDNYwWZFMCAYp4IEDI6oIqBbGQFigagiAHd3MCA6uYBCj8qQizuAwB1YsxGrzwLdwAAAABJRU5ErkJggg==',
			'6888' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWklEQVR4nGNYhQEaGAYTpIn7WAMYQxhCGaY6IImJTGFtZXR0CAhAEgtoEWl0bQh0EEEWa0BRB3ZSZNTKsFWhq6ZmIbkvBJt5rVjMwyKGzS3Y3DxQ4UdFiMV9AH3+zIBPaUJVAAAAAElFTkSuQmCC',
			'ED80' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWUlEQVR4nGNYhQEaGAYTpIn7QkNEQxhCGVqRxQIaRFoZHR2mOqCKNbo2BAQEoIk5Ojo6iCC5LzRq2sqs0JVZ05Dch6YOybxALGIYdmC4BZubByr8qAixuA8AVijN9HWOK2UAAAAASUVORK5CYII=',
			'7A0D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7QkMZAhimMIY6IIu2MoYwhDI6BKCIsbYyOjo6iCCLTRFpdG0IhIlB3BQ1bWXqqsisaUjuY3RAUQeGrA2ioehiIg0ijY5odgQAxRzQ3AIWQ3fzAIUfFSEW9wEA2obLhiNjM/MAAAAASUVORK5CYII=',
			'BF23' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7QgNEQx1CGUIdkMQCpog0MDo6OgQgi7WKNLA2BDSIoKkDkg0BSO4LjZoatmpl1tIsJPeB1bUyNKCbxzCFAdU8kFgAA4YdjA6MKG4JDQC6JTQAxc0DFX5UhFjcBwBQE83M7EK/ggAAAABJRU5ErkJggg==',
			'39E5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7RAMYQ1hDHUMDkMQCprC2sjYwOqCobBVpdEUXmwIWc3VAct/KqKVLU0NXRkUhu28KY6ArkBZBMY+hEVOMBWwHshjELQwByO6DuNlhqsMgCD8qQizuAwBwhsrv5qqupwAAAABJRU5ErkJggg==',
			'D522' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nM2QwQ2AMAhFfw/doO6DG2BSLh3BKfDQDdoRPNgprbcSPWoiBBJeAnkB7RaKP+UnfsKTQFBpYFyCupmYR5aDel0oWBZ71zD4pb3u7VhbGvw4Y6OrzG6fCzLsvY0YxbDisyOwdXbRyyLxB/97MR/8TjP4zbLbp/hcAAAAAElFTkSuQmCC',
			'A568' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7GB1EQxlCGaY6IImxBog0MDo6BAQgiYlMEWlgbXB0EEESC2gVCWFtYICpAzspaunUpUunrpqaheS+gFaGRlc080JDgWINgejmYRFjbUV3S0ArYwi6mwcq/KgIsbgPALtFzPkSCIhRAAAAAElFTkSuQmCC',
			'AD9B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7GB1EQxhCGUMdkMRYA0RaGR0dHQKQxESmiDS6NgQ6iCCJBbRCxAKQ3Be1dNrKzMzI0Cwk94HUOYQEopgXGgoUw2KeI6YYhlsCWjHdPFDhR0WIxX0AghrMvcbSRDkAAAAASUVORK5CYII=',
			'FDD2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7QkNFQ1hDGaY6IIkFNIi0sjY6BASgijW6NgQ6iGCIAUkk94VGTVuZuioKCBHug6prdMDU28qAKTaFAYtbUMVAbmYMDRkE4UdFiMV9AHHSz5jbq+y6AAAAAElFTkSuQmCC',
			'D572' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7QgNEQ1lDA6Y6IIkFTBEBkQEByGKtILFABxFUsRCGRocGEST3RS2dunTVUiCN5L6AVqCqKSCVyHqB/ACGVgZU8xodHRimoIhNYW1lbWAIQHUzYwhrA2NoyCAIPypCLO4DADoxzljHjs44AAAAAElFTkSuQmCC',
			'EF49' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7QkNEQx0aHaY6IIkFNIg0MLQ6BASgi011dBBBFwuEi4GdFBo1NWxlZlZUGJL7QOpYgXag62UNBZuAal6jA6YdjahuCQ0Bi6G4eaDCj4oQi/sAkIXOIThmKZAAAAAASUVORK5CYII=',
			'C476' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdklEQVR4nGNYhQEaGAYTpIn7WEMYWllDA6Y6IImJtDJMZWgICAhAEgtoZAhlaAh0EEAWa2B0ZWh0dEB2X9SqpUtXLV2ZmoXkvgCQiVMYUc1rEA11CGB0EEG1o5XRAVUMqLOVtYEBRS/YzQ0MKG4eqPCjIsTiPgD+PMvexamYpAAAAABJRU5ErkJggg==',
			'ED9F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAV0lEQVR4nGNYhQEaGAYTpIn7QkNEQxhCGUNDkMQCGkRaGR0dHRhQxRpdGwLxiYGdFBo1bWVmZmRoFpL7QOocQjD1OmAxzxFTDMMtUDejiA1U+FERYnEfAKF/y6FiqBQaAAAAAElFTkSuQmCC',
			'891A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7WAMYQximMLQii4lMYW1lCGGY6oAkFtAq0ugYwhAQgKJOpNFhCqODCJL7lkYtXZo1bWXWNCT3iUxhDERSBzWPAaQ3NARFjKURXR3YLWhiIDczhjqiiA1U+FERYnEfAH3dy4qttBbtAAAAAElFTkSuQmCC',
			'258D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7WANEQxlCGUMdkMREpog0MDo6OgQgiQW0ijSwNgQ6iCDrbhUJAakTQXbftKlLV4WuzJqG7L4AhkZHhDowZHRgaHRFM4+1QQRDDGhrK7pbQkMZQ9DdPFDhR0WIxX0A687KcDDgxNcAAAAASUVORK5CYII=',
			'FE7D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7QkNFQ1lDA0MdkMQCGkSAZKBDABYxEXSxRkeYGNhJoVFTw1YtXZk1Dcl9YHVTGDH1BmCKMTpgirECRVHdAnRzAyOKmwcq/KgIsbgPAFGNzBin4D1vAAAAAElFTkSuQmCC',
			'95CA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAeElEQVR4nGNYhQEaGAYTpIn7WANEQxlCHVqRxUSmiDQwOgRMdUASC2gVaWBtEAgIQBULYQWqFEFy37SpU5cuXbUyaxqS+1hdGRpdEeogsBUsFhqCJCbQKgIUE0RRJzKFtZXRIRBFjDWAMYQh1BHVvAEKPypCLO4DADptyzJ11BZ3AAAAAElFTkSuQmCC',
			'3E3F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAVUlEQVR4nGNYhQEaGAYTpIn7RANEQxmBMARJLGCKSANro6MDispWESAZiCoGVMeAUAd20sqoqWGrpq4MzUJ2H6o63OZhEcPmFqibUfUOUPhREWJxHwB4nsnl/KiumgAAAABJRU5ErkJggg==',
			'85B2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nM2QMQ7AIAhFceAG9D4s3Wkii6fRwRvoEVw8ZXWTtGObyE8YXgi8AP1REXbKL34oh6JC5YVRoYiJRRYmebB4Mdk5P+YiLX4t1Na097D4UYF0Jk5s9g02u70xWQFzA/N0sc7Oozr1G/zvw7z43fpYzaKAji66AAAAAElFTkSuQmCC',
			'438D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpI37prCGMIQyhjogi4WItDI6OjoEIIkxhjA0ujYEOoggibFOYQCrE0Fy37Rpq8JWha7MmobkvgBUdWAYGoppHsMUbGKYbsHq5oEKP+pBLO4DAP7Xypg6C12cAAAAAElFTkSuQmCC',
			'A65F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7GB0YQ1hDHUNDkMRYA1hbWYEyyOpEpog0oosFtIo0sE6Fi4GdFLV0WtjSzMzQLCT3BbSKtjI0BKLoDQ0VaXRAEwOa1+iKIcbayujoiCbGGMIQiuqWgQo/KkIs7gMACEjJ5NZEru4AAAAASUVORK5CYII=',
			'A543' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpIn7GB1EQxkaHUIdkMRYA0QaGFodHQKQxESmAMWmOjSIIIkFtIqEMAQ6NAQguS9q6dSlKzOzlmYhuS+glaHRtRGuDgxDgba6hgagm9fo0IhuBytQN6pbAloZQ9DdPFDhR0WIxX0AwqnOlydJpMwAAAAASUVORK5CYII=',
			'0381' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7GB1YQxhCGVqRxVgDRFoZHR2mIouJTGFodG0ICEUWC2hlAKmD6QU7KWrpqrBVoauWIrsPTR1MDGReKxY7sLkFRQzq5tCAQRB+VIRY3AcA7NPLTLiTS2UAAAAASUVORK5CYII=',
			'DA9B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7QgMYAhhCGUMdkMQCpjCGMDo6OgQgi7WytrI2BDqIoIiJNLoCxQKQ3Be1dNrKzMzI0Cwk94HUOYQEopknCrQT0zxHdLEpQDE0t4QGAM1Dc/NAhR8VIRb3AQDECs28yVSOEAAAAABJRU5ErkJggg==',
			'D37C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7QgNYQ1hDA6YGIIkFTBFpBZIBIshirQyNDg2BDiyoYkBRRwdk90UtXRW2aunKLGT3gdVNYXRgQDcvAFPM0YER1Q6gW1gbGFDcAnZzAwOKmwcq/KgIsbgPAFKizNt5ZvSgAAAAAElFTkSuQmCC'        
        );
        $this->text = array_rand( $images );
        return $images[ $this->text ] ;    
    }
    
    function out_processing_gif(){
        $image = dirname(__FILE__) . '/processing.gif';
        $base64_image = "R0lGODlhFAAUALMIAPh2AP+TMsZiALlcAKNOAOp4ANVqAP+PFv///wAAAAAAAAAAAAAAAAAAAAAAAAAAACH/C05FVFNDQVBFMi4wAwEAAAAh+QQFCgAIACwAAAAAFAAUAAAEUxDJSau9iBDMtebTMEjehgTBJYqkiaLWOlZvGs8WDO6UIPCHw8TnAwWDEuKPcxQml0Ynj2cwYACAS7VqwWItWyuiUJB4s2AxmWxGg9bl6YQtl0cAACH5BAUKAAgALAEAAQASABIAAAROEMkpx6A4W5upENUmEQT2feFIltMJYivbvhnZ3Z1h4FMQIDodz+cL7nDEn5CH8DGZhcLtcMBEoxkqlXKVIgAAibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkphaA4W5upMdUmDQP2feFIltMJYivbvhnZ3V1R4BNBIDodz+cL7nDEn5CH8DGZAMAtEMBEoxkqlXKVIg4HibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpjaE4W5tpKdUmCQL2feFIltMJYivbvhnZ3R0A4NMwIDodz+cL7nDEn5CH8DGZh8ONQMBEoxkqlXKVIgIBibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpS6E4W5spANUmGQb2feFIltMJYivbvhnZ3d1x4JMgIDodz+cL7nDEn5CH8DGZgcBtMMBEoxkqlXKVIggEibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpAaA4W5vpOdUmFQX2feFIltMJYivbvhnZ3V0Q4JNhIDodz+cL7nDEn5CH8DGZBMJNIMBEoxkqlXKVIgYDibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpz6E4W5tpCNUmAQD2feFIltMJYivbvhnZ3R1B4FNRIDodz+cL7nDEn5CH8DGZg8HNYMBEoxkqlXKVIgQCibbK9YLBYvLtHH5K0J0IACH5BAkKAAgALAEAAQASABIAAAROEMkpQ6A4W5spIdUmHQf2feFIltMJYivbvhnZ3d0w4BMAIDodz+cL7nDEn5CH8DGZAsGtUMBEoxkqlXKVIgwGibbK9YLBYvLtHH5K0J0IADs=";
        $binary = is_file($image) ? join("",file($image)) : base64_decode($base64_image); 
        header("Cache-Control: post-check=0, pre-check=0, max-age=0, no-store, no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: image/gif");
        echo $binary;
    }

}
# end of class phpfmgImage
# ------------------------------------------------------
# end of module : captcha


# module user
# ------------------------------------------------------
function phpfmg_user_isLogin(){
    return ( isset($_SESSION['authenticated']) && true === $_SESSION['authenticated'] );
}


function phpfmg_user_logout(){
    session_destroy();
    header("Location: admin.php");
}

function phpfmg_user_login()
{
    if( phpfmg_user_isLogin() ){
        return true ;
    };
    
    $sErr = "" ;
    if( 'Y' == $_POST['formmail_submit'] ){
        if(
            defined( 'PHPFMG_USER' ) && strtolower(PHPFMG_USER) == strtolower($_POST['Username']) &&
            defined( 'PHPFMG_PW' )   && strtolower(PHPFMG_PW) == strtolower($_POST['Password']) 
        ){
             $_SESSION['authenticated'] = true ;
             return true ;
             
        }else{
            $sErr = 'Login failed. Please try again.';
        }
    };
    
    // show login form 
    phpfmg_admin_header();
?>
<form name="frmFormMail" action="" method='post' enctype='multipart/form-data'>
<input type='hidden' name='formmail_submit' value='Y'>
<br><br><br>

<center>
<div style="width:380px;height:260px;">
<fieldset style="padding:18px;" >
<table cellspacing='3' cellpadding='3' border='0' >
	<tr>
		<td class="form_field" valign='top' align='right'>Email :</td>
		<td class="form_text">
            <input type="text" name="Username"  value="<?php echo $_POST['Username']; ?>" class='text_box' >
		</td>
	</tr>

	<tr>
		<td class="form_field" valign='top' align='right'>Password :</td>
		<td class="form_text">
            <input type="password" name="Password"  value="" class='text_box'>
		</td>
	</tr>

	<tr><td colspan=3 align='center'>
        <input type='submit' value='Login'><br><br>
        <?php if( $sErr ) echo "<span style='color:red;font-weight:bold;'>{$sErr}</span><br><br>\n"; ?>
        <a href="admin.php?mod=mail&func=request_password">I forgot my password</a>   
    </td></tr>
</table>
</fieldset>
</div>
<script type="text/javascript">
    document.frmFormMail.Username.focus();
</script>
</form>
<?php
    phpfmg_admin_footer();
}


function phpfmg_mail_request_password(){
    $sErr = '';
    if( $_POST['formmail_submit'] == 'Y' ){
        if( strtoupper(trim($_POST['Username'])) == strtoupper(trim(PHPFMG_USER)) ){
            phpfmg_mail_password();
            exit;
        }else{
            $sErr = "Failed to verify your email.";
        };
    };
    
    $n1 = strpos(PHPFMG_USER,'@');
    $n2 = strrpos(PHPFMG_USER,'.');
    $email = substr(PHPFMG_USER,0,1) . str_repeat('*',$n1-1) . 
            '@' . substr(PHPFMG_USER,$n1+1,1) . str_repeat('*',$n2-$n1-2) . 
            '.' . substr(PHPFMG_USER,$n2+1,1) . str_repeat('*',strlen(PHPFMG_USER)-$n2-2) ;


    phpfmg_admin_header("Request Password of Email Form Admin Panel");
?>
<form name="frmRequestPassword" action="admin.php?mod=mail&func=request_password" method='post' enctype='multipart/form-data'>
<input type='hidden' name='formmail_submit' value='Y'>
<br><br><br>

<center>
<div style="width:580px;height:260px;text-align:left;">
<fieldset style="padding:18px;" >
<legend>Request Password</legend>
Enter Email Address <b><?php echo strtoupper($email) ;?></b>:<br />
<input type="text" name="Username"  value="<?php echo $_POST['Username']; ?>" style="width:380px;">
<input type='submit' value='Verify'><br>
The password will be sent to this email address. 
<?php if( $sErr ) echo "<br /><br /><span style='color:red;font-weight:bold;'>{$sErr}</span><br><br>\n"; ?>
</fieldset>
</div>
<script type="text/javascript">
    document.frmRequestPassword.Username.focus();
</script>
</form>
<?php
    phpfmg_admin_footer();    
}


function phpfmg_mail_password(){
    phpfmg_admin_header();
    if( defined( 'PHPFMG_USER' ) && defined( 'PHPFMG_PW' ) ){
        $body = "Here is the password for your form admin panel:\n\nUsername: " . PHPFMG_USER . "\nPassword: " . PHPFMG_PW . "\n\n" ;
        if( 'html' == PHPFMG_MAIL_TYPE )
            $body = nl2br($body);
        mailAttachments( PHPFMG_USER, "Password for Your Form Admin Panel", $body, PHPFMG_USER, 'You', "You <" . PHPFMG_USER . ">" );
        echo "<center>Your password has been sent.<br><br><a href='admin.php'>Click here to login again</a></center>";
    };   
    phpfmg_admin_footer();
}


function phpfmg_writable_check(){
 
    if( is_writable( dirname(PHPFMG_SAVE_FILE) ) && is_writable( dirname(PHPFMG_EMAILS_LOGFILE) )  ){
        return ;
    };
?>
<style type="text/css">
    .fmg_warning{
        background-color: #F4F6E5;
        border: 1px dashed #ff0000;
        padding: 16px;
        color : black;
        margin: 10px;
        line-height: 180%;
        width:80%;
    }
    
    .fmg_warning_title{
        font-weight: bold;
    }

</style>
<br><br>
<div class="fmg_warning">
    <div class="fmg_warning_title">Your form data or email traffic log is NOT saving.</div>
    The form data (<?php echo PHPFMG_SAVE_FILE ?>) and email traffic log (<?php echo PHPFMG_EMAILS_LOGFILE?>) will be created automatically when the form is submitted. 
    However, the script doesn't have writable permission to create those files. In order to save your valuable information, please set the directory to writable.
     If you don't know how to do it, please ask for help from your web Administrator or Technical Support of your hosting company.   
</div>
<br><br>
<?php
}


function phpfmg_log_view(){
    $n = isset($_REQUEST['file'])  ? $_REQUEST['file']  : '';
    $files = array(
        1 => PHPFMG_EMAILS_LOGFILE,
        2 => PHPFMG_SAVE_FILE,
    );
    
    phpfmg_admin_header();
   
    $file = $files[$n];
    if( is_file($file) ){
        if( 1== $n ){
            echo "<pre>\n";
            echo join("",file($file) );
            echo "</pre>\n";
        }else{
            $man = new phpfmgDataManager();
            $man->displayRecords();
        };
     

    }else{
        echo "<b>No form data found.</b>";
    };
    phpfmg_admin_footer();
}


function phpfmg_log_download(){
    $n = isset($_REQUEST['file'])  ? $_REQUEST['file']  : '';
    $files = array(
        1 => PHPFMG_EMAILS_LOGFILE,
        2 => PHPFMG_SAVE_FILE,
    );

    $file = $files[$n];
    if( is_file($file) ){
        phpfmg_util_download( $file, PHPFMG_SAVE_FILE == $file ? 'form-data.csv' : 'email-traffics.txt', true, 1 ); // skip the first line
    }else{
        phpfmg_admin_header();
        echo "<b>No email traffic log found.</b>";
        phpfmg_admin_footer();
    };

}


function phpfmg_log_delete(){
    $n = isset($_REQUEST['file'])  ? $_REQUEST['file']  : '';
    $files = array(
        1 => PHPFMG_EMAILS_LOGFILE,
        2 => PHPFMG_SAVE_FILE,
    );
    phpfmg_admin_header();

    $file = $files[$n];
    if( is_file($file) ){
        echo unlink($file) ? "It has been deleted!" : "Failed to delete!" ;
    };
    phpfmg_admin_footer();
}


function phpfmg_util_download($file, $filename='', $toCSV = false, $skipN = 0 ){
    if (!is_file($file)) return false ;

    set_time_limit(0);


    $buffer = "";
    $i = 0 ;
    $fp = @fopen($file, 'rb');
    while( !feof($fp)) { 
        $i ++ ;
        $line = fgets($fp);
        if($i > $skipN){ // skip lines
            if( $toCSV ){ 
              $line = str_replace( chr(0x09), ',', $line );
              $buffer .= phpfmg_data2record( $line, false );
            }else{
                $buffer .= $line;
            };
        }; 
    }; 
    fclose ($fp);
  

    
    /*
        If the Content-Length is NOT THE SAME SIZE as the real conent output, Windows+IIS might be hung!!
    */
    $len = strlen($buffer);
    $filename = basename( '' == $filename ? $file : $filename );
    $file_extension = strtolower(substr(strrchr($filename,"."),1));

    switch( $file_extension ) {
        case "pdf": $ctype="application/pdf"; break;
        case "exe": $ctype="application/octet-stream"; break;
        case "zip": $ctype="application/zip"; break;
        case "doc": $ctype="application/msword"; break;
        case "xls": $ctype="application/vnd.ms-excel"; break;
        case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
        case "gif": $ctype="image/gif"; break;
        case "png": $ctype="image/png"; break;
        case "jpeg":
        case "jpg": $ctype="image/jpg"; break;
        case "mp3": $ctype="audio/mpeg"; break;
        case "wav": $ctype="audio/x-wav"; break;
        case "mpeg":
        case "mpg":
        case "mpe": $ctype="video/mpeg"; break;
        case "mov": $ctype="video/quicktime"; break;
        case "avi": $ctype="video/x-msvideo"; break;
        //The following are for extensions that shouldn't be downloaded (sensitive stuff, like php files)
        case "php":
        case "htm":
        case "html": 
                $ctype="text/plain"; break;
        default: 
            $ctype="application/x-download";
    }
                                            

    //Begin writing headers
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: public"); 
    header("Content-Description: File Transfer");
    //Use the switch-generated Content-Type
    header("Content-Type: $ctype");
    //Force the download
    header("Content-Disposition: attachment; filename=".$filename.";" );
    header("Content-Transfer-Encoding: binary");
    header("Content-Length: ".$len);
    
    while (@ob_end_clean()); // no output buffering !
    flush();
    echo $buffer ;
    
    return true;
 
    
}
?>