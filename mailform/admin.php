<?php
require_once( dirname(__FILE__).'/form.lib.php' );

define( 'PHPFMG_USER', "info@green-fox-media.com" ); // must be a email address. for sending password to you.
define( 'PHPFMG_PW', "c65023" );

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
    $public_modules   = false !== strpos('|captcha|', "|{$mod}|", "|ajax|");
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
        
    $info =  @unserialize(base64_decode($_REQUEST['filelink']));
    if( !isset($info['recordID']) ){
        return ;
    };
    
    $file = PHPFMG_SAVE_ATTACHMENTS_DIR . $info['recordID'] . '-' . $info['filename'];
    phpfmg_util_download( $file, $info['filename'] );
}


class phpfmgDataManager
{
    var $dataFile = '';
    var $columns = '';
    var $records = '';
    
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
			'2A1A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7WAMYAhimMLQii4lMYQxhCGGY6oAkFtDK2goUDQhA1t0q0ugwhdFBBNl906atzAIhZPcFoKgDQ0YH0VCgWGgIslsaMNWJYBELDRVpdAx1RBEbqPCjIsTiPgCiRMskWMXLxwAAAABJRU5ErkJggg==',
			'5615' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nM2QMQ6AIAxF24Eb1Pt0ca8JMHAaHLgBcgc5pehU0VET+reX/PyXQn1chJHyi5+zaCGjE8UkmgQWGW6MVuzYIhRbd2bl50vxtewhaL80JcgQSS8nWrljcjFkzSibsyvaz0gzcbzxAP/7MC9+B4L6ywhoBLPSAAAAAElFTkSuQmCC',
			'9F0B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7WANEQx2mMIY6IImJTBFpYAhldAhAEgtoFWlgdHR0EEETY20IhKkDO2na1KlhS1dFhmYhuY/VFUUdBEL1IpsngMUObG5hDQCKobl5oMKPihCL+wCoocrecxs6SQAAAABJRU5ErkJggg==',
			'44E9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpI37pjC0soY6THVAFgthmMrawBAQgCTGGMIQytrA6CCCJMY6hdEVSQzspGnTli5dGroqKgzJfQFTRFqB5k1F1hsaKhrqCqRF0N3SwOCARQzFLVjdPFDhRz2IxX0AGsLKuD+rFW8AAAAASUVORK5CYII=',
			'A721' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7GB1EQx1CGVqRxVgDGBodHR2mIouJTGFodG0ICEUWC2gF6QuA6QU7KWrpqmmrVmYtRXYfUEUAQyuqHaGhjA4MU1DFAlpZG8AqUcREgG7EFGMNDQgNGAThR0WIxX0A4urME1mzTFkAAAAASUVORK5CYII=',
			'E5BC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7QkNEQ1lDGaYGIIkFNIg0sDY6BIigizUEOrCgioWwNjo6ILsvNGrq0qWhK7OQ3Qc0u9EVoQ4hBjQPVUwELIZqB2srultCQxhD0N08UOFHRYjFfQB8R81HStKaHwAAAABJRU5ErkJggg==',
			'A0F5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7GB0YAlhDA0MDkMRYAxhDWEEySGIiU1hb0cUCWkUaXRsYXR2Q3Be1dNrK1NCVUVFI7oOoA5qBpDc0FFMsoBViB6oYyC0MAQEoYkA3NzBMdRgE4UdFiMV9AC5BywDLGbiMAAAAAElFTkSuQmCC',
			'61E2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7WAMYAlhDHaY6IImJTGEMYG1gCAhAEgtoYQWKMTqIIIsB1bCC1CO5LzJqVdTS0FWropDcFzIFrK4R2Y6AVrBYKwOm2BQGFLeAxQJQ3cwayhrqGBoyCMKPihCL+wA1DsnDXAuyFgAAAABJRU5ErkJggg==',
			'273E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7WANEQx1DGUMDkMREpjA0ujY6OiCrC2hlaHRoCEQRY2gFQoQ6iJumAeHUlaFZyO4LAEI08xgdGIF8VPNYgRBdTAQIWdH0hoaKNDCiuXmgwo+KEIv7AP2hykkGIJwwAAAAAElFTkSuQmCC',
			'C3FC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7WEOAMDRgagCSmEirSCtrA0OACJJYQCNDo2sDowMLslgDA1AdowOy+6JWrQpbGroyC9l9aOpgYmDzGAjYgc0tYDc3MKC4eaDCj4oQi/sAkwzK3ugIGaYAAAAASUVORK5CYII=',
			'19A2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAeUlEQVR4nGNYhQEaGAYTpIn7GB0YQximMEx1QBJjdWBtZQhlCAhAEhN1EGl0dHR0EEHRK9Lo2hDQIILkvpVZS5emrooCQoT7gHYEAtU1OqDoZWh0DQ1oRXULC8i8KahirK2sDQEByGKiIYwhrA2BoSGDIPyoCLG4DwDmK8qOuRAb5AAAAABJRU5ErkJggg==',
			'C751' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7WENEQ11DHVqRxURaGRpdGximIosFNILFQlHEGhhaWacywPSCnRS1atW0pZlZS5HdB1QXACRbUfUyOmCINbI2sKKJibSKNDA6orqPNUQE5JLQgEEQflSEWNwHAJxUzHIcR4+eAAAAAElFTkSuQmCC',
			'D07D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7QgMYAlhDA0MdkMQCpjCGMDQEOgQgi7WytoLERFDERBodGh1hYmAnRS2dtjJr6cqsaUjuA6ubwoipNwBdjLWV0QFNDOgW1gZGFLeA3dzAiOLmgQo/KkIs7gMA7ubMiiRZ1BUAAAAASUVORK5CYII=',
			'6332' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7WANYQxhDGaY6IImJTBFpZW10CAhAEgtoYWh0aAh0EEEWa2BoBYmKILkvMmpV2Kqpq1ZFIbkvZApYXSOyHQFgnUASU2wKAxa3YLqZMTRkEIQfFSEW9wEACcPNqPk1vMYAAAAASUVORK5CYII=',
			'E1C1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7QkMYAhhCHVqRxQIaGAMYHQKmooqxBrA2CISiijEAxRhgesFOCo1aFbV01aqlyO5DU0dATABDDOgWFLHQENZQoJtDAwZB+FERYnEfAHTQytrios73AAAAAElFTkSuQmCC',
			'1BC6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7GB1EQxhCHaY6IImxOoi0MjoEBAQgiYk6iDS6Ngg6CKDoFWllBZLI7luZNTVs6aqVqVlI7oOqQzGPEWwekMQQE0QXw3RLCKabByr8qAixuA8AN6nJGpg/ho0AAAAASUVORK5CYII=',
			'A433' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nM2QsQ2AMAwEP0U2CPuYgt6NG0ZgiqTwBmQEN5kSQWUllCDwd6eXfTLaMBl/yit+gaBBIORYZNRYZmLH0g5B5pwcYw0LCmV2fquZtdpsc36sSV3visgkNOyDjjegvcvJeuev/vdgbvwO+FXN9ogSYg0AAAAASUVORK5CYII=',
			'1D40' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7GB1EQxgaHVqRxVgdRFoZWh2mOiCJiTqINAJFAgJQ9ALFAh0dRJDctzJr2srMzMysaUjuA6lzbYSrQ4iFBmKIOTRi2NHK0IjmlhBMNw9U+FERYnEfAMTByyC7cqWbAAAAAElFTkSuQmCC',
			'B464' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7QgMYWhlCGRoCkMQCpjBMZXR0aEQRA6pibXBoRVXH6MrawDAlAMl9oVFLly6duioqCsl9AVNEWlkdHR1QzRMNdW0IDA1BtaOVFegSNLe0At2CIobNzQMVflSEWNwHAHmizun2GdQKAAAAAElFTkSuQmCC',
			'544D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7QkMYWhkaHUMdkMQCGhimMrQ6OgSgioUyTHV0EEESCwxgdGUIhIuBnRQ2benSlZmZWdOQ3dcq0sraiKqXoVU01DU0EEUsoBXsFhQxkSkQMWS3sAZgunmgwo+KEIv7AOPpy9+IvOPqAAAAAElFTkSuQmCC',
			'554A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7QkNEQxkaHVqRxQIaRBoYWh2mOqCLTXUICEASCwwQCWEIdHQQQXJf2LSpS1dmZmZNQ3ZfK0OjayNcHUIsNDA0BNmOVpFGBzR1IlNYgSpRxVgDGEPQxQYq/KgIsbgPAEhezM6ctJz9AAAAAElFTkSuQmCC',
			'187E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7GB0YQ1hDA0MDkMRYHVhbGRoCHZDViTqINDqgiTGC1DU6wsTATlqZtTJs1dKVoVlI7gOrm8KIphdoXgCmmKMDuhhrK2sDqphoCNDNDYwobh6o8KMixOI+ADZOxzvRVDR6AAAAAElFTkSuQmCC',
			'FFCD' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAVElEQVR4nGNYhQEaGAYTpIn7QkNFQx1CHUMdkMQCGkQaGB0CHQLQxFgbBB1EMMQYYWJgJ4VGTQ1bumpl1jQk96GpIyCGaQc2tzCguXmgwo+KEIv7AFHDzE1xam+mAAAAAElFTkSuQmCC',
			'0944' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7GB0YQxgaHRoCkMRYA1hbGVodGpHFRKaINDpMdWhFFgtoBYoFOkwJQHJf1NKlSzMzs6KikNwX0MoY6Nro6ICql6HRNTQwNATFDpZGB2xuQRPD5uaBCj8qQizuAwA2r86jzXGLwQAAAABJRU5ErkJggg==',
			'54CA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAd0lEQVR4nGNYhQEaGAYTpIn7QkMYWhlCHVqRxQIaGKYyOgRMdUAVC2VtEAgIQBILDGB0ZW1gdBBBcl/YtKVLl65amTUN2X2tIq1I6qBioqGuDYyhIch2tDIA1QmiqBOZwtDK6BCIIsYaAHKzI6p5AxR+VIRY3AcAKkTLGHXmAa0AAAAASUVORK5CYII=',
			'15C3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7GB1EQxlCHUIdkMRYHUSA4oEOAUhiokAx1gaBBhEUvSIhrEA6AMl9K7OmLl26atXSLCT3MTowNLoi1KGIoZkHFEO3g7UVwy0hjCHobh6o8KMixOI+AGDjygbfHrxFAAAAAElFTkSuQmCC',
			'77C1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7QkNFQx1CHVpRRFsZGh0dAqaii7k2CISiiE1haGUFySC7L2rVtKWrVi1Fdh+jA0MAkjowZAWKoouJAEVZGwRQxAKAoowOARhiDEBXBwyC8KMixOI+AJsfy7rXhziTAAAAAElFTkSuQmCC',
			'E17C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7QkMYAlhDA6YGIIkFNDAGAMkAERQxVqBYoAMLihhDAEOjowOy+0KjVkWtWroyC9l9YHVTGB0Y0PUGYIoxOjBi2MEKxMhuCQ1hDQWKobh5oMKPihCL+wDHosoFiv2iEAAAAABJRU5ErkJggg==',
			'339D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7RANYQxhCGUMdkMQCpoi0Mjo6OgQgq2xlaHRtCHQQQRabwtDKihADO2ll1KqwlZmRWdOQ3QdUxxCCphdongO6eUAxRzQxbG7B5uaBCj8qQizuAwBkSMrHqOka0wAAAABJRU5ErkJggg==',
			'E7E6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7QkNEQ11DHaY6IIkFNDA0ujYwBARgiDE6CKCKtbICxZDdFxq1atrS0JWpWUjuA6oLAKpDM4/RAaRXBEWMtQFTTAQohuqW0BCgGJqbByr8qAixuA8AYWnMOytAZCEAAAAASUVORK5CYII=',
			'A66F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7GB0YQxhCGUNDkMRYA1hbGR0dHZDViUwRaWRtQBULaBVpYAWagOy+qKXTwpZOXRmaheS+gFbRVlY080JDRRpdGwLRzcMihumWgFawm1HEBir8qAixuA8AC8vJ7jn6Ib4AAAAASUVORK5CYII=',
			'CCB0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7WEMYQ1lDGVqRxURaWRtdGx2mOiCJBTSKNLg2BAQEIIs1iDSwNjo6iCC5L2rVtFVLQ1dmTUNyH5o6hFhDIKoYFjuwuQWbmwcq/KgIsbgPAHBezgKuDllaAAAAAElFTkSuQmCC',
			'0F2E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7GB1EQx1CGUMDkMRYA0QaGB0dHZDViUwRaWBtCEQRC2gVAZJwMbCTopZODVu1MjM0C8l9YHWtjJh6pzBi2MEQgCoGdosDqhijA9AtoYEobh6o8KMixOI+AB5KyNg+nMKDAAAAAElFTkSuQmCC',
			'0974' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nM2QsQ2AMAwEbYlskIHMBo9EmozAFHaRDbJCGqYEUTlACYL/7iTLp6f1EqU/9RU/Fp5DgsKxgFBIYZ7FGk0UxTOUnZlUOL/cWlvamrPzQ+FJKkt/SybgNHc/BhuFLi5Be3Y4n9hX+z3YG78NZePNu8I+DmQAAAAASUVORK5CYII=',
			'C8E5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7WEMYQ1hDHUMDkMREWllbWRsYHZDVBTSKNLqiizWA1bk6ILkvatXKsKWhK6OikNwHUQc0F0UvyDw0MagdyGIQtzAEILsP4maHqQ6DIPyoCLG4DwDx7MtjOtqjDwAAAABJRU5ErkJggg==',
			'C794' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAd0lEQVR4nGNYhQEaGAYTpIn7WENEQx1CGRoCkMREWhkaHR0dGpHFAhoZGl0bAlpRxBoYWlkbAqYEILkvatWqaSszo6KikNwHlA9gCAl0QNXL6MDQEBgagmIHawMjUAbVLSINjI4OKGKsISINDGhuHqjwoyLE4j4A2q3ONpCIPTYAAAAASUVORK5CYII=',
			'B81A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7QgMYQximMLQiiwVMYW1lCGGY6oAs1irS6BjCEBCArm4Ko4MIkvtCo1aGrZq2MmsakvvQ1MHNc5jCGBqCKYaqDotekJsZQx1RxAYq/KgIsbgPAL2/zJC/I3QuAAAAAElFTkSuQmCC',
			'44CC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpI37pjC0MoQ6TA1AFgthmMroEBAggiTGGMIQytog6MCCJMY6hdGVtYHRAdl906YtXbp01cosZPcFTBFpRVIHhqGhoqGuaGIgt6DbARJDdwtWNw9U+FEPYnEfALbZyliZ6ABpAAAAAElFTkSuQmCC',
			'AB2E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7GB1EQxhCGUMDkMRYA0RaGR0dHZDViUwRaXRtCEQRC2gVaWVAiIGdFLV0atiqlZmhWUjuA6trZUTRGxoq0ugwhRHdvEaHAAwxoE50MdEQ1tBAFDcPVPhREWJxHwCZ5MpZqlseEQAAAABJRU5ErkJggg==',
			'3416' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7RAMYWhmmMEx1QBILAPIZQhgCApBVtjKEMoYwOgggi01hdAViB2T3rYxaunTVtJWpWcjumyICtIMRzTzRUAegXhFUO0DqUMSAbgG5D0UvyM2MoQ4obh6o8KMixOI+AAc2yp7OLAXcAAAAAElFTkSuQmCC',
			'50C8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7QkMYAhhCHaY6IIkFNDCGMDoEBASgiLG2sjYIOoggiQUGiDS6NjDA1IGdFDZt2srUVaumZiG7rxVFHZIYI4p5Aa2YdohMwXQLawCmmwcq/KgIsbgPACCNy//EAxWRAAAAAElFTkSuQmCC',
			'0395' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7GB1YQxhCGUMDkMRYA0RaGR0dHZDViUxhaHRtCEQRC2hlaGVtCHR1QHJf1NJVYSszI6OikNwHUscQEtAggqq30aEBVQxkhyPQDhEMtzgEILsP4maGqQ6DIPyoCLG4DwA2eMrSZpP25QAAAABJRU5ErkJggg==',
			'8723' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdklEQVR4nGNYhQEaGAYTpIn7WANEQx1CGUIdkMREpjA0Ojo6OgQgiQW0MjS6NgQ0iKCqawXKNAQguW9p1Kppq1ZmLc1Cch9QXQBEJbJ5jA4MUxhQzAtoZW0AqkSzQ6SB0YERxS2sASINrKEBKG4eqPCjIsTiPgDW+syG1fuGtwAAAABJRU5ErkJggg==',
			'738C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7QkNZQxhCGaYGIIu2irQyOjoEiKCIMTS6NgQ6sCCLTWEAqnN0QHFf1KqwVaErs5Ddx+iAog4MWRsg5iGLiTRg2hHQgOmWgAYsbh6g8KMixOI+APaFyo67ScX1AAAAAElFTkSuQmCC',
			'DA03' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7QgMYAhimMIQ6IIkFTGEMYQhldAhAFmtlbWV0dGgQQRETaXRtCGgIQHJf1NJpK1OBZBaS+9DUQcVEQ0Fi6OY5otsxRaTRAc0toQFAMTQ3D1T4URFicR8Anr3PO2LxZFsAAAAASUVORK5CYII=',
			'F66D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7QkMZQxhCGUMdkMQCGlhbGR0dHQJQxEQaWRscHURQxRpYGxhhYmAnhUZNC1s6dWXWNCT3BTSItrI6YuhtdG0IJEIMm1sw3TxQ4UdFiMV9ABNMzD3IT5cNAAAAAElFTkSuQmCC',
			'B1E7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7QgMYAlhDHUNDkMQCpjAGsAJpEWSxVlZMsSkMYLEAJPeFRq2KWhq6amUWkvug6loZUMwDi03BIhbAgGEHowOqm1lDgW5GERuo8KMixOI+ACmBylJAO3ygAAAAAElFTkSuQmCC',
			'5CE4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7QkMYQ1lDHRoCkMQCGlgbXRsYGlHFRBqAYq3IYoEBIg2sDQxTApDcFzZt2qqloauiopDd1wpSx+iArBcqFhqCbEcr2A4Ut4hMAbsFRYw1ANPNAxV+VIRY3AcArVjODS2KR90AAAAASUVORK5CYII=',
			'9B2E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7WANEQxhCGUMDkMREpoi0Mjo6OiCrC2gVaXRtCEQXa2VAiIGdNG3q1LBVKzNDs5Dcx+oKVNfKiKKXAWiewxRUMQGQWACqGNgtDqhiIDezhgaiuHmgwo+KEIv7APnwyXyU5hkoAAAAAElFTkSuQmCC',
			'D5EC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7QgNEQ1lDHaYGIIkFTBFpYG1gCBBBFmsFiTE6sKCKhYDEkN0XtXTq0qWhK7OQ3RfQytDoilCHR0wELIZixxTWVnS3hAYwhqC7eaDCj4oQi/sAYjbMXLtne9kAAAAASUVORK5CYII=',
			'6A9E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7WAMYAhhCGUMDkMREpjCGMDo6OiCrC2hhbWVtCEQVaxBpdEWIgZ0UGTVtZWZmZGgWkvtCpog0OoSg6W0VDXVAN69VpNERTUwEqNcRzS2sAUDz0Nw8UOFHRYjFfQBcDcsJbpOzEwAAAABJRU5ErkJggg==',
			'330C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7RANYQximMEwNQBILmCLSyhDKECCCrLKVodHR0dGBBVlsCkMra0OgA7L7VkatClu6KjILxX2o6uDmuWIRQ7cDm1uwuXmgwo+KEIv7AFOFyrhygvwrAAAAAElFTkSuQmCC',
			'9E6D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7WANEQxlCGUMdkMREpog0MDo6OgQgiQW0ijSwNjg6iGCIMcLEwE6aNnVq2NKpK7OmIbmP1RWozhFVLwNYbyCKmAAWMWxuwebmgQo/KkIs7gMA4BjKS0hsBXEAAAAASUVORK5CYII=',
			'7865' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7QkMZQxhCGUMDkEVbWVsZHR0dUFS2ijS6NqCJTWFtZW1gdHVAdl/UyrClU1dGRSG5j9EBqM7RoUEESS9rA8i8ABQxEbBYoAOyWEADyC0OAQEoYiA3M0x1GAThR0WIxX0AmmzLQG3wokYAAAAASUVORK5CYII=',
			'64D4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7WAMYWllDGRoCkMREpjBMZW10aEQWC2hhCGVtCGhFEWtgdAWKTQlAcl9k1NKlS1dFRUUhuS9kikgra0OgA4reVtFQ14bA0BAUMaBbgKaiuaUV6BYUMWxuHqjwoyLE4j4AUeHOypZUHJYAAAAASUVORK5CYII=',
			'E560' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7QkNEQxlCGVqRxQIaRBoYHR2mOqCJsTY4BASgioWwNjA6iCC5LzRq6tKlU1dmTUNyH1BPo6ujI0wdQqwhEE1MBCgWgGYHayu6W0JDGEPQ3TxQ4UdFiMV9AJbgzWTrMFkzAAAAAElFTkSuQmCC',
			'7EE3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAW0lEQVR4nGNYhQEaGAYTpIn7QkNFQ1lDHUIdkEVbRRpYGxgdAjDEGBpEkMWmQMQCkN0XNTVsaeiqpVlI7mN0QFEHhqwNmOaJYBELaMB0S0ADFjcPUPhREWJxHwBDZ8uOMwgSTAAAAABJRU5ErkJggg==',
			'9E22' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7WANEQxlCGaY6IImJTBFpYHR0CAhAEgtoFWlgbQh0EEETA5INIkjumzZ1atiqlVmropDcx+oKVNHK0IhsBwNI7xSgKJKYAEgsACiK7hYHoCiam1lDA0NDBkH4URFicR8ABZ/LDMcaK0MAAAAASUVORK5CYII=',
			'B115' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7QgMYAhimMIYGIIkFTGEMYAhhdEBWF9DKGsCILjYFrNfVAcl9oVGrolZNWxkVheQ+iDqGBhEU83CJMTqIYNjBEIDsvtAA1lDGUIepDoMg/KgIsbgPAFhkyiu5vs1XAAAAAElFTkSuQmCC',
			'2696' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7WAMYQxhCGaY6IImJTGFtZXR0CAhAEgtoFWlkbQh0EEDW3SrSABJDcd+0aWErMyNTs5DdFyDayhASiGIeo4NIowNQrwiyWxpEGh3RxIA2YLglNBTTzQMVflSEWNwHAHpwyuspJnBWAAAAAElFTkSuQmCC',
			'6C94' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7WAMYQxlCGRoCkMREprA2Ojo6NCKLBbSINLg2BLSiiDWINLA2BEwJQHJfZNS0VSszo6KikNwXMkWkgSEk0AFFbytQrCEwNARNzBFoKha3oIhhc/NAhR8VIRb3AQBnPM7MeIMZIgAAAABJRU5ErkJggg==',
			'6237' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nGNYhQEaGAYTpIn7WAMYQxhDGUNDkMREprC2sjY6NIggiQW0iABFAlDFGhgaHcCiCPdFRq1aumrqqpVZSO4LmcIwBaiyFdnegFaGACA5BVWM0QFIBjCguqWBtdHRAdXNoqGOoYwoYgMVflSEWNwHAICjzPlAc4GrAAAAAElFTkSuQmCC',
			'01FA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7GB0YAlhDA1qRxVgDGANYGximOiCJiUxhBYkFBCCJAXUBxRgdRJDcF7UUiEJXZk1Dch+aOmSx0BAUOzDVsQZgijE6sIaiiw1U+FERYnEfABn2x+sz/RsSAAAAAElFTkSuQmCC',
			'97F8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7WANEQ11DA6Y6IImJTGFodG1gCAhAEgtoBYkxOoigirWyItSBnTRt6qppS0NXTc1Cch+rK0MAK5p5DK2MDqxo5gkATUMXE5ki0oCulzUALIbi5oEKPypCLO4DAGgpy0tNyc28AAAAAElFTkSuQmCC',
			'0CF6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7GB0YQ1lDA6Y6IImxBrA2ujYwBAQgiYlMEWlwBaoWQBILaBVpYAWKIbsvaum0VUtDV6ZmIbkPqg7FPJheESx2iBBwC9jNDQwobh6o8KMixOI+AO3+yyyYONgjAAAAAElFTkSuQmCC',
			'E13A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7QkMYAhhDGVqRxQIaGANYGx2mOqCIsQYAyYAAFDGGAIZGRwcRJPeFRq2KWjV1ZdY0JPehqUOINQSGhmCKYahjRdMbGsIayhjKiCI2UOFHRYjFfQA1V8s/v9mRwgAAAABJRU5ErkJggg==',
			'1F1D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWklEQVR4nGNYhQEaGAYTpIn7GB1EQx2mMIY6IImxOog0MIQwOgQgiYkCxRiBYiIoeoG8KXAxsJNWZk0NWzVtZdY0JPehqSNLDMUtIUC3hDqiuHmgwo+KEIv7AHFLx7P63iO0AAAAAElFTkSuQmCC',
			'68AB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7WAMYQximMIY6IImJTGFtZQhldAhAEgtoEWl0dHR0EEEWa2BtZW0IhKkDOykyamXY0lWRoVlI7guZgqIOordVpNE1NBDVPJBYA6qYCBa9IDcDxVDcPFDhR0WIxX0AVE7Mku1OhPUAAAAASUVORK5CYII=',
			'CB7D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7WENEQ1hDA0MdkMREWkVaGRoCHQKQxAIaRRodgGIiyGJAlQyNjjAxsJOiVk0NW7V0ZdY0JPeB1U1hRNfb6BCAJga0w9EBVQzkFtYGRhS3gN3cwIji5oEKPypCLO4DAErrzA6fAFWrAAAAAElFTkSuQmCC',
			'CA12' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7WEMYAhimMEx1QBITaWUMYQCKByCJBTSyAkUZHUSQxRpEGh2mANUjuS9q1bSVWdNANMJ9UHWNDih6RUOBYq0MKHaA1U1hQHELWCwA1c0ijY6hjqEhgyD8qAixuA8A8jbNHYrIm0oAAAAASUVORK5CYII=',
			'B0B4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7QgMYAlhDGRoCkMQCpjCGsDY6NKKItbK2sgJJVHUija6NDlMCkNwXGjVtZWroqqgoJPdB1Dk6oJoHFGsIDA3BtAObW1DEsLl5oMKPihCL+wCzGs/XyE8wNAAAAABJRU5ErkJggg==',
			'D54A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7QgNEQxkaHVqRxQKmiDQwtDpMdUAWawWKTXUICEAVC2EIdHQQQXJf1NKpS1dmZmZNQ3JfQCtDo2sjXB1CLDQwNATVvEYHdHVTWIEqUcVCAxhD0MUGKvyoCLG4DwALG85Wi82iqwAAAABJRU5ErkJggg==',
			'0A29' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAeUlEQVR4nGNYhQEaGAYTpIn7GB0YAhhCGaY6IImxBjCGMDo6BAQgiYlMYW1lbQh0EEESC2gVaXRAiIGdFLV02sqslVlRYUjuA6trZZiKqlc01GEK0FwUO4DqAhhQ7GANEGl0BLoR2S2MDiKNrqEBKG4eqPCjIsTiPgAqhMuowH6P6wAAAABJRU5ErkJggg==',
			'290B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7WAMYQximMIY6IImJTGFtZQhldAhAEgtoFWl0dHR0EEHWDRRzbQiEqYO4adrSpamrIkOzkN0XwBiIpA4MGR0YwHqRzWNtYMGwQ6QB0y2hoZhuHqjwoyLE4j4AoCHK6wxGVVoAAAAASUVORK5CYII=',
			'41AC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpI37pjAEAPHUAGSxEMYAhlCGABEkMcYQ1gBGR0cHFiQxVqBe1oZAB2T3TZu2KmrpqsgsZPcFoKoDw1Cg+ayhqGIMUHUsGGIBKG5hmMIaChRDdfNAhR/1IBb3AQAx2slURTfc/AAAAABJRU5ErkJggg==',
			'59F8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7QkMYQ1hDA6Y6IIkFNLC2sjYwBASgiIk0ujYwOoggiQUGgMTg6sBOCpu2dGlq6KqpWcjua2UMdEUzj6GVAcO8gFYWDDGRKZhuYQ0AurmBAcXNAxV+VIRY3AcAY/nMJVdXHZ8AAAAASUVORK5CYII=',
			'CDC9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7WENEQxhCHaY6IImJtIq0MjoEBAQgiQU0ijS6Ngg6iCCLNYDEGGFiYCdFrZq2MhVIhSG5D6KOYSqmXqBdGHYIoNiBzS3Y3DxQ4UdFiMV9AHpfzUTbpi6TAAAAAElFTkSuQmCC',
			'FA03' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7QkMZAhimMIQ6IIkFNDCGMIQyOgSgiLG2Mjo6NIigiIk0ugLJACT3hUZNW5m6KmppFpL70NRBxURDQWLo5jliscMBwy1AMTQ3D1T4URFicR8AFAbO2jvOo9sAAAAASUVORK5CYII=',
			'814F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7WAMYAhgaHUNDkMREpjAGMLQ6OiCrC2gFqpyKKiYyBag3EC4GdtLSqFVRKzMzQ7OQ3AdSx9qIbh5QLDQQQ4yhEYsdaGKsQJ3oYgMVflSEWNwHALTOyIL55wqZAAAAAElFTkSuQmCC',
			'6BDE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXElEQVR4nGNYhQEaGAYTpIn7WANEQ1hDGUMDkMREpoi0sjY6OiCrC2gRaXRtCEQVawCqQ4iBnRQZNTVs6arI0Cwk94VMQVEH0duKxTwsYtjcgs3NAxV+VIRY3AcAgoTLsWXJ+wwAAAAASUVORK5CYII=',
			'6E50' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7WANEQ1lDHVqRxUSmiDSwNjBMdUASC2gBiwUEIIs1AMWmMjqIILkvMmpq2NLMzKxpSO4LmQJSEQhTB9Hbil2MtSEAxQ6QWxgdHVDcAnIzQygDipsHKvyoCLG4DwDSD8vtdwiC+gAAAABJRU5ErkJggg==',
			'C8E0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpIn7WEMYQ1hDHVqRxURaWVtZGximOiCJBTSKNLo2MAQEIIs1gNQxOogguS9q1cqwpaErs6YhuQ9NHVQMZB6aGBY7sLkFm5sHKvyoCLG4DwDsGswCQQLyywAAAABJRU5ErkJggg==',
			'48F8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpI37pjCGsIYGTHVAFgthbWVtYAgIQBJjDBFpdG1gdBBBEmOdgqIO7KRp01aGLQ1dNTULyX0BUzDNCw3FNI9hCjYxTL1gNzcwoLp5oMKPehCL+wA/78uF6K8mygAAAABJRU5ErkJggg==',
			'4B0A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpI37poiGMExhaEURCxFpZQhlmOqAJMYYItLo6OgQEIAkxjpFpJW1IdBBBMl906ZNDVu6KjJrGpL7AlDVgWFoqEija0NgaAiKW0B2OKKoA4oB3cKIJgZyM5rYQIUf9SAW9wEAYGHLgfB2xEAAAAAASUVORK5CYII=',
			'DD90' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpIn7QgNEQxhCGVqRxQKmiLQyOjpMdUAWaxVpdG0ICAjAEAt0EEFyX9TSaSszMyOzpiG5D6TOIQSuDiHWgCnmiG4HFrdgc/NAhR8VIRb3AQBIIc6skHRicAAAAABJRU5ErkJggg==',
			'A32B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nGNYhQEaGAYTpIn7GB1YQxhCGUMdkMRYA0RaGR0dHQKQxESmMDS6NgQ6iCCJBbQytDIAxQKQ3Be1dFXYqpWZoVlI7gOra2VEMS80lKHRYQojunmNDgHoYkC3OKDqDWhlDWENDURx80CFHxUhFvcBAMFny0bDG1+6AAAAAElFTkSuQmCC',
			'B0F7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7QgMYAlhDA0NDkMQCpjCGsAJpEWSxVtZWDLEpIo2uIBrJfaFR01amhq5amYXkPqi6VgYU88BiUxgw7QhAEQO7hdEBw81oYgMVflSEWNwHAKUXzEUJx643AAAAAElFTkSuQmCC',
			'5F20' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7QkNEQx1CGVqRxQIaRBoYHR2mOqCJsTYEBAQgiQUGiIBIBxEk94VNmxq2amVm1jRk97UCVbQywtQhxKagigWAxAIYUOwQmQJ0iwMDiltYgfayhgaguHmgwo+KEIv7ANf/y7GP4fsvAAAAAElFTkSuQmCC',
			'1161' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7GB0YAhhCGVqRxVgdGAMYHR2mIouJOrAGsDY4hKLrZW2A6wU7aWXWqqilU1ctRXYfWJ2jQyum3gCixBjR9IqGsIYC3RwaMAjCj4oQi/sAT9nG01CTO8kAAAAASUVORK5CYII=',
			'C87E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7WEMYQ1hDA0MDkMREWllbGRoCHZDVBTSKNDqgizUA1TU6wsTATopatTJs1dKVoVlI7gOrm8KIphdoXgAjhh2ODqhiILewNqCKgd3cwIji5oEKPypCLO4DAHpGyplZVVc1AAAAAElFTkSuQmCC',
			'0351' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7GB1YQ1hDHVqRxVgDRFpZGximIouJTGFodG1gCEUWC2hlaGWdygDTC3ZS1NJVYUszs5Yiuw+kDkyi6m10QBOD2BGA4RZGR1T3gdwMdElowCAIPypCLO4DACdgy3RoyGEuAAAAAElFTkSuQmCC',
			'3D52' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7RANEQ1hDHaY6IIkFTBFpZW1gCAhAVtkq0ujawOgggiw2BSg2laFBBMl9K6OmrUzNzFoVhew+oDqHhoBGBzTzgGKtDBh2BExhQHMLo6NDALqbGUIZQ0MGQfhREWJxHwA+usz7UxFltgAAAABJRU5ErkJggg==',
			'F8F0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAW0lEQVR4nGNYhQEaGAYTpIn7QkMZQ1hDA1qRxQIaWFtZGximOqCIiTS6NjAEBGCoY3QQQXJfaNTKsKWhK7OmIbkPTR2SedjEsNmB7hagmxsYUNw8UOFHRYjFfQD83czcycFyZAAAAABJRU5ErkJggg==',
			'DF84' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7QgNEQx1CGRoCkMQCpog0MDo6NKKItYo0sAJJdDGguikBSO6LWjo1bFXoqqgoJPdB1Dk6YJoXGBqCaQc2t6CIhQaINDCguXmgwo+KEIv7AA+Oz00JRGzUAAAAAElFTkSuQmCC',
			'3A6F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7RAMYAhhCGUNDkMQCpjCGMDo6OqCobGVtZW1AE5si0ujawAgTAztpZdS0lalTV4ZmIbsPpA7DPNFQ14ZANDGQeahiAUC9jmh6RQNEGh1CGVH1DlD4URFicR8ATr3KFFZlaugAAAAASUVORK5CYII=',
			'B320' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7QgNYQxhCGVqRxQKmiLQyOjpMdUAWa2VodG0ICAhAUQfSF+ggguS+0KhVYatWZmZNQ3IfWF0rI0wd3DyHKVjEAhjQ7AC6xYEBxS0gN7OGBqC4eaDCj4oQi/sARl/NA2ENANQAAAAASUVORK5CYII=',
			'9A6B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpIn7WAMYAhhCGUMdkMREpjCGMDo6OgQgiQW0srayNjg6iKCIiTS6NjDC1IGdNG3qtJWpU1eGZiG5j9UVqA7NPIZW0VDXhkAU8wTA5qGKiUwRaXRE08saINLogObmgQo/KkIs7gMATEvLuFB4+SAAAAAASUVORK5CYII=',
			'1DBF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAW0lEQVR4nGNYhQEaGAYTpIn7GB1EQ1hDGUNDkMRYHURaWRsdHZDViTqINLo2BDqg6gWKIdSBnbQya9rK1NCVoVlI7kNThxDDZh6mGKZbQsBuRhEbqPCjIsTiPgCVI8hy3Tad+wAAAABJRU5ErkJggg==',
			'A767' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7GB1EQx1CGUNDkMRYAxgaHR0dGkSQxESmMDS6NqCKBbQytLKCaCT3RS1dNW3p1FUrs5DcB1QXwOro0Ipsb2goowNrQ8AUBhTzWBuAYgGoYiINjEDHoIsxAPUPhvCjIsTiPgBMWcw2dBZQpwAAAABJRU5ErkJggg==',
			'812B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7WAMYAhhCGUMdkMREpjAGMDo6OgQgiQW0sgawNgQ6iKCoA+oFigUguW9p1KqoVSszQ7OQ3AdW18qIZh5QbAojinlgsQBGDDsYHVD1Al0SyhoaiOLmgQo/KkIs7gMA4kbIsVnVZioAAAAASUVORK5CYII='        
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