<?php
namespace src\Controller;

use src\Collection\CourseCollection;
use src\Collection\SkillCollection;
use src\Constant\ConstantConstant;
use src\Constant\FieldConstant;
use src\Constant\LabelConstant;
use src\Constant\TemplateConstant;
use src\Entity\Course;
use src\Repository\CourseRepository;
use src\Repository\SkillRepository;
use src\Utils\CardUtils;
use src\Utils\HtmlUtils;
use src\Utils\TableUtils;

class CourseController extends UtilitiesController
{
    private Course $course;

    public function __construct(Course $course=null)
    {
        $this->course = $course ?? new Course();
    }

    public function getContentPage($arrParams): string
    {
        $this->arrParams = $arrParams;
        
        $repository = new CourseRepository(new CourseCollection());
        $courses = $repository->findAll([FieldConstant::CATEGORY=>'asc', FieldConstant::LEVEL=>'asc']);

        $table = new TableUtils();
        $table->setTable([ConstantConstant::CST_CLASS=>'table-sm table-striped']);
        $table->setPaginate([
            ConstantConstant::PAGE_OBJS => $courses,
            ConstantConstant::CST_CURPAGE => $this->arrParams[ConstantConstant::CST_CURPAGE] ?? 1,
            ConstantConstant::CST_URL => '/library/?page=courses',
        ]);

        $table->addHeaderRow()
            ->addHeaderCell(['content'=>'#'])
            ->addHeaderCell(['content'=>'Catégorie'])
            ->addHeaderCell(['content'=>'Nom'])
            ->addHeaderCell(['content'=>'Niveau'])
            ->addHeaderCell(['content'=>'Source']);

        $table->addBodyRows($courses, 5, $arrParams);

        $viewCard = '';
        if (isset($this->arrParams['action']) && $this->arrParams['action']=='view') {
            $repository = new CourseRepository(new CourseCollection());
            $course = $repository->find($this->arrParams['id']);
            if ($course!=null) {
                if (!isset($this->arrParams['type']) || $this->arrParams['type']=='course') {
                    $viewCard = $course->getController()->getCard();
                } else {
                    $viewCard = $course->getController()->getAccordion();
                }
            }
        }

        $attributes = [
            LabelConstant::LBL_COURSES,
            $table->display(),
            $viewCard,
        ];
        return $this->getRender(TemplateConstant::TPL_LIBRARY_SUBPANEL, $attributes);
    }

    // TODO : Méthode vraiment utile ou juste un copié collé inutile de Skill ?
    public function getLi(int $score=-1): string
    {
        $classe = 'dropdown-item ajaxAction';
        $href   = '/profile/?action=addcourse&amp;courseId='.$this->course->getField(FieldConstant::ID);
        $attributes = [
            /*
            'data-speclevel'    => $this->skill->getField(FieldConstant::SPECLEVEL),
            'data-skillid'      => $this->skill->getField(FieldConstant::ID),
            'data-parentid'     => $this->skill->getField(FieldConstant::SKILLID),
            'data-score'        => $score,
            'data-trigger'      => 'click',
            'data-ajax'         => 'skillCreation',
            */
        ];
        $label = $this->course->getField(FieldConstant::NAME);
        return HtmlUtils::getLi(HtmlUtils::getLink($label, $href, $classe, $attributes));
    }

