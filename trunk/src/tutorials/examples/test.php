<?php
/*
This is an example of how plot.class.php could be used. In this example we will draw a graph of the mathematical expression sent via. the GET method.
*/

//Tell the client that this is a binary png file, NOT html.
//This is also why you can't use html in this example.
header("Content-type: image/png");


/*
Include plot.class.php, since we need the file to draw with.
*/
include("plot.class.php");

//Create an instance of Plot
$Plot = new Plot;

//Initialize some fields on the Plot
$Plot->Caption = "Example plot";

//Setting image size
$Plot->Width = 500;
$Plot->Height = 500;

//Setting coordinate space
$Plot->MinX = -250;
$Plot->MaxX = 250;
$Plot->MinY = -250;
$Plot->MaxY = 250;

//Creating an instance of Graph
$G = new Graph;

//Set the expression of the graph to the parameter given via the GET method.
$G->Exp = $_GET["exp"];

//Set the color of the Graph
$G->Color = array(0,0,255);

//Set the label of the Graph
$G->Label = "GET graph";

//Add all (in this case only one) instances of Graph to an array and parse to Plot
$Plot->Graphs = array($G);

//Display the final Image, printed as binary png in this file.
$Plot->DisplayPlot();?>
