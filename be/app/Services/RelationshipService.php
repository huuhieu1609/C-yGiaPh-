<?php

namespace App\Services;

use App\Models\ThanhVien;

class RelationshipService
{
    private $nguoi1;
    private $nguoi2;
    private $isNam1;

    private $chaNguoi1;
    private $meNguoi1;

    private $chaNguoi2;
    private $meNguoi2;

    public function resolve(
        ThanhVien $nguoi1,
        ThanhVien $nguoi2
    ): ?string {

        $this->nguoi1 = $nguoi1;
        $this->nguoi2 = $nguoi2;

        $this->isNam1 =
            $nguoi1->gioi_tinh === 'Nam';

        // Tối ưu: Gom 4 câu query `find()` thành 1 câu query `whereIn` duy nhất.
        $parentIds = array_filter([
            $nguoi1->cha_id,
            $nguoi1->me_id,
            $nguoi2->cha_id,
            $nguoi2->me_id,
        ]);

        $parents = !empty($parentIds)
            ? ThanhVien::whereIn('id', $parentIds)->get()->keyBy('id')
            : collect();

        $this->chaNguoi1 = $parents->get($nguoi1->cha_id);
        $this->meNguoi1 = $parents->get($nguoi1->me_id);
        $this->chaNguoi2 = $parents->get($nguoi2->cha_id);
        $this->meNguoi2 = $parents->get($nguoi2->me_id);

        $checks = [
            'checkVoChong',
            'checkChaMe',
            'checkConCai',
            'checkAnhChiEm',
            'checkOngBa',
            'checkChuBacCo',
            'checkDiCau',
            'checkChau', // Gộp cháu nội/ngoại và cháu (con của anh chị em)
        ];

        foreach ($checks as $method) {

            $relationship =
                $this->{$method}();

            if ($relationship) {
                return $relationship;
            }

        }

        return null;
    }

    /**
     * Tra cứu chi tiết quan hệ bao gồm cả lộ trình (path) và danh sách thành viên (members).
     *
     * @param ThanhVien $nguoi1
     * @param ThanhVien $nguoi2
     * @return array
     */
    public function resolveDetailed(ThanhVien $nguoi1, ThanhVien $nguoi2): array
    {
        if ($nguoi1->id === $nguoi2->id) {
            return [
                'relationship' => 'bản thân',
                'path' => [],
                'members' => [
                    [
                        'id' => $nguoi1->id,
                        'ho_ten' => $nguoi1->ho_ten,
                        'gioi_tinh' => $nguoi1->gioi_tinh,
                    ]
                ]
            ];
        }

        $graphData = $this->graphService->buildGraph($nguoi1->chi_nhanh_id);
        $graph = $graphData['graph'];
        $personsMap = $graphData['map'];

        $path = $this->graphService->findShortestPath($nguoi1->id, $nguoi2->id, $graph);

        if ($path === null || empty($path)) {
            return [
                'relationship' => "{$nguoi1->ho_ten} và {$nguoi2->ho_ten} chưa có dữ liệu quan hệ họ hàng gần được hỗ trợ tra cứu.",
                'path' => [],
                'members' => []
            ];
        }

        $relationName = $this->resolvePathToRelationship($path, $personsMap);
        $resultMessage = "{$nguoi1->ho_ten} là {$relationName} của {$nguoi2->ho_ten}";

        // Chuẩn bị danh sách path và members theo đúng yêu cầu API
        $pathSteps = [];
        $membersInPath = [];
        foreach ($path as $step) {
            // Không đẩy bước 'start' vào path nếu chỉ muốn xem các bước chuyển dịch quan hệ,
            // hoặc đẩy tất cả bước để vẽ đường nối
            $pathSteps[] = $step['type'];
            
            $memberId = $step['id'];
            if (isset($personsMap[$memberId])) {
                $membersInPath[] = [
                    'id' => $memberId,
                    'ho_ten' => $personsMap[$memberId]->ho_ten,
                    'gioi_tinh' => $personsMap[$memberId]->gioi_tinh,
                    'avatar' => $personsMap[$memberId]->avatar,
                    'nghe_nghiep' => $personsMap[$memberId]->nghe_nghiep,
                    'doi_thu' => $personsMap[$memberId]->doi_thu,
                ];
            }
        }

        return [
            'relationship' => $resultMessage,
            'path' => $pathSteps,
            'members' => $membersInPath
        ];
    }

    private function checkVoChong(): ?string
    {
        if (
            (
                $this->nguoi1->spouse_of_id &&
                $this->nguoi1->spouse_of_id
                    == $this->nguoi2->id
            )
            ||
            (
                $this->nguoi2->spouse_of_id &&
                $this->nguoi2->spouse_of_id
                    == $this->nguoi1->id
            )
        ) {

            return $this->isNam1
                ? 'chồng'
                : 'vợ';

        }

        return null;
    }

