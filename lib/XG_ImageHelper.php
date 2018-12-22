<?php

/**
 * Useful functions for working with image files.
 */
class XG_ImageHelper {

    /*********************************************/
    /* Fonction: ImageCreateFromBMP              */
    /* Author:   DHKold                          */
    /* Contact:  admin@dhkold.com                */
    /* Date:     The 15th of June 2005           */
    /* Version:  2.0B                            */
    /*********************************************/
    function ImageCreateFromBMP($filename) {
        //Ouverture du fichier en mode binaire
        if (! $f1 = fopen($filename,"rb")) {
            return FALSE;
        }

        //1 : Chargement des ent&#65533;tes FICHIER
        $FILE = unpack("vfile_type/Vfile_size/Vreserved/Vbitmap_offset", fread($f1,14));
        if ($FILE['file_type'] != 19778) {
            return FALSE;
        }

        //2 : Chargement des ent&#65533;tes BMP
        $BMP = unpack('Vheader_size/Vwidth/Vheight/vplanes/vbits_per_pixel'.
        '/Vcompression/Vsize_bitmap/Vhoriz_resolution'.
        '/Vvert_resolution/Vcolors_used/Vcolors_important', fread($f1,40));
        $BMP['colors'] = pow(2,$BMP['bits_per_pixel']);
        if ($BMP['size_bitmap'] == 0) {
            $BMP['size_bitmap'] = $FILE['file_size'] - $FILE['bitmap_offset'];
        }
        $BMP['bytes_per_pixel'] = $BMP['bits_per_pixel']/8;
        $BMP['bytes_per_pixel2'] = ceil($BMP['bytes_per_pixel']);
        $BMP['decal'] = ($BMP['width']*$BMP['bytes_per_pixel']/4);
        $BMP['decal'] -= floor($BMP['width']*$BMP['bytes_per_pixel']/4);
        $BMP['decal'] = 4-(4*$BMP['decal']);
        if ($BMP['decal'] == 4) {
            $BMP['decal'] = 0;
        }

        //3 : Chargement des couleurs de la palette
        $PALETTE = array();
        if ($BMP['colors'] < 16777216) {
            $PALETTE = unpack('V'.$BMP['colors'], fread($f1,$BMP['colors']*4));
        }

        //4 : Cr&#65533;ation de l'image
        $IMG = fread($f1,$BMP['size_bitmap']);
        $VIDE = chr(0);

        $res = imagecreatetruecolor($BMP['width'],$BMP['height']);
        $P = 0;
        $Y = $BMP['height']-1;
        while ($Y >= 0) {
            $X=0;
            while ($X < $BMP['width']) {
                if ($BMP['bits_per_pixel'] == 24) {
                    $COLOR = unpack("V",substr($IMG,$P,3).$VIDE);
                } else if ($BMP['bits_per_pixel'] == 16) {
                    $COLOR = unpack("n",substr($IMG,$P,2));
                    $COLOR[1] = $PALETTE[$COLOR[1]+1];
                } else if ($BMP['bits_per_pixel'] == 8) {
                    $COLOR = unpack("n",$VIDE.substr($IMG,$P,1));
                    $COLOR[1] = $PALETTE[$COLOR[1]+1];
                } else if ($BMP['bits_per_pixel'] == 4) {
                    $COLOR = unpack("n",$VIDE.substr($IMG,floor($P),1));
                    if (($P*2)%2 == 0) {
                        $COLOR[1] = ($COLOR[1] >> 4) ;
                    } else {
                        $COLOR[1] = ($COLOR[1] & 0x0F);
                    }
                    $COLOR[1] = $PALETTE[$COLOR[1]+1];
                } else if ($BMP['bits_per_pixel'] == 1) {
                    $COLOR = unpack("n",$VIDE.substr($IMG,floor($P),1));
                    if (($P*8)%8 == 0) {
                        $COLOR[1] =  $COLOR[1]        >>7;
                    } else if (($P*8)%8 == 1) {
                        $COLOR[1] = ($COLOR[1] & 0x40)>>6;
                    } else if (($P*8)%8 == 2) {
                        $COLOR[1] = ($COLOR[1] & 0x20)>>5;
                    } else if (($P*8)%8 == 3) {
                        $COLOR[1] = ($COLOR[1] & 0x10)>>4;
                    } else if (($P*8)%8 == 4) {
                        $COLOR[1] = ($COLOR[1] & 0x8)>>3;
                    } else if (($P*8)%8 == 5) {
                        $COLOR[1] = ($COLOR[1] & 0x4)>>2;
                    } else if (($P*8)%8 == 6) {
                        $COLOR[1] = ($COLOR[1] & 0x2)>>1;
                    } else if (($P*8)%8 == 7) {
                        $COLOR[1] = ($COLOR[1] & 0x1);
                    }
                    $COLOR[1] = $PALETTE[$COLOR[1]+1];
                } else {
                    return FALSE;
                }
                imagesetpixel($res,$X,$Y,$COLOR[1]);
                $X++;
                $P += $BMP['bytes_per_pixel'];
            }
            $Y--;
            $P+=$BMP['decal'];
        }

        //Fermeture du fichier
        fclose($f1);

        return $res;
    }

    /**
     * Return the resized dimensions to make an image fit in a box of $targetWidth x $targetHeight, 
     * use $upscale=true if you want to also upscale to fit, the default is to downscale only
     */
    public static function getDimensionsScaled($width, $height, $targetWidth, $targetHeight, $upscale = false){
        if ((($width > $targetWidth) && ($height > $targetHeight)) || (($upscale) && ($width <= $targetWidth) && ($height <= $targetHeight))) {
            $widthFac  = $targetWidth / $width;
            $heightFac = $targetHeight / $height;
            if ($widthFac < $heightFac) {
                $imgWidth  = $targetWidth;
                $imgHeight = (int)($height * $widthFac);
            } else {
                $imgWidth  = (int)($width * $heightFac);
                $imgHeight = $targetHeight;
            }
        } else if ($width > $targetWidth) {
            $widthFac  = $targetWidth / $width;
            $imgWidth  = $targetWidth;
            $imgHeight = (int)($height * $widthFac);
        } else if ($height > $targetHeight) {
            $heightFac = $targetHeight / $height;
            $imgWidth  = (int)($width * $heightFac);
            $imgHeight = $targetHeight;
        } else {
            return array($width, $height);
        }
        return array($imgWidth, $imgHeight);
    }

}
