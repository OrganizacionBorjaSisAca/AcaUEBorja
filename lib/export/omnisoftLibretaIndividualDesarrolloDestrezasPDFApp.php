<?php

require('omnisoftPDFIndividualLibretaDesarrolloDestrezas.php');

// require('omnisoftPDFlibreta1.php');
require('../adodb/adodb.inc.php');
require('../../config/config.inc.php');
require('../server/funciones.php');

$serial_per=$_GET['serial_per'];
//$serial_alu=$_GET['serial_alu'];
$nombre_per=$_GET['nombre_per'];
$serial_par=$_GET['serial_par1'];
$nombre_par=$_GET['nombre_par1'];
$serial_sec=$_GET['serial_sec'];
global $DBConnection;

$dblink = NewADOConnection($DBConnection);
if (!$dblink) die("Error Fatal: NO SE PUEDE CONECTAR A LA BASE DE DATOS DEL SERVIDOR");

$sqlCmd='select serial_tri,nombre_tri,abreviatura_tri from trimestre where desplegar_tri="SI" and serial_per='.$serial_per." and serial_sec=".$serial_sec." order by fecini_tri";
$rsTrimestre=$dblink->Execute($sqlCmd);


while (!$rsTrimestre->EOF ) {
     //  echo $rsTrimestre->fields[1]." ";
       $rsParcial=$dblink->Execute("select serial_prc,abreviatura_prc, tipo_prc, formula_prc from parcial where desplegable_prc='SI' and serial_tri=".$rsTrimestre->fields[serial_tri]." and activo_prc='SI' order by orden_prc");
       //   echo $rsParcial->RecordCount()." ";
        $rsTrimestre->MoveNext();
        $numeroParciales+=($rsParcial->RecordCount());
   }
//     echo $numeroParciales;

//          $serial_perNewRegimen=4;
   $newespacio=3;
    if ($numeroParciales<=16){
         $printOBJ=new OmnisoftPDFIndividual($imagePath."/logo.jpg",$omnisoftNombreEmpresa,' ','ACADEMIUM','Helvetica',13,0xf,0x0,35,OMNISOFT_VERTICAL,210,297);
        }
    else if ($numeroParciales>=17 and $numeroParciales<=20) {
          $printOBJ=new OmnisoftPDFIndividual($imagePath."/logo.jpg",$omnisoftNombreEmpresa,' ','ACADEMIUM','Helvetica',13,0xf,0x0,35,OMNISOFT_HORIZONTAL,210,297);
         }
    else {
          $margender=20;
          $yalto=60+$numeroParciales*(8+$newespacio)+$margender;   //8: ancho de c/columna por parcial, 60: empieza bloque de calificaciones
          $printOBJ=new OmnisoftPDFIndividual($imagePath."/logo.jpg",$omnisoftNombreEmpresa,' ','ACADEMIUM','Helvetica',13,0xf,0x0,35,OMNISOFT_HORIZONTAL,210,$yalto);
         }

    $sqlCmd='select distinct alumno.serial_alu, alumno.apellido_alu, alumno.nombre_alu, numeromatricula_paralu, nombre_niv, nombre_par,nombre_per,nivel.serial_sec,observacion_paralu from periodo,nivel, paralelo, alumno,materia_alumno,materia_profesor,paralelo_alumno  where   periodo.serial_per=paralelo_alumno.serial_per and nivel.serial_niv=paralelo.serial_niv and paralelo.serial_par=paralelo_alumno.serial_par and materia_profesor.serial_matpro=materia_alumno.serial_matpro and  alumno.serial_alu=materia_alumno.serial_alu and paralelo_alumno.serial_alu=alumno.serial_alu and paralelo_alumno.serial_per='.$serial_per.' and paralelo_alumno.serial_par='.$serial_par."  and estado_alu='ACTIVO' order by apellido_alu,nombre_alu";
    $rsAlumno=$dblink->Execute($sqlCmd);
