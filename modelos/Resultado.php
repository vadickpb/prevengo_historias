<?php 
//Incluímos inicialmente la conexión a la base de datos
require "../config/Conexion.php";

Class Resultado
{
	//Implementamos nuestro constructor
	public function __construct()
	{

	}

	//Implementamos un método para insertar registros
	public function insertar($idatencion,$motivo_consulta,$tiempo_enfermedad,$antecedentes,$examen_fisico,$tratamiento,$proxima_cita,$iddiagnostico,$tipo,$plan,$alergia,$intervenciones_quirurgicas,$vacunas_completas,$idpersona,$medicamento,$presentacion,$dosis,$duracion,$cantidad,$presion_arterial,$temperatura,$frecuencia_respiratoria,$frecuencia_cardiaca,$saturacion,$peso,$talla,$imc)
	{

		//Actualizamos el estado de la atención
		$sql2="UPDATE atencion set estado='Atendido' WHERE idatencion='$idatencion'";
		ejecutarConsulta($sql2);

		//Insertamos el resultado
		$sql="INSERT INTO resultado (idatencion,motivo_consulta,tiempo_enfermedad,antecedentes,examen_fisico,tratamiento,proxima_cita,estado,plan,fecha,hora)
		VALUES ('$idatencion','$motivo_consulta','$tiempo_enfermedad','$antecedentes','$examen_fisico','$tratamiento','$proxima_cita','Atendido','$plan',curdate(),curtime())";
		$idresultadonew=ejecutarConsulta_retornarID($sql);

		//Insertamos los diagnósticos
		$num_elementos=0;
		$sw=true;

		while ($num_elementos < count($iddiagnostico))
		{
			$sql_detalle = "INSERT INTO detalle_diagnostico(idresultado,iddiagnostico,tipo) VALUES('$idresultadonew','$iddiagnostico[$num_elementos]','$tipo[$num_elementos]')";
			ejecutarConsulta($sql_detalle) or $sw = false;
			$num_elementos=$num_elementos + 1;
		}

		//Insertamos las recetas
		$num_elementos2=0;

		while ($num_elementos2 < count($medicamento))
		{
			$sql_recetas = "INSERT INTO receta(idatencion,medicamento,presentacion,dosis,duracion,cantidad) VALUES('$idatencion','$medicamento[$num_elementos2]','$presentacion[$num_elementos2]','$dosis[$num_elementos2]','$duracion[$num_elementos2]','$cantidad[$num_elementos2]')";
			ejecutarConsulta($sql_recetas) or $sw = false;
			$num_elementos2=$num_elementos2 + 1;
		}

		//Actualizamos el paciente
		$sql3="UPDATE persona set alergia='$alergia',intervenciones_quirurgicas='$intervenciones_quirurgicas',vacunas_completas='$vacunas_completas' WHERE idpersona='$idpersona'";
		ejecutarConsulta($sql3);

		//Actualizamos el triaje
		$sql4="UPDATE triaje set presion_arterial='$presion_arterial',temperatura='$temperatura',frecuencia_respiratoria='$frecuencia_respiratoria',frecuencia_cardiaca='$frecuencia_cardiaca',saturacion='$saturacion',peso='$peso',talla='$talla',imc='$imc' WHERE idatencion='$idatencion'";
		ejecutarConsulta($sql4);

		return $sw;
	}

	//Implementamos un método para editar registros
	public function editar($idatencion,$idresultado,$motivo_consulta,$tiempo_enfermedad,$antecedentes,$examen_fisico,$tratamiento,$proxima_cita,$iddiagnostico,$tipo,$plan,$alergia,$intervenciones_quirurgicas,$vacunas_completas,$idpersona,$medicamento,$presentacion,$dosis,$duracion,$cantidad,$presion_arterial,$temperatura,$frecuencia_respiratoria,$frecuencia_cardiaca,$saturacion,$peso,$talla,$imc)
	{

		//Actualizamos el estado de la atención
		//$sql2="UPDATE atencion set estado='Atendido' WHERE idatencion='$idatencion'";
		//ejecutarConsulta($sql2);

		//actualizamos el resultado
		$sql="UPDATE resultado  set motivo_consulta='$motivo_consulta', tiempo_enfermedad='$tiempo_enfermedad', antecedentes='$antecedentes', examen_fisico='$examen_fisico', tratamiento='$tratamiento', proxima_cita='$proxima_cita', plan='$plan',fecha=curdate(),hora=curtime() WHERE idresultado='$idresultado'";
		ejecutarConsulta($sql);

		//Antes de insertar los diagnósticos, eliminamos los antiguos
		$sqldd="DELETE FROM detalle_diagnostico WHERE idresultado='$idresultado'";
		ejecutarConsulta($sqldd);

		//Insertamos los diagnósticos
		$num_elementos=0;
		$sw=true;

		while ($num_elementos < count($iddiagnostico))
		{
			$sql_detalle = "INSERT INTO detalle_diagnostico(idresultado,iddiagnostico,tipo) VALUES('$idresultado','$iddiagnostico[$num_elementos]','$tipo[$num_elementos]')";
			ejecutarConsulta($sql_detalle) or $sw = false;
			$num_elementos=$num_elementos + 1;
		}

		//Antes de insertar la receta, eliminamos las recetas antiguas
		$sqldr="DELETE FROM receta WHERE idatencion='$idatencion'";
		ejecutarConsulta($sqldr);

		//Insertamos las recetas
		$num_elementos2=0;

		while ($num_elementos2 < count($medicamento))
		{
			$sql_recetas = "INSERT INTO receta(idatencion,medicamento,presentacion,dosis,duracion,cantidad) VALUES('$idatencion','$medicamento[$num_elementos2]','$presentacion[$num_elementos2]','$dosis[$num_elementos2]','$duracion[$num_elementos2]','$cantidad[$num_elementos2]')";
			ejecutarConsulta($sql_recetas) or $sw = false;
			$num_elementos2=$num_elementos2 + 1;
		}

		//Actualizamos el paciente
		$sql3="UPDATE persona set alergia='$alergia',intervenciones_quirurgicas='$intervenciones_quirurgicas',vacunas_completas='$vacunas_completas' WHERE idpersona='$idpersona'";
		ejecutarConsulta($sql3);

		//Actualizamos el triaje
		$sql4="UPDATE triaje set presion_arterial='$presion_arterial',temperatura='$temperatura',frecuencia_respiratoria='$frecuencia_respiratoria',frecuencia_cardiaca='$frecuencia_cardiaca',saturacion='$saturacion',peso='$peso',talla='$talla',imc='$imc' WHERE idatencion='$idatencion'";
		ejecutarConsulta($sql4);

		return $sw;
	}


	//Implementar un método para mostrar los datos de un registro a modificar
	public function mostrar($idatencion)
	{
		$sql="SELECT a.idatencion,a.idpersona,concat(p.apaterno,' ',p.amaterno,' ',p.nombre) as paciente,CONCAT((YEAR( CURDATE( ) ) - YEAR( fecha_nacimiento ) - IF( MONTH( CURDATE( ) ) < MONTH( fecha_nacimiento), 1, 
IF ( MONTH(CURDATE( )) = MONTH(fecha_nacimiento),IF (DAY( CURDATE( ) ) < DAY( fecha_nacimiento ),1,0 ),0))),' años, ',(MONTH(CURDATE()) - MONTH( fecha_nacimiento) + 12 * IF( MONTH(CURDATE())<MONTH(fecha_nacimiento), 1,IF(MONTH(CURDATE())=MONTH(fecha_nacimiento),IF (DAY(CURDATE())<DAY(fecha_nacimiento),1,0),0)) - IF(MONTH(CURDATE())<>MONTH(fecha_nacimiento),(DAY(CURDATE())<DAY(fecha_nacimiento)), IF (DAY(CURDATE())<DAY(fecha_nacimiento),1,0 ))),' meses, ',(DAY( CURDATE( ) ) - DAY( fecha_nacimiento ) +30 * ( DAY(CURDATE( )) < DAY(fecha_nacimiento) )),' días') as edad,p.num_documento,p.alergia,p.intervenciones_quirurgicas,p.vacunas_completas,a.fecha,(select concat(apaterno,' ',amaterno,' ',nombre) from persona where idpersona=a.idempleado) as especialista,s.nombre as servicio,t.presion_arterial,t.temperatura,t.frecuencia_respiratoria,t.frecuencia_cardiaca,t.saturacion,t.peso,t.talla,t.imc FROM atencion a INNER JOIN persona p ON a.idpersona=p.idpersona inner join servicio s on a.idservicio=s.idservicio INNER JOIN triaje t ON a.idatencion=t.idatencion WHERE a.idatencion='$idatencion'";
		return ejecutarConsultaSimpleFila($sql);
	}

	//Implementar un método para mostrar los datos de la atención que se va a modificar
	public function modificar($idatencion)
	{
		$sql="SELECT a.idatencion,a.idpersona,concat(p.apaterno,' ',p.amaterno,' ',p.nombre) as paciente,CONCAT((YEAR( CURDATE( ) ) - YEAR( fecha_nacimiento ) - IF( MONTH( CURDATE( ) ) < MONTH( fecha_nacimiento), 1, 
IF ( MONTH(CURDATE( )) = MONTH(fecha_nacimiento),IF (DAY( CURDATE( ) ) < DAY( fecha_nacimiento ),1,0 ),0))),' años, ',(MONTH(CURDATE()) - MONTH( fecha_nacimiento) + 12 * IF( MONTH(CURDATE())<MONTH(fecha_nacimiento), 1,IF(MONTH(CURDATE())=MONTH(fecha_nacimiento),IF (DAY(CURDATE())<DAY(fecha_nacimiento),1,0),0)) - IF(MONTH(CURDATE())<>MONTH(fecha_nacimiento),(DAY(CURDATE())<DAY(fecha_nacimiento)), IF (DAY(CURDATE())<DAY(fecha_nacimiento),1,0 ))),' meses, ',(DAY( CURDATE( ) ) - DAY( fecha_nacimiento ) +30 * ( DAY(CURDATE( )) < DAY(fecha_nacimiento) )),' días') as edad,p.num_documento,p.alergia,p.intervenciones_quirurgicas,p.vacunas_completas,a.fecha,(select concat(apaterno,' ',amaterno,' ',nombre) from persona where idpersona=a.idempleado) as especialista,s.nombre as servicio,t.presion_arterial,t.temperatura,t.frecuencia_respiratoria,t.frecuencia_cardiaca,t.saturacion,t.peso,t.talla,t.imc,r.motivo_consulta,r.idresultado,r.tiempo_enfermedad,r.antecedentes,r.examen_fisico,r.tratamiento,r.proxima_cita,r.plan FROM atencion a INNER JOIN persona p ON a.idpersona=p.idpersona inner join servicio s on a.idservicio=s.idservicio INNER JOIN triaje t ON a.idatencion=t.idatencion INNER JOIN resultado r ON a.idatencion=r.idatencion WHERE a.idatencion='$idatencion'";
		return ejecutarConsultaSimpleFila($sql);
	}

	//Implementar un método para listar las recetas
	public function listarRecetas($idatencion)
	{
		$sql="SELECT * FROM receta WHERE idatencion='$idatencion'";
		return ejecutarConsulta($sql);		
	}

	//Implementar un método para listar los diagnosticos
	public function listarDetalles($idresultado)
	{
		$sql="SELECT d.iddiagnostico,concat(d.codigo,'-',d.enfermedad) as nenfermedad,dd.tipo FROM detalle_diagnostico dd INNER JOIN diagnostico d ON d.iddiagnostico=dd.iddiagnostico WHERE idresultado='$idresultado'";
		return ejecutarConsulta($sql);		
	}

}

?>