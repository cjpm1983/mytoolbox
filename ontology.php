<?php
/*
* ubicacion ddel plugin en menu admin
*
* @package report_vivo
* @author Carlos Palacios <cjpm1983@gmail.com>
* @copyright  Carlos Palacios 2020
* @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/
require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->dirroot.'/user/profile/lib.php');

$url=new moodle_url('/report/vivo/ontology.php');
$prev= new moodle_url('/report/vivo/index.php');

$systemcontext= context_system::instance();//get_system_context();

$strtitle=get_string('generate_ontology','report_vivo');


$PAGE->set_url($url);
$PAGE->set_context($systemcontext);


    $PAGE->set_title($strtitle);
    $PAGE->set_heading($strtitle);
    $PAGE->set_pagelayout('report');
    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('header', 'report_vivo'));
    
    echo "<a href='".$prev."'  class='btn btn-success' >".get_string('regresar','report_vivo')."</a> "; 
    echo "<br>";

    ?>   
    
    <div class="micontainer">
        <h3 class="encabezado">Esta funcionalidad le permite generar un archivo de ontologia en la
            dirección <?php 
            $ontologia = $CFG->wwwroot.'/static/moodle.owl'; 
            echo "<a target='_blank' href=".$ontologia.">".$ontologia.'</a>';?>

</h3>

<form action="" method="POST">
    <button type="submit" class='btn btn-success'>
        <?php echo get_string('generate_ontology','report_vivo'); ?>
    </button>
    <br><br><br>
    <input type="hidden" name="generar" value="si">
    <input type="checkbox" name="showcode" id="showcode">
    <label for="showcode" style="color: red;">Mostrar salida del script generador. <br>*(Solo para fines inspecci&oacute;n, puede ralentizar considerablemente <br>la recarga de la p&aacute;gina.)</label>
    
</form>


</div>


<?php

if (isset($_REQUEST["generar"])){
    $pmt = " ".$CFG->dbtype." ".$CFG->dbhost." ".$CFG->dbname." ".$CFG->dbuser." ".$CFG->dbpass." ".$CFG->wwwroot." ";
    $resultado = `moodle2owl/moodle2owl$pmt`;
    if (isset($_REQUEST["showcode"])){
        echo '<div class="micontainer">';
        echo "<h4>Salida del script generador:</h4>";
        echo '<textarea name="" id="" cols="50" rows="30">'.$resultado.'</textarea>';
        echo '<div>';
        ;
    }
};





    echo $OUTPUT->footer();


function getFacultad($cid, $ccat){
    global $DB; 
        $category =$DB->get_record('course_categories', array("id" => $ccat));
        // categoryparent
        $parentcatids = explode("/",trim( $category->path, "/"));
        //get the name of the parent category
        $sql = "SELECT id, name FROM {course_categories} WHERE id in (".implode(",", $parentcatids).")"; 
        $parentcatsnames = $DB->get_records_sql($sql);
         //0 = parent or 1 = undercategory
        $headline = $parentcatsnames[$parentcatids[0]];
    return $headline->name;
}

?>
<script src="<?php echo $CFG->dirroot.'/static/moodle.owl'; ?>">

</script>

<style>
    a.btn:visited{
        color: white;
    }
    .micontainer{
        width: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 20px 5px;

    }
    .micontainer textarea{
        color: lime;
        background-color: #343a40;
    }
    .encabezado{
        
    }
    .envio{
        
    }
</style>
<?php