    public function addBodyRow(TableUtils &$table, array $arrParams=[]): void
    {
        $id = $this->course->getField(FieldConstant::ID);
        $href = '/library/?page=courses&amp;action=view&amp;id='.$id;
        if (isset($arrParams[ConstantConstant::CST_CURPAGE])) {
            $href .= '&amp;'.ConstantConstant::CST_CURPAGE.'='.$arrParams[ConstantConstant::CST_CURPAGE];
        }
        $category = $this->course->getField(FieldConstant::CATEGORY);
        $strLinkCateg = HtmlUtils::getLink($category, $href.'&amp;type=category');
        $name = $this->course->getField(FieldConstant::NAME);
        $strLinkName = HtmlUtils::getLink($name, $href.'&amp;type=course');
        $level = $this->course->getField(FieldConstant::LEVEL);

        $strIcon = HtmlUtils::getIcon('medal');
        $bgColor = 'btn btn-sm disabled btn-';
        if ($level==3) {
            $bgColor .= 'gold';
        } elseif ($level==2) {
            $bgColor .= 'silver';
        } else {
            $bgColor .= 'bronze';
        }
        $strSpanLevel = HtmlUtils::getSpan($strIcon, [ConstantConstant::CST_CLASS=>$bgColor]);

        $bgColor = 'btn btn-sm disabled btn-';
        $reference = $this->course->getField(FieldConstant::REFERENCE);
        if (
            strpos($reference, 'post')===false &&
            strpos($reference, 'ici')===false &&
            strpos($reference, 'inconnu')===false
        ) {
            $strIcon = HtmlUtils::getIcon('check');
            $bgColor .= 'success';
        } else {
            $strIcon = HtmlUtils::getIcon('times');
            $bgColor .= 'danger';
        }
        $strSpanOff = HtmlUtils::getSpan($strIcon, [ConstantConstant::CST_CLASS=>$bgColor]);

        $table->addBodyRow()
            ->addBodyCell(['content'=>$id])
            ->addBodyCell(['content'=>$strLinkCateg])
            ->addBodyCell(['content'=>$strLinkName])
            ->addBodyCell(['content'=>$strSpanLevel])
            ->addBodyCell(['content'=>$strSpanOff]);
    }

    public function getCard(bool $hideCateg=false): string
    {
        $card = new CardUtils();
        return $card->setHeader(['content'=>$this->course->getField(FieldConstant::NAME)])
            ->setBody(['content'=>$this->getForm($hideCateg)])
            ->setFooter([ConstantConstant::CST_CLASS=>' hidden'])
            ->display();
    }

    public function getForm(bool $hideCateg=false): string
    {
        $level = $this->course->getField(FieldConstant::LEVEL);
        $bgColor = 'bg-';
        if ($level==3) {
            $bgColor .= 'gold';
        } elseif ($level==2) {
            $bgColor .= 'silver';
        } else {
            $bgColor .= 'bronze';
        }

        $strReference = $this->getStrReference($this->course->getField(FieldConstant::REFERENCE));
        $strPrequisite = $this->getStrPrequisite($this->course->getField(FieldConstant::PREREQUISITE));

        $attributes = [
            '',
            $this->course->getField(FieldConstant::CATEGORY),
            $this->course->getField(FieldConstant::DESCRIPTION),
            $bgColor,
            $level,
            $strReference,
            $strPrequisite,
            $this->course->getField(FieldConstant::CUMUL),
            $this->course->getField(FieldConstant::BONUS),
            $hideCateg ? 'hidden' : '',// Pour cacher la ligne catégory, mettre hidden
        ];
        return $this->getRender(TemplateConstant::TPL_COURSE_FORM, $attributes);
    }

    public function getAccordion(): string
    {
        $category = $this->course->getField(FieldConstant::CATEGORY);
        // On doit récupérer les stages associés à la catégorie du stage courant.
        $repository = new CourseRepository(new CourseCollection());
        $courses = $repository->findBy([FieldConstant::CATEGORY=>$category], [FieldConstant::LEVEL=>'asc']);
        $nb = $courses->length();

        $attributes = [$category];

        while ($courses->valid()) {
            $course = $courses->current();

            // 5 éléments :
            // Le stage de ce rang existe-t-il ? Puisque je suis dans la boucle, oui => ''. Sinon 'hidden'
            array_push($attributes, '');
            // Le stage est-il celui sélectionné ? Si oui => ''. Sinon 'collapsed'
            $blnSelect = $course->getField(FieldConstant::ID)==$this->course->getField(FieldConstant::ID);
            array_push($attributes, $blnSelect ? '' : 'collapsed');
            // Le nom du stage
            array_push($attributes, $course->getField(FieldConstant::NAME));
            // Le stage est-il celui sélectionné ? Si oui => 'show'. Sinon ''
            array_push($attributes, $blnSelect ? 'show' : '');
            // Le card du stage
            array_push($attributes, $course->getController()->getForm(true));

            $courses->next();
        }

        for ($i=$nb; $i<3; ++$i) {
            array_push($attributes, 'hidden', 'collapsed', '', '', '');
        }

        return $this->getRender(TemplateConstant::TPL_COURSE_ACCORDION, $attributes);
    }

