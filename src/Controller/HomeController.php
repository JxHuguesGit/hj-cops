<?php
namespace src\Controller;

use src\Constant\LabelConstant;
use src\Constant\TemplateConstant;

class HomeController extends UtilitiesController
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->isLogged) {
            $this->title = LabelConstant::LBL_LOGIN;
        } else {
            $this->title = LabelConstant::LBL_DESK;
        }
    }

    public function getContentPage(string $msgProcessError): string
    {
        if (!$this->isLogged) {
            $attributes = [
                $msgProcessError=='' ? 'hidden' : '',
                $msgProcessError,
            ];
            return $this->getRender(TemplateConstant::TPL_CONN_PANEL, $attributes);
        } else {
            if ($this->player->isGuest()) {
                $attributes = [
                    12, 'Guest Dashboard WIP', '0 hidden', 'caché',
                ];
            } else {
                $playerWidgets = $this->player->getWidgets();
                $widgetPanel = '';
                while ($playerWidgets->valid()) {
                    $playerWidget = $playerWidgets->current();
                    $widgetPanel .= $playerWidget->getController()->displayCard();
                    $playerWidgets->next();
                }

                $attributes = [
                    '9 row', $widgetPanel, 3, '',
                ];
            }
            return $this->getRender(TemplateConstant::TPL_DASHBOARD_PANEL, $attributes);
        }
    }
}
