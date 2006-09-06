<?php
/*
Copyright (C) 2006 by the WikiPlot project authors (See http://code.google.com/p/WikiPlot).

This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
*/

include('evalmath.class.php');

class Plot
{
	//Graphs to plot
	var $Graphs;

	//Caption, leave if no caption wanted
	var $Caption = null;
	var $CaptionFont = 5; //TODO: add to Hash()

	//Options for output Image
	var $Width = 100;
	var $Height = 100;

	//Options for Coordinat system
	var $MinX = -10;
	var $MaxX = 100;
	var $MinY = -10;
	var $MaxY = 100;

	//Options for grid
	var $EnableAxis = true;
	var $EnableGrid = true;
	var $GridColor = array(240,240,240);
	var $GridFont = 1;
	var $XGridSpace = null;
	var $YGridSpace = null;

	//Get hashsum of the plot
	function GetHash()
	{
		$Hash  = "C:" . $this->Caption;
		$Hash  = "F:" . $this->CaptionFont;
		$Hash .= "W:" . $this->Width;
		$Hash .= "H:" . $this->Height;
		$Hash .= "X:" . $this->MinX . "_" . $this->MaxX;
		$Hash .= "Y:" . $this->MinY . "_" . $this->MaxY;
		$Hash .= "E:" . $this->EnableGrid;
		$Hash .= "A:" . $this->EnableAxis;
		$Hash .= "G:" . $this->EnableGrid . "_" . $this->GridColor . "_" . $this->GridFont;
		$Hash .= "S:" . $this->XGridSpace . "_" . $this->YGridSpace;
		$Hash .= "V:" . "$LastChangedRevision$";
		foreach($this->Graphs as $key => $S)
		{
			$Hash .= "G:" . $key. "_" . $S; 
		}
		return md5($Hash);
	}

	//Get ImageResource of the plot
	function DrawPlot()
	{
		//Get ImageResource
		$ImageResource = imagecreatetruecolor($this->Height,$this->Width);

		//AntiAlias ON
		imageantialias($ImageResource,true);

		//Get a black Color
		$Black = imagecolorexact($ImageResource,0,0,0);

		//Fill the image with white
		imagefill($ImageResource,0,0,imagecolorexact($ImageResource,255,255,255));

		//If grid is enabled
		if($this->EnableGrid)
		{
			$this->DrawGrid($ImageResource);
		}

		//If axis is enabled
		if($this->EnableAxis)
		{
			$this->DrawAxis($ImageResource);
		}
		
		//Draw caption
		$this->DrawCaption($ImageResource);

		//Y position for Labels relative to Image
		$LabelY = 5;

		//Plot all graphs
		foreach($this->Graphs as $key => $S)
		{
			//Get Color
			$Color = imagecolorexact($ImageResource,$S->Color[0],$S->Color[1],$S->Color[3]);

			//Set Expression
			$m = new EvalMath;
			$m->evaluate("f(x) = " . $S->Exp);

			//Set OldCoordinat*, don't start with a line from 0,0
			$OldCoordinatX = $this->GetCoordinatX(0);
			$OldCoordinatY = $m->evaluate("f(".$OldCoordinatX.")");

			//Plot the graph
			for($ImageX=0;$ImageX<$this->Width;$ImageX++)
			{

				//Get some NewCoordinat*
				$NewCoordinatX = $this->GetCoordinatX($ImageX);
				$NewCoordinatY = $m->evaluate("f(".$NewCoordinatX.") ");

				//Draw a line from OldCoordinat*
				imageline(
					$ImageResource,
					$this->GetImageX($OldCoordinatX),
					$this->GetImageY($OldCoordinatY),
					$this->GetImageX($NewCoordinatX),
					$this->GetImageY($NewCoordinatY),
					$Color);

				//Get some OldCoordinat*
				$OldCoordinatX = $NewCoordinatX;
				$OldCoordinatY = $NewCoordinatY;
			}

			//Draw label if it is enabled
			if($S->EnableLabel)
			{
				//Draw label
				imagestring($ImageResource,$S->LabelFont,5,$LabelY,"- ".$S->Label,$Color);

				//Add Label height to next Label X position
				$LabelY += imagefontheight($S->LabelFont);
			}
		}

		//Return ImageResource
		return $ImageResource;
	}

