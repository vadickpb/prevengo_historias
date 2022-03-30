var tabla;

//Función que se ejecuta al inicio
function init(){
	mostrarform(false);
	listar();

	$("#formulario").on("submit",function(e)
	{
		guardaryeditar(e);	
	});

	$("#formulariop").on("submit",function(e)
	{
		editarplan(e);	
	});
	$.post("../ajax/atencion.php?op=selectEspecialista", function(r){
	            $("#idempleado").html(r);
	            $('#idempleado').selectpicker('refresh');	
	});
	$.post("../ajax/atencion.php?op=selectServicio", function(r){
	            $("#idservicio").html(r);
	            $('#idservicio').selectpicker('refresh');	
	});
}

//$("#idservicio").change(mostrarValores);

//function mostrarValores()
  //{
    //datosServicio=document.getElementById('idservicio').value.split('_');
    //$("#costo").val(datosServicio[2]);
  //}

//Función limpiar
function limpiar()
{
	$("#idatencion").val("");
	$("#costo").val("");
	$("#idpaciente").val("");
	$("#paciente").val("");
	$("#documento").val("");

}

//Función mostrar formulario
function mostrarform(flag)
{
	limpiar();
	if (flag)
	{
		$("#listadoregistros").hide();
		$("#formularioregistros").show();
		$("#formularioplan").hide();
		$("#btnGuardar").prop("disabled",false);
		$("#btnagregar").hide();
	}
	else
	{
		$("#listadoregistros").show();
		$("#formularioregistros").hide();
		$("#formularioplan").hide();
		$("#btnagregar").show();
	}
}

//Función cancelarform
function cancelarform()
{
	limpiar();
	mostrarform(false);
}

//Función Listar
function listar()
{
	tabla=$('#tbllistado').dataTable(
	{
		"aProcessing": true,//Activamos el procesamiento del datatables
	    "aServerSide": true,//Paginación y filtrado realizados por el servidor
	    dom: 'Bfrtip',//Definimos los elementos del control de tabla
	    buttons: [		          
		            'copyHtml5',
		            'excelHtml5',
		            'csvHtml5',
		            'pdf'
		        ],
		"ajax":
				{
					url: '../ajax/atencion.php?op=listar',
					type : "get",
					dataType : "json",						
					error: function(e){
						console.log(e.responseText);	
					}
				},
		"bDestroy": true,
		"iDisplayLength": 5
	}).DataTable();
}
//Función para guardar o editar

function guardaryeditar(e)
{
	e.preventDefault(); //No se activará la acción predeterminada del evento
	$("#btnGuardar").prop("disabled",true);
	var formData = new FormData($("#formulario")[0]);

	$.ajax({
		url: "../ajax/atencion.php?op=guardaryeditar",
	    type: "POST",
	    data: formData,
	    contentType: false,
	    processData: false,

	    success: function(datos)
	    {                    
	          bootbox.alert(datos);	          
	          mostrarform(false);
	          tabla.ajax.reload();

	          var url="http://www.lawebdelprogramador.com";
	          
	          window.onload=function(){
				abrirEnPestana(url);
			}
	    }

	});
	limpiar();
}

function mostrar(idatencion)
{
	$.post("../ajax/atencion.php?op=mostrar",{idatencion : idatencion}, function(data, status)
	{
		data = JSON.parse(data);		
		mostrarform(true);
		$("#costo").val(data.total);
 		$("#idatencion").val(data.idatencion);
 		$("#idpaciente").val(data.idpersona);
 		$("#paciente").val(data.paciente);
 		$("#idservicio").val(data.idservicio);
 		$("#idservicio").selectpicker('refresh');
 		$("#idempleado").val(data.idempleado);
 		$("#idempleado").selectpicker('refresh');
 		


 	})
}

//Función para desactivar registros
function anular(idatencion)
{
	bootbox.confirm("¿Está Seguro de anular la atención?", function(result){
		if(result)
        {
        	$.post("../ajax/atencion.php?op=anular", {idatencion : idatencion}, function(e){
        		bootbox.alert(e);
	            tabla.ajax.reload();
        	});	
        }
	})
}

