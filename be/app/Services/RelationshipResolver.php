<?php

namespace App\Services;

use App\Models\ThanhVien;

class RelationshipService
{
    public function traCuu($id1, $id2)
    {
        $n1 = ThanhVien::find($id1);
        $n2 = ThanhVien::find($id2);

        if (!$n1 || !$n2) {
            return "Không tìm thấy thành viên";
        }

        $isNam = $n1->gioi_tinh === 'Nam';
        $rel = null;

        // Vợ chồng
        if ($n1->spouse_of_id == $n2->id || $n2->spouse_of_id == $n1->id) {
            $rel = $isNam ? 'chồng' : 'vợ';
        }

        // Cha mẹ
        elseif ($n2->cha_id == $n1->id || $n2->me_id == $n1->id) {
            $rel = $isNam ? 'cha' : 'mẹ';
        }

        // Con
        elseif ($n1->cha_id == $n2->id || $n1->me_id == $n2->id) {
            $rel = $isNam ? 'con trai' : 'con gái';
        }

        // Anh chị em
        elseif (
            $n1->cha_id &&
            $n1->cha_id == $n2->cha_id &&
            $n1->id != $n2->id
        ) {
            $isOld = strtotime($n1->ngay_sinh) < strtotime($n2->ngay_sinh);

            if ($isNam) {
                $rel = $isOld ? 'anh trai' : 'em trai';
            } else {
                $rel = $isOld ? 'chị gái' : 'em gái';
            }
        }

        return $rel
            ? "{$n1->ho_ten} là {$rel} của {$n2->ho_ten}"
            : "Chưa xác định được quan hệ";
    }
}