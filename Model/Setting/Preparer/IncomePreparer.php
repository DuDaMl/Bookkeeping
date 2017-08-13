<?php
namespace bookkeeping\Model\Setting\Preparer;

use bookkeeping\Model\Setting\Setting;

class IncomePreparer extends Preparer
{
    public static function getInstance($type)
    {

    }

    function prepare(Setting $obj, array $date)
    {
        $obj->format = trim($date['format']);
        $date_mask = trim($date[$obj->format]);

        switch($obj->format)
        {
            case 'day':
                $data_report_start = $date_mask;
                $data_report_end = $date_mask;
                break;
            case 'month':
                $data_report_start = $date_mask . "-01";
                $data_report_end = $date_mask . "-31";
                break;
            case 'year':
                $data_report_start = $date_mask . "-01-01";
                $data_report_end = $date_mask . "-12-31";
                break;
            default:
                $data_report_start = date('Y-m-01');
                $data_report_end = date('Y-m-31');
                break;
        }

        $obj->date_start = $this->validateDate( $data_report_start, 'Y-m-d');
        $obj->date_end = $this->validateDate( $data_report_end, 'Y-m-d');
    }
}