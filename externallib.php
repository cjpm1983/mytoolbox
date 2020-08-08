<?php

/**
 * External functions backported.
 *
 *
 * @package report_vivo
 * @author Carlos Palacios <cjpm1983@gmail.com>
 * @copyright  Carlos Palacios 2020
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once("$CFG->libdir/externallib.php");
//require_once("$CFG->dirroot/report/vivo/futurelib.php");

class report_vivo_external extends external_api {

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function get_user_from_cfield_parameters() {
        return new external_function_parameters(
            array(
                            'customfield_name' => new external_value(PARAM_RAW, 'Nombre del campo personalizado por el que se va  a hacer la busqueda - debe ser unico e. orcid'),
                            'customfield_value' => new external_value(PARAM_RAW, 'Valor del campo personalizado'),
                             )
        );
    }


    /**
     * Returns description of method result value
     *
     * @return external_single_structure
     * @since Moodle 3.1
     */
    public static function get_user_from_cfield_returns() {
        return new external_single_structure(
            array(
                            'id' => new external_value(PARAM_INT, 'Id del usuario de Moodle'),
                            'name' => new external_value(PARAM_TEXT, 'Nombre'),
                            'lastname' => new external_value(PARAM_TEXT, 'Apellidos'),
                            'email' => new external_value(PARAM_TEXT, 'Correo electronico')
                        
                )
            );
    }


/**
     * @return array de valores del campo usuario
     */
    public static function get_user_from_cfield($customfield_name,$customfield_value) {

        global $CFG, $DB;
        require_once("$CFG->dirroot/group/lib.php");

        $params = self::validate_parameters(
            self::get_user_from_cfield_parameters(), array('customfield_name'=>$customfield_name,'customfield_value'=>$customfield_value)
        );

        $campo = $customfield_name;
        //$campo = 'campo1';
        //die($campo);
    
        $parametros=array('param1'=>$campo) ;
        $registro_user_info_fields = $DB->get_record_sql('SELECT * FROM {user_info_field} WHERE shortname = :param1',$parametros );


        $parametros1=array( 
                            'param1'=>$customfield_value,
                            'param2'=>$registro_user_info_fields->id
                            ) ;


        $registro_user_info_data = $DB->get_record_sql('SELECT * FROM {user_info_data} WHERE data = :param1 AND fieldid=:param2',$parametros1 );
      
        $usuario=@$DB->get_record('user', ['id' => $registro_user_info_data->userid] );

        $result = array(
            'id' => $usuario->id,
            'name' => $usuario->firstname,
            'lastname' => $usuario->lastname ,
            'email' => $usuario->email,

        );
        return $result;
    }

	
	
	
	
	////*************************funciones para obtener los datos especificos requeridos por VIVO de cursos donde un usuario es teacher, dado su id
	
	   /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function get_courses_by_teacher_id_parameters() {
        return new external_function_parameters(
            array('teacher_id' => new external_value(PARAM_RAW, 'Id del profesor del que se desea ver los cursos'))
        );
    }



    /**
     * Returns description of method result value
     *
     * @return external_single_structure
     * @since Moodle 3.1
     */
    public static function get_courses_by_teacher_id_returns() {
        return new external_single_structure(
            array(
                'courses' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'id' => new external_value(PARAM_INT, 'Id del curso de Moodle'),
                            'course_name' => new external_value(PARAM_TEXT, 'Nombre del Curso'),
                            'course_faculty' => new external_value(PARAM_TEXT, 'Facultad'),
                            'start_date' => new external_value(PARAM_INT, 'Timestamp de la fecha de inicio'),
							'end_date' => new external_value(PARAM_INT, 'Timestamp de la fecha de culminacion')
                        
               )
			)
		),
                'warnings' => new external_warnings(),
		)
	);
    }

