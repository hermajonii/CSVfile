<?php
    $patternUPC='#^[A-Za-z-,()& ]{2,50}$#';
    echo preg_match($patternUPC,"Battery Backup (UPS)-& ");
?>