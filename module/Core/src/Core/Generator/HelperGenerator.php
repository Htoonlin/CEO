<?php
namespace Core\Generator;

use Zend\Db\Metadata\Metadata;
use HumanResource\Entity\Staff;
use Zend\Filter\Word\CamelCaseToSeparator;
use Zend\Filter\Word\UnderscoreToCamelCase;
use Zend\Code\Generator\MethodGenerator;
use Zend\Code\Generator\PropertyGenerator;
/**
 *
 * @author htoonlin
 *
 */
class HelperGenerator extends SundewGenerator
{
    /**
     *
     * @param Metadata $dbMeta
     *
     * @param string $tbl_name
     *
     * @param string $module
     *
     * @param Staff $staff
     *
     */
    public function __construct(Metadata $dbMeta, $tbl_name,
        $module, Staff $staff)
    {
        parent::__construct($dbMeta, $tbl_name, $module, $staff);
    }

   /**
    * (non-PHPdoc)
    * @see \Core\Generator\SundewGenerator::getClassName()
    */
    public function getClassName(){
        return parent::generateClass($this->module, $this->tbl_name, 'Helper');
    }

    /**
     * (non-PHPdoc)
     * @see \Core\Generator\SundewGenerator::generate()
     */
    public function generate()
    {
        $className = $this->generateClass($this->module, $this->tbl_name, 'Helper');
        $nameSpace = $this->module . '\\Helper';

        $class = $this->initClass($className, $nameSpace);
        $class->addUse('Zend\Form\Element');
        $class->addUse('Zend\Form\Form');
        $class->addUse('Zend\InputFilter\InputFilter');

        $class->addProperties(array(
            array('dbAdapter', null, PropertyGenerator::FLAG_PROTECTED),
            array('form', null, PropertyGenerator::FLAG_PROTECTED),
            array('inputFilter', null, PropertyGenerator::FLAG_PROTECTED)
        ));

        $getForm = new MethodGenerator('getForm');
        $getFormCode = 'if(!$this->form){' . "\n\t" . '$form = new Form();' . PHP_EOL;

        $getFilter = new MethodGenerator('getInputFilter');
        $getFilterCode = 'if(!$this->inputFilter){' . "\n\t" . '$filter = new InputFilter();' . PHP_EOL;

        $columns = $this->dbMeta->getColumns($this->tbl_name);

        foreach($columns as $col)
        {
            $name = $col->getName();
            if(in_array($name, $this->skipFields)){
                continue;
            }

            $type = $col->getDataType();
            $maxLength = $col->getCharacterMaximumLength();
            $primary = $this->dbMeta->isPrimary($this->tbl_name, $name);
            $allowNull = $col->getIsNullable();
            $getFormCode .= $this->createControl($name, $type, $allowNull, $maxLength, $primary) . PHP_EOL;
            $getFilterCode .= $this->createFilter($name, $type, $allowNull, $maxLength) . PHP_EOL;
        }

        $getFormCode .= "\t" . '$this->form = $form;';
        $getFormCode .= "\n}\n" . 'return $this->form;';
        $getForm->setBody($getFormCode);

        $getFilterCode .= "\t" . '$this->inputFilter = $filter;';
        $getFilterCode .= "\n}\n" . 'return $this->inputFilter;';
        $getFilter->setBody($getFilterCode);

        $setForm = new MethodGenerator('setForm');
        $setForm->setParameter(array('type' => 'Form', 'name' => 'form'));
        $setForm->setBody('$this->form = $form;');

        $setFilter = new MethodGenerator('setInputFilter');
        $setFilter->setParameter(array('type' => 'InputFilter', 'name' => 'filter'));
        $setFilter->setBody('$this->inputFilter = $filter;');

        $class->addMethods(array($getForm, $setForm, $getFilter, $setFilter));
        return '<?php' . PHP_EOL . $class->generate();
    }