	//Draw caption to plot
	function DrawCaption(&$ImageResource)
	{
		//Get a black color for caption
		$Black = imagecolorexact($ImageResource,0,0,0);

		//width of the caption
		$CaptionWidth = strlen($this->Caption)*imagefontwidth($this->CaptionFont);

		//X position of the caption, making it centered
		$X = ($this->Width-$CaptionWidth)/2;
	
		//Draw the caption
		imagestring($ImageResource,$this->CaptionFont,$X,0,$this->Caption,$Black);
	}

	//Rewrite numbers into something short 1.000.000 -> 1E6
	function ShortNumber($Number, $MaxLen = 7)
	{
		//If $Number isn't too long return it as it is
		if(strlen($Number)<=$MaxLen)
		{
			return $Number;
		}else{
			//Convert to scientific notation
			$NSci = sprintf("%e",$Number);

			//Follwing hack prevents the function from showing too many decimals
			$ArrNSci = split($NSci,"e");
			return round($ArrNSci[0],$MaxLen-5) . "e" . $ArrNSci[1];
		}
	}

	//Returns X grid spaces from parameter or calculated 
	function GetXGridSpace()
	{
		if($this->XGridSpace==null)
		{
			//Text length max 7 when using $this->ShortNumber();
			$XTextLen = 7*imagefontwidth($this->GridFont) + 10;

			//Convering to coordinate space
			return (($this->MaxX-$this->MinX)/$this->Width)*$XTextLen;
		}else{
			$XGridSpace = $this->XGridSpace;
		}
		return $XGridSpace;
	}
	
	//Returns Y grid spaces from parameter or calculated 
	function GetYGridSpace()
	{
		if($this->YGridSpace==null)
		{
			//Text length max 7 when using $this->ShortNumber();
			$XTextLen = 7*imagefontwidth($this->GridFont) + 10;

			//Convering to coordinate space
			return (($this->MaxX-$this->MinX)/$this->Width)*$XTextLen;
		}else{
			$YGridSpace = $this->YGridSpace;
		}
		return $YGridSpace;
	}

	//Draw grid, for both X and Y
	function DrawGrid(&$ImageResource)
	{
		$this->DrawXGrid($ImageResource);
		$this->DrawYGrid($ImageResource);
	}

	//Draw X grid
	function DrawXGrid(&$ImageResource)
	{
		//Get grid width
		$XGridSpace = $this->GetXGridSpace();
		
		//Get color to draw with
		$Color = imagecolorexact($ImageResource,$this->GridColor[0],$this->GridColor[1],$this->GridColor[2]);
		//Get text color
		$Black = imagecolorexact($ImageResource,0,0,0);

		//Calculate start and end coordinats of the grid
		$XGridStart = ($this->MinX-fmod($this->MinX,$XGridSpace));
		$XGridEnd = $this->MaxX-fmod(($this->MaxX-$this->MinX),$XGridSpace);

		//Draw the grid
		for($XCordinate=$XGridStart;$XCordinate<$XGridEnd;$XCordinate+=$XGridSpace)
		{
			imageline(
				$ImageResource,
				$this->GetImageX($XCordinate),
				$this->GetImageY($this->MinY),
				$this->GetImageX($XCordinate),
				$this->GetImageY($this->MaxY),
				$Color);

			//If Y axes is not on the image (working in ImageSpace not CoordinatSpace)
			$Y = $this->GetImageY(0);
			if($Y > ($this->Height-imagefontheight($this->GridFont)))
			{
				$Y = $this->Height-(imagefontheight($this->GridFont)+2);
			}else{
				if($Y<0)
				{
					$Y = 0;
				}
			}
			imagestring(
				$ImageResource,
				$this->GridFont,
				$this->GetImageX($XCordinate)+2,
				$Y+2,
				$this->ShortNumber($XCordinate),
				$Black);
		}
	}
	
