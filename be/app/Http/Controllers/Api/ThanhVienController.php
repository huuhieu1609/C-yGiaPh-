<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ThanhVien;
use Exception;
use Illuminate\Http\Request;

class ThanhVienController extends Controller
{
    // API Thêm thành viên mới vào dòng họ
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'id_chi_nhanh' => 'required|exists:chi_nhanhs,id',
                'ho_ten' => 'required|string|max:255',
                'email' => 'nullable|email',
                'gioi_tinh' => 'required|string',
                'ngay_sinh' => 'nullable|date',
                'ngay_mat' => 'nullable|date',
                'id_cha' => 'nullable|exists:thanh_viens,id',
                'id_me' => 'nullable|exists:thanh_viens,id',
                'trang_thai' => 'nullable|string',
                'thong_tin_them' => 'nullable|string',
            ]);

            $thanhVien = ThanhVien::create($data);

            return response()->json([
                'status' => true,
                'message' => 'Thêm thành viên thành công!',
                'data' => $thanhVien,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi khi thêm thành viên: '.$e->getMessage(),
            ], 500);
        }
    }

    // API Lấy toàn bộ danh sách thành viên của 1 chi nhánh (để vẽ cây / hiển thị danh sách)
    public function getByChiNhanh($chiNhanhId)
    {
        try {
            $thanhViens = ThanhVien::where('id_chi_nhanh', $chiNhanhId)->get();

            return response()->json(['status' => true, 'data' => $thanhViens]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi lấy dữ liệu: '.$e->getMessage(),
            ], 500);
        }
    }

    // Cung cấp thêm các hàm chuẩn theo API Resource nếu Frontend dùng
    public function getData()
    {
        try {
            $data = ThanhVien::all();

            return response()->json([
                'status' => true,
                'message' => 'Lấy dữ liệu thành công!',
                'data' => $data,
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function create(Request $request)
    {
        try {
            $data = $request->all();
            if ($request->hasFile('avatar')) {
                $image = $request->file('avatar');
                $imageName = time().'.'.$image->getClientOriginalExtension();
                $image->move(public_path('uploads/avatars'), $imageName);
                $data['avatar'] = asset('uploads/avatars/'.$imageName);
            }
            $item = ThanhVien::create($data);

            return response()->json([
                'status' => true,
                'message' => 'Tạo mới thành công!',
                'data' => $item,
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            $item = ThanhVien::findOrFail($request->id);
            $data = $request->all();
            if ($request->hasFile('avatar')) {
                $image = $request->file('avatar');
                $imageName = time().'.'.$image->getClientOriginalExtension();
                $image->move(public_path('uploads/avatars'), $imageName);
                $data['avatar'] = asset('uploads/avatars/'.$imageName);
            }
            $item->update($data);

            return response()->json([
                'status' => true,
                'message' => 'Cập nhật thành công!',
                'data' => $item,
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function delete(Request $request)
    {
        try {
            $item = ThanhVien::findOrFail($request->id);
            $item->delete();

            return response()->json([
                'status' => true,
                'message' => 'Xóa thành công!',
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function status(Request $request)
    {
        try {
            $item = ThanhVien::findOrFail($request->id);
            if (isset($item->trang_thai)) {
                $item->trang_thai = $item->trang_thai == 'Còn sống' ? 'Đã mất' : 'Còn sống';
                $item->save();
            }

            return response()->json([
                'status' => true,
                'message' => 'Cập nhật trạng thái thành công!',
                'trang_thai' => $item->trang_thai,
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function search(Request $request)
    {
        try {
            $query = $request->value;
            $data = ThanhVien::where('ho_ten', 'like', '%'.$query.'%')
                ->orWhere('email', 'like', '%'.$query.'%')
                ->get();

            return response()->json([
                'status' => true,
                'message' => 'Tìm thấy '.count($data).' kết quả',
                'data' => $data,
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function xacDinhQuanHe(Request $request)
    {
        try {
            $idA = $request->input('id_a');
            $idB = $request->input('id_b');

            if (! $idA || ! $idB) {
                return response()->json([
                    'status' => false,
                    'message' => 'Vui lòng cung cấp đầy đủ thông tin hai thành viên!',
                ], 400);
            }

            if ($idA == $idB) {
                return response()->json([
                    'status' => false,
                    'message' => 'Hai thành viên phải khác nhau!',
                ], 400);
            }

            // Tải toàn bộ thành viên để dựng đồ thị
            $members = ThanhVien::all()->keyBy('id');

            // Cả hai phải tồn tại
            if (! isset($members[$idA]) || ! isset($members[$idB])) {
                return response()->json([
                    'status' => false,
                    'message' => 'Không tìm thấy một trong hai thành viên!',
                ], 404);
            }

            // Dựng danh sách kề cho đồ thị vô hướng
            $adj = [];
            foreach ($members as $m) {
                $u = $m->id;

                // 1. Kết nối với cha ruột
                if ($m->cha_id) {
                    $v = $m->cha_id;
                    $adj[$u][] = [
                        'id' => $v,
                        'relation_type' => 'parent',
                        'label' => 'Bố ruột',
                    ];
                    $adj[$v][] = [
                        'id' => $u,
                        'relation_type' => 'child',
                        'label' => $m->gioi_tinh == 'Nam' ? 'Con trai' : 'Con gái',
                    ];
                }

                // 2. Kết nối với mẹ ruột
                if ($m->me_id) {
                    $v = $m->me_id;
                    $adj[$u][] = [
                        'id' => $v,
                        'relation_type' => 'parent',
                        'label' => 'Mẹ ruột',
                    ];
                    $adj[$v][] = [
                        'id' => $u,
                        'relation_type' => 'child',
                        'label' => $m->gioi_tinh == 'Nam' ? 'Con trai' : 'Con gái',
                    ];
                }

                // 3. Kết nối với vợ/chồng (spouse)
                if ($m->spouse_of_id) {
                    $v = $m->spouse_of_id;
                    $exists = false;
                    if (isset($adj[$u])) {
                        foreach ($adj[$u] as $edge) {
                            if ($edge['id'] == $v && $edge['relation_type'] == 'spouse') {
                                $exists = true;
                                break;
                            }
                        }
                    }
                    if (! $exists) {
                        $spouseGender = isset($members[$v]) ? $members[$v]->gioi_tinh : null;
                        $adj[$u][] = [
                            'id' => $v,
                            'relation_type' => 'spouse',
                            'label' => $spouseGender == 'Nam' ? 'Chồng' : 'Vợ',
                        ];
                        $adj[$v][] = [
                            'id' => $u,
                            'relation_type' => 'spouse',
                            'label' => $m->gioi_tinh == 'Nam' ? 'Chồng' : 'Vợ',
                        ];
                    }
                }
            }

            // Bổ sung từ bảng vo_chongs nếu có
            try {
                $voChongs = \DB::table('vo_chongs')->get();
                foreach ($voChongs as $vc) {
                    $u = $vc->chong_id;
                    $v = $vc->vo_id;
                    if (isset($members[$u]) && isset($members[$v])) {
                        $exists = false;
                        if (isset($adj[$u])) {
                            foreach ($adj[$u] as $edge) {
                                if ($edge['id'] == $v && $edge['relation_type'] == 'spouse') {
                                    $exists = true;
                                    break;
                                }
                            }
                        }
                        if (! $exists) {
                            $adj[$u][] = [
                                'id' => $v,
                                'relation_type' => 'spouse',
                                'label' => 'Vợ',
                            ];
                            $adj[$v][] = [
                                'id' => $u,
                                'relation_type' => 'spouse',
                                'label' => 'Chồng',
                            ];
                        }
                    }
                }
            } catch (Exception $ex) {
                // Bỏ qua nếu có lỗi
            }

            // Bổ sung từ bảng con_nuois nếu có
            try {
                $conNuois = \DB::table('con_nuois')->get();
                foreach ($conNuois as $cn) {
                    $u = $cn->cha_me_id;
                    $v = $cn->con_id;
                    if (isset($members[$u]) && isset($members[$v])) {
                        $exists = false;
                        if (isset($adj[$u])) {
                            foreach ($adj[$u] as $edge) {
                                if ($edge['id'] == $v && $edge['relation_type'] == 'adoptive_child') {
                                    $exists = true;
                                    break;
                                }
                            }
                        }
                        if (! $exists) {
                            $adj[$u][] = [
                                'id' => $v,
                                'relation_type' => 'adoptive_child',
                                'label' => 'Con nuôi',
                            ];
                            $adj[$v][] = [
                                'id' => $u,
                                'relation_type' => 'adoptive_parent',
                                'label' => 'Cha/Mẹ nuôi',
                            ];
                        }
                    }
                }
            } catch (Exception $ex) {
                // Bỏ qua nếu bảng con_nuois chưa có hoặc có lỗi
            }

            // Thuật toán BFS tìm đường đi ngắn nhất từ idA tới idB
            $queue = new \SplQueue;
            $queue->enqueue([$idA, []]);
            $visited = [$idA => true];
            $foundPath = null;

            while (! $queue->isEmpty()) {
                [$currId, $path] = $queue->dequeue();

                if ($currId == $idB) {
                    $foundPath = $path;
                    break;
                }

                if (isset($adj[$currId])) {
                    foreach ($adj[$currId] as $edge) {
                        $nextId = $edge['id'];
                        if (! isset($visited[$nextId])) {
                            $visited[$nextId] = true;
                            $newPath = $path;
                            $newPath[] = [
                                'from_id' => $currId,
                                'to_id' => $nextId,
                                'relation_type' => $edge['relation_type'],
                                'label' => $edge['label'],
                            ];
                            $queue->enqueue([$nextId, $newPath]);
                        }
                    }
                }
            }

            if (! $foundPath) {
                return response()->json([
                    'status' => true,
                    'term' => 'Họ hàng xa',
                    'description' => 'Không tìm thấy đường liên kết huyết thống trực tiếp trong hệ thống gia phả.',
                    'path' => [],
                ]);
            }

            // Phân tích đường đi để xác định quan hệ xưng hô
            $term = 'Họ hàng';
            $description = 'Có quan hệ huyết thống hoặc hôn nhân trong dòng họ.';

            // Xây dựng đường dẫn chi tiết phục vụ hiển thị ở Frontend
            $detailedPath = [];
            $detailedPath[] = [
                'id' => $idA,
                'ho_ten' => $members[$idA]->ho_ten,
                'gioi_tinh' => $members[$idA]->gioi_tinh,
                'doi_thu' => $members[$idA]->doi_thu,
                'avatar' => $members[$idA]->avatar,
                'relation_label' => 'Bản thân',
            ];

            foreach ($foundPath as $step) {
                $toId = $step['to_id'];
                $detailedPath[] = [
                    'id' => $toId,
                    'ho_ten' => $members[$toId]->ho_ten,
                    'gioi_tinh' => $members[$toId]->gioi_tinh,
                    'doi_thu' => $members[$toId]->doi_thu,
                    'avatar' => $members[$toId]->avatar,
                    'relation_label' => $step['label'],
                ];
            }

            // Phân loại quan hệ dựa trên đặc điểm đường đi
            $pathLen = count($foundPath);
            $personA = $members[$idA];
            $personB = $members[$idB];

            // 1. Quan hệ trực tiếp
            if ($pathLen == 1) {
                $step = $foundPath[0];
                if ($step['relation_type'] == 'parent') {
                    $term = $personB->gioi_tinh == 'Nam' ? 'Bố / Cha' : 'Mẹ';
                    $description = 'Quan hệ sinh thành trực hệ (đời F1).';
                } elseif ($step['relation_type'] == 'adoptive_parent') {
                    $term = $personB->gioi_tinh == 'Nam' ? 'Cha nuôi' : 'Mẹ nuôi';
                    $description = 'Cha/Mẹ nuôi được gia đình công nhận.';
                } elseif ($step['relation_type'] == 'child') {
                    $term = $personB->gioi_tinh == 'Nam' ? 'Con trai' : 'Con gái';
                    $description = 'Hậu duệ trực hệ đời thứ nhất.';
                } elseif ($step['relation_type'] == 'adoptive_child') {
                    $term = 'Con nuôi';
                    $description = 'Con nhận nuôi được gia đình công nhận.';
                } elseif ($step['relation_type'] == 'spouse') {
                    $term = $personB->gioi_tinh == 'Nam' ? 'Chồng' : 'Vợ';
                    $description = 'Bạn đời kết hôn.';
                }
            }
            // 2. Độ dài đường đi >= 2
            else {
                // Đếm số bước đi LÊN (parent/adoptive_parent) và bước đi XUỐNG (child/adoptive_child)
                $upCount = 0;
                $downCount = 0;
                $spouseCount = 0;

                // Lưu trữ chuỗi các kiểu quan hệ
                $types = array_column($foundPath, 'relation_type');

                foreach ($types as $t) {
                    if ($t == 'parent' || $t == 'adoptive_parent') {
                        $upCount++;
                    } elseif ($t == 'child' || $t == 'adoptive_child') {
                        $downCount++;
                    } elseif ($t == 'spouse') {
                        $spouseCount++;
                    }
                }

                // Cắt nghĩa quan hệ
                // A. Quan hệ huyết thống thuần túy (Không có hôn nhân ở giữa đường đi)
                if ($spouseCount == 0) {
                    // Trực hệ đi thẳng lên (Bố/Mẹ -> Ông/Bà -> Cụ...)
                    if ($downCount == 0) {
                        if ($upCount == 2) {
                            $term = $personB->gioi_tinh == 'Nam' ? 'Ông' : 'Bà';
                            // Phân biệt nội ngoại dựa trên giới tính của mắt xích trung gian
                            $midId = $foundPath[0]['to_id'];
                            $mid = $members[$midId];
                            $term .= ($mid->gioi_tinh == 'Nam' ? ' nội' : ' ngoại');
                            $description = 'Bậc tiền bối đời thứ hai (ông bà).';
                        } elseif ($upCount == 3) {
                            $term = $personB->gioi_tinh == 'Nam' ? 'Cụ ông' : 'Cụ bà';
                            $midId = $foundPath[0]['to_id'];
                            $mid = $members[$midId];
                            $term .= ($mid->gioi_tinh == 'Nam' ? ' nội' : ' ngoại');
                            $description = 'Bậc tiền bối đời thứ ba (cụ/cố).';
                        } else {
                            $term = 'Cụ tổ';
                            $description = 'Bậc tiền bối đời cao.';
                        }
                    }
                    // Trực hệ đi thẳng xuống (Con -> Cháu -> Chắt...)
                    elseif ($upCount == 0) {
                        if ($downCount == 2) {
                            $midId = $foundPath[0]['to_id'];
                            $mid = $members[$midId];
                            $term = 'Cháu';
                            $term .= ($mid->gioi_tinh == 'Nam' ? ' nội' : ' ngoại');
                            $description = 'Hậu duệ đời thứ hai.';
                        } elseif ($downCount == 3) {
                            $term = 'Chắt';
                            $description = 'Hậu duệ đời thứ ba.';
                        } else {
                            $term = 'Chít / Hậu duệ';
                            $description = 'Hậu duệ đời thứ tư trở đi.';
                        }
                    }
                    // Bàng hệ (Cùng chung tổ tiên, có đi lên và đi xuống)
                    else {
                        // 1. Chung bố mẹ (up=1, down=1) -> Anh chị em ruột
                        if ($upCount == 1 && $downCount == 1) {
                            $isOlder = $personB->ngay_sinh && $personA->ngay_sinh ? ($personB->ngay_sinh < $personA->ngay_sinh) : ($personB->doi_thu <= $personA->doi_thu);
                            if ($personB->gioi_tinh == 'Nam') {
                                $term = $isOlder ? 'Anh trai' : 'Em trai';
                            } else {
                                $term = $isOlder ? 'Chị gái' : 'Em gái';
                            }
                            $description = 'Anh chị em ruột cùng chung huyết thống.';
                        }
                        // 2. Cháu ruột (up=1, down=2) -> Con của anh chị em
                        elseif ($upCount == 1 && $downCount == 2) {
                            $term = 'Cháu ruột';
                            $description = 'Con của anh/chị/em ruột của bạn.';
                        }
                        // 3. Cô, Dì, Chú, Bác ruột (up=2, down=1)
                        elseif ($upCount == 2 && $downCount == 1) {
                            $parentStep = $foundPath[0];
                            $parentId = $parentStep['to_id'];
                            $parent = $members[$parentId];

                            $uncleStep = $foundPath[2];
                            $uncleId = $uncleStep['to_id'];
                            $uncle = $members[$uncleId];

                            if ($parent->gioi_tinh == 'Nam') {
                                // Nhánh bố
                                $isOlderThanParent = $uncle->ngay_sinh && $parent->ngay_sinh ? ($uncle->ngay_sinh < $parent->ngay_sinh) : false;
                                if ($isOlderThanParent) {
                                    $term = 'Bác';
                                } else {
                                    $term = $uncle->gioi_tinh == 'Nam' ? 'Chú' : 'Cô';
                                }
                                $term .= ' ruột';
                                $description = 'Anh/em ruột của bố bạn.';
                            } else {
                                // Nhánh mẹ
                                $term = $uncle->gioi_tinh == 'Nam' ? 'Cậu ruột' : 'Dì ruột';
                                $description = 'Anh/em ruột của mẹ bạn.';
                            }
                        }
                        // 4. Anh chị em họ (up=2, down=2)
                        elseif ($upCount == 2 && $downCount == 2) {
                            // Xác định vai vế của anh chị em họ qua tuổi/ngày sinh của bố mẹ
                            $parentAStep = $foundPath[0];
                            $parentA = $members[$parentAStep['to_id']];

                            $parentBStep = $foundPath[2];
                            $parentB = $members[$parentBStep['to_id']]; // Đối tác đi xuống từ cụ tổ là cha/mẹ của B

                            $parentBIsOlder = $parentB->ngay_sinh && $parentA->ngay_sinh ? ($parentB->ngay_sinh < $parentA->ngay_sinh) : false;

                            if ($personB->gioi_tinh == 'Nam') {
                                $term = $parentBIsOlder ? 'Anh họ' : 'Em họ';
                            } else {
                                $term = $parentBIsOlder ? 'Chị họ' : 'Em họ';
                            }
                            $description = 'Anh chị em họ (con của bác/chú/cô/cậu/dì).';
                        }
                        // 5. Quan hệ họ hàng bàng hệ xa hơn
                        else {
                            if ($personB->doi_thu < $personA->doi_thu) {
                                $term = $personB->gioi_tinh == 'Nam' ? 'Bác họ / Chú họ' : 'Cô họ / Dì họ';
                            } elseif ($personB->doi_thu > $personA->doi_thu) {
                                $term = 'Cháu họ';
                            } else {
                                $term = 'Anh/Chị/Em họ';
                            }
                            $description = 'Quan hệ họ hàng bàng hệ.';
                        }
                    }
                }
                // B. Quan hệ có chứa kết hôn (Thông gia, Dâu/Rể, Vợ/Chồng của họ hàng)
                else {
                    // Nếu bước cuối cùng là Spouse -> B là vợ/chồng của họ hàng
                    $lastStep = end($foundPath);
                    if ($lastStep['relation_type'] == 'spouse') {
                        // Xác định quan hệ của người phối ngẫu trước đó với A
                        // Lấy đường đi trừ bước cuối
                        $subPath = array_slice($foundPath, 0, -1);
                        $subUp = 0;
                        $subDown = 0;
                        $subSpouse = 0;
                        foreach ($subPath as $s) {
                            if ($s['relation_type'] == 'parent' || $s['relation_type'] == 'adoptive_parent') {
                                $subUp++;
                            } elseif ($s['relation_type'] == 'child' || $s['relation_type'] == 'adoptive_child') {
                                $subDown++;
                            } elseif ($s['relation_type'] == 'spouse') {
                                $subSpouse++;
                            }
                        }

                        // Mối quan hệ của người phối ngẫu (relative) với A
                        if ($subSpouse == 0) {
                            if ($subUp == 2 && $subDown == 1) { // Cô/Chú/Cậu/Dì ruột
                                $parentStep = $foundPath[0];
                                $parent = $members[$parentStep['to_id']];
                                $uncleId = $foundPath[2]['to_id'];
                                $uncle = $members[$uncleId];

                                if ($parent->gioi_tinh == 'Nam') { // Nhánh bố
                                    $isOlder = $uncle->ngay_sinh && $parent->ngay_sinh ? ($uncle->ngay_sinh < $parent->ngay_sinh) : false;
                                    if ($isOlder) {
                                        $term = $personB->gioi_tinh == 'Nam' ? 'Bác trai (Dượng)' : 'Bác dâu';
                                    } else {
                                        $term = $uncle->gioi_tinh == 'Nam' ? 'Thím' : 'Dượng';
                                    }
                                } else { // Nhánh mẹ
                                    $term = $uncle->gioi_tinh == 'Nam' ? 'Mợ' : 'Dượng';
                                }
                                $description = 'Vợ/chồng của cô/chú/cậu/dì bạn.';
                            } elseif ($subUp == 1 && $subDown == 1) { // Anh chị em ruột
                                $relativeId = $lastStep['from_id'];
                                $relative = $members[$relativeId];
                                $isOlder = $relative->ngay_sinh && $personA->ngay_sinh ? ($relative->ngay_sinh < $personA->ngay_sinh) : ($relative->doi_thu <= $personA->doi_thu);

                                if ($relative->gioi_tinh == 'Nam') {
                                    $term = $isOlder ? 'Chị dâu' : 'Em dâu';
                                } else {
                                    $term = $isOlder ? 'Anh rể' : 'Em rể';
                                }
                                $description = 'Vợ/chồng của anh/chị/em ruột của bạn.';
                            } elseif ($subUp == 0 && $subDown == 1) { // Con ruột
                                $term = $personB->gioi_tinh == 'Nam' ? 'Con rể' : 'Con dâu';
                                $description = 'Bạn đời của con bạn.';
                            } else {
                                $term = $personB->gioi_tinh == 'Nam' ? 'Anh/Em rể họ' : 'Chị/Em dâu họ';
                                $description = 'Người phối ngẫu trong họ hàng.';
                            }
                        }
                    }
                    // Nếu bước đầu tiên là Spouse -> B là họ hàng bên Vợ/Chồng
                    elseif ($foundPath[0]['relation_type'] == 'spouse') {
                        $term = ($personA->gioi_tinh == 'Nam' ? 'Bên Vợ' : 'Bên Chồng');
                        $description = 'Quan hệ thông gia/họ hàng của bạn đời.';
                    }
                }
            }

            return response()->json([
                'status' => true,
                'message' => 'Xác định quan hệ thành công!',
                'term' => $term,
                'description' => $description,
                'path' => $detailedPath,
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi xác định quan hệ: '.$e->getMessage(),
            ], 500);
        }
    }
}
