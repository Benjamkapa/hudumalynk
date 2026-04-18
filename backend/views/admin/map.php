<?php
/** @var yii\web\View $this */
/** @var array $vendors    // [['id'=>1,'business_name'=>'...','lat'=>-1.28,'lng'=>37.22,'category'=>'Cleaning','orders'=>12,'rating'=>4.5,'county'=>'Nairobi'], ...] */
/** @var array $categories // ['Cleaning','Plumbing','Electrical', ...] */
/** @var array $counties   // ['Nairobi','Westlands','Kilimani', ...] */
/** @var int   $totalVendors */
/** @var int   $activeToday  // vendors with orders today */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Map';

/* Leaflet + MarkerCluster from CDN — CSS anywhere, JS at POS_END in correct order */
$this->registerCssFile('https://unpkg.com/leaflet@1.9.4/dist/leaflet.css');
$this->registerCssFile('https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css');
$this->registerCssFile('https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css');

$this->registerJsFile('https://unpkg.com/leaflet@1.9.4/dist/leaflet.js',
    ['position' => \yii\web\View::POS_END]);
$this->registerJsFile('https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js',
    ['position' => \yii\web\View::POS_END, 'depends' => []]);

/* ── Pre-encode PHP data for JS ── */
$vendorsJson = json_encode(array_map(function ($v) {
    return [
        'id'            => (int)$v['id'],
        'business_name' => $v['business_name'],
        'lat'           => $v['lat'] !== null ? (float)$v['lat'] : null,
        'lng'           => $v['lng'] !== null ? (float)$v['lng'] : null,
        'category'      => $v['category'],
        'county'        => $v['county'],
        'orders'        => (int)$v['orders'],
        'rating'        => round((float)($v['rating'] ?? 0), 1),
    ];
}, $vendors));

$catColorMap = [];
$colors = ['#6C5CE7','#00B894','#3B82F6','#E17055','#FDCB6E','#A29BFE','#F59E0B'];
foreach (array_values($categories) as $ci => $cat) {
    $catColorMap[$cat] = $colors[$ci % count($colors)];
}
$categoryColorsJson = json_encode($catColorMap);
$vendorViewBase     = Url::to(['/admin/vendor-view', 'id' => '']);