$vicc=11; $vicc2=55;
$numeroalumno=1;
//$posYY=32;
$posYY=40;
$posYYY=27;
$cuantitativa=false;
while (!$rsAlumno->EOF ){
  $serial_alu=$rsAlumno->fields[0];
  $serial_sec=$rsAlumno->fields[7];

  $sqlCmd="select materia.serial_mat,nombre_mat,materia_alumno.serial_matpro,global_mat,tipo_mat,codigo_mat,promedia_mat from nivel,paralelo,materia,materia_alumno,materia_nivel, materia_profesor where   (despliegue_mat='LIBRETAS' or despliegue_mat='TODO') and nivel.serial_niv=paralelo.serial_niv and paralelo.serial_niv=materia_nivel.serial_niv and paralelo.serial_par=materia_profesor.serial_par and materia.serial_mat=materia_nivel.serial_mat and materia_nivel.serial_matniv=materia_profesor.serial_matniv  and materia_profesor.serial_matpro=materia_alumno.serial_matpro   and materia_alumno.serial_alu=".$serial_alu.' and materia_profesor.serial_par='.$serial_par.' order by orden_matniv';
  //echo $sqlCmd;
  $rsMateria=$dblink->Execute($sqlCmd);

  $salumno=($rsAlumno->fields[1])." ".($rsAlumno->fields[2]);

 // $printOBJ->addColumn('',17,utf8_encode('REPORTE DE EVALUACION ').$rsAlumno->fields[6],45,1-$posYY+7,"string","center",'Helvetica','12',true,false,0,237,28,36);
 // $printOBJ->addColumn('No. '.$numeroalumno,17,'',10,1-$posYY+7,"string","center",'Helvetica','10',true,false,0,237,28,36);
  $printOBJ->addColumn(' ',15,"../../fotos/LOGOINSTITUCION.JPG",10,-45,"image");
  $printOBJ->addColumn(' ',150,'INFORME QUIMESTRAL DE DESARROLLO Y APRENDIZAJE',50+20,-41,"string","center",'Helvetica','10',true,false,0,0,0,128);
  $cursogrado=$nombre_par;
  $printOBJ->addColumn('',15,$cursogrado,85+20,-37,"string","center",'Helvetica','10',true,false,0,0,0,128);
  $printOBJ->addColumn(utf8_decode($rsAlumno->fields[6]),150,' ',75+20,-34,"string","center",'Helvetica','10',true,false,0,0,0,128);


  $printOBJ->addColumn('No. '.$numeroalumno.' ESTUDIANTE: ',17,$salumno,5,1-$posYY+14,"string","center",'Helvetica','10',true,false,0,0,0,128);
//$printOBJ->addColumn(' ',150,$cursogrado,5,1-$posYY+18,"string","center",'Helvetica','10',true,false,0,0,0,128);
  $sqlCmd='select serial_tri,nombre_tri,abreviatura_tri from trimestre where serial_per='.$serial_per." and serial_sec=".$serial_sec." order by fecini_tri";
//echo $sqlCmd;
  $rsTrimestre=$dblink->Execute($sqlCmd);
  
  
 
$rsParcial=$dblink->Execute("select serial_prc,abreviatura_prc, tipo_prc, formula_prc,desplegable_prc,formato_prc from parcial where desplegable_prc='SI' and serial_tri=".$rsTrimestre->fields[0]." and activo_prc='SI' order by orden_prc");
      
	    if ($rsParcial->fields['abreviatura_prc']='Q1')
			$printOBJ->addColumn('PRIMER QUIMESTRE',4,'',110,-30,"string","center",'helvetica','8',true,false,0,0,0,128); 
		else
		    $printOBJ->addColumn('SEGUNDO QUIMESTRE',4,'',110,-30,"string","center",'helvetica','8',true,false,0,0,0,128);
  

  $posY=12-$posYYY-5;
  $posX=120;
  $posX1=120;
  $i=0;
  $anotas=Array();

  $posY+=2;
  $posYIni=$posY+1-10;
//  $posY+=2;
  $aPosYTrimestre=Array();

  $nt=0;
  $ntri=0;

  while (!$rsTrimestre->EOF ) {
  $ntv=0;
     $i++;
     $posX=$posX1;
     $aPosYTrimestre[$ntri++]=$posX;

//               echo "select serial_prc,abreviatura_prc, tipo_prc, formula_prc from parcial where serial_tri=".$rsTrimestre->fields[serial_tri]." and activo_prc='SI' order by orden_prc";

     $rsParcial=$dblink->Execute("select serial_prc,abreviatura_prc, tipo_prc, formula_prc from parcial where desplegable_prc='SI' and serial_tri=".$rsTrimestre->fields[serial_tri]." and activo_prc='SI' order by orden_prc");

     while (!$rsParcial->EOF ) {
    		//$printOBJ->addColumn($rsParcial->fields['abreviatura_prc'],20,'',$posX1,$posY-2,"string","center",'Helvetica','7',true);
    		$posX1+=(8+$newespacio);
    		$anotas[$nt]=Array(0,0,0,0,0,0,0);
   		$rsParcial->MoveNext();
                if ($rsParcial->EOF)
                        $anotas[$nt][4]=1;
                $nt++;
    	        $ntv++;
            	}


    $posXaux=$posX+3; //(strlen($rsTrimestre->fields[abreviatura_tri]))/2;
    //echo $rsTrimestre->fields[abreviatura_tri]."=".$posX1."<br>";
    if($ntv==0)
         $printOBJ->addColumn(' ',20,'',$posXaux-3,$posY+2,"string","center",'Helvetica','7',true);
    else  {
       $strimestre=explode(" ",$rsTrimestre->fields['nombre_tri']);
//2016       $printOBJ->addColumn($rsTrimestre->fields['nombre_tri'],20,'',$posXaux-3,$posY-8,"string","center",'Helvetica','8',true,false,0,0,0,128);

    }
    	$rsTrimestre->MoveNext();
    }

    $posXFin=$posX1+3+40-3;  //2016
    $printOBJ->addColumn(' ',$posXFin-5+$vicc,0.2,5,$posYIni,"line","center",'Helvetica','5',true);

    $posY+=4;

    $posX=60;
    $row=0;
    $nlineas=0;

			/////
			    //$printOBJ->addColumn(' ',$posYFin-$posYIni+65,0.1,$posXFin,$posYIni,"linev","center",'helvetica','5',true);     ///////////////////
	//		    $printOBJ->addColumn(' ',$posYIni+280,0.1,$posXFin,$posYIni,"linev","center",'helvetica','5',true);     ///////////////////



    while (!$rsMateria->EOF ) {
	$posX1=120;
        $nt=0;
        if (substr($rsMateria->fields['codigo_mat'],0,4)!='COND') {
                if($rsMateria->fields['tipo_mat']!='TERMINAL') {   //victor2017
//     	             $nombreeje=wordwrap($rsMateria->fields[1],70,'¬');
       	             $nombreeje=wordwrap($rsMateria->fields[1],120,'¬');
                     $snombreeje=explode('¬',$nombreeje);


                        if ($posY>221) {
                     		$printOBJ->addColumn(' ',$posXFin-5+$vicc,0.2,5,$posY+4,"line","center",'Helvetica','5',false);


    $rsTrimestre->MoveFirst();
    $posXlineasParcial=120+8+$newespacio;
    while (!$rsTrimestre->EOF ) {
      $contParciales=1;
      $rsParcial=$dblink->Execute("select serial_prc,abreviatura_prc, tipo_prc, formula_prc,desplegable_prc,formato_prc from parcial where desplegable_prc='SI' and serial_tri=".$rsTrimestre->fields[0]." and activo_prc='SI' order by orden_prc");
      while (!$rsParcial->EOF ) {
         //$printOBJ->addColumn(' ',$posYFin-$posYIni+7-9,0.2,$posXlineasParcial,$posYIni+9,"linev","center",'helvetica','5',false);
  //2016                       $printOBJ->addColumn(' ',4,$rsParcial->fields['abreviatura_prc'],$posXlineasParcial-$contParciales*8-1,$posYIni+8,"string","center",'helvetica','7',true);
                         $printOBJ->addColumn(' ',4,' ',$posXlineasParcial-$contParciales*8,$posYIni+4,"string","center",'helvetica','5',true);
           //$printOBJ->addColumn(' ',$posYFin-$posYIni+7-4,0.1,$posXlineasParcial,$posYIni+4,"linev","center",'helvetica','5',true);   //linev

        $posXlineasParcial+=(8+$newespacio);

        $rsParcial->MoveNext();
      }


      $rsTrimestre->MoveNext();
    }

		  //
  	//	     $printOBJ->addColumn('a1 ',$posYFin-$posYIni+226,0.1,5,$posYIni,"linev","center",'helvetica','5',true);  //2016  linea1 //victor2017



//2016	             $printOBJ->addColumn(' ',$posYFin-$posYIni+226,0.1,$posXFin,$posYIni,"linev","center",'helvetica','5',true);




	    $posY-=4;
 //   $printOBJ->addColumn(' ',$posXFin-5,0.1,5,$posY+2,"line","center",'Helvetica','5',true);

    $posYFin=$posY;

    $posY+=6;
    //$printOBJ->addColumn(' ',$posYFin-$posYIni+7-9,0.1,5,$posYIni-9,"linev","center",'helvetica','5',true);                   //5 //////////////////////


	////////////

   $rsTrimestre->MoveFirst();
    $posXlineasParcial=120+8+$newespacio+40;   //2016 aqui ampliar espacio

//  $printOBJ->addColumn('b ',$posYFin-$posYIni+7-9,0.2,$posXlineasParcial-8-$newespacio,$posYIni+9,"linev","center",'helvetica','5',false);  //2016 linea2 p1-4 //victor2017

  while (!$rsTrimestre->EOF ) {
      $contParciales=1;
      $rsParcial=$dblink->Execute("select serial_prc,abreviatura_prc, tipo_prc, formula_prc,desplegable_prc,formato_prc from parcial where desplegable_prc='SI' and serial_tri=".$rsTrimestre->fields[0]." and activo_prc='SI' order by orden_prc");
      while (!$rsParcial->EOF ) {
         $printOBJ->addColumn(' ',$posYFin-$posYIni+7-9,0.2,$posXlineasParcial,$posYIni+9,"linev","center",'helvetica','5',false);
            //             $printOBJ->addColumn(' ',4,$rsParcial->fields['abreviatura_prc'],$posXlineasParcial-$contParciales*8-1+$vicc,$posYIni+8,"string","center",'helvetica','7',true);
			
                         $printOBJ->addColumn(' ',4,' ',$posXlineasParcial-$contParciales*8,$posYIni+4,"string","center",'helvetica','5',true);
           //$printOBJ->addColumn(' ',$posYFin-$posYIni+7-4,0.1,$posXlineasParcial,$posYIni+4,"linev","center",'helvetica','5',true);   //linev
        $posXlineasParcial+=(8+$newespacio);

        $rsParcial->MoveNext();
      }
      $rsTrimestre->MoveNext();
    }


                           $printOBJ->addColumn(' ',$posYFin-$posYIni+8,0.1,5,$posYIni,"linev","center",'helvetica','5',true);  //2016  linea1
			   $printOBJ->addColumn(' ',$posYFin-$posYIni+8,0.1,120+40+$vicc,$posYIni,"linev","center",'helvetica','5',true);  //2016 linea2 p1-4
	 		   $printOBJ->addColumn(' ',$posYFin-$posYIni+8,0.1,153+29,$posYIni,"linev","center",'helvetica','5',true);  //2016 linea5 p1-4




                            $printOBJ->addColumn('',30,' ',11,$posY,"string","center",'Helvetica','7',true,true,0,0,0,128);
                            $posY=12-$posYYY+1;
 //                          $posXFin=$posX1+3;
                            $printOBJ->addColumn(' ',$posXFin-5+$vicc,0.2,5,$posYIni,"line","center",'Helvetica','5',true);


//						     $printOBJ->addColumn('',17,utf8_encode('REPORTE DE EVALUACION ').$rsAlumno->fields[6],45,1-$posYY+7,"string","center",'Helvetica','12',true,false,0,237,28,36);
 // $printOBJ->addColumn('No. '.$numeroalumno,17,'',10,1-$posYY+7,"string","center",'Helvetica','10',true,false,0,237,28,36);

  $cursogrado=$nombre_par;
  $printOBJ->addColumn('No. '.$numeroalumno.' ESTUDIANTE: ',17,$salumno,5,1-$posYY+14,"string","center",'Helvetica','10',true,false,0,0,0,128);
//  $printOBJ->addColumn(' ',150,$cursogrado,5,1-$posYY+18,"string","center",'Helvetica','10',true,false,0,0,0,128);  //victor2017

//  $printOBJ->addColumn(' ',$posXFin-5,0.5,5,$posY-2,"line","center",'Helvetica','5',false);   //victor2017


     $rsTrimestre->MoveFirst();
     $i=0;
	 while (!$rsTrimestre->EOF){

//2016		 $printOBJ->addColumn($rsTrimestre->fields['nombre_tri'],20,'',$aPosYTrimestre[$i],$posYIni+1,"string","center",'Helvetica','8',true,false,0,0,0,128);

		 $i++;
		 $rsTrimestre->MoveNext();
	 }


                        }
                     $printOBJ->addColumn(' ',$posXFin-5+$vicc,0.1,5,$posY,"line","center",'Helvetica','5',false);

                   $posY+=1;

//                   if(substr($snombreeje[0],3,3)=='EJE' or substr($snombreeje[0],4,3)=='EJE')
                     if(substr($snombreeje[0],0,2)=='1.' or substr($snombreeje[0],0,2)=='2.' or substr($snombreeje[0],0,2)=='3.' or substr($snombreeje[0],0,2)=='4.' or substr($snombreeje[0],0,2)=='5.' or substr($snombreeje[0],0,2)=='6.' or substr($snombreeje[0],0,2)=='7.' or substr($snombreeje[0],0,2)=='8.' or substr($snombreeje[0],0,2)=='9.')
                     
                                        $printOBJ->addColumn('',30,$snombreeje[0],11,$posY,"string","center",'Helvetica','9',true,false,0,237,28,36);
				     else {
					 if(substr($snombreeje[0],0,5)=='     ')
				            $printOBJ->addColumn('',30,$snombreeje[0],11,$posY,"string","center",'Helvetica','8',true,false,0,237,28,36);		 
                     else                      
                       $printOBJ->addColumn('',30,$snombreeje[0],11,$posY,"string","center",'Helvetica','6',true,false,0,0,0,128);
					   
					 }
                     if (count($snombreeje)>1) {
                         $posY+=4;
                     $printOBJ->addColumn('',30,$snombreeje[1],11,$posY,"string","center",'Helvetica','6',true,false,0,0,0,128);
                     }

                  }
       	          else  {
        	         $row++;
                         $printOBJ->addColumn(' ',5,$row,5,$posY+1,"number","right",'Helvetica','8',true,false,0,237,28,36);
                         if($rsMateria->fields['promedia_mat']=='SI')  {
                                      //  $printOBJ->addColumn('.',3,'',8,$posY,"number","right",'Helvetica','5',true);
                                        $printOBJ->addColumn('*',3,'',57.7,$posY,"number","right",'Helvetica','9',true,false,0,237,28,36);
                                       }
                         $nombreeje=wordwrap($rsMateria->fields[1],40,'¬');
                         $snombreeje=explode('¬',$nombreeje);
                         //  var_dump($nombreeje);
                         $printOBJ->addColumn(' ',$posXFin-5,0.5,5,$posY,"line","center",'Helvetica','5',false);
                         $posY+=1;
    	                 $printOBJ->addColumn('EJE DE APRENDIZAJE:',64,$snombreeje[0],8,$posY,"string","center",'Helvetica','8',true,false,0,237,28,36);
                         $posY+=4;
    	                 $printOBJ->addColumn(' ',64,$snombreeje[1],10,$posY,"string","center",'Helvetica','8',true,false,0,237,28,36);

   	                }
    	    }
    	$rsTrimestre=$dblink->Execute('select serial_tri,nombre_tri,insuficiencia_sec from seccion,trimestre where desplegar_tri="SI" and seccion.serial_sec=trimestre.serial_sec and serial_per='.$serial_per." and trimestre.serial_sec=".$serial_sec." order by fecini_tri");


		while (!$rsTrimestre->EOF ) {
    		$notatrimestre=0;
    		$nparciales=0;
    		$rsParcial=$dblink->Execute("select serial_prc,abreviatura_prc, tipo_prc, formula_prc,desplegable_prc,formato_prc from parcial where desplegable_prc='SI' and serial_tri=".$rsTrimestre->fields[0]." and activo_prc='SI' order by orden_prc");
		while (!$rsParcial->EOF ) {
                       $formato='%'.$rsParcial->fields['formato_prc']."f";
                       $nparciales++;
    		       $rsNotaParcial=$dblink->Execute('select serial_prccri,nota_prccri,criterio.serial_cri from criterio,alumno,materia_alumno,materia_nivel,materia_profesor,nota_trimestre,nota_parcial,parcial_criterio where criterio.serial_matniv=materia_profesor.serial_matniv and materia_nivel.serial_matniv=materia_profesor.serial_matniv and materia_profesor.serial_matpro=materia_alumno.serial_matpro and parcial_criterio.serial_nprc=nota_parcial.serial_nprc and parcial_criterio.serial_cri=criterio.serial_cri and nota_parcial.serial_ntri=nota_trimestre.serial_ntri and nota_trimestre.serial_matalu=materia_alumno.serial_matalu and alumno.serial_alu=materia_alumno.serial_alu and materia_alumno.serial_matpro='.$rsMateria->fields[2].' and nota_trimestre.serial_tri='.$rsTrimestre->fields[0].' and alumno.serial_alu='.$serial_alu.' and nota_parcial.serial_prc='.$rsParcial->fields[0].'  and materia_nivel.serial_mat='.$rsMateria->fields[0]);

   		       $nota=($rsNotaParcial->fields[1]==0 || $rsNotaParcial->fields[1]=='')? '':$rsNotaParcial->fields[1];
		       $notatrimestre+=($rsNotaParcial->fields[1]==0 || $rsNotaParcial->fields[1]=='')? 0:$rsNotaParcial->fields[1];
                       if (substr($rsMateria->fields['codigo_mat'],0,4)=='COND' ) {
                                        $anotas[$nt][3]=$nota;
                                        //  echo "nota=".$nota;
                                        $anotas[$nt][6]=$rsParcial->fields['tipo_prc'];
                                          $anotas[$nt][1]=$posX1;

//                                        echo 'select serial_prccri,nota_prccri,criterio.serial_cri from criterio,alumno,materia_alumno,materia_nivel,materia_profesor,nota_trimestre,nota_parcial,parcial_criterio where criterio.serial_matniv=materia_profesor.serial_matniv and materia_nivel.serial_matniv=materia_profesor.serial_matniv and materia_profesor.serial_matpro=materia_alumno.serial_matpro and parcial_criterio.serial_nprc=nota_parcial.serial_nprc and parcial_criterio.serial_cri=criterio.serial_cri and nota_parcial.serial_ntri=nota_trimestre.serial_ntri and nota_trimestre.serial_matalu=materia_alumno.serial_matalu and alumno.serial_alu=materia_alumno.serial_alu and materia_alumno.serial_matpro='.$rsMateria->fields[2].' and nota_trimestre.serial_tri='.$rsTrimestre->fields[0].' and alumno.serial_alu='.$serial_alu.' and nota_parcial.serial_prc='.$rsParcial->fields[0].'  and materia_nivel.serial_mat='.$rsMateria->fields[0]."<br>";
//                                        echo "nota=".$nota."<br>";
                           }
                       else
//                            if ($rsMateria->fields[3]==0 && ($rsMateria->fields['tipo_mat']<>'CUALITATIVA') && ($rsMateria->fields['promedia_mat']!='NO')) {
                            if ($rsMateria->fields[3]==0 && ($rsMateria->fields['promedia_mat']=='SI')) {
                                        $decimales=explode(".",$rsParcial->fields['formato_prc']);
                                        $nota=round($nota,$decimales[1]);
                                        $anotas[$nt][1]=$posX1;
                                        $anotas[$nt][0]=$anotas[$nt][0]+$nota;
                                    //  print  "nota=".$nota ." anota=".$anotas[$nt][0] ."<br>";
                                        }
                       else {
                                //   $anotas[$nt][3]=$nota;
                                //   $anotas[$nt][1]=$posX1;
                            }

//                    if ($nota>0 && ($rsMateria->fields['tipo_mat']<>'CUALITATIVA') && (substr($rsMateria->fields['codigo_mat'],0,4)!='COND'  && $rsMateria->fields[3]==0) && !(substr($rsMateria->fields['codigo_mat'],0,4)=='COND' || ($rsMateria->fields['promedia_mat']=='NO')) ){ //&& ($rsMateria->fields[1]!='COMPUTACION' && $serial_sec==2))
                      if ($nota>0 && (substr($rsMateria->fields['codigo_mat'],0,4)!='COND'  && $rsMateria->fields[3]==0) && !(substr($rsMateria->fields['codigo_mat'],0,4)=='COND' || ($rsMateria->fields['promedia_mat']=='NO')) ){ //&& ($rsMateria->fields[1]!='COMPUTACION' && $serial_sec==2))                                 //echo "materia:". $rsMateria->fields[1]." seccion=".$serial_sec."<br>";
                                         $anotas[$nt][5]=$formato;
                                         $anotas[$nt][2]=$anotas[$nt][2]+1;
                                         }

                      $nt++;



                      $decimales=explode(".",$rsParcial->fields['formato_prc']);
                      $nota=round($nota,$decimales[1]);
                      $snota=($nota!='')?sprintf($formato,$nota):'';
                      if (substr($rsMateria->fields['codigo_mat'],0,4)!='COND' )
                                  if($rsMateria->fields['tipo_mat']=='CUALITATIVA') {
                                               $equivalencia=calcularAbreviaturaEquivalencia($dblink,$snota,$serial_sec,$serial_per);
	                                       $printOBJ->addColumn($equivalencia,20,'',$posX1+$vicc2,$posY,"number","center",'Helvetica','8',true,false,0,0,0,128);   //victor2017               0,237,28,36
	                                       }
                                  else if ($rsParcial->fields[4]=='SI' || !esMateriaConsolidada($rsMateria->fields[0])  ) {
                                              $vX=6.5;
                                              if ($rsTrimestre->fields['insuficiencia_sec']>=$snota)
                                                             if($rsMateria->fields['tipo_mat']=='TERMINAL') {
                                               $equivalencia=calcularAbreviaturaEquivalencia($dblink,$snota,$serial_sec,$serial_per);
	                                       $printOBJ->addColumn($equivalencia,20,'',$posX1+3,$posY,"number","center",'Helvetica','8',true,false,0,0,0,128);
							     }
							     else {
                                               $equivalencia=calcularAbreviaturaEquivalencia($dblink,$snota,$serial_sec,$serial_per);
	                                       $printOBJ->addColumn($equivalencia,20,'',$posX1+3,$posY,"number","center",'Helvetica','8',true,false,0,237,28,36);

	                                                            }
                                              else if($rsMateria->fields['tipo_mat']=='TERMINAL') {
                                               $equivalencia=calcularAbreviaturaEquivalencia($dblink,$snota,$serial_sec,$serial_per);
	                                       $printOBJ->addColumn($equivalencia,20,'',$posX1+3+40,$posY,"number","center",'Helvetica','8',true,false,0,0,0,128);
       	                                      }
	                                      else  {
                                               $equivalencia=calcularAbreviaturaEquivalencia($dblink,$snota,$serial_sec,$serial_per);
	                                       $printOBJ->addColumn($equivalencia,20,'',$posX1+3,$posY,"number","center",'Helvetica','8',true,false,0,0,0,128);

				       }
	                                       }
 	                          else
	                                     $printOBJ->addColumn(' ',20,' ',$posX1,$posY,"number","center",'Helvetica','8',true);

                                 // $printOBJ->addColumn(' ',$posXFin-5,0.1,5,$posY,"line","center",'Helvetica','5',false);
                                  $posX1+=(8+$newespacio);
                                  $rsParcial->MoveNext();
                        }



         $rsTrimestre->MoveNext();
//    	 $posX1+=7;
        }


    $posY+=4;
    $rsMateria->MoveNext();

//		$printOBJ->addColumn(' ',$posXFin-5,0.1,5,$posY,"line","center",'Helvetica','5',false);           //victor2017  hh


    }





    $posY-=4;
 //   $printOBJ->addColumn(' ',$posXFin-5,0.1,5,$posY+2,"line","center",'Helvetica','5',true);

    $posYFin=$posY;

    $posY+=6;
    //$printOBJ->addColumn(' ',$posYFin-$posYIni+7-9,0.1,5,$posYIni-9,"linev","center",'helvetica','5',true);                   //5 //////////////////////


	////////////

   $rsTrimestre->MoveFirst();
   $posXlineasParcial=120+8+$newespacio+40;

  $printOBJ->addColumn(' ',$posYFin-$posYIni+7-9,0.2,$posXlineasParcial-8-$newespacio+$vicc,$posYIni+9,"linev","center",'helvetica','5',false);  //2016 linea2 p5

  while (!$rsTrimestre->EOF ) {
      $contParciales=1;
      $rsParcial=$dblink->Execute("select serial_prc,abreviatura_prc, tipo_prc, formula_prc,desplegable_prc,formato_prc from parcial where desplegable_prc='SI' and serial_tri=".$rsTrimestre->fields[0]." and activo_prc='SI' order by orden_prc");
     	 	  
	  while (!$rsParcial->EOF ) {
         $printOBJ->addColumn(' ',$posYFin-$posYIni+7-9,0.2,$posXlineasParcial+$vicc,$posYIni+9,"linev","center",'helvetica','5',false);
                     //    $printOBJ->addColumn(' ',4,$rsParcial->fields['abreviatura_prc'],$posXlineasParcial-$contParciales*8-1+$vicc,$posYIni+8,"string","center",'helvetica','7',true);
                                         	
					 
					 $printOBJ->addColumn(' ',4,' ',$posXlineasParcial-$contParciales*8,$posYIni+4,"string","center",'helvetica','5',true);
           //$printOBJ->addColumn(' ',$posYFin-$posYIni+7-4,0.1,$posXlineasParcial,$posYIni+4,"linev","center",'helvetica','5',true);   //linev
        $posXlineasParcial+=(8+$newespacio);
        $rsParcial->MoveNext();
      }
	  
 

      $rsTrimestre->MoveNext();
    }
		

		             $printOBJ->addColumn(' ',$posYFin-$posYIni+7,0.1,120+40+$vicc,$posYIni,"linev","center",'helvetica','5',true);   //2016 linea2 p5
	 		     $printOBJ->addColumn(' ',$posYFin-$posYIni+7,0.1,153+29,$posYIni,"linev","center",'helvetica','5',true);    //2016 linea5 p5


/////////////

 //   for ($i=0; $i < $ntri;$i++) {
 //           $printOBJ->addColumn(' ',$posYFin-$posYIni+7,0.1,$aPosYTrimestre[$i],$posYIni,"linev","center",'helvetica','5',true);    ////////////////////////
   //        }

//2016    $printOBJ->addColumn(' ',$posYFin-$posYIni+7,0.1,$posXFin,$posYIni,"linev","center",'helvetica','5',true);     ///////////////////pag5

    $printOBJ->addColumn(' ',$posYFin-$posYIni+7,0.1,5,$posYIni,"linev","center",'helvetica','5',true);     //2016 linea1 pag5


	$printOBJ->addColumn(' ',$posXFin-5+$vicc,0.2,5,$posY+1,"line","center",'Helvetica','5',true);



    $posXe=27;

    $posYYYY=10;
    $posY=$posY-$posYYYY-2;




       	$printOBJ->addColumn(' ',52,0.1,5,$posY+16,"line","center",'Helvetica','5',true);
        $printOBJ->addColumn('ESCALA CUALITATIVA',17,'',7,$posY+17,"string","center",'Helvetica','7',true);
        $printOBJ->addColumn('EQUIVALENCIA',17,'',37,$posY+17,"string","center",'Helvetica','7',true);

       	$printOBJ->addColumn(' ',52,0.1,5,$posY+20,"line","center",'Helvetica','5',true);
        /*
        $printOBJ->addColumn('Supera los aprendizajes requeridos',17,'',5,$posY+24,"string","center",'Helvetica','7');
        $printOBJ->addColumn('SU',17,'',68,$posY+24,"string","center",'Helvetica','7');
	$printOBJ->addColumn(' 10',17,'',75,$posY+24,"string","center",'Helvetica','7');
	$printOBJ->addColumn(' ',85,0.1,5,$posY+27,"line","center",'Helvetica','5');
        */
	$printOBJ->addColumn('ADQUIRIDA',17,'',5,$posY+21,"string","center",'Helvetica','7');
	$printOBJ->addColumn('A',17,'',44,$posY+21,"string","center",'Helvetica','7');
	$printOBJ->addColumn(' ',52,0.1,5,$posY+24,"line","center",'Helvetica','5');

	$printOBJ->addColumn('EN PROCESO',17,'',5,$posY+25,"string","center",'Helvetica','7');
	$printOBJ->addColumn('EP',17,'',44,$posY+25,"string","center",'Helvetica','7');
	$printOBJ->addColumn(' ',52,0.1,5,$posY+28,"line","center",'Helvetica','5');

	$printOBJ->addColumn('INICIADA',17,'',5,$posY+29,"string","center",'Helvetica','7');
	$printOBJ->addColumn('I',17,'',44,$posY+29,"string","center",'Helvetica','7');
	$printOBJ->addColumn(' ',52,0.1,5,$posY+32,"line","center",'Helvetica','5');

	//$printOBJ->addColumn('NO EVALUADA',17,'',5,$posY+36,"string","center",'Helvetica','7');
	//$printOBJ->addColumn('NE',17,'',77,$posY+36,"string","center",'Helvetica','7');
	//$printOBJ->addColumn(' ',85,0.1,5,$posY+39,"line","center",'Helvetica','5',true);


	$printOBJ->addColumn(' ',20-4,0.1,5,$posY+16,"linev","center",'Helvetica','5',true);
	$printOBJ->addColumn(' ',20-4,0.1,36,$posY+16,"linev","center",'Helvetica','5',true);
	//$printOBJ->addColumn(' ',24-4,0.1,76,$posY+19,"linev","center",'Helvetica','5',true);
	$printOBJ->addColumn(' ',20-4,0.1,57,$posY+16,"linev","center",'Helvetica','5',true);

/* 
	$posVx=88;

       	$printOBJ->addColumn(' ',105,0.1,5+$posVx,$posY+19,"line","center",'Helvetica','5',true);
        $printOBJ->addColumn('ESCALA COMPORTAMENTAL',17,'',4+$posVx,$posY+20,"string","center",'Helvetica','6.8',true);

       	$printOBJ->addColumn(' ',105,0.1,5+$posVx,$posY+23,"line","center",'Helvetica','5',true);

        $printOBJ->addColumn('Muy Satistactorio',17,'',7+$posVx,$posY+24,"string","center",'Helvetica','7');
        $printOBJ->addColumn('A',17,'',35+$posVx,$posY+24,"string","center",'Helvetica','7');
//	$printOBJ->addColumn('Lidera el cumplimiento de los compromisos establecidos para la sana convivencia social.',17,'',40+$posVx,$posY+24,"string","center",'Helvetica','7',true);
	$printOBJ->addColumn('Lidera el cumplimiento de los compromisos establecidos.',17,'',40+$posVx,$posY+24,"string","center",'Helvetica','7');
	$printOBJ->addColumn(' ',105,0.1,5+$posVx,$posY+27,"line","center",'Helvetica','5');

	$printOBJ->addColumn('Satisfactorio',17,'',7+$posVx,$posY+28,"string","center",'Helvetica','7');
	$printOBJ->addColumn('B',17,'',35+$posVx,$posY+28,"string","center",'Helvetica','7');
//	$printOBJ->addColumn('Cumple con los compromisos establecidos para la sana convivencia social.',17,'',40+$posVx,$posY+28,"string","center",'Helvetica','7');
	$printOBJ->addColumn('Cumple con los compromisos establecidos.',17,'',40+$posVx,$posY+28,"string","center",'Helvetica','7');
	$printOBJ->addColumn(' ',105,0.1,5+$posVx,$posY+31,"line","center",'Helvetica','5');

	$printOBJ->addColumn('Poco Satisfactorio',17,'',7+$posVx,$posY+32,"string","center",'Helvetica','7');
	$printOBJ->addColumn('C',17,'',35+$posVx,$posY+32,"string","center",'Helvetica','7');
//	$printOBJ->addColumn('Falla ocasionalmente en el cumplimiento de los compromisos establecidos para la sana convivencia social.',17,'',40+$posVx,$posY+32,"string","center",'Helvetica','6.5');
	$printOBJ->addColumn('Falla ocasionalmente en el cumplimiento de los compromisos.',17,'',40+$posVx,$posY+32,"string","center",'Helvetica','6.5');
	$printOBJ->addColumn(' ',105,0.1,5+$posVx,$posY+35,"line","center",'Helvetica','5');

	$printOBJ->addColumn('Mejorable',17,'',7+$posVx,$posY+36,"string","center",'Helvetica','7');
	$printOBJ->addColumn('D',17,'',35+$posVx,$posY+36,"string","center",'Helvetica','7');
//	$printOBJ->addColumn('Falla reiteradamente en el cumplimiento de los compromisos establecidos para la sana convivencia social.',17,'',40+$posVx,$posY+36,"string","center",'Helvetica','6.6');
	$printOBJ->addColumn('Falla reiteradamente en el cumplimiento de los compromisos.',17,'',40+$posVx,$posY+36,"string","center",'Helvetica','6.6');
	$printOBJ->addColumn(' ',105,0.1,5+$posVx,$posY+39,"line","center",'Helvetica','5');

	$printOBJ->addColumn('Insatisfactorio',17,'',7+$posVx,$posY+40,"string","center",'Helvetica','7');
	$printOBJ->addColumn('E',17,'',35+$posVx,$posY+40,"string","center",'Helvetica','7');
//	$printOBJ->addColumn('No cumple con los compromisos establecidos para la sana convivencia social.',17,'',40+$posVx,$posY+40,"string","center",'Helvetica','7');
	$printOBJ->addColumn('No cumple con los compromisos establecidos.',17,'',40+$posVx,$posY+40,"string","center",'Helvetica','7');
	$printOBJ->addColumn(' ',105,0.1,5+$posVx,$posY+43,"line","center",'Helvetica','5',true);

	$printOBJ->addColumn(' ',24,0.1,5+$posVx,$posY+19,"linev","center",'Helvetica','5',true);
	$printOBJ->addColumn(' ',20,0.1,35+$posVx,$posY+23,"linev","center",'Helvetica','5',true);
	$printOBJ->addColumn(' ',24,0.1,40+$posVx,$posY+19,"linev","center",'Helvetica','5',true);
	$printOBJ->addColumn(' ',24,0.1,110+$posVx,$posY+19,"linev","center",'Helvetica','5',true);

*/
         $posYAuxPeriodo=80;

//COMENTAR
//    $printOBJ->addColumn('EL/LA ESTUDIANTE HA SIDO PROMOVIDO AL GRADO/CURSO INMEDIATO SUPERIOR',85,'',32,$posY+$posYAuxPeriodo-35,"string","center",'Helvetica','11',true,false,0,0,0,128);


    $observacion_paralu=$rsAlumno->fields[8];
    $observacion_alu=leerObservacionAlumno($dblink,$serial_alu);
    $observacion_par=leerObservacionParalelo($dblink,$serial_par);

  //  $printOBJ->addColumn('OBSERVACIONES:',85,$observacion_par,5,$posY+$posYAuxPeriodo-28-5,"string","center",'Helvetica','8',true);
//  $printOBJ->addColumn('                                 ',85,$observacion_alu,5,$posY+$posYAuxPeriodo-24,"string","center",'Helvetica','8',true);
 //   $printOBJ->addColumn('                                 ',85,$observacion_paralu,5,$posY+$posYAuxPeriodo-24-5,"string","center",'Helvetica','8',true);

//   $printOBJ->addColumn('__________________________________________________________________________________________________________',17,'',32,$posY+$posYAuxPeriodo-33,"string","center",'Helvetica','8',true);
 //  $printOBJ->addColumn('__________________________________________________________________________________________________________',17,'',32,$posY+$posYAuxPeriodo-28,"string","center",'Helvetica','8',true);
// $printOBJ->addColumn('__________________________________________________________________________________________________________',17,'',32,$posY+$posYAuxPeriodo-23,"string","center",'Helvetica','8',true);
// $printOBJ->addColumn('__________________________________________________________________________________________________________',17,'',32,$posY+$posYAuxPeriodo-18,"string","center",'Helvetica','8',true);
// $printOBJ->addColumn('__________________________________________________________________________________________________________',17,'',32,$posY+$posYAuxPeriodo-13,"string","center",'Helvetica','8',true);

     $rector=leerConsejoDirectivo($dblink,$serial_per,$serial_sec,'RECTOR');
$vicerrector=leerConsejoDirectivo($dblink,$serial_per,$serial_sec,'VICERRECTOR');
   $coordinador=leerConsejoDirectivo($dblink,$serial_per,$serial_sec,'COORDINADOR');
   $secretario=leerConsejoDirectivo($dblink,$serial_per,$serial_sec,'SECRETARIO');
  $dirigente=leerDirigente($dblink,$serial_per,$serial_par);
        $espacio0=30;
        $espacio=50;
	$POSYYY1=4;

         if($secretario!=''){
                    $printOBJ->addColumn('________________________',17,'',60,$posY+23,"string","center",'Helvetica','8',true);
                    $printOBJ->addColumn(' ',17,$secretario,58,$posY+26,"string","center",'Helvetica','6',true);
                    $printOBJ->addColumn('SECRETARIA',17,' ',68,$posY+29,"string","center",'Helvetica','8',true);
                    }
    

           if($dirigente!=''){
                   $printOBJ->addColumn('________________________',17,'',105,$posY+23,"string","center",'Helvetica','8',true);
                   $printOBJ->addColumn(' ',17,$dirigente,103,$posY+26,"string","center",'Helvetica','6',true);
                   $printOBJ->addColumn('DOCENTE',17,' ',113,$posY+29,"string","center",'Helvetica','8',true);
                  }
		   if($coordinador!=''){
                   $printOBJ->addColumn('________________________',17,'',150,$posY+23,"string","center",'Helvetica','8',true);
                   $printOBJ->addColumn(' ',17,$coordinador,148,$posY+26,"string","center",'Helvetica','6',true);
                   $printOBJ->addColumn('COORDINADORA',17,' ',158,$posY+29,"string","center",'Helvetica','8',true);
                  }
		
/*
           $printOBJ->addColumn('___________________________',17,'',5+$espacio*3,$posY+$posYAuxPeriodo-$POSYYY1,"string","center",'Helvetica','8',true);
           $printOBJ->addColumn('REPRESENTANTE',17,'',10+$espacio*3+4,$posY+$posYAuxPeriodo+4-$POSYYY1,"string","center",'Helvetica','9',true);
*/


    $d=$omnisoftCiudad.", ".date("Y")."-".date("n")."-".date("d")." ".date("H:i:s");

//    $printOBJ->addColumn('',17,$d,145,$posY+($posYAuxPeriodo-20)-5,"string","center",'Helvetica','7');

$printOBJ->columnCount=0;
$printOBJ->columnDetailCount=0;
$printOBJ->printPage();
$rsAlumno->MoveNext();
$numeroalumno++;
}
    $printOBJ->showIt();

?>
