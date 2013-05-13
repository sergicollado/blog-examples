<?php
////A field named 'file' is required' 
class CModelWithFile extends CActiveRecord{ 

    public $file_uploaded;

    
    public function createValidators()
    {
        $rules = $this->rules();

        $rules = CMap::mergeArray($this->rules(), array(array('file', 'length', 'max'=>255))); 
        $validators=new CList;
        foreach($rules as $rule)
        {
            if(isset($rule[0],$rule[1]))  // attributes, validator name
                $validators->add(CValidator::createValidator($rule[1],$this,$rule[0],array_slice($rule,2)));
            else
                throw new CException(Yii::t('yii','{class} has an invalid validation rule. The rule must specify attributes to be validated and the validator name.',
                    array('{class}'=>get_class($this))));
        }
        return $validators;
    }

    public function saveWithImage($post){
          $this->attributes=$post;
          if(!$this->validate())
              return false;

          $this->saveFile();

          return $this->save();
    }
    
    private function saveFile(){
        $this->file_uploaded   =   CUploadedFile::getInstance($this,'file_uploaded');
        if(!$this->file_uploaded)
            return false;

        $this->file = '';
        $location_image = $this->getFileLocation();
        $this->file =  $this->moveFile($location_image,$this->file_uploaded);
    }
    
    private function moveFile($folder,$file_uploaded){
        $image_name= md5($file_uploaded.'code4code').$file_uploaded;
        $image_name = strtolower($image_name);
        
        $this->createDirectory();
        $file_uploaded->saveAs($folder.$image_name);
        return $image_name;
    }

    private function getFileUrl(){
        return Yii::app()->getBaseUrl(true).'/images/'.get_class($this).'/';
    }
    private function getFileLocation(){
        return Yii::getPathOfAlias('webroot').'/images/'.get_class($this).'/';
    }
    
    public function showImageFile(){
        if($this->file): ?>
        <li class="span3">
            <a href="#" class="thumbnail" rel="tooltip" data-title="Imagen">
            <?php $root = $this->getFileUrl().$this->file; ?>
                <img src="<?php echo $root;?>" />
            </a>
        </li>
        <?php endif;
    }
    
    private function createDirectory(){
        $directory = rtrim($this->getFileLocation(),'/');
        if (is_dir($directory))
            return true;
        
        mkdir($directory);
        
    }
    
}