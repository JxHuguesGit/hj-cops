<?php
namespace src\Controller;

use src\Constant\FieldConstant;
use src\Constant\TemplateConstant;
use src\Entity\Player;
use src\Exception\TemplateException;
use src\Utils\SessionUtils;

class UtilitiesController
{
    protected array $arrParams=[];
    protected bool $isLogged;
    protected string $title;
    protected Player $player;

    public function __construct(array $arrUri=[])
    {
        $this->isLogged = SessionUtils::isLogged();
        $this->player = SessionUtils::getPlayer() ?? new Player();

        if (isset($arrUri[2]) && !empty($arrUri[2])) {
            $params = substr($arrUri[2], 0, 1)=='?' ? substr($arrUri[2], 1) : $arrUri[2];
            $arrParams = explode('&', $params);
            while (!empty($arrParams)) {
                $param = array_shift($arrParams);
                list($key, $value) = explode('=', $param);
                $this->arrParams[str_replace('amp;', '', $key)] = $value;
            }
        }
    }

    public function setParams(array $params=[]): self
    {
        $this->arrParams = $params;
        return $this;
    }
    
    public function getRender(string $urlTemplate, array $args=[]): string
    {
        if (file_exists(PLUGIN_PATH.$urlTemplate)) {
            return vsprintf(file_get_contents(PLUGIN_PATH.$urlTemplate), $args);
        } else {
            throw new TemplateException($urlTemplate);
        }
    }

    public function getContentFooter()
    {
        if ($this->isLogged) {
            return $this->getRender(TemplateConstant::TPL_FOOTER);
        } else {
            return '';
        }
    }

    public function getContentHeader()
    {
        if ($this->isLogged) {
            $attributes = [
                PLUGINS_COPS.'assets/images/',
                $this->player->getFullName(),
                'mask-4',
                $this->player->getField(FieldConstant::ID)==64 ? 'hidden' : '',
            ];
            return $this->getRender(TemplateConstant::TPL_HEADER, $attributes);
        } else {
            return '';
        }
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getClassLogged(): string
    {
        return $this->isLogged ? '' : 'notlogged';
    }

}
