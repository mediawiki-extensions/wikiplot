<?php
/*
Copyright (C) 2006 by the WikiPlot project authors (See http://code.google.com/p/WikiPlot).

This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
*/


/**
* File use to draw plots
* 
* This file contains a class used to draw plot's. It's dependent on graph.plot.class.php and evalmath.class.php.
* 
* @package WikiPlot
* @subpackage PlotClass
* @license http://www.gnu.org/licenses/gpl.txt GNU General Public License
* @author WikiPlot development team.
* @copyright Copyright 2006, WikiPlot development team.
*/

/**
*Includes EvalMath
*
*EvalMath is used to evaluate mathematical expressions in a safe environment.
*/
require_once('evalmath.class.php');

/**
*Includes Graph representation class
*
*Graph is used as a representation of a graph.
*/
require_once('graph.plot.class.php');

/**
* Class used to draw plots
* 
* Class containing functions to draw plots to an image.
* 
* @package WikiPlot
* @subpackage PlotClass
* @license http://www.gnu.org/licenses/gpl.txt GNU General Public License
* @author WikiPlot development team.
* @copyright Copyright 2006, WikiPlot development team.
*/
class Plot
{
	/**
	* Graphs to plot
	*
	* Array containing list of Graphs to plot.
	*
	* @var array
	* @access public
	* @see Graph
	*/
	var $Graphs = array();

	/**
	* Caption of the plot
	* 
	* Caption of the plot, will be shown as text centered on the final plot. 
	* Leave this variable as null if no Caption is wanted.
	* 
	* @var string
	* @access public
	* @see DrawCaption
	*/
	var $Caption = null;

	/**
	* Caption font
	* 
	* Font of the Caption, the fonts 1-5 is built in, and behaves as different sizes.
	* 
	* @var integer
	* @access public
	* @see DrawCaption
	*/
	var $CaptionFont = 5;

	/**
	* Width of output image
	*
	* The width of the output image, in pixels.
	*
	* @var integer
	* @access public
	* @see DrawPlots
	*/
	var $Width = 100;

	/**
	* Height of output image
	*
	* The width of the output image, in pixels.
	*
	* @var integer
	* @access public
	* @see DrawPlots
	*/
	var $Height = 100;

	/**
	* Minimum X
	*
	* Minimum X in coordinate space. 
	* Together with MaxX this variable defines width of the plot in coordinate space. 
	* This width may differ from width of the image, the coordinate will be scaled correctly.
	*
	* @var integer
	* @access public
	* @see DrawPlots
	* @see MaxX
	*/
	var $MinX = -10;
	/**
	* Maximum X
	*
	* Maximum X in coordinate space. 
	* Together with MinX this variable defines width of the plot in coordinate space. 
	* This width may differ from width of the image, the coordinate will be scaled correctly.
	*
	* @var integer
	* @access public
	* @see DrawPlots
	* @see MinX
	*/
	var $MaxX = 100;
	/**
	* Minimum Y
	*
	* Minimum Y in coordinate space. 
	* Together with MaxY this variable defines height of the plot in coordinate space. 
	* This height may differ from height of the image, the coordinate will be scaled correctly.
	*
	* @var integer
	* @access public
	* @see DrawPlots
	* @see MaxY
	*/
	var $MinY = -10;
	/**
	* Maximum Y
	*
	* Maximum Y in coordinate space. 
	* Together with MinY this variable defines height of the plot in coordinate space. 
	* This height may differ from height of the image, the coordinate will be scaled correctly.
	*
	* @var integer
	* @access public
	* @see DrawPlots
	* @see MinY
	*/
	var $MaxY = 100;