/* ── Register the entire map JS at POS_END so L is guaranteed loaded ── */
$this->registerJs(<<<JS
(function () {

    var vendors        = {$vendorsJson};
    var categoryColors = {$categoryColorsJson};
    var vendorViewBase = '{$vendorViewBase}';

    function getColor(cat) { return categoryColors[cat] || '#9898B8'; }

    /* ── Leaflet map ── */
    var isDark    = document.documentElement.getAttribute('data-hl-t') === 'dark';
    var lightTile = 'https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png';
    var darkTile  = 'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png';
    var tileAttr  = '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a> contributors &copy; <a href="https://carto.com/">CARTO</a>';

    var map = L.map('hlNairobiMap', {
        center: [-1.2921, 36.8219],
        zoom: 12,
        zoomControl: false,
        attributionControl: true
    });

    var tileLayer = L.tileLayer(isDark ? darkTile : lightTile, { attribution: tileAttr, maxZoom: 19 }).addTo(map);

    /* Custom zoom buttons */
    document.getElementById('mapZoomIn').addEventListener('click',  function () { map.zoomIn(); });
    document.getElementById('mapZoomOut').addEventListener('click', function () { map.zoomOut(); });

    /* Sync tile layer on theme toggle */
    var themeBtn = document.getElementById('hlThemeBtn');
    if (themeBtn) {
        themeBtn.addEventListener('click', function () {
            setTimeout(function () {
                var t = document.documentElement.getAttribute('data-hl-t');
                map.removeLayer(tileLayer);
                tileLayer = L.tileLayer(t === 'dark' ? darkTile : lightTile, { attribution: tileAttr, maxZoom: 19 }).addTo(map);
            }, 80);
        });
    }

    /* ── Custom SVG pin marker ── */
    function makeIcon(color) {
        var svg = '<svg xmlns="http://www.w3.org/2000/svg" width="28" height="34" viewBox="0 0 28 34">'
            + '<ellipse cx="14" cy="31" rx="5" ry="2" fill="rgba(0,0,0,0.18)"/>'
            + '<path d="M14 0C7.37 0 2 5.37 2 12c0 8.16 12 21 12 21s12-12.84 12-21C26 5.37 20.63 0 14 0z" fill="' + color + '"/>'
            + '<circle cx="14" cy="12" r="5.5" fill="rgba(255,255,255,0.88)"/>'
            + '</svg>';
        return L.divIcon({ html: svg, className: '', iconSize: [28,34], iconAnchor: [14,34], popupAnchor: [0,-36] });
    }

    /* ── Marker cluster group ── */
    var clusterGroup = L.markerClusterGroup({
        maxClusterRadius: 50,
        iconCreateFunction: function (cluster) {
            var cnt = cluster.getChildCount();
            return L.divIcon({
                html: '<div style="width:36px;height:36px;border-radius:50%;background:#6C5CE7;display:flex;align-items:center;justify-content:center;color:#fff;font-size:12px;font-weight:800;border:2.5px solid rgba(255,255,255,.7);box-shadow:0 2px 8px rgba(108,92,231,.35);">' + cnt + '</div>',
                className: '', iconSize: [36,36], iconAnchor: [18,18]
            });
        }
    });

    /* ── Build markers ── */
    var markers = {};

    vendors.forEach(function (v) {
        if (v.lat === null || v.lng === null || isNaN(v.lat) || isNaN(v.lng)) return;

        var lat      = v.lat;
        var lng      = v.lng;
        var color    = getColor(v.category);
        var initials = v.business_name.substring(0, 2).toUpperCase();

        var popup = '<div class="hlm-popup">'
            + '<div class="hlm-popup-head">'
            + '<div class="hlm-popup-av" style="background:' + color + '">' + initials + '</div>'
            + '<div><div class="hlm-popup-name">' + v.business_name + '</div>'
            + '<div class="hlm-popup-cat">' + v.category + '</div></div>'
            + '</div>'
            + '<div class="hlm-popup-row"><span class="hlm-popup-key">County</span><span class="hlm-popup-val">' + v.county + '</span></div>'
            + '<div class="hlm-popup-row"><span class="hlm-popup-key">Orders</span><span class="hlm-popup-val">' + v.orders + '</span></div>'
            + '<div class="hlm-popup-row"><span class="hlm-popup-key">Rating</span><span class="hlm-popup-val">★ ' + v.rating + '</span></div>'
            + '<a class="hlm-popup-link" href="' + vendorViewBase + v.id + '">View vendor profile →</a>'
            + '</div>';

        var marker = L.marker([lat, lng], { icon: makeIcon(color) }).bindPopup(popup, { maxWidth: 230 });
        markers[v.id] = marker;
        clusterGroup.addLayer(marker);
    });

    map.addLayer(clusterGroup);

    /* ── Sidebar: click to fly ── */
    function flyToVendor(vendorId) {
        var v = vendors.find(function (x) { return x.id === vendorId; });
        if (!v || v.lat === null) return;
        map.flyTo([v.lat, v.lng], 15, { duration: 1 });
        if (markers[vendorId]) {
            setTimeout(function () { markers[vendorId].openPopup(); }, 900);
        }
        document.querySelectorAll('.map-vitem').forEach(function (el) { el.classList.remove('highlighted'); });
        var el = document.querySelector('.map-vitem[data-vendor-id="' + vendorId + '"]');
        if (el) el.classList.add('highlighted');
    }

    document.querySelectorAll('.map-vitem').forEach(function (item) {
        item.addEventListener('click', function () { flyToVendor(parseInt(item.dataset.vendorId)); });
    });

    /* ── Filters ── */
    var activeStatus = 'all';

    function applyFilters() {
        var search   = document.getElementById('mapVendorSearch').value.toLowerCase().trim();
        var category = document.getElementById('mapCategoryFilter').value;
        var county   = document.getElementById('mapCountyFilter').value;
        var visCount = 0;

        document.querySelectorAll('.map-vitem').forEach(function (item) {
            var nameMatch   = !search   || item.dataset.name.includes(search);
            var catMatch    = !category || item.dataset.category === category;
            var countyMatch = !county   || item.dataset.county   === county;
            var statusMatch = activeStatus === 'all'
                || (activeStatus === 'active' && item.dataset.status !== 'inactive')
                || (activeStatus === 'top'    && item.dataset.status === 'top');
            var show = nameMatch && catMatch && countyMatch && statusMatch;
            item.style.display = show ? '' : 'none';
            if (show) visCount++;
        });

        document.getElementById('mapListCount').textContent = visCount;

        clusterGroup.clearLayers();
        vendors.forEach(function (v) {
            if (!markers[v.id]) return;
            var nameMatch   = !search   || v.business_name.toLowerCase().includes(search);
            var catMatch    = !category || v.category === category;
            var countyMatch = !county   || v.county   === county;
            var statusMatch = activeStatus === 'all'
                || (activeStatus === 'active')
                || (activeStatus === 'top' && v.rating >= 4);
            if (nameMatch && catMatch && countyMatch && statusMatch) {
                clusterGroup.addLayer(markers[v.id]);
            }
        });
    }

    document.getElementById('mapVendorSearch').addEventListener('input',   applyFilters);
    document.getElementById('mapCategoryFilter').addEventListener('change', applyFilters);
    document.getElementById('mapCountyFilter').addEventListener('change',   applyFilters);

    document.querySelectorAll('.map-filter-pill').forEach(function (pill) {
        pill.addEventListener('click', function () {
            document.querySelectorAll('.map-filter-pill').forEach(function (p) { p.classList.remove('active'); });
            pill.classList.add('active');
            activeStatus = pill.dataset.status;
            applyFilters();
        });
    });

})();
JS, \yii\web\View::POS_END);
?>

