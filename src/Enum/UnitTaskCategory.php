<?php

namespace App\Enum;

enum UnitTaskCategory: string
{
    case MAINTENANCE = 'Maintenance'; 
    case REPAIR = 'Repair';           
    case INSTALLATION = 'Installation';
    case MONITORING = 'Monitoring';   
    case INSPECTION = 'Inspection';    
    case CLEANING = 'Cleaning';         
    case UPGRADING = 'Upgrading';    
    case TROUBLESHOOTING = 'Troubleshooting';

    /* ÄNDRA DESSA EFTERHAND */
}

