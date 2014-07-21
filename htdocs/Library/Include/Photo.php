<?php
	class Photo {
		private $db;
        private $tablename = 'pictures';

        //For Photo Editting
        private $imgHandle = null;
        private $editHandle = null;
        private $path = "";
        private $imgType = "";
        private $width = 0;
        private $height = 0;


		public function __construct(&$db = null) {
            $this->db = ( $db == null ? new Database() : $db );
		}

    /**********************************
     *                                *
     *      Photo Edit Interface      *
     *                                *
     *********************************/
        //creates a gd image handle for editting
        public function openImage($path) {
            $this->path = $path;
            $this->imgType = strtolower(strrchr($path, '.'));
            switch($this->imgType) {
                case '.jpg':
                case '.jpeg':
                    $this->imgHandle = @imagecreatefromjpeg($path);
                    break;
                case '.png':
                    $this->imgHandle = @imagecreatefrompng($path);
                    break;
                default:
                    $this->imgType = null;
                    return false;
                    break;
            }
            $this->width = imagesy($this->imgHandle);
            $this->height = imagesx($this->imgHandle);
            return true;
        }

        //if no path is given, the original is replaced
        public function saveImage($imgQuality="100", $path = null) {  
            if($this->imgHandle == null) return false;
            //we do this so $this->path can be unset as well as return the new $path
            //by initializing $path with existing for replacement
            if($path == null) $path = $this->path;
            $this->imgType = strtolower(strrchr($path, '.'));

          
            switch($this->imgType)  
            {  
                case '.jpg':  
                case '.jpeg':  
                    if (imagetypes() & IMG_JPG) imagejpeg($this->imgHandle, $path, $imgQuality);  
                    else return false;
                    break;    
                case '.png':  
                    // *** Scale quality from 0-100 to 0-9  
                    $imgQuality = round(($imgQuality/100) * 9);  
                    // *** Invert quality setting as 0 is best, not 9  
                    $imgQuality = 9 - $imgQuality;  
                    if (imagetypes() & IMG_PNG) imagepng($this->imgHandle, $path, $imgQuality);  
                    else return false;
                    break;  
                default:  
                    return false; 
                    break;  
            }
            //unsetting uneeded variables
            $this->path = "";
            $this->imgType = "";
            $this->width = 0;
            $this->height = 0;
            imagedestroy($this->imgHandle);  
            return $path;
        } 

        public function resizeImage($newWidth, $newHeight, $option = 'auto') {
            if($this->imgHandle == null) return false;
            $optimal = $this->getDimensions($newWidth, $newHeight, strtolower($option));  
            $this->editHandle = imagecreatetruecolor($optimal['width'], $optimal['height']);  

            //if image has not been editted
            imagecopyresampled($this->editHandle, $this->imgHandle, 0, 0, 0, 0, $optimal['width'], $optimal['height'], $this->width, $this->height);  

            if ($option == 'crop')
                $this->crop($optimal['width'], $optimal['height'], $newWidth, $newHeight);  

            $this->width = imagesx($this->editHandle);
            $this->height = imagesy($this->editHandle);           
            //replace uneditted with new version
            $this->imgHandle = $this->editHandle; 
            //destroy copy
            imagedestroy($this->editHandle);
            return true;
        }

        //default rotates clockwise one turn
        public function rotateImage($degree = "90") {
            if($this->imgHandle == null) return false;
            $this->editHandle = imagerotate($this->editHandle, $degree, 0);

            //re-populate with new width and height
            $this->width = imagesx($this->editHandle);
            $this->height = imagesy($this->editHandle);

            $this->imgHandle = $this->editHandle;
            imagedestroy($this->editHandle);
            return true;
        }

        //Thumbnail should be restricted to 300 x 300
/***NEEDS TO BE TESTED BEFORE LIVE IMPLEMENTATION, SOMETHING IS NOT QUITE RIGHT*****/
        public function createThumb($thumb_topx, $thum_topy, $thumb_width, $thumb_height){
            if($this->imgHandle == null) return false;

            //creates a canvas for the new thumbnail capable of storing 16.8 million bits
            $this->editHandle = imagecreatetruecolor($thumb_width, $thumb_height);
            imagecopyresampled($this->editHandle, $this->imgHandle, 
                0, 0, $thumb_topx, $thumb_topy, 
                $thumb_width, $thumb_height, $thumb_width, $thumb_height);
            
            //re-populate with new width and height
            $this->width = imagesx($this->editHandle);
            $this->height = imagesy($this->editHandle);

            $this->imgHandle = $this->editHandle;
            imagedestroy($this->editHandle);
            return true;
        }

    /**********************************
     *                                *
     *      Photo Edit Helpers        *
     *                                *
     *********************************/

        private function crop($optimalWidth, $optimalHeight, $newWidth, $newHeight)  
        {   
            $cropStartX = ( $optimalWidth / 2) - ( $newWidth /2 );  
            $cropStartY = ( $optimalHeight/ 2) - ( $newHeight/2 );  
          
            $crop = $this->editHandle;  
          
            // *** Now crop from center to exact requested size  
            $this->editHandle = imagecreatetruecolor($newWidth , $newHeight);  
            imagecopyresampled($this->editHandle, $crop , 0, 0, $cropStartX, $cropStartY, $newWidth, $newHeight , $newWidth, $newHeight);  
        } 

        private function getDimensions($newWidth, $newHeight, $option)
        {
            $optimal = null;
            switch ($option)
            {
                 case 'auto':
                    $optimal = $this->getSizeByAuto($newWidth, $newHeight);
                    break;
                case 'portrait':
                    $optimal['width'] = $this->getWidthByFixedHeight($newHeight);
                    $optimal['height'] = $newHeight;
                    break;
                case 'landscape':
                    $optimal['width'] = $newWidth;
                    $optimal['height'] = $this->getHeightByFixedWidth($newWidth);               
                    break;
                case 'crop':
                    $optimal = $this->getOptimalCrop($newWidth, $newHeight);
                    break;
                case 'exact':
                    $optimal['width'] = $newWidth;
                    $optimal['height']= $newHeight;
                    break;
            }
            return $optimal;
        }

        private function getSizeByAuto($newWidth, $newHeight)  
        {  
            $optimal = null;
            if ($this->height > $this->width) {                         //'portrait'
                $optimal['width'] = $this->getWidthByFixedHeight($newHeight);  
                $optimal['height'] = $newHeight;  
            }
            else if ($this->height < $this->width) {                    //'landscape'
                $optimal['width'] = $newWidth;  
                $optimal['height'] = $this->getHeightByFixedWidth($newWidth);  
            }    
            else {                                                      //original dimensions is a square
                if ($newHeight < $newWidth) {                               //calculate new height from new width
                    $optimal['width'] = $newWidth;  
                    $optimal['height'] = $this->getHeightByFixedWidth($newWidth);  
                } else if ($newHeight > $newWidth) {                        //calculate new width from new height
                    $optimal['width'] = $this->getWidthByFixedHeight($newHeight);  
                    $optimal['height'] = $newHeight;  
                } else {                                                    //if both original dimensions & new dimensions is akin to a square
                    $optimal['width'] = $newWidth;  
                    $optimal['height'] = $newHeight;  
                }  
            }  
            return $optimal; 
        }

        //new width is determined by current ratio of (width/height) * new height
        private function getWidthByFixedHeight($newHeight)  
        {  
            $ratio = $this->width / $this->height;  
            $newWidth = $newHeight * $ratio;  
            return $newWidth;  
        }  
          
        //new height is determined by current ratio of (height/width) * new width
        private function getHeightByFixedWidth($newWidth)  
        {  
            $ratio = $this->height / $this->width;  
            $newHeight = $newWidth * $ratio;  
            return $newHeight;  
        } 

        private function getOptimalCrop($newWidth, $newHeight)  
        {  
            $heightRatio = $this->height / $newHeight;  
            $widthRatio  = $this->width /  $newWidth;  
          
            if ($heightRatio < $widthRatio)
                $optimalRatio = $heightRatio;  
            else
                $optimalRatio = $widthRatio;  
          
            $optimal['height'] = $this->height / $optimalRatio;  
            $optimal['width']  = $this->width  / $optimalRatio;  
          
            return $optimal;
        } 

    /**********************************
     *                                *
     *      Photo Table Interface     *
     *                                *
     *********************************/



    /**********************************
     *                                *
     *      Photo Table Model         *
     *                                *
     *********************************/
        public function uploadPhoto($userid, $path, $name, $caption, $size, $postid = 0) {
            $queryStr = "INSERT INTO {$this->tablename} (`userid`, `path`, `name`, `caption`,`size`, `postid`, `create_date`, `modified_date`) 
                VALUES ('{$userid}','{$path}','{$name}','{$caption}','{$size}', '{$postid}', NOW(), NOW())";
            if($this->db->result($queryStr)) return true;
            return false;
        }

        public function getPhoto($userid, $conds = null) {
            $queryStr = "SELECT * FROM {$this->tablename} WHERE `userid`='{$userid}'";
            foreach ($conds as $key => $val) {
                $queryStr .= " AND `{$key}`='{$val}'";
            }
            $response = null;
            if($result = $this->db->result($queryStr)){
                while($row = $result->fetch_assoc()){
                    $response[] = $row;
                }
            }
            return $response;
        }

        public function updatePhoto($pictureid, $keyVal) {
            $queryStr = "UPDATE {$this->tablename} SET `modified_date`=NOW()";
            foreach($keyVal as $key => $val) {
                $queryStr .= ",`{$key}`='{$val}'";
            }
            $queryStr .= " WHERE `pictureid='{$pictureid}'";

            if($this->db->result($queryStr)) return true;
            return false;
        }

        public function deletePhoto($pictureid){
            $queryStr = "DELETE FROM {$this->tablename} WHERE `pictureid`='{$pictureid}'";
            if($this->db->result($queryStr)) return true;
            return false;           
        }

    /**********************************
     *                                *
     *      Photo Object Helpers      *
     *                                *
     *********************************/
	}
?>