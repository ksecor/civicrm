<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.4                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                  |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the Affero General Public License Version 1,    |
 | March 2002.                                                        |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the Affero General Public License for more details.            |
 |                                                                    |
 | You should have received a copy of the Affero General Public       |
 | License along with this program; if not, contact the Social Source |
 | Foundation at info[AT]socialsourcefoundation[DOT]org.  If you have |
 | questions about the Affero General Public License or the licensing |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | at http://www.openngo.org/faqs/licensing.html                       |
 +--------------------------------------------------------------------+
*/
if(!class_exists('FPDF'))
{
    define('FPDF_VERSION','1.53');
    
    /**
     * This class acts as our base class and adds additional 
     * functionality and smarts to the base PDF_Label.
     *
     * @author Donald A. Lobo <lobo@yahoo.com>
     * @copyright Donald A. Lobo (c) 2005
     * $Id$
     *
     */
    class FPDF
    {
        //Private properties
        var $page;               //current page number
        var $n;                  //current object number
        var $offsets;            //array of object offsets
        var $buffer;             //buffer holding in-memory PDF
        var $pages;              //array containing pages
        var $state;              //current document state
        var $compress;           //compression flag
        var $DefOrientation;     //default orientation
        var $CurOrientation;     //current orientation
        var $OrientationChanges; //array indicating orientation changes
        var $scaleFactor;                  //scale factor (number of points in user unit)
        var $fwPt,$fhPt;         //dimensions of page format in points
        var $fw,$fh;             //dimensions of page format in user unit
        var $wPt,$hPt;           //current dimensions of page in points
        var $w,$h;               //current dimensions of page in user unit
        var $marginLeft;            //left margin
        var $marginTop;            //top margin
        var $marginRight;            //right margin
        var $marginBreak;            //page break margin
        var $marginCell;            //cell margin
        var $x,$y;               //current position in user unit for cell positioning
        var $lasth;              //height of last cell printed
        var $LineWidth;          //line width in user unit
        var $CoreFonts;          //array of standard font names
        var $fonts;              //array of used fonts
        var $FontFiles;          //array of font files
        var $diffs;              //array of encoding differences
        var $images;             //array of used images
        var $PageLinks;          //array of links in pages
        var $links;              //array of internal links
        var $FontFamily;         //current font family
        var $FontStyle;          //current font style
        var $underline;          //underlining flag
        var $CurrentFont;        //current font info
        var $FontSizePt;         //current font size in points
        var $FontSize;           //current font size in user unit
        var $DrawColor;          //commands for drawing color
        var $FillColor;          //commands for filling color
        var $TextColor;          //commands for text color
        var $ColorFlag;          //indicates whether fill and text colors are different
        var $ws;                 //word spacing
        var $AutoPageBreak;      //automatic page breaking
        var $PageBreakTrigger;   //threshold used to trigger page breaks
        var $InFooter;           //flag set when processing footer
        var $ZoomMode;           //zoom display mode
        var $LayoutMode;         //layout display mode
        var $title;              //title
        var $subject;            //subject
        var $author;             //author
        var $keywords;           //keywords
        var $creator;            //creator
        var $AliasNbPages;       //alias for total number of pages
        var $PDFVersion;         //PDF version number

        /**
         * Constructor 
         *
         * @param orientation 
         * @param unit type of unit used we can define your label properties in inches by setting metric to 'in'
         * and printing in millimiter by setting unit to 'mm' in constructor.    
         * @param format Size of the paper for this sheet
         *
         * @access public
         */
        
        
        function FPDF($orientation='P',$unit='mm',$format='A4')
        {
            //Some checks
            $this->_dochecks();
            //Initialization of properties
            $this->page=0;
            $this->n=2;
            $this->buffer='';
            $this->pages=array();
            $this->OrientationChanges=array();
            $this->state=0;
            $this->fonts=array();
            $this->FontFiles=array();
            $this->diffs=array();
            $this->images=array();
            $this->links=array();
            $this->InFooter=false;
            $this->lasth=0;
            $this->FontFamily='';
            $this->FontStyle='';
            $this->FontSizePt=12;
            $this->underline=false;
            $this->DrawColor='0 G';
            $this->FillColor='0 g';
            $this->TextColor='0 g';
            $this->ColorFlag=false;
            $this->ws=0;
            //Standard fonts
            $this->CoreFonts=array('courier'=>'Courier','courierB'=>'Courier-Bold','courierI'=>'Courier-Oblique','courierBI'=>'Courier-BoldOblique',
                                   'helvetica'=>'Helvetica','helveticaB'=>'Helvetica-Bold','helveticaI'=>'Helvetica-Oblique','helveticaBI'=>'Helvetica-BoldOblique',
                                   'times'=>'Times-Roman','timesB'=>'Times-Bold','timesI'=>'Times-Italic','timesBI'=>'Times-BoldItalic',
                                   'symbol'=>'Symbol','zapfdingbats'=>'ZapfDingbats');
            //Scale factor
            if($unit=='pt')
                $this->scaleFactor=1;
            elseif($unit=='mm')
                $this->scaleFactor=72/25.4;
            elseif($unit=='cm')
                $this->scaleFactor=72/2.54;
            elseif($unit=='in')
                $this->scaleFactor=72;
            else
                $this->Error('Incorrect unit: '.$unit);
            //Page format
            if(is_string($format))
                {
                    $format=strtolower($format);
                    if($format=='a3')
                        $format=array(841.89,1190.55);
                    elseif($format=='a4')
                        $format=array(595.28,841.89);
                    elseif($format=='a5')
                        $format=array(420.94,595.28);
                    elseif($format=='letter')
                        $format=array(612,792);
                    elseif($format=='legal')
                        $format=array(612,1008);
                    else
                        $this->Error('Unknown page format: '.$format);
                    $this->fwPt=$format[0];
                    $this->fhPt=$format[1];
                }
            else
                {
                    $this->fwPt=$format[0]*$this->scaleFactor;
                    $this->fhPt=$format[1]*$this->scaleFactor;
                }
            $this->fw=$this->fwPt/$this->scaleFactor;
            $this->fh=$this->fhPt/$this->scaleFactor;
            //Page orientation
            $orientation=strtolower($orientation);
            if($orientation=='p' || $orientation=='portrait')
                {
                    $this->DefOrientation='P';
                    $this->wPt=$this->fwPt;
                    $this->hPt=$this->fhPt;
                }
            elseif($orientation=='l' || $orientation=='landscape')
                {
                    $this->DefOrientation='L';
                    $this->wPt=$this->fhPt;
                    $this->hPt=$this->fwPt;
                }
            else
                $this->Error('Incorrect orientation: '.$orientation);
            $this->CurOrientation=$this->DefOrientation;
            $this->w=$this->wPt/$this->scaleFactor;
            $this->h=$this->hPt/$this->scaleFactor;
            //Page margins (1 cm)
            $margin=28.35/$this->scaleFactor;
            $this->SetMargins($margin,$margin);
            //Interior cell margin (1 mm)
            $this->marginCell=$margin/10;
            //Line width (0.2 mm)
            $this->LineWidth=.567/$this->scaleFactor;
            //Automatic page break
            $this->SetAutoPageBreak(true,2*$margin);
            //Full width display mode
            $this->SetDisplayMode('fullwidth');
            //Enable compression
            $this->SetCompression(true);
            //Set default PDF version number
            $this->PDFVersion='1.3';
        }
        
        /*
         * function used to set the margins of a page
         *
         * @params left sets the left margin
         * @params right sets the right margin
         * @params top sets the top margin
         */
        function SetMargins($left,$top,$right=-1)
        {
            //Set left, top and right margins
            $this->marginLeft=$left;
            $this->marginTop=$top;
            if($right==-1)
                $right=$left;
            $this->marginRight=$right;
        }
        /*
         * function used to set the Left margin
         *
         * $margin int 
         */
        
        function SetLeftMargin($margin)
        {
            //Set left margin
            $this->marginLeft=$margin;
            if($this->page>0 && $this->x<$margin)
                $this->x=$margin;
        }
        /*
         * function used to set the Top margin
         *
         * $margin int 
         */
        
        function SetTopMargin($margin)
        {
            //Set top margin
            $this->marginTop=$margin;
        }
        
        /*
         * function used to set the Right margin
         *
         * $margin int 
         */
        function SetRightMargin($margin)
        {
            //Set right margin
            $this->marginRight=$margin;
        }
        
        /*
         * function to set auto page break mode and triggering margin 
         * $margin int 
         * 
         */
        function SetAutoPageBreak($auto,$margin=0)
        {
            $this->AutoPageBreak=$auto;
            $this->marginBreak=$margin;
            $this->PageBreakTrigger=$this->h-$margin;
        }
        /*
         * function to set display mode 
         * $zoom sets display mode
         * $layout sets layout display mode
         * 
         */
        
        function SetDisplayMode($zoom,$layout='continuous')
        {
            //Set display mode in viewer
            if($zoom=='fullpage' || $zoom=='fullwidth' || $zoom=='real' || $zoom=='default' || !is_string($zoom))
                $this->ZoomMode=$zoom;
            else
		$this->Error('Incorrect zoom display mode: '.$zoom);
            if($layout=='single' || $layout=='continuous' || $layout=='two' || $layout=='default')
                $this->LayoutMode=$layout;
            else
                $this->Error('Incorrect layout display mode: '.$layout);
        }
        
        /* 
         * function to set page compression
         *
         * $compress boolean
         */
        function SetCompression($compress)
        {
            if(function_exists('gzcompress'))
                $this->compress=$compress;
            else
                $this->compress=false;
        }
        
        /* 
         * function to set page Title
         *
         * $title string
         */
        function SetTitle($title)
        {
            $this->title=$title;
        }
       
        /* 
         * function to set page Header
         *
         */
        function Header()
        {
           
        }
        /* 
         * function to set page Footer
         *
         */
        function Footer()
        {

        }
       
        /* 
         *  function to set Subject of document
         *
         * $subject sets the subject string
         */
        function SetSubject($subject)
        {
            $this->subject=$subject;
        }
        /* 
         * function to set Author of document
         *
         * $author sets author name
         */
        function SetAuthor($author)
        {
            $this->author=$author;
        }
        /* 
         * function to set Keywords of document
         *
         * $keyword sets keyword
         */
        function SetKeywords($keywords)
        {
            $this->keywords=$keywords;
        }
        /*
         *  function to Creator of document
         *
         * $creator sets the creator of document
         */
        function SetCreator($creator)
        {
            $this->creator=$creator;
        }
        /*
         * function to Define an alias for total number of pages
         */
        function AliasNbPages($alias='{nb}')
        {
            $this->AliasNbPages=$alias;
        }
        /*
         * function to generate message Fatal error if any
         */
        function Error($msg)
        {
            
            die('<B>FPDF error: </B>'.$msg);
        }
        /*
         * function to begin document
         */
        function Open()
        {           
            $this->state=1;
        }
        /*
         * function to terminate document
         */
        function Close()
        {
            if($this->state==3)
                return;
            if($this->page==0)
                $this->AddPage();
            //Page footer
            $this->InFooter=true;
            $this->Footer();
            $this->InFooter=false;
            //Close page
            $this->_endpage();
            //Close document
            $this->_enddoc();
        }
        /*
         * function to Start a new page
         */ 
        function AddPage($orientation='')
        {
            if($this->state==0)
                $this->Open();
            $family=$this->FontFamily;
            $style=$this->FontStyle.($this->underline ? 'U' : '');
            $size=$this->FontSizePt;
            $lw=$this->LineWidth;
            $dc=$this->DrawColor;
            $fc=$this->FillColor;
            $tc=$this->TextColor;
            $cf=$this->ColorFlag;
            if($this->page>0)
                {
                    //Page footer
                    $this->InFooter=true;
                    $this->Footer();
                    $this->InFooter=false;
                    //Close page
                    $this->_endpage();
                }
            //Start new page
            $this->_beginpage($orientation);
            //Set line cap style to square
            $this->_out('2 J');
            //Set line width
            $this->LineWidth=$lw;
            $this->_out(sprintf('%.2f w',$lw*$this->scaleFactor));
            //Set font
            if($family)
                $this->SetFont($family,$style,$size);
            //Set colors
            $this->DrawColor=$dc;
            if($dc!='0 G')
                $this->_out($dc);
            $this->FillColor=$fc;
            if($fc!='0 g')
                $this->_out($fc);
            $this->TextColor=$tc;
            $this->ColorFlag=$cf;
            //Page header
            $this->Header();
            //Restore line width
            if($this->LineWidth!=$lw)
                {
                    $this->LineWidth=$lw;
                    $this->_out(sprintf('%.2f w',$lw*$this->scaleFactor));
                }
            //Restore font
            if($family)
                $this->SetFont($family,$style,$size);
            //Restore colors
            if($this->DrawColor!=$dc)
                {
                    $this->DrawColor=$dc;
                    $this->_out($dc);
                }
            if($this->FillColor!=$fc)
                {
                    $this->FillColor=$fc;
                    $this->_out($fc);
                }
            $this->TextColor=$tc;
            $this->ColorFlag=$cf;
        }
        
        /*
         * function to Get current page number
         */

        function PageNo()
        {
            return $this->page;
        }
        /*
         * function to Set color for all stroking operations
         *
         * @params $r,$g,$b red, green and blue color
         */
        function SetDrawColor($r,$g=-1,$b=-1)
        {
            if(($r==0 && $g==0 && $b==0) || $g==-1)
                $this->DrawColor=sprintf('%.3f G',$r/255);
            else
                $this->DrawColor=sprintf('%.3f %.3f %.3f RG',$r/255,$g/255,$b/255);
            if($this->page>0)
                $this->_out($this->DrawColor);
        }
        /*
         * function to Set color for all filling operations
         * @params $r,$g,$b red, green and blue color
         */
        function SetFillColor($r,$g=-1,$b=-1)
        {
            if(($r==0 && $g==0 && $b==0) || $g==-1)
                $this->FillColor=sprintf('%.3f g',$r/255);
            else
                $this->FillColor=sprintf('%.3f %.3f %.3f rg',$r/255,$g/255,$b/255);
            $this->ColorFlag=($this->FillColor!=$this->TextColor);
            if($this->page>0)
                $this->_out($this->FillColor);
        }
        /*
         * function to Set color for all filling operations
         * @params $r,$g,$b red, green and blue color
         */
        function SetTextColor($r,$g=-1,$b=-1)
        {
            //Set color for text
            if(($r==0 && $g==0 && $b==0) || $g==-1)
                $this->TextColor=sprintf('%.3f g',$r/255);
            else
                $this->TextColor=sprintf('%.3f %.3f %.3f rg',$r/255,$g/255,$b/255);
            $this->ColorFlag=($this->FillColor!=$this->TextColor);
        }
        /*
         * function to Get width of a string in the current font
         * @params $s string
         */
        
        function GetStringWidth($s)
        {
            $s=(string)$s;
            $cw=&$this->CurrentFont['cw'];
            $w=0;
            $l=strlen($s);
            for($i=0;$i<$l;$i++)
                $w+=$cw[$s{$i}];
            return $w*$this->FontSize/1000;
        }
        /*
         * function to Set line width
         *
         * $width float
         */
        function SetLineWidth($width)
        {
         
            $this->LineWidth=$width;
            if($this->page>0)
                $this->_out(sprintf('%.2f w',$width*$this->scaleFactor));
        }
        /*
         * function to draw a line
         *
         * @params $x1,$y1,$x2,y2 float 
         */
        function Line($x1,$y1,$x2,$y2)
        {
            $this->_out(sprintf('%.2f %.2f m %.2f %.2f l S',$x1*$this->scaleFactor,($this->h-$y1)*$this->scaleFactor,$x2*$this->scaleFactor,($this->h-$y2)*$this->scaleFactor));
        }
        /*
         * function to draw a rectangle
         *
         * @params $x1,$y1,$x2,y2 float 
         * 
         */
        function Rect($x,$y,$w,$h,$style='')
        {
            if($style=='F')
                $op='f';
            elseif($style=='FD' || $style=='DF')
                $op='B';
            else
                $op='S';
            $this->_out(sprintf('%.2f %.2f %.2f %.2f re %s',$x*$this->scaleFactor,($this->h-$y)*$this->scaleFactor,$w*$this->scaleFactor,-$h*$this->scaleFactor,$op));
        }
        /*
         *function to add TrueType or Type1 font
         *
         *@params $family font type ariel, helvetica if any
         *$style style oblique, bold itallics if any
         */
        function AddFont($family,$style='',$file='')
        {
            $family=strtolower($family);
            if($file=='')
                $file=str_replace(' ','',$family).strtolower($style).'.php';
            if($family=='arial')
                $family='helvetica';
            $style=strtoupper($style);
            if($style=='IB')
                $style='BI';
            $fontkey=$family.$style;
            if(isset($this->fonts[$fontkey]))
                $this->Error('Font already added: '.$family.' '.$style);
            include($this->_getfontpath().$file);
            if(!isset($name))
                $this->Error('Could not include font definition file');
            $i=count($this->fonts)+1;
            $this->fonts[$fontkey]=array('i'=>$i,'type'=>$type,'name'=>$name,'desc'=>$desc,'up'=>$up,'ut'=>$ut,'cw'=>$cw,'enc'=>$enc,'file'=>$file);
            if($diff)
                {
                    //Search existing encodings
                    $d=0;
                    $nb=count($this->diffs);
                    for($i=1;$i<=$nb;$i++)
                        {
                            if($this->diffs[$i]==$diff)
                                {
                                    $d=$i;
                                    break;
                                }
                        }
                    if($d==0)
                        {
                            $d=$nb+1;
                            $this->diffs[$d]=$diff;
                        }
                    $this->fonts[$fontkey]['diff']=$d;
                }
            if($file)
                {
                    if($type=='TrueType')
                        $this->FontFiles[$file]=array('length1'=>$originalsize);
                    else
                        $this->FontFiles[$file]=array('length1'=>$size1,'length2'=>$size2);
                }
        }
        /* 
         *   function to select a font; size given in points
         *@params $family font type ariel, helvetica if any
         */
        function SetFont($family,$style='',$size=0)
        {
            global $fpdf_charwidths;
            
            $family=strtolower($family);
            if($family=='')
                $family=$this->FontFamily;
            if($family=='arial')
                $family='helvetica';
            elseif($family=='symbol' || $family=='zapfdingbats')
                $style='';
            $style=strtoupper($style);
            if(strpos($style,'U')!==false)
                {
                    $this->underline=true;
                    $style=str_replace('U','',$style);
                }
            else
                $this->underline=false;
            if($style=='IB')
                $style='BI';
            if($size==0)
                $size=$this->FontSizePt;
            //Test if font is already selected
            if($this->FontFamily==$family && $this->FontStyle==$style && $this->FontSizePt==$size)
                return;
            //Test if used for the first time
            $fontkey=$family.$style;
            if(!isset($this->fonts[$fontkey]))
                {
                    //Check if one of the standard fonts
                    if(isset($this->CoreFonts[$fontkey]))
                        {
                            if(!isset($fpdf_charwidths[$fontkey]))
                                {
                                    //Load metric file
                                    $file=$family;
                                    if($family=='times' || $family=='helvetica')
                                        $file.=strtolower($style);
                                    include($this->_getfontpath().$file.'.php');
                                    if(!isset($fpdf_charwidths[$fontkey]))
                                        $this->Error('Could not include font metric file');
                                }
                            $i=count($this->fonts)+1;
                            $this->fonts[$fontkey]=array('i'=>$i,'type'=>'core','name'=>$this->CoreFonts[$fontkey],'up'=>-100,'ut'=>50,'cw'=>$fpdf_charwidths[$fontkey]);
                        }
                    else
                        $this->Error('Undefined font: '.$family.' '.$style);
                }
            //Select it
            $this->FontFamily=$family;
            $this->FontStyle=$style;
            $this->FontSizePt=$size;
            $this->FontSize=$size/$this->scaleFactor;
            $this->CurrentFont=&$this->fonts[$fontkey];
            if($this->page>0)
                $this->_out(sprintf('BT /F%d %.2f Tf ET',$this->CurrentFont['i'],$this->FontSizePt));
        }
        /* 
         * function to set font size
         * @params $size float 
         */
        function SetFontSize($size)
        {
            if($this->FontSizePt==$size)
                return;
            $this->FontSizePt=$size;
            $this->FontSize=$size/$this->scaleFactor;
            if($this->page>0)
                $this->_out(sprintf('BT /F%d %.2f Tf ET',$this->CurrentFont['i'],$this->FontSizePt));
        }
        /* 
         * function to  create a new internal link
         *  
         */
        function AddLink()
        {
            $n=count($this->links)+1;
            $this->links[$n]=array(0,0);
            return $n;
        }
        /* 
         * function to Set destination of internal link
         * 
         */

        function SetLink($link,$y=0,$page=-1)
        {
            if($y==-1)
                $y=$this->y;
            if($page==-1)
                $page=$this->page;
            $this->links[$link]=array($page,$y);
        }
        /*
         * function to Put a link on the page
         */
        function Link($x,$y,$w,$h,$link)
        {
            $this->PageLinks[$this->page][]=array($x*$this->scaleFactor,$this->hPt-$y*$this->scaleFactor,$w*$this->scaleFactor,$h*$this->scaleFactor,$link);
        }
        /*
         * function to output a string
         */
        function Text($x,$y,$txt)
        {
            $s=sprintf('BT %.2f %.2f Td (%s) Tj ET',$x*$this->scaleFactor,($this->h-$y)*$this->scaleFactor,$this->_escape($txt));
            if($this->underline && $txt!='')
                $s.=' '.$this->_dounderline($x,$y,$txt);
            if($this->ColorFlag)
                $s='q '.$this->TextColor.' '.$s.' Q';
            $this->_out($s);
        }
        /*
         *  function to Accept automatic page break or not
         */
        function AcceptPageBreak()
        {
            return $this->AutoPageBreak;
        }
        /*
         *  function to Output a cell
         */
        function Cell($w,$h=0,$txt='',$border=0,$ln=0,$align='',$fill=0,$link='')
        {
            $scaleFactor=$this->scaleFactor;
            if($this->y+$h>$this->PageBreakTrigger && !$this->InFooter && $this->AcceptPageBreak())
                {
                    //Automatic page break
                    $x=$this->x;
                    $ws=$this->ws;
                    if($ws>0)
                        {
                            $this->ws=0;
                            $this->_out('0 Tw');
                        }
                    $this->AddPage($this->CurOrientation);
                    $this->x=$x;
                    if($ws>0)
                        {
                            $this->ws=$ws;
                            $this->_out(sprintf('%.3f Tw',$ws*$scaleFactor));
                        }
                }
            if($w==0)
                $w=$this->w-$this->marginRight-$this->x;
            $s='';
            if($fill==1 || $border==1)
                {
                    if($fill==1)
                        $op=($border==1) ? 'B' : 'f';
                    else
                        $op='S';
                    $s=sprintf('%.2f %.2f %.2f %.2f re %s ',$this->x*$scaleFactor,($this->h-$this->y)*$scaleFactor,$w*$scaleFactor,-$h*$scaleFactor,$op);
                }
            if(is_string($border))
                {
                    $x=$this->x;
                    $y=$this->y;
                    if(strpos($border,'L')!==false)
                        $s.=sprintf('%.2f %.2f m %.2f %.2f l S ',$x*$scaleFactor,($this->h-$y)*$scaleFactor,$x*$scaleFactor,($this->h-($y+$h))*$scaleFactor);
                    if(strpos($border,'T')!==false)
                        $s.=sprintf('%.2f %.2f m %.2f %.2f l S ',$x*$scaleFactor,($this->h-$y)*$scaleFactor,($x+$w)*$scaleFactor,($this->h-$y)*$scaleFactor);
                    if(strpos($border,'R')!==false)
                        $s.=sprintf('%.2f %.2f m %.2f %.2f l S ',($x+$w)*$scaleFactor,($this->h-$y)*$scaleFactor,($x+$w)*$scaleFactor,($this->h-($y+$h))*$scaleFactor);
                    if(strpos($border,'B')!==false)
                        $s.=sprintf('%.2f %.2f m %.2f %.2f l S ',$x*$scaleFactor,($this->h-($y+$h))*$scaleFactor,($x+$w)*$scaleFactor,($this->h-($y+$h))*$scaleFactor);
                }
            if($txt!=='')
                {
                    if($align=='R')
                        $dx=$w-$this->marginCell-$this->GetStringWidth($txt);
                    elseif($align=='C')
                        $dx=($w-$this->GetStringWidth($txt))/2;
                    else
                        $dx=$this->marginCell;
                    if($this->ColorFlag)
                        $s.='q '.$this->TextColor.' ';
                    $txt2=str_replace(')','\\)',str_replace('(','\\(',str_replace('\\','\\\\',$txt)));
                    $s.=sprintf('BT %.2f %.2f Td (%s) Tj ET',($this->x+$dx)*$scaleFactor,($this->h-($this->y+.5*$h+.3*$this->FontSize))*$scaleFactor,$txt2);
                    if($this->underline)
                        $s.=' '.$this->_dounderline($this->x+$dx,$this->y+.5*$h+.3*$this->FontSize,$txt);
                    if($this->ColorFlag)
                        $s.=' Q';
                    if($link)
                        $this->Link($this->x+$dx,$this->y+.5*$h-.5*$this->FontSize,$this->GetStringWidth($txt),$this->FontSize,$link);
                }
            if($s)
                $this->_out($s);
            $this->lasth=$h;
            if($ln>0)
                {
                    //Go to next line
                    $this->y+=$h;
                    if($ln==1)
                        $this->x=$this->marginLeft;
                }
            else
                $this->x+=$w;
        }
        /* 
         * function to Output text with automatic or explicit line breaks
         */
        function MultiCell($w,$h,$txt,$border=0,$align='J',$fill=0)
        {
            $cw=&$this->CurrentFont['cw'];
            if($w==0)
                $w=$this->w-$this->marginRight-$this->x;
            $wmax=($w-2*$this->marginCell)*1000/$this->FontSize;
            $s=str_replace("\r",'',$txt);
            $nb=strlen($s);
            if($nb>0 && $s[$nb-1]=="\n")
                $nb--;
            $b=0;
            if($border)
                {
                    if($border==1)
                        {
                            $border='LTRB';
                            $b='LRT';
                            $b2='LR';
                        }
                    else
                        {
                            $b2='';
                            if(strpos($border,'L')!==false)
                                $b2.='L';
                            if(strpos($border,'R')!==false)
                                $b2.='R';
                            $b=(strpos($border,'T')!==false) ? $b2.'T' : $b2;
                        }
                }
            $sep=-1;
            $i=0;
            $j=0;
            $l=0;
            $ns=0;
            $nl=1;
            while($i<$nb)
                {
                    //Get next character
                    $c=$s{$i};
                    if($c=="\n")
                        {
                            //Explicit line break
                            if($this->ws>0)
                                {
                                    $this->ws=0;
                                    $this->_out('0 Tw');
                                }
                            $this->Cell($w,$h,substr($s,$j,$i-$j),$b,2,$align,$fill);
                            $i++;
                            $sep=-1;
                            $j=$i;
                            $l=0;
                            $ns=0;
                            $nl++;
                            if($border && $nl==2)
                                $b=$b2;
                            continue;
                        }
                    if($c==' ')
                        {
                            $sep=$i;
                            $ls=$l;
                            $ns++;
                        }
                    $l+=$cw[$c];
                    if($l>$wmax)
                        {
                            //Automatic line break
                            if($sep==-1)
                                {
                                    if($i==$j)
                                        $i++;
                                    if($this->ws>0)
                                        {
                                            $this->ws=0;
                                            $this->_out('0 Tw');
                                        }
                                    $this->Cell($w,$h,substr($s,$j,$i-$j),$b,2,$align,$fill);
                                }
                            else
                                {
                                    if($align=='J')
                                        {
                                            $this->ws=($ns>1) ? ($wmax-$ls)/1000*$this->FontSize/($ns-1) : 0;
                                            $this->_out(sprintf('%.3f Tw',$this->ws*$this->scaleFactor));
                                        }
                                    $this->Cell($w,$h,substr($s,$j,$sep-$j),$b,2,$align,$fill);
                                    $i=$sep+1;
                                }
                            $sep=-1;
                            $j=$i;
                            $l=0;
                            $ns=0;
                            $nl++;
                            if($border && $nl==2)
                                $b=$b2;
                        }
                    else
                        $i++;
                }
            //Last chunk
            if($this->ws>0)
                {
                    $this->ws=0;
                    $this->_out('0 Tw');
                }
            if($border && strpos($border,'B')!==false)
                $b.='B';
            $this->Cell($w,$h,substr($s,$j,$i-$j),$b,2,$align,$fill);
            $this->x=$this->marginLeft;
        }
        /*
         * function to Output text in flowing mode
         */
        function Write($h,$txt,$link='')
        {
            $cw=&$this->CurrentFont['cw'];
            $w=$this->w-$this->marginRight-$this->x;
            $wmax=($w-2*$this->marginCell)*1000/$this->FontSize;
            $s=str_replace("\r",'',$txt);
            $nb=strlen($s);
            $sep=-1;
            $i=0;
            $j=0;
            $l=0;
            $nl=1;
            while($i<$nb)
                {
                    //Get next character
                    $c=$s{$i};
                    if($c=="\n")
                        {
                            //Explicit line break
                            $this->Cell($w,$h,substr($s,$j,$i-$j),0,2,'',0,$link);
                            $i++;
                            $sep=-1;
                            $j=$i;
                            $l=0;
                            if($nl==1)
                                {
                                    $this->x=$this->marginLeft;
                                    $w=$this->w-$this->marginRight-$this->x;
                                    $wmax=($w-2*$this->marginCell)*1000/$this->FontSize;
                                }
                            $nl++;
                            continue;
                        }
                    if($c==' ')
                        $sep=$i;
                    $l+=$cw[$c];
                    if($l>$wmax)
                        {
                            //Automatic line break
                            if($sep==-1)
                                {
                                    if($this->x>$this->marginLeft)
                                        {
                                            //Move to next line
                                            $this->x=$this->marginLeft;
                                            $this->y+=$h;
                                            $w=$this->w-$this->marginRight-$this->x;
                                            $wmax=($w-2*$this->marginCell)*1000/$this->FontSize;
                                            $i++;
                                            $nl++;
                                            continue;
                                        }
                                    if($i==$j)
                                        $i++;
                                    $this->Cell($w,$h,substr($s,$j,$i-$j),0,2,'',0,$link);
                                }
                            else
                                {
                                    $this->Cell($w,$h,substr($s,$j,$sep-$j),0,2,'',0,$link);
                                    $i=$sep+1;
                                }
                            $sep=-1;
                            $j=$i;
                            $l=0;
                            if($nl==1)
                                {
                                    $this->x=$this->marginLeft;
                                    $w=$this->w-$this->marginRight-$this->x;
                                    $wmax=($w-2*$this->marginCell)*1000/$this->FontSize;
                                }
                            $nl++;
                        }
                    else
                        $i++;
                }
            //Last chunk
            if($i!=$j)
                $this->Cell($l/1000*$this->FontSize,$h,substr($s,$j),0,0,'',0,$link);
        }
        /*
         * function to Put an image on the page 
         */
        function Image($file,$x,$y,$w=0,$h=0,$type='',$link='')
        {
            if(!isset($this->images[$file]))
                {
                    //First use of image, get info
                    if($type=='')
                        {
                            $pos=strrpos($file,'.');
                            if(!$pos)
                                $this->Error('Image file has no extension and no type was specified: '.$file);
                            $type=substr($file,$pos+1);
                        }
                    $type=strtolower($type);
                    $mqr=get_magic_quotes_runtime();
                    set_magic_quotes_runtime(0);
                    if($type=='jpg' || $type=='jpeg')
                        $info=$this->_parsejpg($file);
                    elseif($type=='png')
                        $info=$this->_parsepng($file);
                    else
                        {
                            //Allow for additional formats
                            $mtd='_parse'.$type;
                            if(!method_exists($this,$mtd))
                                $this->Error('Unsupported image type: '.$type);
                            $info=$this->$mtd($file);
                        }
                    set_magic_quotes_runtime($mqr);
                    $info['i']=count($this->images)+1;
                    $this->images[$file]=$info;
                }
            else
                $info=$this->images[$file];
            //Automatic width and height calculation if needed
            if($w==0 && $h==0)
                {
                    //Put image at 72 dpi
                    $w=$info['w']/$this->scaleFactor;
                    $h=$info['h']/$this->scaleFactor;
                }
            if($w==0)
                $w=$h*$info['w']/$info['h'];
            if($h==0)
                $h=$w*$info['h']/$info['w'];
            $this->_out(sprintf('q %.2f 0 0 %.2f %.2f %.2f cm /I%d Do Q',$w*$this->scaleFactor,$h*$this->scaleFactor,$x*$this->scaleFactor,($this->h-($y+$h))*$this->scaleFactor,$info['i']));
            if($link)
                $this->Link($x,$y,$w,$h,$link);
        }
        /*
         * 	function to put Line feed; default value is last cell height
         */
        function Ln($h='')
        {
            
            $this->x=$this->marginLeft;
            if(is_string($h))
                $this->y+=$this->lasth;
            else
                $this->y+=$h;
        }
        /*
         * function to Get x position
         */
        function GetX()
        {
            return $this->x;
        }
        /*
         * function to set x position
         */
        function SetX($x)
        {
            if($x>=0)
                $this->x=$x;
            else
                $this->x=$this->w+$x;
        }
        /*
         * function to get y position
         */
        function GetY()
        {
            //Get y position
            return $this->y;
        }
        /*
         * function to Set y position and reset x
         */
        function SetY($y)
        {
            $this->x=$this->marginLeft;
            if($y>=0)
                $this->y=$y;
            else
                $this->y=$this->h+$y;
        }
        /*
         * function to Set x and y positions
         */
        function SetXY($x,$y)
        {
            $this->SetY($y);
            $this->SetX($x);
        }
        /*
         *function to send the Output PDF to some destination
         */
        function Output($name='',$dest='')
        {
            if($this->state<3)
                $this->Close();
            //Normalize parameters
            if(is_bool($dest))
                $dest=$dest ? 'D' : 'F';
            $dest=strtoupper($dest);
            if($dest=='')
                {
                    if($name=='')
                        {
                            $name='doc.pdf';
                            $dest='I';
                        }
                    else
                        $dest='F';
                }
            switch($dest)
                {
                case 'I':
                    //Send to standard output
                    if(ob_get_contents())
                        $this->Error('Some data has already been output, can\'t send PDF file');
                    if(php_sapi_name()!='cli')
                        {
                            //We send to a browser
                            header('Content-Type: application/pdf');
                            if(headers_sent())
                                $this->Error('Some data has already been output to browser, can\'t send PDF file');
                            header('Content-Length: '.strlen($this->buffer));
                            header('Content-disposition: inline; filename="'.$name.'"');
                        }
                    echo $this->buffer;
                    break;
                case 'D':
                    //Download file
                    if(ob_get_contents())
                        $this->Error('Some data has already been output, can\'t send PDF file');
                    if(isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'],'MSIE'))
                        header('Content-Type: application/force-download');
                    else
                        header('Content-Type: application/octet-stream');
                    if(headers_sent())
                        $this->Error('Some data has already been output to browser, can\'t send PDF file');
                    header('Content-Length: '.strlen($this->buffer));
                    header('Content-disposition: attachment; filename="'.$name.'"');
                    echo $this->buffer;
                    break;
                case 'F':
                    //Save to local file
                    $f=fopen($name,'wb');
                    if(!$f)
                        $this->Error('Unable to create output file: '.$name);
                    fwrite($f,$this->buffer,strlen($this->buffer));
                    fclose($f);
                    break;
                case 'S':
                    //Return as a string
                    return $this->buffer;
                default:
                    $this->Error('Incorrect output destination: '.$dest);
                }
            return '';
        }
        
        /*
         * function to Check for locale-related bug                                                                             
         * Protected methods                               
         *                                                                              
         **/
        function _dochecks()
        {
            if(1.1==1)
                $this->Error('Don\'t alter the locale before including class file');
            //Check for decimal separator
            if(sprintf('%.1f',1.0)!='1.0')
                setlocale(LC_NUMERIC,'C');
        }
        
        function _getfontpath()
        {
            if(!defined('FPDF_FONTPATH') && is_dir(dirname(__FILE__).'/font'))
                define('FPDF_FONTPATH',dirname(__FILE__).'/font/');
            return defined('FPDF_FONTPATH') ? FPDF_FONTPATH : '';
        }
        
        function _putpages()
        {
            $nb=$this->page;
            if(!empty($this->AliasNbPages))
                {
                    //Replace number of pages
                    for($n=1;$n<=$nb;$n++)
                        $this->pages[$n]=str_replace($this->AliasNbPages,$nb,$this->pages[$n]);
                }
            if($this->DefOrientation=='P')
                {
                    $wPt=$this->fwPt;
                    $hPt=$this->fhPt;
                }
            else
                {
                    $wPt=$this->fhPt;
                    $hPt=$this->fwPt;
                }
            $filter=($this->compress) ? '/Filter /FlateDecode ' : '';
            for($n=1;$n<=$nb;$n++)
                {
                    //Page
                    $this->_newobj();
                    $this->_out('<</Type /Page');
                    $this->_out('/Parent 1 0 R');
                    if(isset($this->OrientationChanges[$n]))
                        $this->_out(sprintf('/MediaBox [0 0 %.2f %.2f]',$hPt,$wPt));
                    $this->_out('/Resources 2 0 R');
                    if(isset($this->PageLinks[$n]))
                        {
                            //Links
                            $annots='/Annots [';
                            foreach($this->PageLinks[$n] as $pl)
                                {
                                    $rect=sprintf('%.2f %.2f %.2f %.2f',$pl[0],$pl[1],$pl[0]+$pl[2],$pl[1]-$pl[3]);
                                    $annots.='<</Type /Annot /Subtype /Link /Rect ['.$rect.'] /Border [0 0 0] ';
                                    if(is_string($pl[4]))
                                        $annots.='/A <</S /URI /URI '.$this->_textstring($pl[4]).'>>>>';
                                    else
                                        {
                                            $l=$this->links[$pl[4]];
                                            $h=isset($this->OrientationChanges[$l[0]]) ? $wPt : $hPt;
                                            $annots.=sprintf('/Dest [%d 0 R /XYZ 0 %.2f null]>>',1+2*$l[0],$h-$l[1]*$this->k);
                                        }
                                }
                            $this->_out($annots.']');
                        }
                    $this->_out('/Contents '.($this->n+1).' 0 R>>');
                    $this->_out('endobj');
                    //Page content
                    $p=($this->compress) ? gzcompress($this->pages[$n]) : $this->pages[$n];
                    $this->_newobj();
                    $this->_out('<<'.$filter.'/Length '.strlen($p).'>>');
                    $this->_putstream($p);
                    $this->_out('endobj');
                }
            //Pages root
            $this->offsets[1]=strlen($this->buffer);
            $this->_out('1 0 obj');
            $this->_out('<</Type /Pages');
            $kids='/Kids [';
            for($i=0;$i<$nb;$i++)
                $kids.=(3+2*$i).' 0 R ';
            $this->_out($kids.']');
            $this->_out('/Count '.$nb);
            $this->_out(sprintf('/MediaBox [0 0 %.2f %.2f]',$wPt,$hPt));
            $this->_out('>>');
            $this->_out('endobj');
        }

        function _putfonts()
        {
            $nf=$this->n;
            foreach($this->diffs as $diff)
                {
                    //Encodings
                    $this->_newobj();
                    $this->_out('<</Type /Encoding /BaseEncoding /WinAnsiEncoding /Differences ['.$diff.']>>');
                    $this->_out('endobj');
                }
            $mqr=get_magic_quotes_runtime();
            set_magic_quotes_runtime(0);
            foreach($this->FontFiles as $file=>$info)
                {
                    //Font file embedding
                    $this->_newobj();
                    $this->FontFiles[$file]['n']=$this->n;
                    $font='';
                    $f=fopen($this->_getfontpath().$file,'rb',1);
                    if(!$f)
                        $this->Error('Font file not found');
                    while(!feof($f))
                        $font.=fread($f,8192);
                    fclose($f);
                    $compressed=(substr($file,-2)=='.z');
                    if(!$compressed && isset($info['length2']))
                        {
                            $header=(ord($font{0})==128);
                            if($header)
                                {
                                    //Strip first binary header
                                    $font=substr($font,6);
                                }
                            if($header && ord($font{$info['length1']})==128)
                                {
                                    //Strip second binary header
                                    $font=substr($font,0,$info['length1']).substr($font,$info['length1']+6);
                                }
                        }
                    $this->_out('<</Length '.strlen($font));
                    if($compressed)
                        $this->_out('/Filter /FlateDecode');
                    $this->_out('/Length1 '.$info['length1']);
                    if(isset($info['length2']))
                        $this->_out('/Length2 '.$info['length2'].' /Length3 0');
                    $this->_out('>>');
                    $this->_putstream($font);
                    $this->_out('endobj');
                }
            set_magic_quotes_runtime($mqr);
            foreach($this->fonts as $scaleFactor=>$font)
                {
                    //Font objects
                    $this->fonts[$scaleFactor]['n']=$this->n+1;
                    $type=$font['type'];
                    $name=$font['name'];
                    if($type=='core')
                        {
                            //Standard font
                            $this->_newobj();
                            $this->_out('<</Type /Font');
                            $this->_out('/BaseFont /'.$name);
                            $this->_out('/Subtype /Type1');
                            if($name!='Symbol' && $name!='ZapfDingbats')
                                $this->_out('/Encoding /WinAnsiEncoding');
                            $this->_out('>>');
                            $this->_out('endobj');
                        }
                    elseif($type=='Type1' || $type=='TrueType')
                        {
                            //Additional Type1 or TrueType font
                            $this->_newobj();
                            $this->_out('<</Type /Font');
                            $this->_out('/BaseFont /'.$name);
                            $this->_out('/Subtype /'.$type);
                            $this->_out('/FirstChar 32 /LastChar 255');
                            $this->_out('/Widths '.($this->n+1).' 0 R');
                            $this->_out('/FontDescriptor '.($this->n+2).' 0 R');
                            if($font['enc'])
                                {
                                    if(isset($font['diff']))
                                        $this->_out('/Encoding '.($nf+$font['diff']).' 0 R');
                                    else
                                        $this->_out('/Encoding /WinAnsiEncoding');
                                }
                            $this->_out('>>');
                            $this->_out('endobj');
                            //Widths
                            $this->_newobj();
                            $cw=&$font['cw'];
                            $s='[';
                            for($i=32;$i<=255;$i++)
                                $s.=$cw[chr($i)].' ';
                            $this->_out($s.']');
                            $this->_out('endobj');
                            //Descriptor
                            $this->_newobj();
                            $s='<</Type /FontDescriptor /FontName /'.$name;
                            foreach($font['desc'] as $scaleFactor=>$v)
                                $s.=' /'.$scaleFactor.' '.$v;
                            $file=$font['file'];
                            if($file)
                                $s.=' /FontFile'.($type=='Type1' ? '' : '2').' '.$this->FontFiles[$file]['n'].' 0 R';
                            $this->_out($s.'>>');
                            $this->_out('endobj');
                        }
                    else
                        {
                            //Allow for additional types
                            $mtd='_put'.strtolower($type);
                            if(!method_exists($this,$mtd))
                                $this->Error('Unsupported font type: '.$type);
                            $this->$mtd($font);
                        }
                }
        }
        
        function _putimages()
        {
            $filter=($this->compress) ? '/Filter /FlateDecode ' : '';
            reset($this->images);
            while(list($file,$info)=each($this->images))
                {
                    $this->_newobj();
                    $this->images[$file]['n']=$this->n;
                    $this->_out('<</Type /XObject');
                    $this->_out('/Subtype /Image');
                    $this->_out('/Width '.$info['w']);
                    $this->_out('/Height '.$info['h']);
                    if($info['cs']=='Indexed')
                        $this->_out('/ColorSpace [/Indexed /DeviceRGB '.(strlen($info['pal'])/3-1).' '.($this->n+1).' 0 R]');
                    else
                        {
                            $this->_out('/ColorSpace /'.$info['cs']);
                            if($info['cs']=='DeviceCMYK')
                                $this->_out('/Decode [1 0 1 0 1 0 1 0]');
                        }
                    $this->_out('/BitsPerComponent '.$info['bpc']);
                    if(isset($info['f']))
                        $this->_out('/Filter /'.$info['f']);
                    if(isset($info['parms']))
                        $this->_out($info['parms']);
                    if(isset($info['trns']) && is_array($info['trns']))
                        {
                            $trns='';
                            for($i=0;$i<count($info['trns']);$i++)
                                $trns.=$info['trns'][$i].' '.$info['trns'][$i].' ';
                            $this->_out('/Mask ['.$trns.']');
                        }
                    $this->_out('/Length '.strlen($info['data']).'>>');
                    $this->_putstream($info['data']);
                    unset($this->images[$file]['data']);
                    $this->_out('endobj');
                    //Palette
                    if($info['cs']=='Indexed')
                        {
                            $this->_newobj();
                            $pal=($this->compress) ? gzcompress($info['pal']) : $info['pal'];
                            $this->_out('<<'.$filter.'/Length '.strlen($pal).'>>');
                            $this->_putstream($pal);
                            $this->_out('endobj');
                        }
                }
        }
        
        function _putxobjectdict()
        {
            foreach($this->images as $image)
                $this->_out('/I'.$image['i'].' '.$image['n'].' 0 R');
        }
        
        function _putresourcedict()
        {
            $this->_out('/ProcSet [/PDF /Text /ImageB /ImageC /ImageI]');
            $this->_out('/Font <<');
            foreach($this->fonts as $font)
                $this->_out('/F'.$font['i'].' '.$font['n'].' 0 R');
            $this->_out('>>');
            $this->_out('/XObject <<');
            $this->_putxobjectdict();
            $this->_out('>>');
        }
        
        function _putresources()
        {
            $this->_putfonts();
            $this->_putimages();
            //Resource dictionary
            $this->offsets[2]=strlen($this->buffer);
            $this->_out('2 0 obj');
            $this->_out('<<');
            $this->_putresourcedict();
            $this->_out('>>');
            $this->_out('endobj');
        }
        
        function _putinfo()
        {
            $this->_out('/Producer '.$this->_textstring('FPDF '.FPDF_VERSION));
            if(!empty($this->title))
                $this->_out('/Title '.$this->_textstring($this->title));
            if(!empty($this->subject))
                $this->_out('/Subject '.$this->_textstring($this->subject));
            if(!empty($this->author))
                $this->_out('/Author '.$this->_textstring($this->author));
            if(!empty($this->keywords))
                $this->_out('/Keywords '.$this->_textstring($this->keywords));
            if(!empty($this->creator))
                $this->_out('/Creator '.$this->_textstring($this->creator));
            $this->_out('/CreationDate '.$this->_textstring('D:'.date('YmdHis')));
        }
        
        function _putcatalog()
        {
            $this->_out('/Type /Catalog');
            $this->_out('/Pages 1 0 R');
            if($this->ZoomMode=='fullpage')
                $this->_out('/OpenAction [3 0 R /Fit]');
            elseif($this->ZoomMode=='fullwidth')
                $this->_out('/OpenAction [3 0 R /FitH null]');
            elseif($this->ZoomMode=='real')
                $this->_out('/OpenAction [3 0 R /XYZ null null 1]');
            elseif(!is_string($this->ZoomMode))
                $this->_out('/OpenAction [3 0 R /XYZ null null '.($this->ZoomMode/100).']');
            if($this->LayoutMode=='single')
                $this->_out('/PageLayout /SinglePage');
            elseif($this->LayoutMode=='continuous')
                $this->_out('/PageLayout /OneColumn');
            elseif($this->LayoutMode=='two')
                $this->_out('/PageLayout /TwoColumnLeft');
        }
        
        function _putheader()
        {
            $this->_out('%PDF-'.$this->PDFVersion);
        }
        
        function _puttrailer()
        {
            $this->_out('/Size '.($this->n+1));
            $this->_out('/Root '.$this->n.' 0 R');
            $this->_out('/Info '.($this->n-1).' 0 R');
        }
        
        function _enddoc()
        {
            $this->_putheader();
            $this->_putpages();
            $this->_putresources();
            //Info
            $this->_newobj();
            $this->_out('<<');
            $this->_putinfo();
            $this->_out('>>');
            $this->_out('endobj');
            //Catalog
            $this->_newobj();
            $this->_out('<<');
            $this->_putcatalog();
            $this->_out('>>');
            $this->_out('endobj');
            //Cross-ref
            $o=strlen($this->buffer);
            $this->_out('xref');
            $this->_out('0 '.($this->n+1));
            $this->_out('0000000000 65535 f ');
            for($i=1;$i<=$this->n;$i++)
                $this->_out(sprintf('%010d 00000 n ',$this->offsets[$i]));
            //Trailer
            $this->_out('trailer');
            $this->_out('<<');
            $this->_puttrailer();
            $this->_out('>>');
            $this->_out('startxref');
            $this->_out($o);
            $this->_out('%%EOF');
            $this->state=3;
        }
        
        function _beginpage($orientation)
        {
            $this->page++;
            $this->pages[$this->page]='';
            $this->state=2;
            $this->x=$this->marginLeft;
            $this->y=$this->marginTop;
            $this->FontFamily='';
            //Page orientation
            if(!$orientation)
                $orientation=$this->DefOrientation;
            else
                {
                    $orientation=strtoupper($orientation{0});
                    if($orientation!=$this->DefOrientation)
                        $this->OrientationChanges[$this->page]=true;
                }
            if($orientation!=$this->CurOrientation)
                {
                    //Change orientation
                    if($orientation=='P')
                        {
                            $this->wPt=$this->fwPt;
                            $this->hPt=$this->fhPt;
                            $this->w=$this->fw;
                            $this->h=$this->fh;
                        }
                    else
                        {
                            $this->wPt=$this->fhPt;
                            $this->hPt=$this->fwPt;
                            $this->w=$this->fh;
                            $this->h=$this->fw;
                        }
                    $this->PageBreakTrigger=$this->h-$this->marginBreak;
                    $this->CurOrientation=$orientation;
                }
        }
        
        function _endpage()
        {
            //End of page contents
            $this->state=1;
        }
        
        function _newobj()
        {
            //Begin a new object
            $this->n++;
            $this->offsets[$this->n]=strlen($this->buffer);
            $this->_out($this->n.' 0 obj');
        }
        
        function _dounderline($x,$y,$txt)
        {
            //Underline text
            $up=$this->CurrentFont['up'];
            $ut=$this->CurrentFont['ut'];
            $w=$this->GetStringWidth($txt)+$this->ws*substr_count($txt,' ');
            return sprintf('%.2f %.2f %.2f %.2f re f',$x*$this->scaleFactor,($this->h-($y-$up/1000*$this->FontSize))*$this->scaleFactor,$w*$this->scaleFactor,-$ut/1000*$this->FontSizePt);
        }
        
        function _parsejpg($file)
        {
            //Extract info from a JPEG file
            $a=GetImageSize($file);
            if(!$a)
                $this->Error('Missing or incorrect image file: '.$file);
            if($a[2]!=2)
                $this->Error('Not a JPEG file: '.$file);
            if(!isset($a['channels']) || $a['channels']==3)
                $colspace='DeviceRGB';
            elseif($a['channels']==4)
                $colspace='DeviceCMYK';
            else
                $colspace='DeviceGray';
            $bpc=isset($a['bits']) ? $a['bits'] : 8;
            //Read whole file
            $f=fopen($file,'rb');
            $data='';
            while(!feof($f))
                $data.=fread($f,4096);
            fclose($f);
            return array('w'=>$a[0],'h'=>$a[1],'cs'=>$colspace,'bpc'=>$bpc,'f'=>'DCTDecode','data'=>$data);
        }
        
        function _parsepng($file)
        {
            //Extract info from a PNG file
            $f=fopen($file,'rb');
            if(!$f)
                $this->Error('Can\'t open image file: '.$file);
            //Check signature
            if(fread($f,8)!=chr(137).'PNG'.chr(13).chr(10).chr(26).chr(10))
                $this->Error('Not a PNG file: '.$file);
            //Read header chunk
            fread($f,4);
            if(fread($f,4)!='IHDR')
                $this->Error('Incorrect PNG file: '.$file);
            $w=$this->_freadint($f);
            $h=$this->_freadint($f);
            $bpc=ord(fread($f,1));
            if($bpc>8)
                $this->Error('16-bit depth not supported: '.$file);
            $ct=ord(fread($f,1));
            if($ct==0)
                $colspace='DeviceGray';
            elseif($ct==2)
                $colspace='DeviceRGB';
            elseif($ct==3)
                $colspace='Indexed';
            else
                $this->Error('Alpha channel not supported: '.$file);
            if(ord(fread($f,1))!=0)
                $this->Error('Unknown compression method: '.$file);
            if(ord(fread($f,1))!=0)
                $this->Error('Unknown filter method: '.$file);
            if(ord(fread($f,1))!=0)
                $this->Error('Interlacing not supported: '.$file);
            fread($f,4);
            $parms='/DecodeParms <</Predictor 15 /Colors '.($ct==2 ? 3 : 1).' /BitsPerComponent '.$bpc.' /Columns '.$w.'>>';
            //Scan chunks looking for palette, transparency and image data
            $pal='';
            $trns='';
            $data='';
            do
                {
                    $n=$this->_freadint($f);
                    $type=fread($f,4);
                    if($type=='PLTE')
                        {
                            //Read palette
                            $pal=fread($f,$n);
                            fread($f,4);
                        }
                    elseif($type=='tRNS')
                        {
                            //Read transparency info
                            $t=fread($f,$n);
                            if($ct==0)
                                $trns=array(ord(substr($t,1,1)));
                            elseif($ct==2)
                                $trns=array(ord(substr($t,1,1)),ord(substr($t,3,1)),ord(substr($t,5,1)));
                            else
                                {
                                    $pos=strpos($t,chr(0));
                                    if($pos!==false)
                                        $trns=array($pos);
                                }
                            fread($f,4);
                        }
                    elseif($type=='IDAT')
                        {
                            //Read image data block
                            $data.=fread($f,$n);
                            fread($f,4);
                        }
                    elseif($type=='IEND')
                        break;
                    else
                        fread($f,$n+4);
                }
            while($n);
            if($colspace=='Indexed' && empty($pal))
                $this->Error('Missing palette in '.$file);
            fclose($f);
            return array('w'=>$w,'h'=>$h,'cs'=>$colspace,'bpc'=>$bpc,'f'=>'FlateDecode','parms'=>$parms,'pal'=>$pal,'trns'=>$trns,'data'=>$data);
        }
        
        function _freadint($f)
        {
            //Read a 4-byte integer from file
            $a=unpack('Ni',fread($f,4));
            return $a['i'];
        }
        
        function _textstring($s)
        {
            //Format a text string
            return '('.$this->_escape($s).')';
        }
        
        function _escape($s)
        {
            //Add \ before \, ( and )
            return str_replace(')','\\)',str_replace('(','\\(',str_replace('\\','\\\\',$s)));
        }
        
        function _putstream($s)
        {
            $this->_out('stream');
            $this->_out($s);
            $this->_out('endstream');
        }
        
        function _out($s)
        {
            //Add a line to the document
            if($this->state==2)
                $this->pages[$this->page].=$s."\n";
            else
                $this->buffer.=$s."\n";
        }
        //End of class
    }
    
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.4                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                  |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the Affero General Public License Version 1,    |
 | March 2002.                                                        |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the Affero General Public License for more details.            |
 |                                                                    |
 | You should have received a copy of the Affero General Public       |
 | License along with this program; if not, contact the Social Source |
 | Foundation at info[AT]socialsourcefoundation[DOT]org.  If you have |
 | questions about the Affero General Public License or the licensing |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | at http://www.openngo.org/faqs/licensing.html                       |
 +--------------------------------------------------------------------+
*/
 /**
     *  Class to print labels in Avery or custom formats
     * functionality and smarts to the base PDF_Label.
     *
     * @author Donald A. Lobo <lobo@yahoo.com>
     * @copyright Donald A. Lobo (c) 2005
     * 
     *
     */
require_once ('fpdf.php');
class PDF_Label extends FPDF {

    // Private properties
    var $_Avery_Name    = '';                 // Name of format
    var $_Margin_Left    = 0;                 // Left margin of labels
    var $_Margin_Top    = 0;                  // Top margin of labels
    var $_X_Space         = 0;                // Horizontal space between 2 labels
    var $_Y_Space         = 0;                // Vertical space between 2 labels
    var $_X_Number         = 0;               // Number of labels horizontally
    var $_Y_Number         = 0;               // Number of labels vertically
    var $_Width         = 0;                  // Width of label
    var $_Height         = 0;                 // Height of label
    var $_Char_Size        = 10;              // Character size
    var $_Line_Height    = 10;                // Default line height
    var $_Metric         = 'mm';              // Type of metric for labels.. Will help to calculate good values
    var $_Metric_Doc     = 'mm';              // Type of metric for the document
    //    var $_Font_Name        = 'Arial';       // Name of the font
    var $_Font_Name        = 'symbol';        // Name of the font
    var $_COUNTX = 1;
    var $_COUNTY = 1;
    
    // Listing of labels size
    protected  $_Avery_Labels = array (
                                       '5160'=>array('name'=>'5160', 'paper-size'=>'letter', 'metric'=>'mm',
                                                     'marginLeft'=>1.762, 'marginTop'=>10.7, 'NX'=>3, 'NY'=>10,
                                                     'SpaceX'=>3.175, 'SpaceY'=>0, 'width'=>66.675, 'height'=>25.4,
                                                     'font-size'=>8),
                                       '5161'=>array('name'=>'5161', 'paper-size'=>'letter', 'metric'=>'mm',  
                                                     'marginLeft'=>0.967, 'marginTop'=>10.7, 'NX'=>2, 'NY'=>10, 
                                                     'SpaceX'=>3.967, 'SpaceY'=>0, 'width'=>101.6,
                                                     'height'=>25.4, 'font-size'=>8),
                                       '5162'=>array('name'=>'5162', 'paper-size'=>'letter', 'metric'=>'mm', 
                                                     'marginLeft'=>0.97, 'marginTop'=>20.224, 'NX'=>2, 'NY'=>7, 
                                                     'SpaceX'=>4.762, 'SpaceY'=>0, 'width'=>100.807, 
                                                     'height'=>35.72, 'font-size'=>8),
                                       '5163'=>array('name'=>'5163', 'paper-size'=>'letter', 'metric'=>'mm',
                                                     'marginLeft'=>1.762,'marginTop'=>10.7, 'NX'=>2,
                                                     'NY'=>5, 'SpaceX'=>3.175, 'SpaceY'=>0, 'width'=>101.6,
                                                     'height'=>50.8, 'font-size'=>8),
                                       '5164'=>array('name'=>'5164', 'paper-size'=>'letter', 'metric'=>'in',
                                                     'marginLeft'=>0.148, 'marginTop'=>0.5, 'NX'=>2, 'NY'=>3, 
                                                     'SpaceX'=>0.2031, 'SpaceY'=>0, 'width'=>4.0, 'height'=>3.33,
                                                     'font-size'=>12),
                                       '8600'=>array('name'=>'8600', 'paper-size'=>'letter', 'metric'=>'mm',
                                                     'marginLeft'=>7.1, 'marginTop'=>19, 'NX'=>3, 'NY'=>10,
                                                     'SpaceX'=>9.5, 'SpaceY'=>3.1, 'width'=>66.6,
                                                     'height'=>25.4, 'font-size'=>8),
                                       'L7163'=>array('name'=>'L7163', 'paper-size'=>'A4', 'metric'=>'mm', 'marginLeft'=>5,
                                                      'marginTop'=>15, 'NX'=>2, 'NY'=>7, 'SpaceX'=>25, 'SpaceY'=>0,
                                                      'width'=>99.1, 'height'=>38.1, 'font-size'=>9)
                                       );
   
    /**
     * Constructor 
     *
     * @param $format type of label ($_AveryValues)
     * @param unit type of unit used we can define your label properties in inches by setting metric to 'in'
     *
     * @access public
     */

   function PDF_Label ($format, $unit='mm') {
       if (is_array($format)) {
           // Custom format
           $Tformat = $format;
       } else {
           // Avery format
           $Tformat = $this->_Avery_Labels[$format];
       }
       
       parent::FPDF('P', $Tformat['metric'], $Tformat['paper-size']);
       $this->_Set_Format($Tformat);
       $this->Set_Font_Name('Arial');
       $this->SetMargins(0,0);
       $this->SetAutoPageBreak(false);
       
       $this->_Metric_Doc = $unit;
       // Start at the given label position
       //  if ($posX > 1) $posX--; else $posX=0;
       //         if ($posY > 1) $posY--; else $posY=0;
       //         if ($posX >=  $this->_X_Number) $posX =  $this->_X_Number-1;
       //         if ($posY >=  $this->_Y_Number) $posY =  $this->_Y_Number-1;
       //         $this->_COUNTX = $posX;
       //         $this->_COUNTY = $posY;
       
       if($format == $_Avery_Labels['name']){
           if ($_Avery_Labels['marginLeft'] > 1) $_Avery_Labels['marginLeft']--; else $_Avery_Labels['marginLeft']=0;
           if ($_Avery_Labels['marginTop'] > 1) $_Avery_Labels['marginTop']--; else $_Avery_Labels['marginTop']=0;
           if ($_Avery_Labels['marginLeft'] >=  $this->_X_Number) $_Avery_Labels['marginLeft'] =  $this->_X_Number-1;
           if ($_Avery_Labels['marginTop'] >=  $this->_Y_Number) $_Avery_Labels['marginTop'] =  $this->_Y_Number-1;
           $this->_COUNTX = $_Avery_Labels['marginLeft'];
           $this->_COUNTY = $_Avery_Labels['marginTop'];
       }
   }
    
   /*
    * function to convert units (in to mm, mm to in)
    *
    */ 
    function _Convert_Metric ($value, $src, $dest) {
        if ($src != $dest) {
            $tab['in'] = 39.37008;
            $tab['mm'] = 1000;
            return $value * $tab[$dest] / $tab[$src];
        } else {
            return $value;
        }
    }
    /*
     * function to Give the height for a char size given.
     */
    function _Get_Height_Chars($pt) {
        // Array matching character sizes and line heights
        $_Table_Hauteur_Chars = array(6=>2, 7=>2.5, 8=>3, 9=>4, 10=>5, 11=>6, 12=>7, 13=>8, 14=>9, 15=>10);
        if (in_array($pt, array_keys($_Table_Hauteur_Chars))) {
            return $_Table_Hauteur_Chars[$pt];
        } else {
            return 100; // There is a prob..
        }
    }
    /*
     * function to convert units (in to mm, mm to in)
     * $format Type of $_Avery_Name
     */ 
    function _Set_Format($format) {
        $this->_Metric         = $format['metric'];
        $this->_Avery_Name     = $format['name'];
        $this->_Margin_Left    = $this->_Convert_Metric ($format['marginLeft'], $this->_Metric, $this->_Metric_Doc);
        $this->_Margin_Top    = $this->_Convert_Metric ($format['marginTop'], $this->_Metric, $this->_Metric_Doc);
        $this->_X_Space     = $this->_Convert_Metric ($format['SpaceX'], $this->_Metric, $this->_Metric_Doc);
        $this->_Y_Space     = $this->_Convert_Metric ($format['SpaceY'], $this->_Metric, $this->_Metric_Doc);
        $this->_X_Number     = $format['NX'];
        $this->_Y_Number     = $format['NY'];
        $this->_Width         = $this->_Convert_Metric ($format['width'], $this->_Metric, $this->_Metric_Doc);
        $this->_Height         = $this->_Convert_Metric ($format['height'], $this->_Metric, $this->_Metric_Doc);
        $this->Set_Font_Size($format['font-size']);
    }
    /*
     * function to set the character size
     * $pt weight of character
     */
    function Set_Font_Size($pt) {
        if ($pt > 3) {
            $this->_Char_Size = $pt;
            $this->_Line_Height = $this->_Get_Height_Chars($pt);
            $this->SetFontSize($this->_Char_Size);
        }
    }
    /*
     * Method to change font name
     *
     * $fontname name of font 
     */
    function Set_Font_Name($fontname) {
        if ($fontname != '') {
            $this->_Font_Name = $fontname;
            $this->SetFont($this->_Font_Name);
        }
    }
    
    /*
     * function to Print a label
     */
    function Add_PDF_Label($texte) {
        // We are in a new page, then we must add a page
        if (($this->_COUNTX ==0) and ($this->_COUNTY==0)) {
            $this->AddPage();
        }
        
        $_PosX = $this->_Margin_Left+($this->_COUNTX*($this->_Width+$this->_X_Space));
        $_PosY = $this->_Margin_Top+($this->_COUNTY*($this->_Height+$this->_Y_Space));
        $this->SetXY($_PosX+3, $_PosY+3);
        $this->MultiCell($this->_Width, $this->_Line_Height, $texte);
        $this->_COUNTY++;
        
        if ($this->_COUNTY == $this->_Y_Number) {
            // End of column reached, we start a new one
            $this->_COUNTX++;
            $this->_COUNTY=0;
        }
        
        if ($this->_COUNTX == $this->_X_Number) {
            // Page full, we start a new one
            $this->_COUNTX=0;
            $this->_COUNTY=0;
        }
    }
    
}
//Handle special IE contype request
if(isset($_SERVER['HTTP_USER_AGENT']) && $_SERVER['HTTP_USER_AGENT']=='contype')
{
	header('Content-Type: application/pdf');
	exit;
}
}
?>
