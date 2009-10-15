<?php
/*
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

############# Motto: "Imagination is more important than knowledge. Knowledge is limited. (Albert E.)";
/**
 * Concrete CLASS providing multimedia methods for: images/video (graphics). Also, this can serve as a CLASS that can handle processing
 * of other multimedia files as long as they have support for them in PHP;
 *
 * @package RA-Multimedia-Management
 * @category RA-Concrete-CORE
 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
 * @copyright Under the terms of the GNU General Public License v3
 * @access public
 */
class GPH extends SQL {
    protected static $objName                   = 'GPH :: RA PHP Framework';
    protected $objIdentificationString          = __CLASS__;

    /**
     * Will return the image object resource, after determining the image type. This method is used by this CLASS to check-out what
	 * type a file is before it's used as a resource for own purposes (like resizing, croping etc.);
     *
     * @param A $uploadedImageType The passed _FILES array
     * @param S $imagePath The file path to where to find the file
     * @return R Depends on the resource type
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 12_GPH.php 314 2009-10-09 14:24:35Z catalin.zamfir $
	 * @since Version 1.0
	 * @access protected
	 * @static
	 * @final
     */
    protected static final function getImageObjectFromType (A $uploadedImageType, FilePath $imagePath) {
        // Do a switch ...
        switch ($uploadedImageType['type']) {
            case 'image/gif'  : return imagecreatefromgif  ($imagePath); break;
            case 'image/png'  : return imagecreatefrompng  ($imagePath); break;
            case 'image/bmp'  : return imagecreatefromwbmp ($imagePath); break;
            case 'image/jpeg' : return imagecreatefromjpeg ($imagePath); break;
            default: return NULL; break;
        }
    }

    /**
     * Will resize the images given from the path. Given a FileDirectory of images we want to resize, this method will provide a way
	 * to automatically resize images to the specified width/height. It takes both arguments, including the height/width of the images
	 * to be resized as the new array of dimensions to which to resize the file to;
     *
     * @param S $directoryFrom In what directory to save the files
     * @param A $resizeFileArray The array of files to be resized
     * @param A $resizeDimArray The resize array, containing key/var pairs of width and height, to process the images
     * @return void Will not return a thing, but process the images
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 12_GPH.php 314 2009-10-09 14:24:35Z catalin.zamfir $
	 * @since Version 1.0
	 * @access protected
	 * @static
	 * @final
     */
    protected static final function resizeImageFromUploadPATH (FileDirectory $directoryFrom,
    A $resizeFileArray, A $resizeDimArray) {
        foreach ($resizeFileArray as $kF => $vF) {
            if (($imagePath = new FilePath ($directoryFrom . $vF['name'], FALSE)) &&
                ($imagePath->checkPathExists ())) {
                // Process the LIST;
                list ($imgWidth, $imgHeight) = getimagesize ($imagePath =
                new FilePath ($imagePath->doToken (DOCUMENT_ROOT, _NONE)));

                // Get the image object, as a resource;
                if (($curIMG = self::getImageObjectFromType (new A ($vF), $imagePath)) != NULL) {
                    $currentImageProcessing = new R ($curIMG);
                } else {
                    continue;
                }

                // Go further,
                if ($currentImageProcessing->checkIs ('res')->toBoolean ()) {
                    foreach ($resizeDimArray as $k => $v) {
                        $ratioOriginalImage = $imgWidth/$imgHeight;
                        if ($k/$v > $ratioOriginalImage) {
                            // Set width;
                            $thumbWidth     = ceil ($v * $ratioOriginalImage);
                            $thumbHeight    = $v;
                        } else {
                            // Set height;
                            $thumbHeight    = ceil ($k / $ratioOriginalImage);
                            $thumbWidth     = $k;
                        }
                        // Create the temporary;
                        $temporaryImage = new R (imagecreatetruecolor ($thumbWidth, $thumbHeight));
                        if (imagecopyresampled ($temporaryImage->toResource (), $currentImageProcessing
                        ->toResource (), 0, 0, 0, 0, $thumbWidth, $thumbHeight, $imgWidth, $imgHeight)) {
                            $uDir = $directoryFrom;
                            switch ($vF['type']) {
                                case 'image/gif'  : imagegif  ($temporaryImage->toResource (),
                                $uDir . $k . '_' . $v . '_' . $vF['name']); break;
                                case 'image/png'  : imagepng  ($temporaryImage->toResource (),
                                $uDir . $k . '_' . $v . '_' . $vF['name']); break;
                                case 'image/bmp'  : imagewbmp ($temporaryImage->toResource (),
                                $uDir . $k . '_' . $v . '_' . $vF['name']); break;
                                case 'image/jpeg' : imagejpeg ($temporaryImage->toResource (),
                                $uDir . $k . '_' . $v . '_' . $vF['name']); break;
                            }
                        }
                        // Destroy the temporary;
                        imagedestroy ($temporaryImage->toResource ());
                        unset ($temporaryImage);
                    }
                    // Destroy the image!
                    imagedestroy ($currentImageProcessing->toResource ());
                    unset ($currentImageProcessing);
                }
            }
        }
    }
}
?>