    /**
     * @param $name
     * @param $type
     * @return string
     */
    private function createControl($name, $type, $allowNull, $length = 0, $isPrimary = false)
    {
        $isForeign = (!$isPrimary && strpos($name, 'Id') && $type == 'int');

        $toCamelCase = new UnderscoreToCamelCase();
        $var = lcfirst($toCamelCase->filter($name));
        $toSeperator = new CamelCaseToSeparator(array(" "));
        $label = $toSeperator->filter($toCamelCase->filter($name));
        if($allowNull){
            $label .= '(*)';
        }

        $code = '';
        if($isPrimary){
            $code .= "\t\${$var} = new Element\\Hidden('{$name}');\n";
        }else if($isForeign){
            $code .= "\t\${$var} = new Element\\Select('{$name}');\n";
            $code .= "\t\${$var}->setAttribute('class', 'form-control');\n";
        }else if(in_array($type, $this->typeNum) || in_array($type, $this->typeFloat)){
            $code .= "\t\${$var} = new Element\\Number('{$name}');\n";
            if(in_array($type, $this->typeNum)){
                $code .= "\t\${$var}->setAttributes(array(\n";
                $code .= "\t\t'min' => '0',\n";
                $code .= "\t\t'max' => '99999999999',\n";
                $code .= "\t\t'step' => '1',\n";
                $code .= "\t));\n";
            }elseif(in_array($type, $this->typeNum)){
                $code .= "\t\${$var}->setAttributes(array(\n";
                $code .= "\t\t'min' => '0',\n";
                $code .= "\t\t'max' => '99999999999',\n";
                $code .= "\t\t'step' => '0.5',\n";
                $code .= "\t));\n";
            }
        }else if(in_array($type, $this->typeDate)){
            $code .= "\t\${$var} = new Element\\Date('{$name}');\n";
            $code .= "\t\${$var}->setAttributes(array(\n";
            $code .= "\t\t'allowPastDates' => true,\n";
            $code .= "\t\t'momentConfig' => array('format' => 'YYYY-MM-DD'),\n";
            $code .= "\t));\n";
        }else{
            if($length >= 500){
                $code .= "\t" . '$' . $var . " = new Element\\Textarea('{$name}');\n";
            }else{
                $code .= "\t" . '$' . $var . " = new Element\\Text('{$name}');\n";
            }
            $code .= "\t\${$var}->setAttribute('class', 'form-control');\n";
        }

        if(!$isPrimary){
            $code .= "\t\${$var}->setLabel('{$label}');\n";
        }
        $code .= "\t\$form->add(\${$var});\n";

        return $code;
    }

    private function createFilter($name, $type, $allowNull, $length = 0)
    {
        $null = $allowNull ? 'false' : 'true';

        $code = "\t\$filter->add(array(\n";
        $code .= "\t\t'name' => '{$name}',\n";
        $code .= "\t\t'required' => {$null},\n";

        if(!$allowNull){
            if(in_array($type, $this->typeNum)){
                $code .= "\t\t'validators' => array(array('name' => 'Zend\\I18n\\Validator\\IsInt')),\n";
            }else if(in_array($type, $this->typeFloat)){
                $code .= "\t\t'validators' => array(array('name' => 'Zend\\I18n\\Validator\\IsFloat')),\n";
            }else if(in_array($type, $this->typeString)){
                $code .= "\t\t'filters' => array(\n";
                $code .= "\t\t\tarray('name' => 'Zend\\Filter\\StripTags'),\n";
                $code .= "\t\t\tarray('name' => 'Zend\\Filter\\StringTrim'),\n";
                $code .= "\t\t),\n";
                $code .= "\t\t'validators' => array(\n";
                $code .= "\t\t\tarray(\n";
                $code .= "\t\t\t\t'name' => 'StringLength',\n";
                $code .= "\t\t\t\t'max' => {$length},\n";
                $code .= "\t\t\t\t'min' => 1,\n";
                $code .= "\t\t\t\t'encoding' => 'UTF-8',\n";
                $code .= "\t\t\t),\n";
                $code .= "\t\t),\n";
            }
        }

        $code .= "\t));";
        return $code;
    }
}

?>