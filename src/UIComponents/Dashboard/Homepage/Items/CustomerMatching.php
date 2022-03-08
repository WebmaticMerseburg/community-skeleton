<?php

namespace App\UIComponents\Dashboard\Homepage\Items;

use Webkul\UVDesk\CoreFrameworkBundle\Dashboard\Segments\HomepageSectionItem;
use Webkul\UVDesk\CoreFrameworkBundle\UIComponents\Dashboard\Homepage\Sections\Users;

class CustomerMatching extends HomepageSectionItem
{
    CONST SVG = <<<SVG
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512">
    <!--! Font Awesome Pro 6.0.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. -->
    <path d="M96 304.1c0-12.16 4.971-23.83 13.64-32.01l72.13-68.08c1.65-1.555 3.773-2.311 5.611-3.578C177.1 176.8 155 160 128 160H64C28.65 160 0 188.7 0 224v96c0 17.67 14.33 32 31.1 32L32 480c0 17.67 14.33 32 32 32h64c17.67 0 32-14.33 32-32v-96.39l-50.36-47.53C100.1 327.9 96 316.2 96 304.1zM480 128c35.38 0 64-28.62 64-64s-28.62-64-64-64s-64 28.62-64 64S444.6 128 480 128zM96 128c35.38 0 64-28.62 64-64S131.4 0 96 0S32 28.62 32 64S60.63 128 96 128zM444.4 295.3L372.3 227.3c-3.49-3.293-8.607-4.193-13.01-2.299C354.9 226.9 352 231.2 352 236V272H224V236c0-4.795-2.857-9.133-7.262-11.03C212.3 223.1 207.2 223.1 203.7 227.3L131.6 295.3c-4.805 4.535-4.805 12.94 0 17.47l72.12 68.07c3.49 3.291 8.607 4.191 13.01 2.297C221.1 381.3 224 376.9 224 372.1V336h128v36.14c0 4.795 2.857 9.135 7.262 11.04c4.406 1.893 9.523 .9922 13.01-2.299l72.12-68.07C449.2 308.3 449.2 299.9 444.4 295.3zM512 160h-64c-26.1 0-49.98 16.77-59.38 40.42c1.842 1.271 3.969 2.027 5.623 3.588l72.12 68.06C475 280.2 480 291.9 480 304.1c.002 12.16-4.969 23.83-13.64 32.01L416 383.6V480c0 17.67 14.33 32 32 32h64c17.67 0 32-14.33 32-32v-128c17.67 0 32-14.33 32-32V224C576 188.7 547.3 160 512 160z"/>
    </svg>
SVG;

    public static function getIcon() : string
    {
        return self::SVG;
    }

    public static function getTitle() : string
    {
        return "Customer matching";
    }

    public static function getRouteName() : string
    {
        return 'webmatic_customer_matching';
    }

    public static function getRoles() : array
    {
        return ['ROLE_AGENT_MANAGE_CUSTOMER'];
    }

    public static function getSectionReferenceId() : string
    {
        return Users::class;
    }
}
