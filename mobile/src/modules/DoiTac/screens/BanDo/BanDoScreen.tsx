import React, {
  useEffect,
  useState,
  useRef,
  useCallback,
  useMemo,
} from "react";
import {
  View,
  Text,
  StyleSheet,
  TouchableOpacity,
  Platform,
  StatusBar,
  ActivityIndicator,
  FlatList,
  TextInput,
  Alert,
  Dimensions,
  Animated,
  Image,
  ScrollView,
  Linking,
  Modal,
} from "react-native";
import { WebView } from "react-native-webview";
import * as Location from "expo-location";
import { Ionicons } from "@expo/vector-icons";
import { router } from "expo-router";
import api from "../../../../shared/services/api";

const { width: SCREEN_W, height: SCREEN_H } = Dimensions.get("window");

// ─── TYPES ────────────────────────────────────────────────────────────────────
interface MoPhan {
  id: number;
  thanh_vien_id: number;
  ten_mo: string;
  dia_chi: string | null;
  khu: string | null;
  hang: string | null;
  so_mo: string | null;
  huong_mo: string | null;
  ten_nghia_trang: string | null;
  vi_do: string | null;
  kinh_do: string | null;
  hinh_anh: string | null;
  ghi_chu: string | null;
  has_location?: boolean;
  thanh_vien?: {
    id: number;
    ho_ten: string;
    ngay_sinh: string | null;
    ngay_mat: string | null;
    trang_thai: string;
    avatar: string | null;
    nghe_nghiep: string | null;
  };
}

// ─── BUILD LEAFLET HTML (tối ưu - chỉ 2 CDN, markers qua postMessage) ────────
function buildLeafletHTML(): string {
  return `<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"><\/script>
  <style>
    * { margin:0; padding:0; box-sizing:border-box; }
    html, body, #map { width:100%; height:100%; background:#e8dfd5; }
    .gv { display:flex; align-items:center; justify-content:center;
          background:#fff; border:2px solid #FF9F43; border-radius:50%;
          font-size:13px; box-shadow:0 2px 6px rgba(255,159,67,0.3); }
    .gv.sel { background:#FF9F43; border-color:#D97706;
              box-shadow:0 0 0 7px rgba(255,159,67,0.18); }
    .uv { background:#3B82F6; border:2px solid #fff;
          border-radius:50%; box-shadow:0 2px 5px rgba(59,130,246,0.4); }
    .leaflet-popup-content-wrapper {
      border-radius:14px!important; padding:0!important;
      box-shadow:0 5px 20px rgba(0,0,0,0.1)!important;
      border:1px solid rgba(255,159,67,0.2)!important; }
    .leaflet-popup-content { margin:0!important; }
    .pb { padding:12px 14px; min-width:170px; }
    .pn { font-size:13px; font-weight:800; color:#0F172A; margin-bottom:2px; }
    .ps { font-size:10px; color:#D97706; font-weight:600; }
    .pbtn { margin-top:8px; background:#FF9F43; color:#0c0e12;
            border:none; border-radius:9px; padding:7px 0;
            font-weight:800; font-size:11px; width:100%; cursor:pointer; }
  <\/style>
</head>
<body>
  <div id="map"></div>
  <script>
    var map = L.map('map', { zoomControl:false, preferCanvas:true })
               .setView([16.047079, 108.206230], 6);

    var tile = L.tileLayer(
      'https://api.openmap.vn/map/tiles/{z}/{x}/{y}?apikey=DNmLXlnYjfHFnvaThBU1FXYrXAGhfpgD',
      { maxZoom:20, attribution:'&copy; OpenMap VN' }
    ).addTo(map);

    var fbDone = false;
    tile.on('tileerror', function() {
      if (fbDone) return; fbDone = true;
      map.removeLayer(tile);
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
        { maxZoom:19, attribution:'&copy; OSM' }).addTo(map);
    });

    var layerGroup = L.layerGroup().addTo(map);
    var markers = {}, selId = null, route = null, uMarker = null;

    function mkIcon(sel) {
      return L.divIcon({
        className:'',
        html:'<div class="gv' + (sel?' sel':'') + '" style="width:28px;height:28px;">' +
             (sel ? '\uD83C\uDF38' : '\uD83E\uDEA6') + '</div>',
        iconSize:[28,28], iconAnchor:[14,14], popupAnchor:[0,-16],
      });
    }
    function mkUser() {
      return L.divIcon({
        className:'',
        html:'<div class="uv" style="width:14px;height:14px;"></div>',
        iconSize:[14,14], iconAnchor:[7,7],
      });
    }

    function addMarker(lat, lng, id, name, sub) {
      var m = L.marker([lat,lng], { icon:mkIcon(false) });
      m.bindPopup(
        '<div class="pb"><div class="pn">'+name+'</div>' +
        '<div class="ps">'+(sub||'')+'</div>' +
        '<button class="pbtn" onclick="onNav('+id+')">\uD83E\uDDED D\u1eabn \u0111\u01b0\u1eddng</button></div>',
        { maxWidth:230, closeButton:false }
      );
      m.on('click', function() {
        onSel(id);
        window.ReactNativeWebView.postMessage(JSON.stringify({type:'SELECT_GRAVE',id:id}));
      });
      markers[id] = m;
      layerGroup.addLayer(m);
    }

    function onSel(id) {
      if (selId && markers[selId]) markers[selId].setIcon(mkIcon(false));
      selId = id;
      if (markers[id]) { markers[id].setIcon(mkIcon(true)); markers[id].openPopup(); }
    }
    function onNav(id) {
      window.ReactNativeWebView.postMessage(JSON.stringify({type:'NAVIGATE',id:id}));
    }
    function drawRoute(coords) {
      if (route) map.removeLayer(route);
      route = L.polyline(coords, {color:'#FF9F43',weight:5,opacity:0.9}).addTo(map);
      map.fitBounds(route.getBounds(), {padding:[80,60]});
    }
    function clearRoute() { if (route) { map.removeLayer(route); route=null; } }
    function focusGrave(id,lat,lng) { onSel(id); map.setView([lat,lng],17,{animate:true}); }
    function setUser(lat,lng) {
      if (uMarker) map.removeLayer(uMarker);
      uMarker = L.marker([lat,lng],{icon:mkUser(),zIndexOffset:1000}).addTo(map);
      map.setView([lat,lng],14,{animate:true});
    }
    function fitAll() {
      var ls = layerGroup.getLayers();
      if (ls.length > 0) map.fitBounds(L.featureGroup(ls).getBounds(),{padding:[60,60]});
    }

    document.addEventListener('message', onMsg);
    window.addEventListener('message', onMsg);
    function onMsg(e) {
      try {
        var msg = JSON.parse(e.data);
        if (msg.type==='FOCUS')         focusGrave(msg.id,msg.lat,msg.lng);
        if (msg.type==='DRAW_ROUTE')    drawRoute(msg.coords);
        if (msg.type==='CLEAR_ROUTE')   clearRoute();
        if (msg.type==='USER_LOCATION') setUser(msg.lat,msg.lng);
        if (msg.type==='FIT_ALL')       fitAll();
        if (msg.type==='ADD_MARKERS') {
          var list=msg.graves, i=0;
          function batch() {
            for (var end=Math.min(i+5,list.length); i<end; i++) {
              var g=list[i]; addMarker(g.lat,g.lng,g.id,g.name,g.sub);
            }
            if (i<list.length) {
              setTimeout(batch,50);
            } else {
              fitAll();
              window.ReactNativeWebView.postMessage(JSON.stringify({type:'MARKERS_LOADED'}));
            }
          }
          batch();
        }
      } catch (e) {}
    }

    // Ready signal (markers sẽ được gửi sau)
    window.ReactNativeWebView.postMessage(JSON.stringify({ type: 'MAP_READY' }));
  </script>
</body>
</html>`;
}

