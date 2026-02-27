<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

use Bitrix\Main\Localization\Loc;

if (!defined("WIZARD_TEMPLATE_ID"))
	return;

//$bitrixTemplateDir = $_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/".WIZARD_TEMPLATE_ID."_".WIZARD_THEME_ID;
$bitrixTemplateDir = WIZARD_TEMPLATE_ABSOLUTE_PATH ."b2bcabinet_v2.0";

$localTemplateDir = $_SERVER["DOCUMENT_ROOT"] ."/local/templates/";

if(is_dir($localTemplateDir)) {
	mkdir($localTemplateDir, 0775, true);
}

CopyDirFiles(WIZARD_TEMPLATE_ABSOLUTE_PATH, $localTemplateDir, true, true);


$fileTemplate = $bitrixTemplateDir .'/template_content.php';
$fileStyle = $bitrixTemplateDir .'/styles.css';

/*
if(file_exists($fileTemplate)){
	$fileRes = fopen($fileTemplate, 'r');
	$content = fread($fileRes, filesize($fileTemplate));
	fclose($fileRes);
}*/

if(file_exists($fileStyle)){
	$fileRes = fopen($fileStyle, 'r');
	$styles = fread($fileRes, filesize($fileStyle));
	fclose($fileRes);
}

if(!CSiteTemplate::GetById('b2bcabinet_v2.0')->fetch())
{
	$template = new CSiteTemplate();

	$res = $template->Add(array(
		'ID' => 'b2bcabinet_v2.0',
		'CONTENT' => $content,
		'NAME' => Loc::getMessage('TEMPLATE_MODULE_NAME'),
		'DESCRIPTION' => Loc::getMessage('TEMPLATE_MODULE_NAME'),
		'SORT' => '100',
		'STYLES' => $styles
	));
}
else
{
	$template = new CSiteTemplate();

	$res = $template->Update(
		'b2bcabinet_v2.0',
		array(
			'CONTENT' => $content,
			'STYLES' => $styles
		)
	);
}

$rsTemplates = CSite::GetTemplateList(WIZARD_SITE_ID);
while($arTemplate = $rsTemplates->Fetch())
{
	if($arTemplate['TEMPLATE'] !== 'b2bcabinet_v2.0')
		$result[] = $arTemplate;
}

$ssort=array();
$res2=array();
$methodInstall = $wizard->GetVar("method_install");

if($methodInstall == 'AS_TEMPLATE')
{
	foreach($result as $template)
	{
		unset($template["ID"]);
		unset($template["SITE_ID"]);
		array_push($ssort,intval($template["SORT"]));
		$res2[]=$template;
	}
}

$res2[] = array(
	'CONDITION' => ($methodInstall == 'AS_TEMPLATE' ? "CSite::InDir('/b2bcabinet/')" : '' ),
	'SORT' => 50,
	'TEMPLATE' => "b2bcabinet_v2.0"
);

$obSite = new CSite();
$t = $obSite->Update(WIZARD_SITE_ID, array('TEMPLATE'=>$res2));

$wizrdTemplateId = $wizard->GetVar("wizTemplateID");

if (!in_array($wizrdTemplateId, array("b2bcabinet_v2.0")))
	$wizrdTemplateId = "b2bcabinet_v2.0";

COption::SetOptionString("main", "wizard_template_id", $wizrdTemplateId, false, WIZARD_SITE_ID);
COption::SetOptionString("sotbit.b2bcabinet", "method_install", $methodInstall, false, WIZARD_SITE_ID);
COption::SetOptionString("sotbit.b2bcabinet", "HEADER_TYPE", $wizard->GetVar("installTheme"), false, WIZARD_SITE_ID);
?>