<style>
/* ── Map page layout ── */
.map-shell { display: grid; grid-template-columns: 300px minmax(0,1fr); gap: 12px; height: calc(100vh - 130px); }

/* Sidebar panel */
.map-panel { display: flex; flex-direction: column; gap: 10px; overflow: hidden; }
.map-filters { background: var(--bg2); border: 1px solid var(--border); border-radius: var(--r-lg); padding: 13px 14px; flex-shrink: 0; }
.map-filters-title { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .1em; color: var(--text3); margin-bottom: 10px; }
.map-select { width: 100%; background: var(--bg); border: 1px solid var(--border); border-radius: var(--r-md); padding: 7px 10px; font-size: 12px; color: var(--text1); font-family: var(--font, sans-serif); outline: none; cursor: pointer; margin-bottom: 7px; }
.map-select:focus { border-color: var(--acc); }
.map-search-wrap { display: flex; align-items: center; gap: 7px; background: var(--bg); border: 1px solid var(--border); border-radius: var(--r-md); padding: 0 10px; height: 34px; margin-bottom: 7px; }
.map-search-wrap svg { width: 12px; height: 12px; color: var(--text3); flex-shrink: 0; }
.map-search-inp  { background: none; border: none; outline: none; font-size: 12px; color: var(--text1); font-family: var(--font, sans-serif); width: 100%; }
.map-search-inp::placeholder { color: var(--text3); }
.map-filter-row  { display: flex; gap: 6px; }
.map-filter-pill { flex: 1; text-align: center; padding: 5px 0; font-size: 11px; font-weight: 600; border-radius: var(--r-md); border: 1px solid var(--border); cursor: pointer; color: var(--text2); transition: background .13s, color .13s; }
.map-filter-pill.active { background: var(--acc-pale); border-color: var(--acc); color: var(--acc); }
[data-hl-t="dark"] .map-filter-pill.active { color: var(--acc2); border-color: var(--acc2); }

/* KPI strip */
.map-kpi-strip { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; flex-shrink: 0; }
.map-kpi { background: var(--bg2); border: 1px solid var(--border); border-radius: var(--r-lg); padding: 11px 13px; }
.map-kpi-lbl { font-size: 9.5px; font-weight: 700; text-transform: uppercase; letter-spacing: .1em; color: var(--text3); }
.map-kpi-val { font-size: 22px; font-weight: 800; letter-spacing: -.04em; color: var(--text1); line-height: 1.1; margin-top: 2px; }
.map-kpi-sub { font-size: 10px; color: var(--text3); margin-top: 2px; }

