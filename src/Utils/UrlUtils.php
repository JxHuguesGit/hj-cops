<?php
namespace src\Utils;

class UrlUtils
{

    public static function getAdminUrl(array $attributes=[]): string
    {
        $urlRoot = '/wp-admin/admin.php?page=hj-cops/admin_manage.php';
    
        if (!empty($attributes)) {
            foreach ($attributes as $key => $value) {
                $urlRoot .= '&amp;'.$key.'='.$value;
            }
        }
        return $urlRoot;
    }

    public static function getPublicUrl(string $slug, array $attributes=[]): string
    {
        $urlRoot  = '/'.$slug.'/';
    
        if (!empty($attributes)) {
            $urlRoot .= '?';
            $first = true;
            foreach ($attributes as $key => $value) {
                if ($first) {
                    $first = false;
                } else {
                    $urlRoot .= '&amp;';
                }
                $urlRoot .= $key.'='.$value;
            }
        }
        return $urlRoot;
    }

    public static function getAssetUrl(string $dir): string
    {
        return PLUGINS_COPS.'assets/'.$dir.'/';
    }
}