function buscarPaciente()
{
	var documento=$("#documento").val();
	$.post("../ajax/persona.php?op=buscar",{documento : documento}, function(data, status)
	{
		$("#idpaciente").val('');
		$("#paciente").val('');

		if (data==null){
			//mostrarform(true);
			$("#idpaciente").val('');
			$("#paciente").val('No existe el paciente');
		}
		else
		{
			data = JSON.parse(data);		
			//mostrarform(true);
			$("#idpaciente").val(data.idpersona);
			$("#paciente").val(data.apaterno + ' ' +data.amaterno + ' ' + data.nombre);
		}
		
 	})
}
function abrirEnPestana(url) {
		$("<a>").attr("href", url).attr("target", "_blank")[0].click();
}




















//Declaración de variables necesarias para trabajar con las compras y
//sus detalles
var impuesto=18;
var cont=0;
var contr=1;
var detalles=0;
var valor=0;
$("#btnGuardarP").hide();

//Función para guardar o editar
function editarplan(e)
{
	idatencion=$("#idatencionp").val();
	e.preventDefault(); //No se activará la acción predeterminada del evento
	//$("#btnGuardarP").prop("disabled",true);
	var formData = new FormData($("#formulariop")[0]);

	$.ajax({
		url: "../ajax/resultado.php?op=guardaryeditar",
	    type: "POST",
	    data: formData,
	    contentType: false,
	    processData: false,

	    success: function(datos)
	    {                    
	          bootbox.alert(datos);	          
	          mostrarform(false);
	          tabla.ajax.reload();
	          window.open("/sis/reportes/historia.php?idatencion=" +idatencion, '_blank');
	          window.open("/sis/reportes/receta.php?idatencion=" +idatencion, '_blank');
	    }

	});
	limpiarPlan();
}

//Función limpiarPlan
function limpiarPlan()
{
	$("#idatencion").val("");
	$("#tiempo_enfermedad").val("");
	$("#antecedentes").val("");
	$("#examen_fisico").val("");
	$("#tratamiento").val("");
	$("#receta").val("");
	$("#proxima_cita").val("");
	$("#motivo_consulta").val("");
	$("#plan").val("");
	$("#texto").val("");
	$("#diagnosticos").html("");
	//$("#estado").val("");
	$(".filas").remove();
	$(".filasr").remove();
	cont=0;
	contr=1;
	detalles=0;
	valor=0;
	$("#btnGuardarP").hide();

	//Agregamos la receta inicial
	var fila='<tr class="filasr" id="filar0">'+
    	'<td><button type="button" class="btn btn-sm btn-danger" onclick="eliminarReceta('+contr+')"><i class="fa fa-trash"></i></button></td>'+
    	'<td><input type="text" class="control" name="medicamento[]" required=""></td>'+
    	'<td><input type="text" class="control" name="presentacion[]"></td>'+
    	'<td><input type="text" class="control" name="dosis[]"></td>'+
    	'<td><input type="text" class="control" name="duracion[]"></td>'+
    	'<td><input type="text" class="control" name="cantidad[]"></td>'+    	
    	'</tr>';
    	$('#recetas').append(fila);
}

