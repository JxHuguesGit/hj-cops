<?php
namespace src\Controller;

use src\Constant\ConstantConstant;
use src\Constant\CssConstant;
use src\Constant\LabelConstant;
use src\Constant\TemplateConstant;
use src\Utils\CardUtils;
use src\Utils\HtmlUtils;
use src\Utils\TableUtils;

class CentralController extends UtilitiesController
{
    public function __construct(array $arrUri=[])
    {
        parent::__construct($arrUri);
        $this->title = LabelConstant::LBL_CENTRAL_LAPD;
    }

    public function getContentPage(): string
    {
        if (!isset($this->arrParams[ConstantConstant::CST_PAGE])) {
            // Sur la vue globale, pas de page en particulier
            $returned =  $this->getEtages();
        } elseif (substr($this->arrParams[ConstantConstant::CST_PAGE], 0, 3)=='wp-') {
            // Si on veut afficher une page "article".
            $slug = $this->arrParams[ConstantConstant::CST_PAGE];
            $args = ['name'=>$slug, 'post_type'=>'post', 'post_status'=>'publish', 'posts_per_page'=>1];
            $posts = get_posts($args);
            if (!empty($posts)) {
                $returned = $this->getWpArticle($posts);
            } else {
                $returned =  $this->getEtages();
            }
        } else {
            $returned = 'Traitement particulier de certains étages.';
        }
        return $returned;
    }

    private function getEtages(): string
    {
        $etages = [
            '35' => ['row'=>1, 'data'=>['services'=>['COPS'], 'titles'=>[''], 'urls'=>[''], 'descriptions'=>['Description à mettre']]],
            '31' => ['row'=>1, 'data'=>['services'=>['SAD'], 'titles'=>['Special Affairs Division'], 'urls'=>['wp-sad'], 'descriptions'=>['Description à mettre']]],
            '25' => ['row'=>2, 'data'=>['services'=>['ASD'], 'titles'=>['Air Support Division'], 'urls'=>['wp-asd'], 'descriptions'=>['Partie administration et bureaux des détectives', 'Hangars et ateliers mécaniques']]],
        ];

        $table = new TableUtils();
        $table->setTable([ConstantConstant::CST_CLASS => CssConstant::CSS_TABLE_SM.' '.CssConstant::CSS_TABLE_STRIPED])
            ->addHeaderRow()
            ->addHeaderCell([ConstantConstant::CST_CONTENT=>'Etages'])
            ->addHeaderCell([ConstantConstant::CST_CONTENT=>'Services'])
            ->addHeaderCell([ConstantConstant::CST_CONTENT=>'Description']);
        
        foreach ($etages as $numEtage => $dataEtage) {
            $table->addBodyRow();
            $table->addBodyCell([ConstantConstant::CST_CONTENT=>$numEtage]);

            $rows = $dataEtage['row'];
            for ($i=0; $i<$rows; $i++) {
                if ($i!=0) {
                    $table->addBodyRow();
                    $table->addBodyCell([ConstantConstant::CST_CONTENT=>$numEtage-$i]);
                }
                $data = $dataEtage['data'];
                if ($i==0) {
                    $strService = '';
                    $nbServices = count($data['services']);
                    $rkService = 0;
                    foreach ($data['services'] as $service) {
                        if ($rkService!=0) {
                            if ($rkService==$nbServices-1) {
                                $strService .= ' &amp; ';
                            } else {
                                $strService .= ', ';
                            }
                        }
                        $slug = array_shift($data['urls']);
                        if ($slug!='') {
                            $specialService = HtmlUtils::getLink($service, '/central/?page='.$slug);
                        } else {
                            $specialService = $service;
                        }
                        $title = array_shift($data['titles']);
                        $strService .= $title=='' ? $specialService : HtmlUtils::getBalise('abbr', $specialService, ['title'=>$title]);

                        $rkService++;
                    }
                    $table->addBodyCell([ConstantConstant::CST_CONTENT=>$strService, 'attributes'=>['rowspan'=>$rows]]);
                }
                $table->addBodyCell([ConstantConstant::CST_CONTENT=>$data['descriptions'][$i]]);
            }
        }

        return $this->getRender(TemplateConstant::TPL_CENTRAL_PANEL, [$table->display()]);
    }

    private function getWpArticle(array $posts): string
    {
        $post = $posts[0];

        $postId = $post->ID; // current post ID
        $categoryNames = wp_get_post_categories($postId, ['fields'=>'ids']);
        $currentCatId = $categoryNames[0]; // current category ID
        $args = [
            'category' => $currentCatId,
            'orderby'  => 'post_title',
            'order'    => 'ASC'
        ];
        $posts = get_posts($args);

        $prevLink = '';
        $nextLink = '';
        $blnPrev = true;
        // get IDs of posts retrieved from get_posts
        foreach ($posts as $thepost) {
            $id = $thepost->ID;
            if ($postId==$id) {
                $blnPrev = false;
            } elseif ($blnPrev) {
                $prevLink = '<a rel="prev" href="/central/?page=' . $thepost->post_name . '">' . $thepost->post_title . '</a>';
            } else {
                $nextLink = '<a rel="next" href="/central/?page=' . $thepost->post_name . '">' . $thepost->post_title . '</a>';
                break;
            }
        }

        $footerContent  = '<span class="float-start">'.$prevLink.'</span>';
        $footerContent .= '<span class="float-end">'.$nextLink.'</span>';

        $card = new CardUtils(['style'=>'height:80%;']);
        $card
            ->addClass('col-12 col-sm-8 offset-sm-2')
            ->setHeader([ConstantConstant::CST_CONTENT=>$post->post_title])
            ->setBody([ConstantConstant::CST_CLASS=>' overflow-auto',ConstantConstant::CST_CONTENT=>$post->post_content])
            ->setFooter([ConstantConstant::CST_CONTENT=>$footerContent]);
        return '<br>'.$card->display();
    }
}
