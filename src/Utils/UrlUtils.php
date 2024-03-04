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
}
