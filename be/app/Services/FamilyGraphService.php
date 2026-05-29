<?php

namespace App\Services;

use App\Models\ThanhVien;

class FamilyGraphService
{
    /**
     * Xây dựng đồ thị danh sách kề (Adjacency List Graph) cho một chi nhánh dòng họ.
     * Tránh tối đa truy vấn N+1 bằng cách eager load toàn bộ thành viên trong chi nhánh.
     *
     * @param int $chiNhanhId
     * @return array
     */
    public function buildGraph(int $chiNhanhId): array
    {
        // 1. Lấy toàn bộ thành viên thuộc chi nhánh này
        $members = ThanhVien::where('chi_nhanh_id', $chiNhanhId)->get();
        $membersMap = [];
        foreach ($members as $m) {
            $membersMap[$m->id] = $m;
        }

        $adjList = [];

        // 2. Xây dựng các cạnh (edges) quan hệ cha, mẹ, con, vợ/chồng
        foreach ($members as $m) {
            $currentId = $m->id;
            if (!isset($adjList[$currentId])) {
                $adjList[$currentId] = [];
            }

            // Mối quan hệ: Cha
            if ($m->cha_id) {
                $fatherId = $m->cha_id;
                if (isset($membersMap[$fatherId])) {
                    $adjList[$currentId][] = [
                        'id' => $fatherId,
                        'type' => 'parent',
                    ];
                    // Quan hệ ngược lại: Con
                    if (!isset($adjList[$fatherId])) {
                        $adjList[$fatherId] = [];
                    }
                    $adjList[$fatherId][] = [
                        'id' => $currentId,
                        'type' => 'child',
                    ];
                }
            }

            // Mối quan hệ: Mẹ
            if ($m->me_id) {
                $motherId = $m->me_id;
                if (isset($membersMap[$motherId])) {
                    $adjList[$currentId][] = [
                        'id' => $motherId,
                        'type' => 'parent',
                    ];
                    // Quan hệ ngược lại: Con
                    if (!isset($adjList[$motherId])) {
                        $adjList[$motherId] = [];
                    }
                    $adjList[$motherId][] = [
                        'id' => $currentId,
                        'type' => 'child',
                    ];
                }
            }

            // Mối quan hệ: Hôn nhân (Vợ/Chồng)
            if ($m->spouse_of_id) {
                $spouseId = $m->spouse_of_id;
                if (isset($membersMap[$spouseId])) {
                    $adjList[$currentId][] = [
                        'id' => $spouseId,
                        'type' => 'spouse',
                    ];
                    if (!isset($adjList[$spouseId])) {
                        $adjList[$spouseId] = [];
                    }
                    $adjList[$spouseId][] = [
                        'id' => $currentId,
                        'type' => 'spouse',
                    ];
                }
            }
        }

        // Mối quan hệ: Anh chị em ruột (cùng cha và cùng mẹ)
        foreach ($members as $m) {
            $currentId = $m->id;
            if ($m->cha_id && $m->me_id) {
                $siblings = $members->filter(function ($item) use ($m) {
                    return $item->id !== $m->id && 
                           $item->cha_id === $m->cha_id && 
                           $item->me_id === $m->me_id;
                });

                foreach ($siblings as $sib) {
                    $adjList[$currentId][] = [
                        'id' => $sib->id,
                        'type' => 'sibling',
                    ];
                }
            }
        }

        // Loại bỏ các phần tử trùng lặp trong liên kết của từng đỉnh
        foreach ($adjList as $id => $connections) {
            $uniqueConnections = [];
            $seen = [];
            foreach ($connections as $conn) {
                $key = $conn['id'] . '-' . $conn['type'];
                if (!isset($seen[$key])) {
                    $seen[$key] = true;
                    $uniqueConnections[] = $conn;
                }
            }
            $adjList[$id] = $uniqueConnections;
        }

        return [
            'graph' => $adjList,
            'map' => $membersMap,
        ];
    }

    /**
     * Thuật toán BFS (Breadth-First Search) Engine để tìm lộ trình quan hệ ngắn nhất.
     *
     * @param int $startId  ID người bắt đầu tra cứu
     * @param int $endId    ID người cần tìm quan hệ
     * @param array $adjList Đồ thị kề
     * @return array|null   Lộ trình tìm kiếm gồm các bước có kèm nhãn loại quan hệ
     */
    public function findShortestPath(int $startId, int $endId, array $adjList): ?array
    {
        if ($startId === $endId) {
            return [];
        }

        $visited = [$startId => true];
        $queue = [
            [
                'path' => [['id' => $startId, 'type' => 'start']],
                'currentId' => $startId
            ]
        ];

        while (!empty($queue)) {
            $state = array_shift($queue);
            $currId = $state['currentId'];
            $path = $state['path'];

            if ($currId === $endId) {
                return $path;
            }

            $neighbors = $adjList[$currId] ?? [];
            foreach ($neighbors as $neighbor) {
                $nId = $neighbor['id'];
                $type = $neighbor['type'];

                if (!isset($visited[$nId])) {
                    $visited[$nId] = true;
                    $newPath = $path;
                    $newPath[] = ['id' => $nId, 'type' => $type];

                    if ($nId === $endId) {
                        return $newPath;
                    }

                    $queue[] = [
                        'path' => $newPath,
                        'currentId' => $nId
                    ];
                }
            }
        }

        return null;
    }
}
