<?php

namespace Sotbit\B2bCabinet\Form;


class FormTemplates
{
    private $formWrapper;
    private $formHeader;
    private $formBody;
    private $formHeaderAction;
    private $formGroup;
    private $formGroupTitle;
    private $formGroupCollapse;
    private $formInputRowBasic;
    private $formInputRowVertical;
    private $formInputBasic;
    private $formInputVertical;

    public function __construct()
    {
        $this->setFormWrapper(
            '<div class="card">
                    #FORM_HEADER#
                    #FORM_BODY#
                </div>'
        );

        $this->setFormHeader(
            '<div class="card-header header-elements-inline">
                    <h5 class="card-title">#FORM_TITLE#</h5>
                    <div class="header-elements">
                        <div class="list-icons">
                            #FORM_ACTION#
                        </div>
                    </div>
                </div>'
        );

        $this->setFormHeaderAction(
            '<a class="list-icons-item" data-action="#ACTION#"></a>'
        );

        $this->setFormBody(
            '<div class="card-body">
                    <form #FORM_PARAMS#>
                        <div class="row">
                            #FORM_GROUPS#
                        </div>
                        <div>
                          #FORM_BUTTONS#
                        </div>
                    </form>
                </div>'
        );

        $this->setFormGroup(
            '<div class="col-md-#COL_SIZE#">
                    <fieldset class="card">
                    <div class="card-body">
                        #GROUP_TITLE#
                        <div class="collapse show" id="#GROUP_ID#">
                            #GROUP_INPUTS#
                        </div>
                    </div>
                    </fieldset>
                </div>'
        );

        $this->setFormGroupTitle(
            '<legend class="font-weight-semibold text-uppercase font-size-sm">
                        <i class="#GROUP_ICON#"></i>
                        #GROUP_NAME#
                        #GROUP_COLLAPSE#
                    </legend>'
        );

        $this->setFormGroupCollapse(
            '<a class="float-right text-default" data-toggle="collapse" data-target="##GROUP_ID#">
                            <i class="icon-circle-down2"></i>
                        </a>'
        );

        $this->setFormInputRowBasic(
            '<div class="form-group row">
                    <label class="col-form-label col-lg-#LABEL_SIZE#">#LABEL#</label>
                    <div class="col-lg-#INPUT_SIZE#">
                        <div class="row">
                            #INPUT#
                        </div>
                    </div>
                </div>'
        );

        $this->setFormInputRowVertical(
            '<div class="form-group row">
                    #INPUT#
                </div>'
        );

        $this->setFormInputBasic(
            '<div class="col-lg-#INPUT_SIZE#">
                     #INPUT#
                     <span class="form-text text-muted">#HELPER_TEXT#</span>
                </div>'
        );

        $this->setFormInputVertical(
            '<div class="col-md-#INPUT_SIZE#">
                    <label>#LABEL#</label>
                    #INPUT#
                   <span class="form-text text-muted">#HELPER_TEXT#</span>
                </div>'
        );
    }

    public function getFormWrapper()
    {
        return $this->formWrapper;
    }

    public function setFormWrapper(string $val)
    {
        $this->formWrapper = $val;
    }

    public function getFormHeader()
    {
        return $this->formHeader;
    }

    public function setFormHeader(string $val)
    {
        $this->formHeader = $val;
    }

    public function getFormBody()
    {
        return $this->formBody;
    }

    public function setFormBody(string $val)
    {
        $this->formBody = $val;
    }

    public function getFormGroup()
    {
        return $this->formGroup;
    }

    public function setFormGroup(string $val)
    {
        $this->formGroup = $val;
    }

    public function getFormGroupCollapse()
    {
        return $this->formGroupCollapse;
    }

    public function setFormGroupCollapse(string $val)
    {
        $this->formGroupCollapse = $val;
    }

    public function getFormHeaderAction()
    {
        return $this->formHeaderAction;
    }

    public function setFormHeaderAction(string $val)
    {
        $this->formHeaderAction = $val;
    }

    public function getFormGroupTitle()
    {
        return $this->formGroupTitle;
    }

    public function setFormGroupTitle(string $val)
    {
        $this->formGroupTitle = $val;
    }

    public function getFormInputRowBasic()
    {
        return $this->formInputRowBasic;
    }

    public function setFormInputRowBasic(string $val)
    {
        $this->formInputRowBasic = $val;
    }

    public function getFormInputRowVertical()
    {
        return $this->formInputRowVertical;
    }

    public function setFormInputRowVertical(string $val)
    {
        $this->formInputRowVertical = $val;
    }

    public function getFormInputBasic()
    {
        return $this->formInputBasic;
    }

    public function setFormInputBasic(string $val)
    {
        $this->formInputBasic = $val;
    }

    public function getFormInputVertical()
    {
        return $this->formInputVertical;
    }

    public function setFormInputVertical(string $val)
    {
        $this->formInputVertical = $val;
    }

    protected function replaceTagsTemplate($template, array $aReplace)
    {
        $s = $template;

        if ($aReplace !== null && is_array($aReplace)) {
            foreach ($aReplace as $search => $replace) {
                $s = str_replace($search, $replace, $s);
            }
        }

        return $s;
    }
}