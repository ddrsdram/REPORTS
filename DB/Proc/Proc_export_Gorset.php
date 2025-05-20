<?php

namespace DB\Proc;

class Proc_export_Gorset extends \DB\Table
{
    public function parameters($_ORG,$_id_month)
    {
        return $this
	            ->set('_ORG',$_ORG)
	            ->set('_id_month',$_id_month)
            ->SQLExec();
    }
}