function modificar(idatencion)
{
	
	$("#formularioplan").show();
	$("#listadoregistros").hide();
	$("#formularioregistros").hide();
	//$("#btnGuardar").prop("disabled",false);
	$("#btnagregar").hide();

	$.post("../ajax/resultado.php?op=modificar",{idatencion : idatencion}, function(data, status)
	{
		data = JSON.parse(data);

		$("#edad").html(data.edad);
		$("#dni").html(data.num_documento);
		$("#paciente").html(data.paciente);
		$("#especialista").html(data.especialista);
		$("#servicio").html(data.servicio);
 		$("#idatencionp").val(data.idatencion);

 		$("#presion_arterial").val(data.presion_arterial);
 		$("#temperatura").val(data.temperatura);
 		$("#frecuencia_respiratoria").val(data.frecuencia_respiratoria);
 		$("#frecuencia_cardiaca").val(data.frecuencia_cardiaca);

 		$("#saturacion").val(data.saturacion);
 		$("#peso").val(data.peso);
 		$("#talla").val(data.talla);
 		var imc2=Number(data.imc);
 		$("#imc").val(imc2.toFixed(2));

 		$("#idpersona").val(data.idpersona);
 		$("#alergia").val(data.alergia);
 		$("#intervenciones_quirurgicas").val(data.intervenciones_quirurgicas);
 		$("#vacunas_completas").val(data.vacunas_completas);
 		$("#vacunas_completas").selectpicker('refresh');

 		$("#idresultado").val(data.idresultado);
 		$("#motivo_consulta").val(data.motivo_consulta);
 		$("#tiempo_enfermedad").val(data.tiempo_enfermedad);
 		$("#antecedentes").val(data.antecedentes);
 		$("#examen_fisico").val(data.examen_fisico);
 		$("#tratamiento").val(data.tratamiento);
 		$("#proxima_cita").val(data.proxima_cita);
 		$("#plan").val(data.plan);

 		$.post("../ajax/resultado.php?op=detalles&idresultado="+data.idresultado,function(r){
	        $("#tablabody").html(r);
	        $("#btnGuardarP").show();
		});
 	});

 	$.post("../ajax/resultado.php?op=recetas&idatencion="+idatencion,function(r){
	        $("#tablabodyrecetas").html(r);
	        //Agregamos la receta inicial
			var fila='<tr class="filasr" id="filar0">'+
		    	'<td><button type="button" class="btn btn-sm btn-danger" onclick="eliminarReceta(0)"><i class="fa fa-trash"></i></button></td>'+
		    	'<td><input type="text" class="control" name="medicamento[]" required=""></td>'+
		    	'<td><input type="text" class="control" name="presentacion[]"></td>'+
		    	'<td><input type="text" class="control" name="dosis[]"></td>'+
		    	'<td><input type="text" class="control" name="duracion[]"></td>'+
		    	'<td><input type="text" class="control" name="cantidad[]"></td>'+    	
		    	'</tr>';
		    	$('#recetas').append(fila);
		});
}

function buscarDiagnostico()
{
	var texto=$("#texto").val();
	$.post("../ajax/resultado.php?op=diagnosticos&texto="+texto,function(r){
	        $("#diagnosticos").html(r);
	});
}

function agregar(iddiagnostico,enfermedad)
  {
    if (iddiagnostico!="")
    {
    	var fila='<tr class="filas" id="fila'+cont+'">'+
    	'<td><button type="button" class="btn btn-danger" onclick="eliminarDetalle('+cont+')"><i class="fa fa-trash"></i></button></td>'+
    	'<td><select name="tipo[]"><option value="P">P</option><option value="D">D</option><option value="R">R</option></select></td>'+
    	'<td><input type="hidden" name="iddiagnostico[]" value="'+iddiagnostico+'">'+enfermedad+'</td>'+    	
    	'</tr>';
    	cont++;
    	detalles=detalles+1;
    	$('#detalles').append(fila);
    }
    else
    {
    	alert("Error al aplicar el diagnóstico, revise los datos.");
    }
    evaluar();
  }

  function agregarReceta()
  {
    	var fila='<tr class="filasr" id="filar'+contr+'">'+
    	'<td><button type="button" class="btn btn-sm btn-danger" onclick="eliminarReceta('+contr+')"><i class="fa fa-trash"></i></button></td>'+
    	'<td><input type="text" class="control" name="medicamento[]" required=""></td>'+
    	'<td><input type="text" class="control" name="presentacion[]"></td>'+
    	'<td><input type="text" class="control" name="dosis[]"></td>'+
    	'<td><input type="text" class="control" name="duracion[]"></td>'+
    	'<td><input type="text" class="control" name="cantidad[]"></td>'+    	
    	'</tr>';
    	contr++;
    	$('#recetas').append(fila);
  }

  function evaluar(){
  	if (detalles>0)
    {
      $("#btnGuardarP").show();
    }
    else
    {
      $("#btnGuardarP").show(); 
      cont=0;
    }
  }

  function eliminarDetalle(indice){
  	$("#fila" + indice).remove();
  	detalles=detalles-1;
  	evaluar();
  }

function eliminarReceta(indice){
  	$("#filar" + indice).remove();
 }

init();