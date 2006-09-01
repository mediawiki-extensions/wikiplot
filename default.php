<?php
/*
This is an example of how to use plot.class.php
*/
header("Content-type: image/png");

include("plot.class.php");

$Plot = new Plot;

//Playing with SVN... I'll revert this later.. :)
$G = new Graph;
$G->Exp = "0.002x^3";
$G->Color = array(0,0,255);
$G->Label = "test";

$G1 = new Graph;
$G1->Exp = "sin(x*0.3)*50+0.00005x^3+0.001x^2";
$G1->Color = array(0,255,0);
$G1->Label = "test1";

$G2 = new Graph;
$G2->Exp = "sin(x*0.3)*50+0.05x^2+100";
$G2->Color = array(255,0,0);
$G2->Label = "Sjov graf";

$Plot->Graphs = array($G,$G1,$G2);

$Plot->Caption = "Test Graph";

$Plot->Width = 500;
$Plot->Height = 500;

$Plot->MinX = -250;
$Plot->MaxX = 250;
$Plot->MinY = -500;
$Plot->MaxY = 500;

//$Plot->DisplayPlot();

$im = $Plot->DrawPlot();
imagepng($im);


?>