/* Vendor list */
.map-vendor-list { flex: 1; overflow-y: auto; background: var(--bg2); border: 1px solid var(--border); border-radius: var(--r-lg); scrollbar-width: thin; scrollbar-color: var(--border) transparent; }
.map-vendor-list::-webkit-scrollbar { width: 4px; }
.map-vendor-list::-webkit-scrollbar-thumb { background: var(--border); border-radius: 99px; }
.map-vlist-head { padding: 11px 13px 9px; border-bottom: 1px solid var(--border); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; color: var(--text3); display: flex; align-items: center; justify-content: space-between; }
.map-vlist-count { background: var(--acc-pale); color: var(--acc); font-size: 10px; font-weight: 700; padding: 2px 8px; border-radius: 20px; }
[data-hl-t="dark"] .map-vlist-count { color: var(--acc2); }
.map-vitem { display: flex; align-items: center; gap: 10px; padding: 10px 13px; border-bottom: 1px solid var(--border); cursor: pointer; transition: background .12s; }
.map-vitem:last-child { border-bottom: none; }
.map-vitem:hover, .map-vitem.highlighted { background: var(--acc-pale2, rgba(108,92,231,.1)); }
.map-vitem-avatar { width: 34px; height: 34px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 800; color: #fff; flex-shrink: 0; }
.map-vitem-name { font-size: 12.5px; font-weight: 600; color: var(--text1); line-height: 1.2; }
.map-vitem-meta { font-size: 10.5px; color: var(--text3); margin-top: 1px; }
.map-vitem-badge { margin-left: auto; display: flex; flex-direction: column; align-items: flex-end; gap: 3px; flex-shrink: 0; }
.map-live-dot { width: 7px; height: 7px; border-radius: 50%; background: var(--teal); animation: hl-pulse 2s infinite; flex-shrink: 0; }

/* Map container */
.map-container-wrap { position: relative; border-radius: var(--r-lg); overflow: hidden; border: 1px solid var(--border); }
#hlNairobiMap { width: 100%; height: 100%; }

/* Map overlays */
.map-overlay-tl { position: absolute; top: 12px; left: 12px; z-index: 500; display: flex; flex-direction: column; gap: 7px; }
.map-overlay-tr { position: absolute; top: 12px; right: 12px; z-index: 500; }
.map-legend-card { background: var(--bg2); border: 1px solid var(--border); border-radius: var(--r-lg); padding: 10px 13px; min-width: 160px; }
.map-legend-title { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .1em; color: var(--text3); margin-bottom: 8px; }
.map-legend-item  { display: flex; align-items: center; gap: 7px; font-size: 11px; color: var(--text2); margin-bottom: 5px; }
.map-legend-item:last-child { margin-bottom: 0; }
.map-legend-dot   { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
.map-stat-chip    { background: var(--bg2); border: 1px solid var(--border); border-radius: var(--r-lg); padding: 8px 12px; font-size: 11.5px; display: flex; align-items: center; gap: 6px; color: var(--text1); font-weight: 600; white-space: nowrap; }
.map-stat-chip svg { width: 12px; height: 12px; color: var(--teal); }
.map-zoom-ctrl    { display: flex; flex-direction: column; background: var(--bg2); border: 1px solid var(--border); border-radius: var(--r-lg); overflow: hidden; }
.map-zoom-btn     { width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 16px; font-weight: 400; color: var(--text1); border: none; background: transparent; transition: background .12s; }
.map-zoom-btn:hover { background: var(--bg3); }
.map-zoom-btn:first-child { border-bottom: 1px solid var(--border); }

/* Popup styles */
.hlm-popup { font-family: 'Plus Jakarta Sans', sans-serif; min-width: 190px; }
.hlm-popup-head { display: flex; align-items: center; gap: 8px; margin-bottom: 9px; }
.hlm-popup-av   { width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 10.5px; font-weight: 800; color: #fff; flex-shrink: 0; }
.hlm-popup-name { font-size: 13px; font-weight: 700; color: #0F0F1A; line-height: 1.2; }
.hlm-popup-cat  { font-size: 10.5px; color: #9898B8; }
.hlm-popup-row  { display: flex; justify-content: space-between; font-size: 11px; padding: 3px 0; border-bottom: 1px solid rgba(0,0,0,.06); }
.hlm-popup-row:last-of-type { border-bottom: none; }
.hlm-popup-key  { color: #9898B8; }
.hlm-popup-val  { font-weight: 600; color: #0F0F1A; }
.hlm-popup-link { display: block; margin-top: 9px; text-align: center; font-size: 11.5px; font-weight: 700; color: #6C5CE7; text-decoration: none; padding: 5px; background: rgba(108,92,231,.08); border-radius: 6px; }

@media (max-width: 860px) {
    .map-shell { grid-template-columns: 1fr; height: auto; }
    .map-container-wrap { height: 420px; }
    .map-panel { display: grid; grid-template-columns: 1fr 1fr; }
    .map-vendor-list { grid-column: 1 / -1; max-height: 280px; }
}
@media (max-width: 540px) {
    .map-panel { grid-template-columns: 1fr; }
}
</style>

<div class="map-shell">

    <!-- ── Left panel ── -->
    <div class="map-panel">

        <!-- Filters -->
        <div class="map-filters">
            <div class="map-filters-title">Filters</div>
            <div class="map-search-wrap">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input class="map-search-inp" type="text" id="mapVendorSearch" placeholder="Search vendors…">
            </div>
            <select class="map-select" id="mapCategoryFilter">
                <option value="">All categories</option>
                <?php foreach ($categories as $cat): ?>
                <option value="<?= Html::encode($cat) ?>"><?= Html::encode($cat) ?></option>
                <?php endforeach; ?>
            </select>
            <select class="map-select" id="mapCountyFilter">
                <option value="">All counties</option>
                <?php foreach ($counties as $county): ?>
                <option value="<?= Html::encode($county) ?>"><?= Html::encode($county) ?></option>
                <?php endforeach; ?>
            </select>
            <div class="map-filter-row">
                <div class="map-filter-pill active" data-status="all">All</div>
                <div class="map-filter-pill" data-status="active">Active</div>
                <div class="map-filter-pill" data-status="top">Top rated</div>
            </div>
        </div>

        <!-- KPI strip -->
        <div class="map-kpi-strip">
            <div class="map-kpi">
                <div class="map-kpi-lbl">Total vendors</div>
                <div class="map-kpi-val"><?= number_format($totalVendors) ?></div>
                <div class="map-kpi-sub">on the platform</div>
            </div>
            <div class="map-kpi">
                <div class="map-kpi-lbl">Active today</div>
                <div class="map-kpi-val" style="color:var(--teal);"><?= number_format($activeToday) ?></div>
                <div class="map-kpi-sub">with orders today</div>
            </div>
        </div>

        <!-- Vendor list -->
        <div class="map-vendor-list">
            <div class="map-vlist-head">
                Vendors
                <span class="map-vlist-count" id="mapListCount"><?= count($vendors) ?></span>
            </div>
            <div id="mapVendorListBody">
                <?php
                $avColors = ['#6C5CE7','#00B894','#3B82F6','#FDCB6E','#E17055','#A29BFE'];
                foreach ($vendors as $vi => $v):
                    $initials = strtoupper(substr($v['business_name'], 0, 2));
                    $bg       = $avColors[$vi % count($avColors)];
                    $rating   = number_format($v['rating'] ?? 0, 1);
                ?>
                <div class="map-vitem"
                     data-vendor-id="<?= (int)$v['id'] ?>"
                     data-lat="<?= (float)($v['lat'] ?? -1.2921) ?>"
                     data-lng="<?= (float)($v['lng'] ?? 36.8219) ?>"
                     data-name="<?= Html::encode(strtolower($v['business_name'])) ?>"
                     data-category="<?= Html::encode($v['category']) ?>"
                     data-county="<?= Html::encode($v['county']) ?>"
                     data-status="<?= ($v['rating'] ?? 0) >= 4 ? 'top' : 'active' ?>">
                    <div class="map-vitem-avatar" style="background:<?= $bg ?>"><?= Html::encode($initials) ?></div>
                    <div style="flex:1;min-width:0;">
                        <div class="map-vitem-name"><?= Html::encode($v['business_name']) ?></div>
                        <div class="map-vitem-meta"><?= Html::encode($v['category']) ?> · <?= Html::encode($v['county']) ?></div>
                    </div>
                    <div class="map-vitem-badge">
                        <span style="font-size:10.5px;font-weight:700;color:var(--amber);">★ <?= $rating ?></span>
                        <span style="font-size:10px;color:var(--text3);"><?= $v['orders'] ?> orders</span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- ── Map ── -->
    <div class="map-container-wrap">
        <div id="hlNairobiMap"></div>

        <!-- Overlay: top-left -->
        <div class="map-overlay-tl">
            <div class="map-stat-chip">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="10" r="3"/><path d="M12 2a8 8 0 0 1 8 8c0 5.25-8 12-8 12S4 15.25 4 10a8 8 0 0 1 8-8z"/></svg>
                Nairobi, KE · Live
                <div class="map-live-dot"></div>
            </div>
        </div>

        <!-- Overlay: top-right legend + zoom -->
        <div class="map-overlay-tr" style="display:flex;flex-direction:column;gap:7px;align-items:flex-end;">
            <div class="map-zoom-ctrl">
                <button class="map-zoom-btn" id="mapZoomIn">+</button>
                <button class="map-zoom-btn" id="mapZoomOut">−</button>
            </div>
            <div class="map-legend-card">
                <div class="map-legend-title">Marker key</div>
                <?php
                $legendCats   = array_slice($categories, 0, 5);
                $legendColors = ['#6C5CE7','#00B894','#3B82F6','#E17055','#FDCB6E'];
                foreach ($legendCats as $li => $lcat):
                ?>
                <div class="map-legend-item">
                    <div class="map-legend-dot" style="background:<?= $legendColors[$li % count($legendColors)] ?>"></div>
                    <?= Html::encode($lcat) ?>
                </div>
                <?php endforeach; ?>
                <?php if (count($categories) > 5): ?>
                <div class="map-legend-item" style="color:var(--text3);font-size:10px;">+ <?= count($categories)-5 ?> more categories</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>