// ─── MAIN COMPONENT ───────────────────────────────────────────────────────────
export default function BanDoScreen() {
  const webViewRef = useRef<WebView>(null);

  const [showMap, setShowMap] = useState(false); // Tắt mặc định để tiết kiệm RAM tối đa cho emulator
  const [graves, setGraves] = useState<MoPhan[]>([]);
  const [loading, setLoading] = useState(true);
  const [mapReady, setMapReady] = useState(false);
  const [userLocation, setUserLocation] = useState<{ latitude: number; longitude: number } | null>(null);
  const [selectedGrave, setSelectedGrave] = useState<MoPhan | null>(null);
  const [searchQuery, setSearchQuery] = useState("");
  const [showList, setShowList] = useState(false);
  const [showDetail, setShowDetail] = useState(false);
  const [routeInfo, setRouteInfo] = useState<{ distanceKm: string; etaMin: string } | null>(null);
  const [routeLoading, setRouteLoading] = useState(false);
  // Animated values
  const listAnim   = useRef(new Animated.Value(0)).current;
  const detailAnim = useRef(new Animated.Value(0)).current;
  const bannerAnim = useRef(new Animated.Value(0)).current;

  // ─── LOAD DATA ─────────────────────────────────────────────────────────────
  const loadGraves = useCallback(async () => {
    try {
      setLoading(true);
      const res = await api.get("/mo-phan/get-data");
      if (res.data.status) {
        setGraves(res.data.data || []);
      }
    } catch (err) {
      console.log("Load error:", err);
    } finally {
      setLoading(false);
    }
  }, []);

  // ─── GPS ───────────────────────────────────────────────────────────────────
  const requestLocation = useCallback(async () => {
    try {
      const { status } = await Location.requestForegroundPermissionsAsync();
      if (status !== "granted") {
        Alert.alert("Cần quyền GPS", "Vui lòng cấp quyền vị trí để sử dụng tính năng dẫn đường.");
        return;
      }
      const loc = await Location.getCurrentPositionAsync({ accuracy: Location.Accuracy.Balanced });
      const coords = { latitude: loc.coords.latitude, longitude: loc.coords.longitude };
      setUserLocation(coords);
      // Gửi vị trí user lên map
      sendToMap({ type: "USER_LOCATION", lat: coords.latitude, lng: coords.longitude });
    } catch (err) {
      console.log("Location error:", err);
    }
  }, []);

  useEffect(() => {
    loadGraves();
    requestLocation();
  }, []);

  // ─── SEND MESSAGE TO MAP ───────────────────────────────────────────────────
  const sendToMap = useCallback((msg: object) => {
    webViewRef.current?.postMessage(JSON.stringify(msg));
  }, []);

  // ─── SEND MARKERS via postMessage (batch, không block UI) ────────────────
  const sendMarkersToMap = useCallback((graveList: MoPhan[]) => {
    const payload = graveList
      .filter(g => g.vi_do && g.kinh_do)
      .map(g => ({
        id: g.id,
        lat: parseFloat(g.vi_do!),
        lng: parseFloat(g.kinh_do!),
        name: (g.thanh_vien?.ho_ten ?? g.ten_mo ?? "Mộ phần").replace(/"/g, '').replace(/'/g, ''),
        sub: [g.ten_nghia_trang, g.khu, g.so_mo].filter(Boolean).join(' · '),
      }));
    sendToMap({ type: "ADD_MARKERS", graves: payload });
  }, [sendToMap]);

  // ─── RECEIVE MESSAGE FROM MAP ──────────────────────────────────────────────
  const handleWebViewMessage = useCallback((event: any) => {
    try {
      const msg = JSON.parse(event.nativeEvent.data);

      if (msg.type === "MAP_READY") {
        setMapReady(true);
        // Gửi vị trí user
        if (userLocation) {
          sendToMap({ type: "USER_LOCATION", lat: userLocation.latitude, lng: userLocation.longitude });
        }
        // Gửi markers theo batch - không block UI
        setTimeout(() => sendMarkersToMap(graves), 300);
      }

      if (msg.type === "SELECT_GRAVE") {
        const grave = graves.find(g => g.id === msg.id);
        if (grave) {
          setSelectedGrave(grave);
          setShowDetail(true);
          Animated.spring(detailAnim, { toValue: 1, useNativeDriver: true, tension: 60, friction: 8 }).start();
        }
      }

      if (msg.type === "NAVIGATE") {
        const grave = graves.find(g => g.id === msg.id);
        if (grave) {
          setShowDetail(false);
          drawRoute(grave);
        }
      }
    } catch (e) {}
  }, [graves, userLocation, sendMarkersToMap]);

  // ─── DẪN ĐƯỜNG (OSRM → vẽ route trên map) ────────────────────────────────
  const drawRoute = useCallback(async (grave: MoPhan) => {
    if (!userLocation) {
      Alert.alert("Chưa có GPS", "Đang xác định vị trí, vui lòng thử lại...");
      requestLocation();
      return;
    }
    if (!grave.vi_do || !grave.kinh_do) {
      Alert.alert("Thiếu tọa độ", "Mộ phần này chưa được gắn tọa độ GPS.");
      return;
    }
    setRouteLoading(true);

    const destLat = parseFloat(grave.vi_do);
    const destLng = parseFloat(grave.kinh_do);

    try {
      const url = `https://router.project-osrm.org/route/v1/driving/${userLocation.longitude},${userLocation.latitude};${destLng},${destLat}?overview=full&geometries=geojson`;
      const res  = await fetch(url);
      const json = await res.json();

      if (json.code === "Ok" && json.routes?.length > 0) {
        const route = json.routes[0];
        // Leaflet format: [[lat, lng], ...]
        const coords = route.geometry.coordinates.map((c: number[]) => [c[1], c[0]]);
        const distKm = (route.distance / 1000).toFixed(1);
        const etaMin = Math.round(route.duration / 60).toString();

        sendToMap({ type: "DRAW_ROUTE", coords });
        setRouteInfo({ distanceKm: distKm, etaMin });
      } else {
        // Fallback: đường thẳng
        const coords = [
          [userLocation.latitude, userLocation.longitude],
          [destLat, destLng],
        ];
        sendToMap({ type: "DRAW_ROUTE", coords });
        const dist = calcDistance(userLocation, { latitude: destLat, longitude: destLng });
        setRouteInfo({ distanceKm: dist.toFixed(1), etaMin: "N/A" });
      }

      Animated.spring(bannerAnim, { toValue: 1, useNativeDriver: true, tension: 60, friction: 8 }).start();
    } catch (err) {
      // Fallback khi mất mạng
      const coords = [
        [userLocation.latitude, userLocation.longitude],
        [destLat, destLng],
      ];
      sendToMap({ type: "DRAW_ROUTE", coords });
      const dist = calcDistance(userLocation, { latitude: destLat, longitude: destLng });
      setRouteInfo({ distanceKm: dist.toFixed(1), etaMin: "N/A (offline)" });
      Animated.spring(bannerAnim, { toValue: 1, useNativeDriver: true, tension: 60, friction: 8 }).start();
    } finally {
      setRouteLoading(false);
    }
  }, [userLocation, sendToMap]);

  const clearRoute = useCallback(() => {
    sendToMap({ type: "CLEAR_ROUTE" });
    setRouteInfo(null);
    Animated.timing(bannerAnim, { toValue: 0, duration: 200, useNativeDriver: true }).start();
  }, [sendToMap]);

  // ─── FOCUS GRAVE ──────────────────────────────────────────────────────────
  const focusGrave = useCallback((grave: MoPhan) => {
    if (!grave.vi_do || !grave.kinh_do) return;
    setSelectedGrave(grave);
    sendToMap({
      type: "FOCUS",
      id: grave.id,
      lat: parseFloat(grave.vi_do),
      lng: parseFloat(grave.kinh_do),
    });
  }, [sendToMap]);

  const handleSelectGrave = useCallback((grave: MoPhan) => {
    setShowList(false);
    setShowDetail(true);
    focusGrave(grave);
    Animated.spring(detailAnim, { toValue: 1, useNativeDriver: true, tension: 60, friction: 8 }).start();
  }, [focusGrave]);

  const dismissDetail = useCallback(() => {
    Animated.timing(detailAnim, { toValue: 0, duration: 200, useNativeDriver: true }).start(() => {
      setShowDetail(false);
    });
  }, []);

  // ─── ADD/EDIT/DELETE STATES ────────────────────────────────────────────────
  const [showModal, setShowModal] = useState(false);
  const [isEditing, setIsEditing] = useState(false);
  const [editGraveId, setEditGraveId] = useState<number | null>(null);
  
  // Form fields
  const [formThanhVienId, setFormThanhVienId] = useState<number | null>(null);
  const [formTenMo, setFormTenMo] = useState("");
  const [formDiaChi, setFormDiaChi] = useState("");
  const [formKhu, setFormKhu] = useState("");
  const [formHang, setFormHang] = useState("");
  const [formSoMo, setFormSoMo] = useState("");
  const [formHuongMo, setFormHuongMo] = useState("");
  const [formTenNghiaTrang, setFormTenNghiaTrang] = useState("");
  const [formKinhDo, setFormKinhDo] = useState("");
  const [formViDo, setFormViDo] = useState("");
  const [formGhiChu, setFormGhiChu] = useState("");
  const [formHinhAnh, setFormHinhAnh] = useState("");

  // Members list states
  const [members, setMembers] = useState<any[]>([]);
  const [loadingMembers, setLoadingMembers] = useState(false);
  const [showMemberSelector, setShowMemberSelector] = useState(false);

  // Load members from BE
  const loadMembers = useCallback(async () => {
    try {
      setLoadingMembers(true);
      const res = await api.get("/thanh-vien/get-data");
      if (res.data.status) {
        setMembers(res.data.data || []);
      }
    } catch (err) {
      console.log("Load members error:", err);
    } finally {
      setLoadingMembers(false);
    }
  }, []);

  const openCreateModal = useCallback(() => {
    setIsEditing(false);
    setEditGraveId(null);
    setFormThanhVienId(null);
    setFormTenMo("");
    setFormDiaChi("");
    setFormKhu("");
    setFormHang("");
    setFormSoMo("");
    setFormHuongMo("");
    setFormTenNghiaTrang("");
    setFormViDo(userLocation ? userLocation.latitude.toString() : "");
    setFormKinhDo(userLocation ? userLocation.longitude.toString() : "");
    setFormGhiChu("");
    setFormHinhAnh("");
    loadMembers();
    setShowModal(true);
  }, [userLocation, loadMembers]);

  const openEditModal = useCallback((grave: MoPhan) => {
    setIsEditing(true);
    setEditGraveId(grave.id);
    setFormThanhVienId(grave.thanh_vien_id);
    setFormTenMo(grave.ten_mo || "");
    setFormDiaChi(grave.dia_chi || "");
    setFormKhu(grave.khu || "");
    setFormHang(grave.hang || "");
    setFormSoMo(grave.so_mo || "");
    setFormHuongMo(grave.huong_mo || "");
    setFormTenNghiaTrang(grave.ten_nghia_trang || "");
    setFormViDo(grave.vi_do || "");
    setFormKinhDo(grave.kinh_do || "");
    setFormGhiChu(grave.ghi_chu || "");
    setFormHinhAnh(grave.hinh_anh || "");
    loadMembers();
    setShowModal(true);
  }, [loadMembers]);

  const fetchCurrentGPSForForm = useCallback(async () => {
    try {
      const loc = await Location.getCurrentPositionAsync({ accuracy: Location.Accuracy.Balanced });
      setFormViDo(loc.coords.latitude.toString());
      setFormKinhDo(loc.coords.longitude.toString());
      Alert.alert("Thành công", "Đã lấy tọa độ GPS hiện tại!");
    } catch (err) {
      Alert.alert("Lỗi", "Không thể định vị vị trí hiện tại.");
    }
  }, []);

  const handleSaveGrave = useCallback(async () => {
    if (!formTenMo.trim()) {
      Alert.alert("Thiếu thông tin", "Vui lòng nhập tên mộ phần.");
      return;
    }
    try {
      setLoading(true);
      const payload = {
        id: editGraveId,
        thanh_vien_id: formThanhVienId,
        ten_mo: formTenMo,
        dia_chi: formDiaChi,
        khu: formKhu,
        hang: formHang,
        so_mo: formSoMo,
        huong_mo: formHuongMo,
        ten_nghia_trang: formTenNghiaTrang,
        vi_do: formViDo || null,
        kinh_do: formKinhDo || null,
        ghi_chu: formGhiChu,
        hinh_anh: formHinhAnh,
      };

      const url = isEditing ? "/mo-phan/update" : "/mo-phan/create";
      const res = await api.post(url, payload);

      if (res.data.status) {
        Alert.alert("Thành công", isEditing ? "Cập nhật mộ phần thành công!" : "Tạo mới mộ phần thành công!");
        setShowModal(false);
        loadGraves();
      } else {
        Alert.alert("Thất bại", res.data.message || "Không thể lưu thông tin.");
      }
    } catch (err) {
      Alert.alert("Lỗi", "Không thể lưu thông tin.");
    } finally {
      setLoading(false);
    }
  }, [
    editGraveId,
    formThanhVienId,
    formTenMo,
    formDiaChi,
    formKhu,
    formHang,
    formSoMo,
    formHuongMo,
    formTenNghiaTrang,
    formViDo,
    formKinhDo,
    formGhiChu,
    formHinhAnh,
    isEditing,
    loadGraves,
  ]);

  const openExternalMap = useCallback((grave: MoPhan) => {
    if (!grave.vi_do || !grave.kinh_do) return;
    const lat = parseFloat(grave.vi_do);
    const lng = parseFloat(grave.kinh_do);
    const name = grave.thanh_vien?.ho_ten ?? grave.ten_mo ?? "Mộ phần";
    const label = encodeURIComponent(name);
    const url = Platform.select({
      ios: `maps:0,0?q=${lat},${lng}(${label})`,
      android: `geo:0,0?q=${lat},${lng}(${label})`,
    }) || `https://www.google.com/maps/search/?api=1&query=${lat},${lng}`;
    
    Linking.openURL(url).catch(() => {
      Linking.openURL(`https://www.google.com/maps/search/?api=1&query=${lat},${lng}`);
    });
  }, []);

  const handleDeleteGrave = useCallback((id: number, name: string) => {
    Alert.alert(
      "Xác nhận xóa",
      `Bạn có chắc chắn muốn xóa mộ phần "${name}" này không?`,
      [
        { text: "Hủy", style: "cancel" },
        {
          text: "Xóa",
          style: "destructive",
          onPress: async () => {
            try {
              setLoading(true);
              const res = await api.post("/mo-phan/delete", { id });
              if (res.data.status) {
                Alert.alert("Thành công", "Đã xóa mộ phần thành công!");
                dismissDetail();
                loadGraves();
              } else {
                Alert.alert("Thất bại", res.data.message || "Không thể xóa mộ phần.");
              }
            } catch (err) {
              Alert.alert("Lỗi", "Không thể xóa mộ phần.");
            } finally {
              setLoading(false);
            }
          }
        }
      ]
    );
  }, [loadGraves, dismissDetail]);

  // ─── TOGGLE LIST ──────────────────────────────────────────────────────────
  const toggleList = useCallback(() => {
    if (showList) {
      Animated.timing(listAnim, { toValue: 0, duration: 250, useNativeDriver: true }).start(() => setShowList(false));
    } else {
      setShowList(true);
      Animated.spring(listAnim, { toValue: 1, useNativeDriver: true, tension: 60, friction: 9 }).start();
    }
  }, [showList]);

  // ─── FILTER ───────────────────────────────────────────────────────────────
  const filteredList = useMemo(() => {
    const q = searchQuery.toLowerCase().trim();
    if (!q) return graves;
    return graves.filter(g =>
      g.ten_mo?.toLowerCase().includes(q) ||
      g.thanh_vien?.ho_ten?.toLowerCase().includes(q) ||
      g.ten_nghia_trang?.toLowerCase().includes(q) ||
      g.khu?.toLowerCase().includes(q)
    );
  }, [graves, searchQuery]);

  const gravesWithLocation = useMemo(() => graves.filter(g => g.vi_do && g.kinh_do), [graves]);

  const getDistance = useCallback((grave: MoPhan) => {
    if (!userLocation || !grave.vi_do || !grave.kinh_do) return null;
    const d = calcDistance(userLocation, {
      latitude: parseFloat(grave.vi_do),
      longitude: parseFloat(grave.kinh_do),
    });
    return d < 1 ? `${(d * 1000).toFixed(0)}m` : `${d.toFixed(1)}km`;
  }, [userLocation]);

  // ─── LEAFLET HTML (build 1 lần, markers gửi sau qua postMessage) ───────────
  const leafletHTML = useMemo(() => buildLeafletHTML(), []);

  // ─── ANIMATION INTERPOLATES ───────────────────────────────────────────────
  const listTranslateY = listAnim.interpolate({ inputRange: [0, 1], outputRange: [SCREEN_H, 0] });
  const detailTranslateY = detailAnim.interpolate({ inputRange: [0, 1], outputRange: [400, 0] });
  const bannerTranslateY = bannerAnim.interpolate({ inputRange: [0, 1], outputRange: [-120, 0] });

  // ─── RENDER ───────────────────────────────────────────────────────────────
  // ─── RENDER ───────────────────────────────────────────────────────────────
  return (
    <View style={s.container}>
      <StatusBar barStyle="dark-content" />

      {/* MAP (WebView + Leaflet) - Chỉ hiển thị khi được bật */}
      {showMap ? (
        <WebView
          ref={webViewRef}
          style={s.map}
          source={{ html: leafletHTML }}
          onMessage={handleWebViewMessage}
          originWhitelist={["*"]}
          javaScriptEnabled
          domStorageEnabled
          startInLoadingState
          cacheEnabled
          mixedContentMode="always"
          androidLayerType="hardware"
          setSupportMultipleWindows={false}
          renderLoading={() => (
            <View style={s.mapLoading}>
              <ActivityIndicator color="#FF9F43" size="large" />
              <Text style={s.mapLoadingText}>Đang tải bản đồ...</Text>
            </View>
          )}
        />
      ) : (
        /* GIAO DIỆN THAY THẾ KHI TẮT BẢN ĐỒ (TIẾT KIỆM RAM) */
        <View style={s.fallbackContent}>
          {/* Banner bật bản đồ */}
          <TouchableOpacity style={s.mapToggleBanner} onPress={() => setShowMap(true)}>
            <View style={s.mapToggleIconWrap}>
              <Ionicons name="map" size={20} color="#fff" />
            </View>
            <View style={{ flex: 1, marginLeft: 12 }}>
              <Text style={s.mapToggleTitle}>Bản đồ đang tắt để tiết kiệm RAM</Text>
              <Text style={s.mapToggleSub}>Nhấn vào đây để bật hiển thị bản đồ trực quan</Text>
            </View>
            <Ionicons name="chevron-forward-outline" size={18} color="#D97706" />
          </TouchableOpacity>

          {/* Thanh tìm kiếm nhanh */}
          <View style={s.inlineSearchBar}>
            <Ionicons name="search-outline" size={18} color="#FF9F43" />
            <TextInput
              style={s.searchInput}
              placeholder="Tìm tên người, nghĩa trang, khu..."
              placeholderTextColor="#94A3B8"
              value={searchQuery}
              onChangeText={setSearchQuery}
            />
            {searchQuery.length > 0 && (
              <TouchableOpacity onPress={() => setSearchQuery("")}>
                <Ionicons name="close-circle" size={18} color="#CBD5E1" />
              </TouchableOpacity>
            )}
          </View>

          {/* Danh sách các mộ */}
          {loading ? (
            <View style={{ flex: 1, justifyContent: "center", alignItems: "center" }}>
              <ActivityIndicator color="#FF9F43" size="large" />
            </View>
          ) : (
            <FlatList
              data={filteredList}
              keyExtractor={item => item.id.toString()}
              showsVerticalScrollIndicator={false}
              contentContainerStyle={{ paddingBottom: 110, paddingTop: 6 }}
              ListEmptyComponent={
                <View style={s.empty}>
                  <Ionicons name="map-outline" size={48} color="#CBD5E1" />
                  <Text style={s.emptyText}>Không tìm thấy mộ phần</Text>
                </View>
              }
              renderItem={({ item }) => (
                <GraveRow
                  grave={item}
                  isSelected={selectedGrave?.id === item.id}
                  distance={getDistance(item)}
                  onPress={() => handleSelectGrave(item)}
                />
              )}
            />
          )}
        </View>
      )}

      {/* HEADER */}
      <View style={s.header}>
        <TouchableOpacity style={s.headerBtn} onPress={() => router.back()}>
          <Ionicons name="arrow-back-outline" size={22} color="#0F172A" />
        </TouchableOpacity>
        <View style={s.headerCenter}>
          <Text style={s.headerTitle}>Bản Đồ Mộ Phần</Text>
          <Text style={s.headerSub}>
            {loading
              ? "Đang tải..."
              : showMap
              ? `${gravesWithLocation.length} mộ trên bản đồ`
              : `${graves.length} mộ trong danh sách`}
          </Text>
        </View>
        <View style={{ flexDirection: "row", alignItems: "center" }}>
          <TouchableOpacity style={[s.headerBtn, { marginRight: 6 }]} onPress={openCreateModal}>
            <Ionicons name="add-circle" size={22} color="#22C55E" />
          </TouchableOpacity>
          <TouchableOpacity style={[s.headerBtn, { marginRight: 6 }]} onPress={loadGraves}>
            <Ionicons name="refresh-outline" size={20} color="#D97706" />
          </TouchableOpacity>
          <TouchableOpacity
            style={[s.headerBtn, showMap && { backgroundColor: "rgba(255,159,67,0.18)" }]}
            onPress={() => setShowMap(!showMap)}
          >
            <Ionicons name={showMap ? "map" : "map-outline"} size={20} color="#D97706" />
          </TouchableOpacity>
        </View>
      </View>

      {/* ROUTE BANNER */}
      {routeInfo && (
        <Animated.View style={[s.routeBanner, { transform: [{ translateY: bannerTranslateY }] }]}>
          <Ionicons name="navigate" size={20} color="#FF9F43" />
          <View style={{ marginLeft: 10, flex: 1 }}>
            <Text style={s.routeDist}>{routeInfo.distanceKm} km</Text>
            <Text style={s.routeEta}>
              {routeInfo.etaMin !== "N/A" && routeInfo.etaMin !== "N/A (offline)"
                ? `~${routeInfo.etaMin} phút`
                : "Đường thẳng ước tính"}
            </Text>
          </View>
          <TouchableOpacity onPress={clearRoute}>
            <Ionicons name="close-circle" size={24} color="#64748B" />
          </TouchableOpacity>
        </Animated.View>
      )}

      {/* FAB BUTTONS (Chỉ hiện khi bật map) */}
      {showMap && (
        <View style={s.fabGroup}>
          <TouchableOpacity style={s.fab} onPress={requestLocation}>
            <Ionicons name="locate-outline" size={22} color="#D97706" />
          </TouchableOpacity>
          <TouchableOpacity
            style={[s.fab, showList && s.fabActive]}
            onPress={toggleList}
          >
            <Ionicons name={showList ? "list" : "list-outline"} size={22} color={showList ? "#fff" : "#D97706"} />
          </TouchableOpacity>
          <TouchableOpacity
            style={s.fab}
            onPress={() => sendToMap({ type: "FIT_ALL" })}
          >
            <Ionicons name="expand-outline" size={22} color="#D97706" />
          </TouchableOpacity>
        </View>
      )}

      {/* ROUTE LOADING */}
      {routeLoading && (
        <View style={s.routeLoading}>
          <ActivityIndicator color="#FF9F43" size="small" />
          <Text style={s.routeLoadingText}>Đang tính đường...</Text>
        </View>
      )}

      {/* BOTTOM SHEET: DANH SÁCH (Chỉ hiện khi bật map) */}
      {showList && showMap && (
        <Animated.View style={[s.sheet, { transform: [{ translateY: listTranslateY }] }]}>
          <View style={s.sheetHandle} />
          <View style={s.sheetHeader}>
            <View>
              <Text style={s.sheetTitle}>Danh Sách Mộ Phần</Text>
              <Text style={s.sheetSub}>{filteredList.length} mộ trong hệ thống</Text>
            </View>
            <TouchableOpacity onPress={toggleList}>
              <Ionicons name="close-circle-outline" size={26} color="#94A3B8" />
            </TouchableOpacity>
          </View>

          {/* Search */}
          <View style={s.searchBar}>
            <Ionicons name="search-outline" size={18} color="#FF9F43" />
            <TextInput
              style={s.searchInput}
              placeholder="Tìm tên người, nghĩa trang, khu..."
              placeholderTextColor="#94A3B8"
              value={searchQuery}
              onChangeText={setSearchQuery}
            />
            {searchQuery.length > 0 && (
              <TouchableOpacity onPress={() => setSearchQuery("")}>
                <Ionicons name="close-circle" size={18} color="#CBD5E1" />
              </TouchableOpacity>
            )}
          </View>

          {loading ? (
            <View style={s.sheetLoading}>
              <ActivityIndicator color="#FF9F43" size="large" />
            </View>
          ) : (
            <FlatList
              data={filteredList}
              keyExtractor={item => item.id.toString()}
              showsVerticalScrollIndicator={false}
              contentContainerStyle={{ paddingBottom: 30 }}
              ListEmptyComponent={
                <View style={s.empty}>
                  <Ionicons name="map-outline" size={48} color="#CBD5E1" />
                  <Text style={s.emptyText}>Không tìm thấy mộ phần</Text>
                </View>
              }
              renderItem={({ item }) => (
                <GraveRow
                  grave={item}
                  isSelected={selectedGrave?.id === item.id}
                  distance={getDistance(item)}
                  onPress={() => handleSelectGrave(item)}
                />
              )}
            />
          )}
        </Animated.View>
      )}

      {/* DETAIL CARD */}
      {showDetail && selectedGrave && (
        <Animated.View style={[s.detailCard, { transform: [{ translateY: detailTranslateY }] }]}>
          <View style={s.detailActions}>
            <TouchableOpacity style={s.actionBtn} onPress={() => { dismissDetail(); openEditModal(selectedGrave); }}>
              <Ionicons name="pencil-outline" size={16} color="#D97706" />
            </TouchableOpacity>
            <TouchableOpacity style={[s.actionBtn, { marginLeft: 8 }]} onPress={() => handleDeleteGrave(selectedGrave.id, selectedGrave.ten_mo)}>
              <Ionicons name="trash-outline" size={16} color="#EF4444" />
            </TouchableOpacity>
            <TouchableOpacity style={[s.actionBtn, { marginLeft: 8 }]} onPress={dismissDetail}>
              <Ionicons name="close" size={18} color="#64748B" />
            </TouchableOpacity>
          </View>

          <ScrollView showsVerticalScrollIndicator={false} bounces={false}>
            {/* Person */}
            <View style={s.personRow}>
              {selectedGrave.thanh_vien?.avatar ? (
                <Image source={{ uri: selectedGrave.thanh_vien.avatar }} style={s.avatar} />
              ) : (
                <View style={s.avatarDefault}>
                  <Ionicons name="person-outline" size={26} color="#FF9F43" />
                </View>
              )}
              <View style={{ flex: 1, marginLeft: 12 }}>
                <Text style={s.detailName}>{selectedGrave.thanh_vien?.ho_ten ?? "Không rõ"}</Text>
                <Text style={s.detailLife}>
                  {selectedGrave.thanh_vien?.ngay_sinh
                    ? new Date(selectedGrave.thanh_vien.ngay_sinh).getFullYear() : "?"}
                  {" — "}
                  {selectedGrave.thanh_vien?.ngay_mat
                    ? new Date(selectedGrave.thanh_vien.ngay_mat).getFullYear() : "?"}
                </Text>
                {selectedGrave.thanh_vien?.nghe_nghiep && (
                  <Text style={s.detailJob}>{selectedGrave.thanh_vien.nghe_nghiep}</Text>
                )}
              </View>
            </View>

            {/* Info grid */}
            <View style={s.infoGrid}>
              {selectedGrave.ten_nghia_trang && (
                <InfoRow icon="location-outline" label="Nghĩa trang" value={selectedGrave.ten_nghia_trang} />
              )}
              {(selectedGrave.khu || selectedGrave.hang || selectedGrave.so_mo) && (
                <InfoRow
                  icon="grid-outline"
                  label="Vị trí mộ"
                  value={[selectedGrave.khu, selectedGrave.hang, selectedGrave.so_mo].filter(Boolean).join(" · ")}
                />
              )}
              {selectedGrave.huong_mo && (
                <InfoRow icon="compass-outline" label="Hướng mộ" value={selectedGrave.huong_mo} />
              )}
              {selectedGrave.dia_chi && (
                <InfoRow icon="map-outline" label="Địa chỉ" value={selectedGrave.dia_chi} />
              )}
              {getDistance(selectedGrave) && (
                <InfoRow icon="walk-outline" label="Khoảng cách" value={getDistance(selectedGrave)!} highlight />
              )}
              {selectedGrave.ghi_chu && (
                <InfoRow icon="document-text-outline" label="Ghi chú" value={selectedGrave.ghi_chu} />
              )}
            </View>

            {/* Navigate button */}
            <TouchableOpacity
              style={[s.navBtn, (!selectedGrave.vi_do || routeLoading) && { opacity: 0.6 }]}
              disabled={!selectedGrave.vi_do || routeLoading}
              onPress={() => {
                dismissDetail();
                if (!showMap) {
                  Alert.alert(
                    "Dẫn đường đến mộ phần",
                    `Khoảng cách ước tính: ${getDistance(selectedGrave) || "N/A"}. Bạn muốn sử dụng bản đồ nào?`,
                    [
                      {
                        text: "Google/Apple Maps (Khuyên dùng)",
                        onPress: () => {
                          drawRoute(selectedGrave);
                          openExternalMap(selectedGrave);
                        }
                      },
                      {
                        text: "Bật Bản đồ trong App",
                        onPress: () => {
                          setShowMap(true);
                          setTimeout(() => {
                            drawRoute(selectedGrave);
                          }, 1200);
                        }
                      },
                      { text: "Hủy", style: "cancel" }
                    ]
                  );
                } else {
                  drawRoute(selectedGrave);
                }
              }}
              activeOpacity={0.85}
            >
              {routeLoading
                ? <ActivityIndicator color="#0c0e12" size="small" />
                : <>
                    <Ionicons name="navigate-outline" size={19} color="#0c0e12" />
                    <Text style={s.navBtnText}>  DẪN ĐƯỜNG ĐẾN MỘ</Text>
                  </>
              }
            </TouchableOpacity>
          </ScrollView>
        </Animated.View>
      )}

      {/* MEMBER SELECTOR OVERLAY */}
      <Modal visible={showMemberSelector} transparent animationType="fade">
        <TouchableOpacity style={s.modalOverlay} activeOpacity={1} onPress={() => setShowMemberSelector(false)}>
          <View style={s.selectorContainer} onStartShouldSetResponder={() => true}>
            <Text style={s.selectorTitle}>Chọn thành viên liên kết</Text>
            {loadingMembers ? (
              <ActivityIndicator color="#FF9F43" size="large" style={{ marginVertical: 40 }} />
            ) : (
              <FlatList
                data={members}
                keyExtractor={item => item.id.toString()}
                contentContainerStyle={{ paddingBottom: 20 }}
                style={{ maxHeight: SCREEN_H * 0.4 }}
                renderItem={({ item }) => (
                  <TouchableOpacity
                    style={s.selectorItem}
                    onPress={() => {
                      setFormThanhVienId(item.id);
                      setFormTenMo(`Mộ phần ${item.ho_ten}`); // Gợi ý tên mộ
                      setShowMemberSelector(false);
                    }}
                  >
                    {item.avatar ? (
                      <Image source={{ uri: item.avatar }} style={s.selectorAvatar} />
                    ) : (
                      <View style={s.selectorAvatarDefault}>
                        <Ionicons name="person" size={14} color="#FF9F43" />
                      </View>
                    )}
                    <View style={{ flex: 1, marginLeft: 10 }}>
                      <Text style={s.selectorName}>{item.ho_ten}</Text>
                      <Text style={s.selectorSub}>
                        {[item.ngay_sinh ? new Date(item.ngay_sinh).getFullYear() : '?', item.ngay_mat ? new Date(item.ngay_mat).getFullYear() : '?'].join(' - ')}
                      </Text>
                    </View>
                  </TouchableOpacity>
                )}
                ListEmptyComponent={
                  <View style={{ alignItems: "center", paddingVertical: 20 }}>
                    <Text style={{ color: "#94A3B8" }}>Không tìm thấy thành viên</Text>
                  </View>
                }
              />
            )}
            <TouchableOpacity style={s.selectorClose} onPress={() => setShowMemberSelector(false)}>
              <Text style={s.selectorCloseText}>Đóng</Text>
            </TouchableOpacity>
          </View>
        </TouchableOpacity>
      </Modal>

      {/* ADD/EDIT GRAVE MODAL */}
      <Modal visible={showModal} transparent animationType="slide">
        <View style={s.modalOverlay}>
          <Animated.View style={s.formContainer}>
            <View style={s.formHeader}>
              <Text style={s.formTitle}>{isEditing ? "Cập nhật mộ phần" : "Thêm mộ phần mới"}</Text>
              <TouchableOpacity onPress={() => setShowModal(false)}>
                <Ionicons name="close-circle" size={24} color="#94A3B8" />
              </TouchableOpacity>
            </View>

            <ScrollView showsVerticalScrollIndicator={false} style={{ paddingHorizontal: 16 }}>
              {/* Chọn thành viên liên kết */}
              <Text style={s.formLabel}>Thành viên gia đình liên kết</Text>
              <TouchableOpacity style={s.memberDropdown} onPress={() => setShowMemberSelector(true)}>
                <Ionicons name="person-outline" size={16} color="#FF9F43" />
                <Text style={[s.memberDropdownText, !formThanhVienId && { color: "#94A3B8" }]}>
                  {formThanhVienId 
                    ? members.find(m => m.id === formThanhVienId)?.ho_ten || "Đã liên kết thành viên"
                    : "Chọn thành viên gia phả..."}
                </Text>
                <Ionicons name="chevron-down-outline" size={16} color="#64748B" />
              </TouchableOpacity>

              {/* Tên mộ phần */}
              <Text style={s.formLabel}>Tên mộ phần <Text style={{ color: "#EF4444" }}>*</Text></Text>
              <TextInput
                style={s.formInput}
                placeholder="Ví dụ: Mộ tổ cụ Nguyễn Văn A"
                placeholderTextColor="#94A3B8"
                value={formTenMo}
                onChangeText={setFormTenMo}
              />

              {/* Tên nghĩa trang */}
              <Text style={s.formLabel}>Tên nghĩa trang / Nơi an táng</Text>
              <TextInput
                style={s.formInput}
                placeholder="Ví dụ: Nghĩa trang Lạc Hồng Viên"
                placeholderTextColor="#94A3B8"
                value={formTenNghiaTrang}
                onChangeText={setFormTenNghiaTrang}
              />

              {/* Vị trí khu, hàng, số mộ */}
              <View style={{ flexDirection: "row", gap: 10 }}>
                <View style={{ flex: 1 }}>
                  <Text style={s.formLabel}>Khu</Text>
                  <TextInput
                    style={s.formInput}
                    placeholder="Khu A"
                    placeholderTextColor="#94A3B8"
                    value={formKhu}
                    onChangeText={setFormKhu}
                  />
                </View>
                <View style={{ flex: 1 }}>
                  <Text style={s.formLabel}>Hàng</Text>
                  <TextInput
                    style={s.formInput}
                    placeholder="Hàng 3"
                    placeholderTextColor="#94A3B8"
                    value={formHang}
                    onChangeText={setFormHang}
                  />
                </View>
                <View style={{ flex: 1 }}>
                  <Text style={s.formLabel}>Số mộ</Text>
                  <TextInput
                    style={s.formInput}
                    placeholder="Số 12"
                    placeholderTextColor="#94A3B8"
                    value={formSoMo}
                    onChangeText={setFormSoMo}
                  />
                </View>
              </View>

              {/* Địa chỉ và Hướng mộ */}
              <View style={{ flexDirection: "row", gap: 10 }}>
                <View style={{ flex: 1 }}>
                  <Text style={s.formLabel}>Hướng mộ</Text>
                  <TextInput
                    style={s.formInput}
                    placeholder="Đông Nam"
                    placeholderTextColor="#94A3B8"
                    value={formHuongMo}
                    onChangeText={setFormHuongMo}
                  />
                </View>
                <View style={{ flex: 2 }}>
                  <Text style={s.formLabel}>Địa chỉ chi tiết</Text>
                  <TextInput
                    style={s.formInput}
                    placeholder="Ấp 1, Xã Phú Sơn..."
                    placeholderTextColor="#94A3B8"
                    value={formDiaChi}
                    onChangeText={setFormDiaChi}
                  />
                </View>
              </View>

              {/* Tọa độ GPS */}
              <View style={{ flexDirection: "row", gap: 10, alignItems: "flex-end" }}>
                <View style={{ flex: 1 }}>
                  <Text style={s.formLabel}>Vĩ độ (Latitude)</Text>
                  <TextInput
                    style={s.formInput}
                    placeholder="10.8231"
                    placeholderTextColor="#94A3B8"
                    keyboardType="numeric"
                    value={formViDo}
                    onChangeText={setFormViDo}
                  />
                </View>
                <View style={{ flex: 1 }}>
                  <Text style={s.formLabel}>Kinh độ (Longitude)</Text>
                  <TextInput
                    style={s.formInput}
                    placeholder="106.6297"
                    placeholderTextColor="#94A3B8"
                    keyboardType="numeric"
                    value={formKinhDo}
                    onChangeText={setFormKinhDo}
                  />
                </View>
                <TouchableOpacity style={s.gpsFormBtn} onPress={fetchCurrentGPSForForm}>
                  <Ionicons name="locate" size={16} color="#fff" />
                  <Text style={s.gpsFormBtnText}> Lấy GPS</Text>
                </TouchableOpacity>
              </View>

              {/* Ghi chú */}
              <Text style={s.formLabel}>Ghi chú thêm</Text>
              <TextInput
                style={[s.formInput, { height: 70, textAlignVertical: "top", paddingTop: 8 }]}
                placeholder="Mô tả đặc điểm nhận diện, cây xanh xung quanh..."
                placeholderTextColor="#94A3B8"
                multiline
                numberOfLines={3}
                value={formGhiChu}
                onChangeText={setFormGhiChu}
              />

              <View style={{ height: 20 }} />
            </ScrollView>

            {/* Footer buttons */}
            <View style={s.formFooter}>
              <TouchableOpacity style={s.btnCancel} onPress={() => setShowModal(false)}>
                <Text style={s.btnCancelText}>Hủy bỏ</Text>
              </TouchableOpacity>
              <TouchableOpacity style={s.btnSave} onPress={handleSaveGrave}>
                <Ionicons name="save-outline" size={16} color="#0c0e12" />
                <Text style={s.btnSaveText}> Lưu mộ phần</Text>
              </TouchableOpacity>
            </View>
          </Animated.View>
        </View>
      </Modal>
    </View>
  );
}

// ─── GraveRow ─────────────────────────────────────────────────────────────────
const GraveRow = React.memo(({
  grave, isSelected, distance, onPress,
}: { grave: MoPhan; isSelected: boolean; distance: string | null; onPress: () => void }) => (
  <TouchableOpacity
    style={[s.listItem, isSelected && s.listItemSelected]}
    onPress={onPress}
    activeOpacity={0.8}
  >
    <View style={s.listAvatarWrap}>
      {grave.thanh_vien?.avatar
        ? <Image source={{ uri: grave.thanh_vien.avatar }} style={s.listAvatar} />
        : <View style={[s.listAvatar, s.listAvatarDefault]}>
            <Ionicons name="person-outline" size={16} color="#FF9F43" />
          </View>
      }
      {grave.has_location && <View style={s.dotGreen} />}
    </View>

    <View style={{ flex: 1, marginLeft: 11 }}>
      <Text style={s.listName} numberOfLines={1}>
        {grave.thanh_vien?.ho_ten ?? "Không rõ tên"}
      </Text>
      <Text style={s.listSub} numberOfLines={1}>
        {grave.ten_nghia_trang ?? grave.dia_chi ?? "Chưa có địa chỉ"}
      </Text>
      {(grave.khu || grave.so_mo) && (
        <Text style={s.listPos} numberOfLines={1}>
          {[grave.khu, grave.hang, grave.so_mo].filter(Boolean).join(" · ")}
        </Text>
      )}
      <Text style={s.listYears}>
        {grave.thanh_vien?.ngay_sinh ? new Date(grave.thanh_vien.ngay_sinh).getFullYear() : "?"}
        {" — "}
        {grave.thanh_vien?.ngay_mat  ? new Date(grave.thanh_vien.ngay_mat).getFullYear()  : "?"}
      </Text>
    </View>

    <View style={{ alignItems: "flex-end" }}>
      {distance && (
        <View style={s.distBadge}>
          <Text style={s.distText}>{distance}</Text>
        </View>
      )}
      {grave.has_location
        ? <Ionicons name="location" size={14} color="#22C55E" style={{ marginTop: 4 }} />
        : <Ionicons name="location-outline" size={14} color="#CBD5E1" style={{ marginTop: 4 }} />
      }
    </View>
  </TouchableOpacity>
));

// ─── InfoRow ──────────────────────────────────────────────────────────────────
const InfoRow = ({
  icon, label, value, highlight,
}: { icon: keyof typeof Ionicons.glyphMap; label: string; value: string; highlight?: boolean }) => (
  <View style={s.infoRow}>
    <View style={[s.infoIcon, highlight && { backgroundColor: "rgba(34,197,94,0.08)" }]}>
      <Ionicons name={icon} size={13} color={highlight ? "#22C55E" : "#D97706"} />
    </View>
    <View style={{ flex: 1 }}>
      <Text style={s.infoLabel}>{label}</Text>
      <Text style={[s.infoVal, highlight && { color: "#22C55E", fontWeight: "800" }]}>{value}</Text>
    </View>
  </View>
);

// ─── Haversine ────────────────────────────────────────────────────────────────
function calcDistance(
  a: { latitude: number; longitude: number },
  b: { latitude: number; longitude: number }
): number {
  const R = 6371;
  const dLat = ((b.latitude - a.latitude) * Math.PI) / 180;
  const dLng = ((b.longitude - a.longitude) * Math.PI) / 180;
  const h =
    Math.sin(dLat / 2) ** 2 +
    Math.cos((a.latitude * Math.PI) / 180) *
      Math.cos((b.latitude * Math.PI) / 180) *
      Math.sin(dLng / 2) ** 2;
  return R * 2 * Math.atan2(Math.sqrt(h), Math.sqrt(1 - h));
}

// ─── STYLES ───────────────────────────────────────────────────────────────────
const s = StyleSheet.create({
  container: { flex: 1, backgroundColor: "#f0e8dc" },
  map: { flex: 1 },
  mapLoading: {
    position: "absolute", top: 0, left: 0, right: 0, bottom: 0,
    justifyContent: "center", alignItems: "center", backgroundColor: "#f0e8dc",
  },
  mapLoadingText: { marginTop: 12, color: "#D97706", fontWeight: "700", fontSize: 14 },

  // Header
  header: {
    position: "absolute",
    top: Platform.OS === "ios" ? 56 : 36,
    left: 14, right: 14,
    flexDirection: "row", alignItems: "center",
    backgroundColor: "#ffffff",
    borderRadius: 20, paddingVertical: 10, paddingHorizontal: 12,
    shadowColor: "#000", shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.1, shadowRadius: 12, elevation: 6,
    borderWidth: 1, borderColor: "rgba(255,159,67,0.12)",
  },
  headerBtn: {
    width: 36, height: 36, borderRadius: 12,
    backgroundColor: "rgba(255,159,67,0.07)",
    justifyContent: "center", alignItems: "center",
  },
  headerCenter: { flex: 1, marginHorizontal: 10 },
  headerTitle: { fontSize: 15, fontWeight: "800", color: "#0F172A" },
  headerSub: { fontSize: 11, color: "#D97706", fontWeight: "600", marginTop: 1 },

  // Route banner
  routeBanner: {
    position: "absolute",
    top: Platform.OS === "ios" ? 118 : 98,
    left: 14, right: 14,
    flexDirection: "row", alignItems: "center",
    backgroundColor: "#1E293B",
    borderRadius: 16, paddingVertical: 12, paddingHorizontal: 16,
    shadowColor: "#000", shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.25, shadowRadius: 10, elevation: 8,
  },
  routeDist: { fontSize: 18, fontWeight: "900", color: "#FF9F43" },
  routeEta: { fontSize: 11, color: "#94A3B8", fontWeight: "600", marginTop: 1 },

  // FABs
  fabGroup: {
    position: "absolute", right: 14, bottom: 200,
    gap: 10,
  },
  fab: {
    width: 48, height: 48, borderRadius: 16,
    backgroundColor: "#ffffff",
    justifyContent: "center", alignItems: "center",
    shadowColor: "#000", shadowOffset: { width: 0, height: 3 },
    shadowOpacity: 0.1, shadowRadius: 8, elevation: 5,
    borderWidth: 1, borderColor: "rgba(255,159,67,0.15)",
  },
  fabActive: { backgroundColor: "#FF9F43", borderColor: "#FF9F43" },

  // Route loading
  routeLoading: {
    position: "absolute", bottom: 190,
    alignSelf: "center",
    backgroundColor: "#1E293B",
    flexDirection: "row", alignItems: "center",
    borderRadius: 14, paddingVertical: 10, paddingHorizontal: 16, gap: 8,
  },
  routeLoadingText: { color: "#FF9F43", fontSize: 13, fontWeight: "700" },

  // Bottom sheet
  sheet: {
    position: "absolute", bottom: 0, left: 0, right: 0,
    height: SCREEN_H * 0.58,
    backgroundColor: "#ffffff",
    borderTopLeftRadius: 26, borderTopRightRadius: 26,
    paddingHorizontal: 18, paddingTop: 8,
    shadowColor: "#000", shadowOffset: { width: 0, height: -4 },
    shadowOpacity: 0.1, shadowRadius: 14, elevation: 12,
    borderTopWidth: 1, borderColor: "rgba(255,159,67,0.1)",
  },
  sheetHandle: {
    width: 38, height: 4, backgroundColor: "rgba(255,159,67,0.25)",
    borderRadius: 2, alignSelf: "center", marginBottom: 12,
  },
  sheetHeader: {
    flexDirection: "row", justifyContent: "space-between",
    alignItems: "center", marginBottom: 12,
  },
  sheetTitle: { fontSize: 17, fontWeight: "800", color: "#0F172A" },
  sheetSub: { fontSize: 11, color: "#D97706", fontWeight: "600", marginTop: 2 },
  sheetLoading: { flex: 1, justifyContent: "center", alignItems: "center" },
  searchBar: {
    flexDirection: "row", alignItems: "center",
    backgroundColor: "rgba(255,159,67,0.04)",
    borderWidth: 1.5, borderColor: "rgba(255,159,67,0.14)",
    borderRadius: 13, paddingHorizontal: 12, height: 44, marginBottom: 12,
    gap: 8,
  },
  searchInput: { flex: 1, fontSize: 13, color: "#1E293B", fontWeight: "600" },
  empty: { alignItems: "center", paddingTop: 40 },
  emptyText: { fontSize: 13, color: "#94A3B8", fontWeight: "600", marginTop: 8 },

  // List item
  listItem: {
    flexDirection: "row", alignItems: "center",
    paddingVertical: 11, paddingHorizontal: 12,
    borderRadius: 16, marginBottom: 8,
    backgroundColor: "#f9fafb",
    borderWidth: 1.5, borderColor: "rgba(255,159,67,0.07)",
  },
  listItemSelected: {
    backgroundColor: "rgba(255,159,67,0.05)", borderColor: "#FF9F43",
  },
  listAvatarWrap: { position: "relative" },
  listAvatar: {
    width: 44, height: 44, borderRadius: 22,
    borderWidth: 2, borderColor: "rgba(255,159,67,0.2)",
  },
  listAvatarDefault: {
    backgroundColor: "rgba(255,159,67,0.07)",
    justifyContent: "center", alignItems: "center",
  },
  dotGreen: {
    position: "absolute", bottom: 0, right: 0,
    width: 11, height: 11, borderRadius: 6,
    backgroundColor: "#22C55E", borderWidth: 2, borderColor: "#ffffff",
  },
  listName: { fontSize: 13, fontWeight: "800", color: "#1E293B" },
  listSub: { fontSize: 11, color: "#D97706", fontWeight: "600", marginTop: 1 },
  listPos: { fontSize: 10, color: "#64748B", fontWeight: "600", marginTop: 1 },
  listYears: { fontSize: 10, color: "#94A3B8", fontWeight: "600", marginTop: 1 },
  distBadge: {
    backgroundColor: "rgba(255,159,67,0.1)", borderRadius: 8,
    paddingHorizontal: 7, paddingVertical: 2,
  },
  distText: { fontSize: 10, fontWeight: "800", color: "#D97706" },

  // Detail card
  detailCard: {
    position: "absolute", bottom: 0, left: 0, right: 0,
    maxHeight: SCREEN_H * 0.55,
    backgroundColor: "#ffffff",
    borderTopLeftRadius: 26, borderTopRightRadius: 26,
    paddingHorizontal: 18, paddingTop: 18, paddingBottom: 20,
    shadowColor: "#000", shadowOffset: { width: 0, height: -4 },
    shadowOpacity: 0.12, shadowRadius: 14, elevation: 12,
    borderTopWidth: 1.5, borderColor: "rgba(255,159,67,0.14)",
  },
  detailClose: {
    position: "absolute", top: 14, right: 14, zIndex: 10,
    width: 32, height: 32, borderRadius: 16,
    backgroundColor: "rgba(148,163,184,0.1)",
    justifyContent: "center", alignItems: "center",
  },
  personRow: { flexDirection: "row", alignItems: "center", marginBottom: 14, paddingRight: 36 },
  avatar: {
    width: 56, height: 56, borderRadius: 28,
    borderWidth: 2, borderColor: "rgba(255,159,67,0.25)",
  },
  avatarDefault: {
    width: 56, height: 56, borderRadius: 28,
    backgroundColor: "rgba(255,159,67,0.08)",
    borderWidth: 2, borderColor: "rgba(255,159,67,0.2)",
    justifyContent: "center", alignItems: "center",
  },
  detailName: { fontSize: 17, fontWeight: "800", color: "#0F172A" },
  detailLife: { fontSize: 13, color: "#D97706", fontWeight: "700", marginTop: 3 },
  detailJob: { fontSize: 11, color: "#64748B", fontWeight: "600", marginTop: 2 },
  infoGrid: {
    backgroundColor: "rgba(255,159,67,0.03)",
    borderRadius: 14, borderWidth: 1,
    borderColor: "rgba(255,159,67,0.09)",
    padding: 10, marginBottom: 12,
  },
  infoRow: {
    flexDirection: "row", alignItems: "flex-start",
    paddingVertical: 6,
    borderBottomWidth: 1, borderBottomColor: "rgba(255,159,67,0.05)",
  },
  infoIcon: {
    width: 24, height: 24, borderRadius: 7,
    backgroundColor: "rgba(255,159,67,0.08)",
    justifyContent: "center", alignItems: "center",
    marginRight: 9, marginTop: 1,
  },
  infoLabel: { fontSize: 9, color: "#94A3B8", fontWeight: "700", textTransform: "uppercase", letterSpacing: 0.5 },
  infoVal: { fontSize: 12, color: "#1E293B", fontWeight: "700", marginTop: 1 },
  navBtn: {
    backgroundColor: "#FF9F43", flexDirection: "row",
    alignItems: "center", justifyContent: "center",
    paddingVertical: 14, borderRadius: 14,
    shadowColor: "#FF9F43", shadowOffset: { width: 0, height: 5 },
    shadowOpacity: 0.28, shadowRadius: 9, elevation: 5,
  },
  navBtnText: { color: "#0c0e12", fontSize: 14, fontWeight: "900", letterSpacing: 0.8 },

  // Giao diện khi tắt map để tiết kiệm RAM
  fallbackContent: {
    flex: 1,
    backgroundColor: "#f0e8dc",
    paddingTop: Platform.OS === "ios" ? 116 : 96,
    paddingHorizontal: 14,
  },
  mapToggleBanner: {
    flexDirection: "row",
    alignItems: "center",
    backgroundColor: "#ffffff",
    borderRadius: 16,
    padding: 12,
    marginBottom: 12,
    borderWidth: 1.5,
    borderColor: "rgba(255,159,67,0.18)",
    shadowColor: "#000",
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.05,
    shadowRadius: 6,
    elevation: 3,
  },
  mapToggleIconWrap: {
    width: 42,
    height: 42,
    borderRadius: 12,
    backgroundColor: "#FF9F43",
    justifyContent: "center",
    alignItems: "center",
  },
  mapToggleTitle: {
    fontSize: 13,
    fontWeight: "800",
    color: "#1E293B",
  },
  mapToggleSub: {
    fontSize: 10,
    color: "#64748B",
    fontWeight: "600",
    marginTop: 2,
  },
  inlineSearchBar: {
    flexDirection: "row",
    alignItems: "center",
    backgroundColor: "#ffffff",
    borderWidth: 1.5,
    borderColor: "rgba(255,159,67,0.18)",
    borderRadius: 14,
    paddingHorizontal: 12,
    height: 46,
    marginBottom: 8,
    gap: 8,
    shadowColor: "#000",
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.05,
    shadowRadius: 6,
    elevation: 3,
  },

  // CRUD và Modals Styles
  detailActions: {
    position: "absolute", top: 14, right: 14, zIndex: 10,
    flexDirection: "row", alignItems: "center",
  },
  actionBtn: {
    width: 32, height: 32, borderRadius: 16,
    backgroundColor: "rgba(255,159,67,0.08)",
    justifyContent: "center", alignItems: "center",
  },
  modalOverlay: {
    flex: 1, backgroundColor: "rgba(12,14,18,0.55)",
    justifyContent: "center", alignItems: "center",
  },
  selectorContainer: {
    width: SCREEN_W * 0.85, backgroundColor: "#ffffff",
    borderRadius: 22, padding: 18,
    shadowColor: "#000", shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.15, shadowRadius: 12, elevation: 8,
  },
  selectorTitle: { fontSize: 16, fontWeight: "800", color: "#0F172A", marginBottom: 12, textAlign: "center" },
  selectorItem: {
    flexDirection: "row", alignItems: "center", paddingVertical: 10,
    borderBottomWidth: 1, borderBottomColor: "rgba(255,159,67,0.06)",
  },
  selectorAvatar: { width: 34, height: 34, borderRadius: 17, borderWidth: 1, borderColor: "rgba(255,159,67,0.2)" },
  selectorAvatarDefault: {
    width: 34, height: 34, borderRadius: 17, backgroundColor: "rgba(255,159,67,0.06)",
    justifyContent: "center", alignItems: "center",
  },
  selectorName: { fontSize: 13, fontWeight: "700", color: "#1E293B" },
  selectorSub: { fontSize: 10, color: "#D97706", fontWeight: "600", marginTop: 2 },
  selectorClose: {
    backgroundColor: "#F1F5F9", paddingVertical: 10, borderRadius: 10,
    alignItems: "center", marginTop: 12,
  },
  selectorCloseText: { fontSize: 13, fontWeight: "700", color: "#64748B" },

  formContainer: {
    width: SCREEN_W * 0.9, maxHeight: SCREEN_H * 0.8,
    backgroundColor: "#ffffff", borderRadius: 24, paddingVertical: 18,
    shadowColor: "#000", shadowOffset: { width: 0, height: 6 },
    shadowOpacity: 0.2, shadowRadius: 14, elevation: 12,
    borderWidth: 1.5, borderColor: "rgba(255,159,67,0.12)",
  },
  formHeader: {
    flexDirection: "row", justifyContent: "space-between", alignItems: "center",
    paddingHorizontal: 16, marginBottom: 14,
  },
  formTitle: { fontSize: 17, fontWeight: "800", color: "#0F172A" },
  formLabel: { fontSize: 10, fontWeight: "800", color: "#D97706", marginTop: 10, marginBottom: 4, textTransform: "uppercase", letterSpacing: 0.5 },
  formInput: {
    backgroundColor: "rgba(255,159,67,0.03)", borderWidth: 1.5, borderColor: "rgba(255,159,67,0.12)",
    borderRadius: 12, paddingHorizontal: 12, height: 42, color: "#1E293B", fontSize: 13, fontWeight: "600",
  },
  memberDropdown: {
    flexDirection: "row", alignItems: "center", backgroundColor: "rgba(255,159,67,0.03)",
    borderWidth: 1.5, borderColor: "rgba(255,159,67,0.12)", borderRadius: 12,
    paddingHorizontal: 12, height: 42,
  },
  memberDropdownText: { flex: 1, fontSize: 13, fontWeight: "600", color: "#1E293B", marginLeft: 8 },
  gpsFormBtn: {
    backgroundColor: "#FF9F43", flexDirection: "row", alignItems: "center",
    height: 42, paddingHorizontal: 12, borderRadius: 12, marginLeft: 4,
  },
  gpsFormBtnText: { color: "#0c0e12", fontSize: 11, fontWeight: "800" },
  formFooter: {
    flexDirection: "row", borderTopWidth: 1, borderTopColor: "rgba(255,159,67,0.08)",
    paddingTop: 12, paddingHorizontal: 16, marginTop: 14, gap: 10,
  },
  btnCancel: { flex: 1, backgroundColor: "#F1F5F9", height: 44, borderRadius: 12, alignItems: "center", justifyContent: "center" },
  btnCancelText: { fontSize: 13, fontWeight: "700", color: "#64748B" },
  btnSave: {
    flex: 2, backgroundColor: "#FF9F43", height: 44, borderRadius: 12,
    flexDirection: "row", alignItems: "center", justifyContent: "center",
  },
  btnSaveText: { fontSize: 13, fontWeight: "800", color: "#0c0e12" },
});