	/**
	* Enable Axis
	*
	* Defaults to true and draws 2 axis.
	*
	* @var boolean
	* @access public
	* @see DrawAxis
	*/
	var $EnableAxis = true;
	/**
	* Enable Grid
	*
	* Defaults to true and draws a grid.
	*
	* @var boolean
	* @access public
	* @see DrawGrid
	*/
	var $EnableGrid = true;
	/**
	* Grid color
	*
	* Defaults to gray, and determains the color of the grid. This is an array of three integers, one for red, green and blue. Where integeres has values between 0 and 255.
	* <code>
	* var $Red = 240;
	* var $Green = 240;
	* var $Blue = 240;
	* $this->GridColor = array($Red,$Green,$Blue);
	* </code>
	* 
	* @var array
	* @access public
	* @see DrawGrid
	*/
	var $GridColor = array(240,240,240);
	/**
	* Grid font
	* 
	* Font of the grids labels, the fonts 1-5 is built in, and behaves as different sizes.
	* 
	* @var integer
	* @access public
	* @see DrawGrid
	*/
	var $GridFont = 1;
	/**
	* X grid space
	* 
	* Distance between grids on the x axis in coordinate space. Defaults to null, leave it null, if you want autogenerated gridspace.
	* 
	* @var integer
	* @access public
	* @see GetXGridSpace
	*/
	var $XGridSpace = null;
	/**
	* Y grid space
	* 
	* Distance between grids on the y axis in coordinate space. Defaults to null, leave it null, if you want autogenerated gridspace.
	* 
	* @var integer
	* @access public
	* @see GetYGridSpace
	*/
	var $YGridSpace = null;
	/**
	* Background color
	*
	* Color of the background when using auto ImageResource created by GeneratePlot().
	*
	* @var array
	* @access public
	*/
	var $BackgroundColor = array(255,255,255);

	/**
	*Generate hash
	*
	*Generates a uniqe hashsum (md5) for the plot, generated from all parameters.
	*
	*@uses $Caption
	*@uses $CaptionFont
	*@uses $Width
	*@uses $Height
	*@uses $MinX
	*@uses $MaxX
	*@uses $MinY
	*@uses $MaxY
	*@uses $EnableGrid
	*@uses $GridColor
	*@uses $GridFont
	*@uses $EnableAxis
	*@uses $XGridSpace
	*@uses $YGridSpace
	*@uses $Graphs
	*@uses Graph::GetHash()
	*@return string Hash representation of the object.
	*/
	function GetHash()
	{
		$Hash  = "C:" . $this->Caption;
		$Hash .= "F:" . $this->CaptionFont;
		$Hash .= "W:" . $this->Width;
		$Hash .= "H:" . $this->Height;
		$Hash .= "X:" . $this->MinX . "_" . $this->MaxX;
		$Hash .= "Y:" . $this->MinY . "_" . $this->MaxY;
		$Hash .= "A:" . $this->EnableAxis;
		$Hash .= "G:" . $this->EnableGrid . "_" . $this->GridColor . "_" . $this->GridFont;
		$Hash .= "S:" . $this->XGridSpace . "_" . $this->YGridSpace;
		$Hash .= "V:" . "$LastChangedRevision$";
		foreach($this->Graphs as $key => $S)
		{
			$Hash .= "G:" . $key. "_" . $S->GetHash(); 
		}
		return md5($Hash);
	}

	/**
	*Get ImageResource of the plot
	*
	*Generates ImageResource representation of the plot.
	*
	*@access public
	*@uses EnableGrid
	*@uses DrawGrid()
	*@uses $Width
	*@uses $Height
	*@uses $EnableAxis
	*@uses DrawAxis()
	*@uses DrawCaption()
	*@uses DrawPlots()
	*@uses $BackgroundColor
	*@param ImageResource $ImageResource Defaults to null, will generate empty ImageResource.
	*@param Boolean $ChangeSize May we change the size of the plot to fit given ImageResource?
	*@return ImageResource ImageResource representation of the plot.
	*/
	function GeneratePlot($ImageResource = null, $ChangeSize = false)
	{
		//If ImageResource is null
		if(is_null($ImageResource))
		{
			//Get ImageResource
			$ImageResource = imagecreatetruecolor($this->Height,$this->Width);

			//AntiAlias ON
			imageantialias($ImageResource,true);

			//Fill the image with white
			imagefill($ImageResource,0,0,imagecolorexact($ImageResource,$this->BackgroundColor[0],$this->BackgroundColor[1],$this->BackgroundColor[2]));
		
		}//If ImageResource doesn't fit image and we may not change size
		elseif($ChangeSize==false&&(imagesx($ImageResource)!=$this->Width||imagesy($ImageResource)!=$this->Height))
		{
			//Get ImageResource
			$ImageResource = imagecreatetruecolor($this->Height,$this->Width);

			//AntiAlias ON
			imageantialias($ImageResource,true);

			//Fill the image with white
			imagefill($ImageResource,0,0,imagecolorexact($ImageResource,$this->BackgroundColor[0],$this->BackgroundColor[1],$this->BackgroundColor[2]));			
		}//If we may change the size of the plot
		elseif($ChangeSize)
		{
			//Changing size of the plot.
			$this->Width = imagesx($ImageResource);
			$this->Height = imagesy($ImageResource);
		}

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

		//Draw plots
		$this->DrawPlots($ImageResource);

		//Return ImageResource
		return $ImageResource;
	}