	//Draw Y grid
	function DrawYGrid(&$ImageResource)
	{
		//Get grid width
		$YGridSpace =  $this->GetYGridSpace();
		
		//Get color to draw with
		$Color = imagecolorexact($ImageResource,$this->GridColor[0],$this->GridColor[1],$this->GridColor[2]);
		//Get text color
		$Black = imagecolorexact($ImageResource,0,0,0);

		//Calculate start and end coordinats of the grid
		$YGridStart = ($this->MinY-fmod($this->MinY,$YGridSpace));
		$YGridEnd = $this->MaxY-fmod(($this->MaxY-$this->MinY),$YGridSpace);

		//Draw the grid
		for($YCordinate=$YGridStart;$YCordinate<$YGridEnd;$YCordinate+=$YGridSpace)
		{
			imageline(
				$ImageResource,
				$this->GetImageX($this->MinX),
				$this->GetImageY($YCordinate),
				$this->GetImageX($this->MaxX),
				$this->GetImageY($YCordinate),
				$Color);

			//If X axes is not on the image (working in ImageSpace not CoordinatSpace)
			$X = $this->GetImageX(0);
			if($X > ($this->Width-(imagefontwidth($this->GridFont)*7)))
			{
				$X = $this->Width-(imagefontwidth($this->GridFont)*7+2);
			}else{ 
				if($X<0)
				{
					$X = 0;
				}
			}
			imagestring(
				$ImageResource,
				$this->GridFont,
				$X+2,
				$this->GetImageY($YCordinate)+2,
				$this->ShortNumber($YCordinate),
				$Black);
		}
	}

	//Draw axis
	function DrawAxis(&$ImageResource)
	{
		//Draw X-axis
		imageline(
			$ImageResource,
			$this->GetImageX(0),
			$this->GetImageY($this->MinY),
			$this->GetImageX(0),
			$this->GetImageY($this->MaxY),
			$Black);
		//Draw Y-axis
		imageline($ImageResource,
			$this->GetImageX($this->MinX),
			$this->GetImageY(0),
			$this->GetImageX($this->MaxX),
			$this->GetImageY(0),
			$Black);
	}

	//Display plot as png
	function DisplayPlot()
	{
		header("Content-type: image/png");
		imagepng($this->DrawPlot());
	}

	function SaveAs($Path)
	{
		imagepng($this->DrawPlot(),$Path);
	}
	
	//Scale image position to coordinat position
	function GetCoordinatX($x)
	{
		return (($this->MaxX-$this->MinX)/$this->Width)*$x+$this->MinX;
	}

	//Scale image position to coordinat position
	function GetCoordinatY($y)
	{
		return (($this->MaxY-$this->MinY)/$this->Height)*($this->Height-$y)+$this->MinY;
	}

	//Scale coordinat position to image position
	function GetImageX($x)
	{
		return ($x-$this->MinX)*($this->Width/($this->MaxX-$this->MinX));
	}

	//Scale coordinat position to image position
	function GetImageY($y)
	{
		return $this->Height-($y-$this->MinY)*($this->Height/($this->MaxY-$this->MinY));
	}
}

class Graph
{
	//The label of the graph
	var $Label;
	var $LabelFont = 2;
	var $EnableLabel = true;

	//The expression for graph
	var $Exp;

	//Color of the graph
	var $Color = array(0,0,0);

	//Get hash sum of the graph
	function GetHash()
	{
		return $this->Label ."_". $this->LabelFont ."_". $this->Exp ."_". $this->Color . "_" . $this->EnableLabel;
	}

}
?>
