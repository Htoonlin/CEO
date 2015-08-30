<?php
namespace Development\Helper\Generator;

use Zend\Db\Metadata\Metadata;
use HumanResource\Entity\Staff;
use Zend\Code\Generator\MethodGenerator;
/**
 *
 * @author htoonlin
 *
 */
class ControllerGenerator extends SundewGenerator
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

    public function getClassName(){
        return parent::generateClass($this->module, $this->tbl_name, 'Controller');
    }

    public function generate()
    {
        $entity = $this->generateClass($this->module, $this->tbl_name);
        $helper = $this->generateClass($this->module, $this->tbl_name, 'Helper');
        $dataAccess = $this->generateClass($this->module, $this->tbl_name, 'DataAccess');
        $className = $this->generateClass($this->module, $this->tbl_name, 'Controller');
        $nameSpace = $this->module . '\\Controller';
        $class = $this->initClass($className, $nameSpace);
        $class->addUse('Core\SundewController');
        $class->addUse('Core\SundewExporting');
        $class->addUse('Zend\View\Model\ViewModel');
        $class->addUse('Zend\View\Model\JsonModel');
        $class->addUse($this->module . '\\Entity\\' . $entity);
        $class->addUse($this->module . '\\Helper\\' . $helper);
        $class->addUse($this->module . '\\DataAccess\\' . $dataAccess);
        $class->setExtendedClass('SundewController');

        $dataAccessMethod = lcfirst($entity) . 'Table';
        $mainTableBody = 'return new ' . $dataAccess . '($this->getDbAdapter());';
        $mainTable = MethodGenerator::fromArray(array(
            'name' => $dataAccessMethod,
            'body' => $mainTableBody,
        ));

        $varEntity = lcfirst($entity);
        $mainColumn = $this->dbMeta->getColumnNames($this->tbl_name)[1];
        $indexActionBody = '$page = (int)$this->params()->fromQuery("page", 1);' . PHP_EOL;
        $indexActionBody .= '$sort = $this->params()->fromQuery("sort", "' . $mainColumn . '");' . PHP_EOL;
        $indexActionBody .= '$sortBy = $this->params()->fromQuery("by", "asc");' . PHP_EOL;
        $indexActionBody .= '$filter = $this->params()->fromQuery("filter", "");' . PHP_EOL;
        $indexActionBody .= '$pageSize = (int)$this->params()->fromQuery("size", 10);' . PHP_EOL;
        $indexActionBody .= PHP_EOL . '$paginator = $this->' . $varEntity . 'Table()->fetchAll(true, $filter, $sort, $sortBy);' . PHP_EOL;
        $indexActionBody .= '$paginator->setCurrentPageNumber($page);' . PHP_EOL;
        $indexActionBody .= '$paginator->setItemCountPerPage($pageSize);' . PHP_EOL;
        $indexActionBody .= PHP_EOL . 'return new ViewModel(array(' . PHP_EOL;
        $indexActionBody .= "\t" . '"paginator" => $paginator,' . PHP_EOL;
        $indexActionBody .= "\t" . '"sort" => $sort,' . PHP_EOL;
        $indexActionBody .= "\t" . '"sortBy" => $sortBy,' . PHP_EOL;
        $indexActionBody .= "\t" . '"filter" => $filter,' . PHP_EOL;
        $indexActionBody .= '));';
        $indexAction = MethodGenerator::fromArray(array(
            'name' => 'indexAction',
            'body' => $indexActionBody,
        ));

        $detailActionBody = '$id = (int)$this->params()->fromRoute("id", 0);' . PHP_EOL;
        $detailActionBody .= '$action = $this->params()->fromQuery("action", "");' . PHP_EOL;
        $detailActionBody .= '$helper = new ' . $helper . '();' . PHP_EOL;
        $detailActionBody .= '$form = $helper->getform();' . PHP_EOL;
        $detailActionBody .= '$' . $varEntity . ' = $this->' . $dataAccessMethod . '()->get' . $entity . '($id);' . PHP_EOL;
        $detailActionBody .= PHP_EOL . '$isEdit = true;' . PHP_EOL;
        $detailActionBody .= 'if(!$'. $varEntity . '){' . PHP_EOL;
        $detailActionBody .= "\t" . '$isEdit = false;' . PHP_EOL;
        $detailActionBody .= "\t\${$varEntity} = new {$entity}();" . PHP_EOL;
        $detailActionBody .= '}' . PHP_EOL;
        $detailActionBody .= "\nif(\$action == 'clone'){" . PHP_EOL;
        $detailActionBody .= "\t\$isEdit = false;\n\t\$id = 0;\n\t\${$varEntity}->set{$entity}Id(0);\n";
        $detailActionBody .= "}\n\n";
        $detailActionBody .= '$form->bind($' . $varEntity . ');' . PHP_EOL;
        $detailActionBody .= "\$request = \$this->getRequest();\n";
        $detailActionBody .= "if(\$request->isPost()){\n";
        $detailActionBody .= "\t\$post_data = \$request->getPost()->toArray();\n";
        $detailActionBody .= "\t\$form->setData(\$post_data);\n";
        $detailActionBody .= "\t\$form->setInputFilter(\$helper->getInputFilter(\$id));\n\n";
        $detailActionBody .= "\tif(\$form->isValid()){\n";
        $detailActionBody .= "\t\t\$this->{$dataAccessMethod}()->save{$entity}(\${$varEntity});\n";
        $detailActionBody .= "\t\t\$this->flashMessenger()->addSuccessMessage('Save successful');\n";
        $detailActionBody .= "\t\treturn \$this->redirect()->toRoute('{$this->module}_{$varEntity}');\n";
        $detailActionBody .= "\t}\n";
        $detailActionBody .= "}\n";
        $detailActionBody .= "return new ViewModel(array('form' => \$form, 'id' => \$id, 'isEdit' => \$isEdit));";
        $detailAction = MethodGenerator::fromArray(array(
            'name' => 'detailAction',
            'body' => $detailActionBody,
        ));

        $deleteActionBody = '$id = (int)$this->params()->fromRoute("id", 0);' . PHP_EOL;
        $deleteActionBody .= "\${$varEntity} = \$this->{$varEntity}Table()->get{$entity}(\$id);\n\n";
        $deleteActionBody .= "if(\${$varEntity}){\n";
        $deleteActionBody .= "\t\$this->{$varEntity}Table()->delete{$entity}(\$id);\n";
        $deleteActionBody .= "\t\$this->flashMessenger()->addInfoMessage('Delete successful');\n";
        $deleteActionBody .= "}\n";
        $deleteActionBody .= "\nreturn \$this->redirect()->toRoute('{$this->module}_{$varEntity}');";
        $deleteAction = MethodGenerator::fromArray(array(
            'name' => 'deleteAction',
            'body' => $deleteActionBody,
        ));

        $jsonDeleteActionBody = '$data = $this->params()->fromPost("chkId", array());' . PHP_EOL;
        $jsonDeleteActionBody .= '$db = $this->' . $varEntity . 'Table()->getAdapter();' . PHP_EOL;
        $jsonDeleteActionBody .= '$conn = $db->getDriver()->getConnection();' . PHP_EOL;
        $jsonDeleteActionBody .= "try{\n\t\$conn->beginTransaction();\n";
        $jsonDeleteActionBody .= "\tforeach(\$data as \$id){\n";
        $jsonDeleteActionBody .= "\t\t\$this->{$varEntity}Table()->delete{$entity}(\$id);\n\t}\n";
        $jsonDeleteActionBody .= "\t\$conn->commit();\n";
        $jsonDeleteActionBody .= "\t\$message = 'success';\n";
        $jsonDeleteActionBody .= "\t\$this->flashMessenger()->addInfoMessage('Delete successful');\n";
        $jsonDeleteActionBody .= "} catch(\\Exception \$ex) {\n";
        $jsonDeleteActionBody .= "\t\$conn->rollback();\n";
        $jsonDeleteActionBody .= "\t\$message = \$ex->getMessage();\n}\n";
        $jsonDeleteActionBody .= "return new JsonModel(array('message' => \$message));";
        $jsonDeleteAction = MethodGenerator::fromArray(array(
            'name' => 'jsonDeleteAction',
            'body' => $jsonDeleteActionBody,
        ));

        $exportActionBody = '$export = new SundewExporting($this->' . $varEntity . 'Table()->fetchAll(false));' . PHP_EOL;
        $exportActionBody .= '$response = $this->getResponse();' . PHP_EOL;
        $exportActionBody .= "\$filename = 'attachment; filename=\"{$entity}-' . date('Ymdhis') . '.xlsx\"';" . PHP_EOL;
        $exportActionBody .= '$headers = $response->getHeaders();' . PHP_EOL;
        $exportActionBody .= '$headers->addHeaderLine("content-Type", "application/ms-excel; charset=UTF-8");' . PHP_EOL;
        $exportActionBody .= '$headers->addHeaderLine("Content-Disposition", $filename);' . PHP_EOL;
        $exportActionBody .= '$response->setContent($export->getExcel());' . PHP_EOL;
        $exportActionBody .= 'return $response;';
        $exportAction = MethodGenerator::fromArray(array(
            'name' => 'exportAction',
            'body' => $exportActionBody,
        ));
        $class->addMethods(array($mainTable, $indexAction, $detailAction,
            $deleteAction, $jsonDeleteAction, $exportAction));
        return '<?php' . PHP_EOL . $class->generate();
    }
}

?>