/**
     * @return array de valores del campo curso
     */
    public static function get_courses_by_teacher_id($teacher_id) {

        global $CFG, $DB;
        require_once("$CFG->dirroot/group/lib.php");

        $params = self::validate_parameters(
            self::get_courses_by_teacher_id_parameters(), array('teacher_id'=>$teacher_id)
        );
        
		
		$usuario=@$DB->get_record('user', ['id' => $teacher_id] );
		
		//sql para obtener los cursos
		$sql="SELECT c.id, c.fullname, c.startdate, c.enddate, u.lastname, r.shortname, ct.path, c.category 
		
		FROM {course} AS c
		JOIN {context} AS ct ON c.id = ct.instanceid
		JOIN {role_assignments} AS ra ON ra.contextid = ct.id
		JOIN {user} AS u ON u.id = ra.userid
		JOIN {role} AS r ON r.id = ra.roleid
		WHERE r.shortname = 'editingteacher' AND u.id = ?
		";
        
		
		@$cursosCrudo = $DB->get_records_sql($sql, array($usuario->id)); 
		
		$cursos=array();
		
		foreach ($cursosCrudo as $curso){
			        $cursos[] = array(
						'id' => $curso->id,
						'course_name' => $curso->fullname,
						'course_faculty' => self::getFacultad($curso->id,$curso->category),
						'start_date' => $curso->startdate,
						'end_date' => $curso->enddate
					);
		}
		
		$result = array(
			'courses' => $cursos
		);

		
        return $result;
    }
	
	private function getFacultad($cid,$ccat){
			global $DB; 
				$category =$DB->get_record('course_categories', array("id" => $ccat));
				// categoryparent
				$parentcatids = explode("/",trim( $category->path, "/"));
				//get the name of the parent category
				$sql = "SELECT id, name FROM {course_categories} WHERE id in (".implode(",", $parentcatids).")"; $parentcatsnames = $DB->get_records_sql($sql);
				 //0 = parent or 1 = undercategory
				$headline = $parentcatsnames[$parentcatids[0]];
			return $headline->name;
		}




////*************************funciones para obtener cursos huerfanos
	
	   /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function get_courses_orphans_parameters() {
        return new external_function_parameters(
            array('orphan' => new external_value(PARAM_TEXT, 'valor por defecto',VALUE_DEFAULT,'nada'))
        );
    }



    /**
     * Returns description of method result value
     *
     * @return external_single_structure
     * @since Moodle 3.1
     */
    public static function get_courses_orphans_returns() {
        return new external_single_structure(
            array(
                'courses' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'id' => new external_value(PARAM_INT, 'Id del curso de Moodle'),
                            'course_name' => new external_value(PARAM_TEXT, 'Nombre del Curso'),
                            'course_faculty' => new external_value(PARAM_TEXT, 'Facultad'),
                            'start_date' => new external_value(PARAM_INT, 'Timestamp de la fecha de inicio'),
							'end_date' => new external_value(PARAM_INT, 'Timestamp de la fecha de culminacion')
                        
               )
			)
		),
                'warnings' => new external_warnings(),
		)
	);
    }

/**
     * @return array de valores del campo curso
     */
    public static function get_courses_orphans($orphan) {

        global $CFG, $DB;
        require_once("$CFG->dirroot/group/lib.php");

        $params = self::validate_parameters(
            self::get_courses_orphans_parameters(), array('orphan'=>$orphan)
        );

        $sql=" SELECT c.id , c.fullname, c.category, c.startdate, c.enddate
        FROM {course} as c
        WHERE c.id NOT IN
        (SELECT c.id
        from {user} AS u, {role_assignments} AS ra ,{context} AS ct, {role} AS r
        WHERE ct.contextlevel = 50 
        AND ra.roleid = r.id
        AND (r.shortname = 'editingteacher' OR r.shortname = 'teacher' OR r.shortname = 'manager')
        AND ra.contextid = ct.id 
        AND c.id = ct.instanceid
        AND u.id = ra.userid)

        AND c.category != 0
        ";
        //c.category != 0 es necesario porque sino carga el nonmbre del sitio como un curso

        $cursosCrudo = $DB->get_records_sql($sql); 

		
		$cursos=array();
		
		foreach ($cursosCrudo as $curso){
			        $cursos[] = array(
						'id' => $curso->id,
						'course_name' => $curso->fullname,
						'course_faculty' => self::getFacultad($curso->id,$curso->category),
						'start_date' => $curso->startdate,
						'end_date' => $curso->enddate
					);
		}
		
		$result = array(
			'courses' => $cursos
		);

		
        return $result;
    }

}