    private function checkChaMe(): ?string
    {
        if (
            $this->nguoi2->cha_id
                == $this->nguoi1->id
            ||
            $this->nguoi2->me_id
                == $this->nguoi1->id
        ) {

            return $this->isNam1
                ? 'cha'
                : 'mẹ';

        }

        return null;
    }

    private function checkConCai(): ?string
    {
        if (
            $this->nguoi1->cha_id
                == $this->nguoi2->id
            ||
            $this->nguoi1->me_id
                == $this->nguoi2->id
        ) {

            return $this->isNam1
                ? 'con trai'
                : 'con gái';

        }

        return null;
    }

    private function checkAnhChiEm(): ?string
    {
        if (
            $this->nguoi1->cha_id &&
            $this->nguoi1->me_id &&

            $this->nguoi1->cha_id
                == $this->nguoi2->cha_id &&

            $this->nguoi1->me_id
                == $this->nguoi2->me_id
        ) {

            $isOlder =
                $this->soSanhTuoi(
                    $this->nguoi1,
                    $this->nguoi2
                );

            return $this->isNam1
                ? (
                    $isOlder
                    ? 'anh trai'
                    : 'em trai'
                )
                : (
                    $isOlder
                    ? 'chị gái'
                    : 'em gái'
                );

        }

        return null;
    }

    private function checkOngBa(): ?string
    {
        if (
            $this->chaNguoi2 &&
            (
                $this->chaNguoi2->cha_id
                    == $this->nguoi1->id
                ||
                $this->chaNguoi2->me_id
                    == $this->nguoi1->id
            )
        ) {

            return $this->isNam1
                ? 'ông nội'
                : 'bà nội';

        }

        if (
            $this->meNguoi2 &&
            (
                $this->meNguoi2->cha_id
                    == $this->nguoi1->id
                ||
                $this->meNguoi2->me_id
                    == $this->nguoi1->id
            )
        ) {

            return $this->isNam1
                ? 'ông ngoại'
                : 'bà ngoại';

        }

        return null;
    }

    private function checkChuBacCo(): ?string
    {
        if (
            $this->chaNguoi2 &&
            $this->nguoi1->cha_id &&
            $this->nguoi1->me_id &&

            $this->nguoi1->cha_id
                == $this->chaNguoi2->cha_id &&

            $this->nguoi1->me_id
                == $this->chaNguoi2->me_id
        ) {

            if ($this->isNam1) {

                return $this->soSanhTuoi(
                    $this->nguoi1,
                    $this->chaNguoi2
                )
                    ? 'bác'
                    : 'chú';

            }

            return 'cô';
        }

        return null;
    }

    private function checkDiCau(): ?string
    {
        if (
            $this->meNguoi2 &&
            $this->nguoi1->cha_id &&
            $this->nguoi1->me_id &&

            $this->nguoi1->cha_id
                == $this->meNguoi2->cha_id &&

            $this->nguoi1->me_id
                == $this->meNguoi2->me_id
        ) {

            return $this->isNam1
                ? 'cậu'
                : 'dì';

        }

        return null;
    }

    private function checkChau(): ?string
    {
        if (
            $this->chaNguoi1 &&
            (
                $this->chaNguoi1->cha_id
                    == $this->nguoi2->id
                ||
                $this->chaNguoi1->me_id
                    == $this->nguoi2->id
            )
        ) {

            return 'cháu nội';

        }

        if (
            $this->meNguoi1 &&
            (
                $this->meNguoi1->cha_id
                    == $this->nguoi2->id
                ||
                $this->meNguoi1->me_id
                    == $this->nguoi2->id
            )
        ) {

            return 'cháu ngoại';

        }

        if (
            (
                $this->chaNguoi1 &&
                $this->nguoi2->cha_id &&
                $this->nguoi2->me_id &&

                $this->chaNguoi1->cha_id
                    == $this->nguoi2->cha_id &&

                $this->chaNguoi1->me_id
                    == $this->nguoi2->me_id
            )
            ||
            (
                $this->meNguoi1 &&
                $this->nguoi2->cha_id &&
                $this->nguoi2->me_id &&

                $this->meNguoi1->cha_id
                    == $this->nguoi2->cha_id &&

                $this->meNguoi1->me_id
                    == $this->nguoi2->me_id
            )
        ) {

            return 'cháu';

        }

        return null;
    }

    private function soSanhTuoi(
        ThanhVien $nguoi1,
        ThanhVien $nguoi2
    ): bool {

        if (
            $nguoi1->ngay_sinh &&
            $nguoi2->ngay_sinh
        ) {

            return strtotime(
                $nguoi1->ngay_sinh
            ) < strtotime(
                $nguoi2->ngay_sinh
            );

        }

        return $nguoi1->id
            < $nguoi2->id;
    }
}