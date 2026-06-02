<?php

namespace App\Services;

use App\Models\ThanhVien;

class RelationshipService
{
    private const ANCESTORS = [
        "",
        "bố/mẹ",
        "ông/bà",
        "cụ",
        "kỵ",
        "sơ",
        "tiệm",
        "tiểu",
        "di",
        "diễn",
    ];

    private const DESCENDANTS = [
        "",
        "con",
        "cháu",
        "chắt",
        "chít",
        "chút",
        "chét",
        "chót",
        "chẹt",
    ];

    protected $graphService;

    public function __construct(FamilyGraphService $graphService)
    {
        $this->graphService = $graphService;
    }

    /**
     * Xác định danh xưng họ hàng của Người 1 đối với Người 2.
     * Ví dụ: Nếu Người 1 là cha của Người 2, kết quả trả về là "cha".
     * Câu hiển thị cuối cùng: "Người 1 là cha của Người 2".
     *
     * @param ThanhVien $nguoi1
     * @param ThanhVien $nguoi2
     * @return string|null
     */
    public function resolve(ThanhVien $nguoi1, ThanhVien $nguoi2): ?string
    {
        if ($nguoi1->id === $nguoi2->id) {
            return 'bản thân';
        }

        // 1. Tạo đồ thị quan hệ gia đình tránh N+1 Query
        $graphData = $this->graphService->buildGraph($nguoi1->chi_nhanh_id);
        $graph = $graphData['graph'];
        $personsMap = $graphData['map'];

        // 2. Chạy thuật toán BFS Engine tìm đường đi ngắn nhất từ Người 1 sang Người 2
        $path = $this->graphService->findShortestPath($nguoi1->id, $nguoi2->id, $graph);

        if ($path === null || empty($path)) {
            return null;
        }

        // 3. Phân tích đường đi BFS và chuyển dịch sang danh xưng từ góc nhìn của Người 1 đối với Người 2
        return $this->resolvePathToRelationship($path, $personsMap);
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


    /**
     * Chuyển đổi lộ trình BFS thành danh xưng quan hệ theo đúng chiều: A là gì của B.
     */
    private function resolvePathToRelationship(array $path, array $personsMap): string
    {
        $types = [];
        for ($i = 1; $i < count($path); $i++) {
            $types[] = $path[$i]['type'];
        }

        $personA = $personsMap[$path[0]['id']];
        $personB = $personsMap[end($path)['id']];
        
        $genderA = $personA->gioi_tinh;
        $genderB = $personB->gioi_tinh;

        // 1. Mối quan hệ: Vợ / Chồng (B là spouse của A -> A là vợ/chồng của B)
        if ($types === ['spouse']) {
            return $genderA === 'Nữ' ? 'vợ' : 'chồng';
        }

        // 2. Mối quan hệ: Cha / Mẹ (B là cha/mẹ của A -> A là con của B)
        if ($types === ['parent']) {
            return $genderA === 'Nữ' ? 'con gái' : 'con trai';
        }

        // 3. Mối quan hệ: Con cái (B là con của A -> A là cha/mẹ của B)
        if ($types === ['child']) {
            return $genderA === 'Nữ' ? 'mẹ' : 'cha';
        }

        // 4. Mối quan hệ: Anh / Chị / Em ruột
        if ($types === ['sibling']) {
            $isAOlder = $this->compareSeniority($personA, $personB) === 'senior';
            if ($isAOlder) {
                return $genderA === 'Nữ' ? 'chị gái' : 'anh trai';
            } else {
                return $genderA === 'Nữ' ? 'em gái' : 'em trai';
            }
        }

        // 5. Mối quan hệ: Ông / Bà nội ngoại (B là ông/bà của A -> A là cháu của B)
        if ($types === ['parent', 'parent']) {
            $parentOfA = $personsMap[$path[1]['id']];
            $isPaternal = $parentOfA->gioi_tinh === 'Nam';
            return $isPaternal ? 'cháu nội' : 'cháu ngoại';
        }

        // 6. Mối quan hệ: Cháu nội ngoại (B là cháu của A -> A là ông/bà của B)
        if ($types === ['child', 'child']) {
            $childOfA = $personsMap[$path[1]['id']];
            $isPaternal = $childOfA->gioi_tinh === 'Nam';
            if ($isPaternal) {
                return $genderA === 'Nữ' ? 'bà nội' : 'ông nội';
            } else {
                return $genderA === 'Nữ' ? 'bà ngoại' : 'ông ngoại';
            }
        }

        // 7. Mối quan hệ: Cô / Dì / Chú / Bác / Cậu (B là cô/dì/chú/bác của A -> A là cháu của B)
        if ($types === ['parent', 'sibling']) {
            return 'cháu';
        }

        // 8. Mối quan hệ: Cháu ruột vế dưới (B là cháu của A -> A là cô/dì/chú/bác/cậu của B)
        if ($types === ['sibling', 'child']) {
            $siblingOfA = $personsMap[$path[1]['id']]; // Anh chị em của A (bố/mẹ của B)
            $isPaternalSide = $siblingOfA->gioi_tinh === 'Nam';
            
            if ($isPaternalSide) {
                if ($genderA === 'Nữ') {
                    return 'cô';
                } else {
                    $isAOlder = $this->compareSeniority($personA, $siblingOfA) === 'senior';
                    return $isAOlder ? 'bác' : 'chú';
                }
            } else {
                return $genderA === 'Nữ' ? 'dì' : 'cậu';
            }
        }

        // 9. Mối quan hệ: Bố mẹ chồng / Bố mẹ vợ (B là bố mẹ của spouse A -> A là dâu/rể của B)
        if ($types === ['spouse', 'parent']) {
            return $genderA === 'Nữ' ? 'con dâu' : 'con rể';
        }

        // 10. Mối quan hệ: Con dâu / Con rể (B là spouse của con A -> A là bố mẹ chồng/vợ của B)
        if ($types === ['child', 'spouse']) {
            $childOfA = $personsMap[$path[1]['id']];
            if ($childOfA->gioi_tinh === 'Nam') {
                return $genderA === 'Nữ' ? 'mẹ chồng' : 'bố chồng';
            } else {
                return $genderA === 'Nữ' ? 'mẹ vợ' : 'bố vợ';
            }
        }

        // 11. Mối quan hệ: Anh rể / Chị dâu / Em rể / Em dâu vế vợ/chồng (B là sibling của spouse A)
        if ($types === ['spouse', 'sibling']) {
            $spouseOfA = $personsMap[$path[1]['id']];
            $isSpouseOlderThanB = $this->compareSeniority($spouseOfA, $personB) === 'senior';
            if ($isSpouseOlderThanB) {
                return $genderA === 'Nữ' ? 'chị dâu' : 'anh rể';
            } else {
                return $genderA === 'Nữ' ? 'em dâu' : 'em rể';
            }
        }

        // 12. Mối quan hệ: Anh vợ / Chị vợ / Em vợ / Anh chồng / Chị chồng / Em chồng (B là spouse của sibling A)
        if ($types === ['sibling', 'spouse']) {
            $siblingOfA = $personsMap[$path[1]['id']];
            $isAOlderThanSibling = $this->compareSeniority($personA, $siblingOfA) === 'senior';
            if ($genderB === 'Nam') { // Chồng của chị/em gái A
                if ($genderA === 'Nam') {
                    return $isAOlderThanSibling ? 'anh vợ' : 'em vợ';
                } else {
                    return $isAOlderThanSibling ? 'chị vợ' : 'em vợ';
                }
            } else { // Vợ của anh/em trai A
                if ($genderA === 'Nam') {
                    return $isAOlderThanSibling ? 'anh chồng' : 'em chồng';
                } else {
                    return $isAOlderThanSibling ? 'chị chồng' : 'em chồng';
                }
            }
        }

        // 13. Mối quan hệ: Cụ nội / Cụ ngoại (B là cụ của A -> A là chắt của B)
        if ($types === ['parent', 'parent', 'parent']) {
            $parentOfA = $personsMap[$path[1]['id']];
            $isPaternal = $parentOfA->gioi_tinh === 'Nam';
            return $isPaternal ? 'chắt nội' : 'chắt ngoại';
        }

        // 14. Mối quan hệ: Chắt nội / Chắt ngoại (B là chắt của A -> A là cụ của B)
        if ($types === ['child', 'child', 'child']) {
            $childOfA = $personsMap[$path[1]['id']];
            $isPaternal = $childOfA->gioi_tinh === 'Nam';
            if ($isPaternal) {
                return $genderA === 'Nữ' ? 'cụ bà (bà cố nội)' : 'cụ ông (ông cố nội)';
            } else {
                return $genderA === 'Nữ' ? 'cụ bà (bà cố ngoại)' : 'cụ ông (ông cố ngoại)';
            }
        }

        // 15. Mối quan hệ: Anh chị em họ (cousin)
        if ($types === ['parent', 'sibling', 'child']) {
            $parentOfA = $personsMap[$path[1]['id']];
            $parentOfB = $personsMap[$path[2]['id']];
            $isParentAOlder = $this->compareSeniority($parentOfA, $parentOfB) === 'senior';
            
            if ($isParentAOlder) {
                return $genderA === 'Nữ' ? 'chị họ' : 'anh họ';
            } else {
                return 'em họ';
            }
        }

        // 16. Mối quan hệ: Dượng / Thím / Mợ ruột (B là spouse của cô/dì/chú/bác của A)
        if ($types === ['parent', 'sibling', 'spouse']) {
            return 'cháu';
        }

        // 17. Mối quan hệ: Ông bác / Ông chú / Bà cô / Ông cậu / Bà dì (B là sibling của ông/bà A)
        if ($types === ['parent', 'parent', 'sibling']) {
            return 'cháu';
        }

        // Cơ chế Fallback cho các trường hợp quan hệ nhiều đời khác
        return $this->resolveFallbackRelationship($path, $personsMap);
    }

    /**
     * Thuật toán Fallback tính toán độ lệch thế hệ (Generation Difference) từ góc nhìn của A.
     */
    private function resolveFallbackRelationship(array $path, array $personsMap): string
    {
        $genDiff = 0;
        for ($i = 1; $i < count($path); $i++) {
            $type = $path[$i]['type'];
            if ($type === 'parent') {
                $genDiff--; // A ở thế hệ dưới B
            } elseif ($type === 'child') {
                $genDiff++; // A ở thế hệ trên B
            }
        }

        $personA = $personsMap[$path[0]['id']];
        $genderA = $personA->gioi_tinh;

        if ($genDiff === 0) {
            $endPerson = $personsMap[end($path)['id']];
            $seniority = $this->compareSeniority($personA, $endPerson);
            if ($seniority === 'senior') {
                return $genderA === 'Nữ' ? 'chị họ' : 'anh họ';
            } else {
                return 'em họ';
            }
        } elseif ($genDiff === 1) {
            return $genderA === 'Nữ' ? 'cô họ' : 'chú họ';
        } elseif ($genDiff === 2) {
            return $genderA === 'Nữ' ? 'bà họ' : 'ông họ';
        } elseif ($genDiff >= 3) {
            return $genderA === 'Nữ' ? 'cụ họ' : 'cụ họ';
        } elseif ($genDiff === -1) {
            return 'cháu họ';
        } elseif ($genDiff === -2) {
            return 'chắt họ';
        } else {
            return 'họ hàng';
        }
    }

    /**
     * Chuyển đổi từ danh xưng quan hệ chi tiết sang danh xưng gọi bậc/xưng hô tự nhiên cho cả 10 đời.
     *
     * @param string $relationshipName
     * @return string
     */
    public function getConversationalTerm(string $relationshipName): string
    {
        $term = mb_strtolower(trim($relationshipName), 'UTF-8');

        // Đời con: con trai, con gái, con dâu, con rể -> con
        if (str_contains($term, 'con trai') || str_contains($term, 'con gái') || str_contains($term, 'con dâu') || str_contains($term, 'con rể')) {
            return 'con';
        }

        // Đời chắt
        if (str_contains($term, 'chắt')) {
            return 'chắt';
        }

        // Đời chít
        if (str_contains($term, 'chít')) {
            return 'chít';
        }

        // Đời chút
        if (str_contains($term, 'chút')) {
            return 'chút';
        }

        // Đời chét
        if (str_contains($term, 'chét')) {
            return 'chét';
        }

        // Đời chót
        if (str_contains($term, 'chót')) {
            return 'chót';
        }

        // Đời chẹt
        if (str_contains($term, 'chẹt')) {
            return 'chẹt';
        }

        // Đời cháu: cháu nội, cháu ngoại, cháu họ -> cháu
        if (str_contains($term, 'cháu')) {
            return 'cháu';
        }

        // Anh chị em: anh họ, anh trai, anh rể, anh vợ, anh chồng -> anh
        if (str_contains($term, 'anh trai') || str_contains($term, 'anh họ') || str_contains($term, 'anh rể') || str_contains($term, 'anh vợ') || str_contains($term, 'anh chồng')) {
            return 'anh';
        }

        // Chị gái, chị họ, chị dâu, chị vợ, chị chồng -> chị
        if (str_contains($term, 'chị gái') || str_contains($term, 'chị họ') || str_contains($term, 'chị dâu') || str_contains($term, 'chị vợ') || str_contains($term, 'chị chồng')) {
            return 'chị';
        }

        // Em gái, em trai, em họ, em rể, em dâu, em vợ, em chồng -> em
        if (str_contains($term, 'em gái') || str_contains($term, 'em trai') || str_contains($term, 'em họ') || str_contains($term, 'em rể') || str_contains($term, 'em dâu') || str_contains($term, 'em vợ') || str_contains($term, 'em chồng')) {
            return 'em';
        }

        // Ông nội, ông ngoại, ông họ -> ông
        if (str_contains($term, 'ông nội') || str_contains($term, 'ông ngoại') || str_contains($term, 'ông họ')) {
            return 'ông';
        }

        // Bà nội, bà ngoại, bà họ -> bà
        if (str_contains($term, 'bà nội') || str_contains($term, 'bà ngoại') || str_contains($term, 'bà họ')) {
            return 'bà';
        }

        // Bố chồng, bố vợ -> cha
        if (str_contains($term, 'bố chồng') || str_contains($term, 'bố vợ') || $term === 'bố/mẹ') {
            return 'cha';
        }

        // Mẹ chồng, mẹ vợ -> mẹ
        if (str_contains($term, 'mẹ chồng') || str_contains($term, 'mẹ vợ')) {
            return 'mẹ';
        }

        // Cụ: cụ ông, cụ bà, cụ họ -> cụ
        if (str_contains($term, 'cụ')) {
            return 'cụ';
        }

        // Kỵ: kỵ ông, kỵ bà, kỵ họ -> kỵ
        if (str_contains($term, 'kỵ')) {
            return 'kỵ';
        }

        // Sơ: sơ ông, sơ bà, sơ họ -> sơ
        if (str_contains($term, 'sơ')) {
            return 'sơ';
        }

        // Tiệm: tiệm họ -> tiệm
        if (str_contains($term, 'tiệm')) {
            return 'tiệm';
        }

        // Tiểu: tiểu họ -> tiểu
        if (str_contains($term, 'tiểu')) {
            return 'tiểu';
        }

        // Di: di họ -> di
        if (str_contains($term, 'di')) {
            if ($term !== 'dì') {
                return 'di';
            }
        }

        // Diễn: diễn họ -> diễn
        if (str_contains($term, 'diễn')) {
            return 'diễn';
        }

        return $relationshipName;
    }

    /**
     * So sánh vai vế (Seniority) lớn/nhỏ dựa trên ngày sinh.
     * Trả về 'senior' (vai vế lớn hơn - sinh trước) hoặc 'junior' (vai vế nhỏ hơn - sinh sau).
     */
    private function compareSeniority(ThanhVien $a, ThanhVien $b): string
    {
        if ($a->id === $b->id) {
            return 'equal';
        }
        if ($a->ngay_sinh && $b->ngay_sinh) {
            $timeA = strtotime($a->ngay_sinh);
            $timeB = strtotime($b->ngay_sinh);
            if ($timeA < $timeB) return 'senior';
            if ($timeA > $timeB) return 'junior';
        }
        return $a->id < $b->id ? 'senior' : 'junior';
    }
}