	/**
	*Get ImageResource of the plot
	*
	*Generates ImageResource representation of the plot.
	*
	*@access private
	*@uses $Width
	*@uses EvalMath
	*@uses EvalMath::evaluate()
	*@uses GetCoordinatX()
	*@uses GetImageX()
	*@uses GetImageY()
	*@uses $Graphs
	*@uses Graph::$Color
	*@uses Graph::$LabelFont
	*@uses Graph::$EnableLabel
	*@uses Graph::$Label
	*@param ImageResource &$ImageResource ImageResource representation of the plot.
	*/
	function DrawPlots(&$ImageResource)
	{
		//Get a black Color
		$Black = imagecolorexact($ImageResource,0,0,0);

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
	}

	/**
	*Draw caption to ImageResource
	*
	*Draws the caption to an ImageResource representation of the plot.
	*
	*@access private
	*@uses $Width
	*@uses $Caption
	*@uses $CaptionFont
	*@param ImageResource &$ImageResource ImageResource representation of the plot.
	*/
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

	/**
	*Generates short numbers
	*
	*Rewrites numbers into scientific notation, with a certain maximum length.<br>
	*Example: ShortNumber(501000000) == 5.01e8
	*
	*@access private
	*@param integer $Number The number you wish to shorten.
	*@param integer $MaxLen The maximum length of the output default to 7.
	*@return string Scientific notation of the given Number at a certain length.
	*/
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

	/**
	*Get X grid space
	*
	*Returns X grid space, either calculated or from given value if given one.
	*
	*@access private
	*@uses $XGridSpace
	*@return integer The space between grid on x axes.
	*/
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
	
	/**
	*Get Y grid space
	*
	*Returns Y grid space, either calculated or from given value if given one. If it is to be calculated, 
	*it is calculated the same way as x axes!
	*
	*@access private
	*@uses $YGridSpace
	*@return integer The space between grid on y axes.
	*/
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

	/**
	*Draw grids
	*
	*Draws both x and y grid, using DrawXGrid() and DrawYGrid().
	*
	*@access private
	*@uses DrawXGrid()
	*@uses DrawYGrid()
	*@param ImageResource &$ImageResource ImageResource representation of the plot.
	*/
	function DrawGrid(&$ImageResource)
	{
		$this->DrawXGrid($ImageResource);
		$this->DrawYGrid($ImageResource);
	}

	/**
	*Draws x-grid
	*
	*Drawing X grid on the plot.
	*
	*@access private
	*@uses GetXGridSpace()
	*@uses $GridColor
	*@uses $MinX
	*@uses $MaxX
	*@uses $MinY
	*@uses $MaxY
	*@uses GetImageX()
	*@uses GetImageY()
	*@uses $GridFont
	*@uses $Height
	*@uses ShortNumber()
	*@param ImageResource &$ImageResource ImageResource representation of the plot.
	*/
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
	
	/**
	*Draws y-grid
	*
	*Drawing y grid on the plot.
	*
	*@access private
	*@uses GetYGridSpace()
	*@uses $GridColor
	*@uses $MinX
	*@uses $MaxX
	*@uses $MinY
	*@uses $MaxY
	*@uses GetImageX()
	*@uses GetImageY()
	*@uses $GridFont
	*@uses $Width
	*@uses ShortNumber()
	*@param ImageResource &$ImageResource ImageResource representation of the plot.
	*/
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