    private function getStrPrequisite(string $str): string
    {
        $this->sRepository = new SkillRepository(new SkillCollection());

        $this->arrCaracs = [
            'CA'=>'Carrure',
            'CH'=>'Charme',
            'CO'=>'Coordination',
            'ED'=>'Éducation',
            'PE'=>'Perception',
            'RE'=>'Réflexes',
            'SA'=>'Sang-froid'
        ];

        $prerequisite = $str;
        $strPrequisite = "$prerequisite\n";
        $arr = explode ('/', $prerequisite);
        while (!empty($arr)) {
            $ref = array_shift($arr);
            // Dans le cas d'un | on met dans un tableau chaque cas et pour coller au reste
            // on met aussi le cas seul dans un tableau.
            if (strpos($ref, '|')!==false) {
                $arrRefs =  explode('|', $ref);
            } else {
                $arrRefs = [$ref];
            }
            $strPrequisite .= $this->getPrequisiteContent($arrRefs).', ';
        }
        return substr($strPrequisite, 0, -2).'.';
    }

    private function getPrequisiteContent(array $arrRefs): string
    {
        $strPrequisite = '';
        $nb = count($arrRefs);
        $strCmp = $nb==1 ? '' : ' ou ';

        while (!empty($arrRefs)) {
            $ref = array_shift($arrRefs);
            $l2 = substr($ref, 0, 2);
            if ($ref=='N1') {
                $strPrequisite .= 'Niveau 1'.(substr($ref, 2)=='' ? '' : ' '.substr($ref, 2)).$strCmp;
            } elseif ($ref=='N2') {
                $strPrequisite .= 'Niveau 2'.$strCmp;
            } elseif (in_array($l2, ['CA', 'CH', 'CO', 'ED', 'PE', 'RE', 'SA'])) {
                $strPrequisite .= $this->arrCaracs[$l2].' '.substr($ref, 2).$strCmp;
            } elseif ($l2=='AN') {
                $strPrequisite .= substr($ref, 2).' point d\'Ancienneté'.$strCmp;
            } elseif ($l2=='AD') {
                $strPrequisite .= substr($ref, 2).' point d\'Adrénaline'.$strCmp;
            } elseif ($l2=='ST') {
                // TODO
                $strPrequisite .= 'Stage '.substr($ref, 2).$strCmp;
            } elseif (strpos($ref, '::')!==false) {
                // TODO
                $strPrequisite .= $ref.$strCmp;
            } elseif (strpos($ref, '??')!==false) {
                // TODO
                $strPrequisite .= $ref.$strCmp;
            } elseif (strpos($ref, ':')!==false) {
                list($skillId, $value) = explode(':', $ref);
                $skill = $this->sRepository->find($skillId);
                $strPrequisite .= $skill->getFullName().' '.$value.'+'.$strCmp;
            } else {
                $strPrequisite .= $ref.$strCmp;
            }
        }

        if ($nb==1) {
            return $strPrequisite;
        } else {
            return substr($strPrequisite, 0, -4);
        }
    }

    private function getStrReference(string $str): string
    {
        $reference = str_replace('-', '', $str);
        $strReference = '';
        switch (substr($reference, 0, 2)) {
            case 'DQ' :
                $strReference = '10-99 p'.substr($reference, 2);
                break;
            case 'HL' :
                $strReference = 'Horizons Lointains p'.substr($reference, 2);
                break;
            case 'HS' :
                $strReference = 'Helter Skelter p'.substr($reference, 2);
                break;
            case 'LB' :
                $strReference = 'Lignes Blanches p'.substr($reference, 2);
                break;
            case 'PI' :
                $strReference = 'Pilote p'.substr($reference, 3);
                break;
            default :
                if (strpos($reference, 'post')!== false) {
                    $strReference = 'Central COPS';
                } elseif (strpos($reference, 'ici')!== false) {
                    $strReference = 'Autre';
                } else {
                    $strReference = $reference;
                }
        }
        return $strReference;
    }
}