	/**
	* Draw axis
	*
	* Draw both x and y axis to the plot.
	*
	*@access private
	*@uses $MinX
	*@uses $MaxX
	*@uses $MinY
	*@uses $MaxY
	*@uses GetImageX()
	*@uses GetImageY()
	*@param ImageResource &$ImageResource ImageResource representation of the plot.
	*/
	function DrawAxis(&$ImageResource)
	{
		$Black = imagecolorexact($ImageResource,0,0,0);
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

	/**
	*Display plot as image
	*
	*Displays plot as image on the page. This makes current http-request return an image. You can set the DisplayType to png, gif or jpeg. Defaults to png, gif not recommanded. Note: this changes the current http-request mimetype to the respective image mimetype.
	*
	*@access public
	*@uses GeneratePlot()
	*@param string $DisplayType Type of image to view (png|jpeg|gif).
	*@param ImageResource $ImageResource Defaults to null, will generate empty ImageResource.
	*@param Boolean $ChangeSize May we change the size of the plot to fit given ImageResource?
	*/
	function DisplayPlot($DisplayType = "png",$ImageResource = null, $ChangeSize = false)
	{
		if($DisplayType == "png")
		{
			header("Content-type: image/png");
			imagepng($this->GeneratePlot($ImageResource, $ChangeSize));	
		}
		elseif($DisplayType == "gif")
		{
			header("Content-type: image/gif");
			imagegif($this->GeneratePlot($ImageResource, $ChangeSize));
		}
		else
		{
			header("Content-type: image/jpeg");
			imagejpeg($this->GeneratePlot($ImageResource, $ChangeSize));
		}
	}

	/**
	*Save plot to image
	*
	*Saves the plot to an image. You can set the SaveAs to a file type: png, gif or jpeg, defaults to png.
	*
	*@access public
	*@uses GeneratePlot()
	*@param string $Path Path of file to save.
	*@param string $SaveAs Filetype definition (png|jpeg|gif).
	*@param ImageResource $ImageResource Defaults to null, will generate empty ImageResource.
	*@param Boolean $ChangeSize May we change the size of the plot to fit given ImageResource?
	*/
	function SaveAs($Path,$SaveAs = "png",$ImageResource = null, $ChangeSize = false)
	{
		if($SaveAs == "png")
		{
			imagepng($this->GeneratePlot($ImageResource, $ChangeSize),$Path);	
		}
		elseif($SaveAs == "gif")
		{
			imagegif($this->GeneratePlot($ImageResource, $ChangeSize),$Path);
		}
		else
		{
			imagejpeg($this->GeneratePlot($ImageResource, $ChangeSize),$Path);
		}
	}
	
	/**
	* Convert to coordinate space
	*
	* Converts an x image position to x coordinate position. Coordinate space may differ from Image space, if Width!= (MaxX-MinX).
	*
	*@access private
	*@uses $MaxX
	*@uses $MinX
	*@uses $Width
	*@param integer $x X image coordinat to be converted.
	*@return integer Coordiante space representation given parameter.
	*/
	function GetCoordinatX($x)
	{
		return (($this->MaxX-$this->MinX)/$this->Width)*$x+$this->MinX;
	}

	/**
	* Convert to coordinate space
	*
	* Converts an y image position to y coordinate position. Coordinate space may differ from Image space, if Height!= (MaxY-MinY).
	*
	*@access private
	*@uses $MaxY
	*@uses $MinY
	*@uses $Height
	*@param integer $y Y image coordinat to be converted.
	*@return integer Coordiante space representation given parameter.
	*/
	function GetCoordinatY($y)
	{
		return (($this->MaxY-$this->MinY)/$this->Height)*($this->Height-$y)+$this->MinY;
	}

	/**
	* Convert to image space
	*
	* Converts an x in coordinate space to x image position. Coordinate space may differ from Image space, if Width!= (MaxX-MinX).
	*
	*@access private
	*@uses $MaxX
	*@uses $MinX
	*@uses $Width
	*@param integer $x X coordinat to be converted.
	*@return integer Image position representation given parameter.
	*/
	function GetImageX($x)
	{
		return ($x-$this->MinX)*($this->Width/($this->MaxX-$this->MinX));
	}

	/**
	* Convert to image space
	*
	* Converts an y in coordinate space to y image position. Coordinate space may differ from Image space, if Height!= (MaxY-MinY).
	*
	*@access private
	*@uses $MaxY
	*@uses $MinY
	*@uses $Height
	*@param integer $y Y coordinat to be converted.
	*@return integer Image position representation given parameter.
	*/
	function GetImageY($y)
	{
		return $this->Height-($y-$this->MinY)*($this->Height/($this->MaxY-$this->MinY));
	}
}
?>
