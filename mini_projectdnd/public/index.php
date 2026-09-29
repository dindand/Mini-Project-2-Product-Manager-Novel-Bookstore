<?php
// public/index.php - mini_projectdnd
session_start();
require_once __DIR__ . "/../config/db.php";

$_SESSION["csrf"] ??= bin2hex(random_bytes(32));

$q = trim($_GET["q"] ?? "");
$cat = trim($_GET["category"] ?? "");
$selectedBrand = trim($_GET["brand"] ?? "");

// 1. Ambil daftar Penulis unik yang relevan berdasarkan kategori terpilih
if ($cat !== "" && $cat !== "Semua") {
    $stmtBrandList = $pdo->prepare("SELECT DISTINCT brand FROM products WHERE category = :cat ORDER BY brand ASC");
    $stmtBrandList->execute(["cat" => $cat]);
    $availableBrands = $stmtBrandList->fetchAll(PDO::FETCH_COLUMN);
} else {
    $stmtBrandList = $pdo->query("SELECT DISTINCT brand FROM products ORDER BY brand ASC");
    $availableBrands = $stmtBrandList->fetchAll(PDO::FETCH_COLUMN);
}

// 2. Query dinamis untuk Katalog Novel
$sql = "SELECT id, name, brand, category, price, stock, image_url, created_at FROM products WHERE 1=1";
$params = [];

if ($q !== "") {
    $sql .= " AND (name LIKE :q1 OR brand LIKE :q2 OR category LIKE :q3)";
    $params["q1"] = "%{$q}%";
    $params["q2"] = "%{$q}%";
    $params["q3"] = "%{$q}%";
}

if ($cat !== "" && $cat !== "Semua") {
    $sql .= " AND category = :cat";
    $params["cat"] = $cat;
}

if ($selectedBrand !== "" && $selectedBrand !== "Semua") {
    $sql .= " AND brand = :brand";
    $params["brand"] = $selectedBrand;
}

$sql .= " ORDER BY id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

// 3. Statistik Inventaris Novel
$totalItemsStmt = $pdo->query("SELECT COUNT(*) as total, SUM(price * stock) as total_value, SUM(CASE WHEN stock > 0 THEN stock ELSE 0 END) as total_units, SUM(CASE WHEN stock <= 15 THEN 1 ELSE 0 END) as low_stock FROM products");
$stats = $totalItemsStmt->fetch();

$totalProducts = $stats["total"] ?? 0;
$totalValuation = $stats["total_value"] ?? 0;
$totalUnits = $stats["total_units"] ?? 0;
$lowStockCount = $stats["low_stock"] ?? 0;

// Notifikasi Status PRG
$status = $_GET["status"] ?? "";
$statusMsg = "";
$statusType = "success";

if ($status === "created") {
    $statusMsg = "Buku novel baru berhasil ditambahkan ke dalam katalog.";
} elseif ($status === "updated") {
    $statusMsg = "Data novel berhasil diperbarui.";
} elseif ($status === "deleted") {
    $statusMsg = "Novel telah berhasil dihapus dari sistem katalog.";
} elseif ($status === "notfound") {
    $statusMsg = "Novel tidak ditemukan.";
    $statusType = "error";
}

function getProductImage($url, $category) {
    if (!empty($url)) {
        return htmlspecialchars($url, ENT_QUOTES, "UTF-8");
    }
    $categoryMap = [
        'petualangan' => 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?w=600&auto=format&fit=crop&q=80',
        'sastra'      => 'https://images.unsplash.com/photo-1497633762265-9d179a990aa6?w=600&auto=format&fit=crop&q=80',
        'drama'       => 'https://images.unsplash.com/photo-1509021436665-8f07dbf5bf1d?w=600&auto=format&fit=crop&q=80',
        'fantasi'     => 'https://images.unsplash.com/photo-1518709268805-4e9042af9f23?w=600&auto=format&fit=crop&q=80',
        'romance'     => 'https://images.unsplash.com/photo-1495640388908-05fa85288e61?w=600&auto=format&fit=crop&q=80',
    ];
    $catLower = strtolower($category);
    foreach ($categoryMap as $key => $img) {
        if (strpos($catLower, $key) !== false) {
            return $img;
        }
    }
    return 'https://images.unsplash.com/photo-1512820790803-83ca734da794?w=600&auto=format&fit=crop&q=80';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DnD Novel Store - Katalog Novel Best Seller Indonesia</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

    <!-- Header Navbar -->
    <header class="navbar">
        <div class="nav-container">
            <a href="index.php" class="brand">
                <div class="brand-icon">
                    <svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                    </svg>
                </div>
                <div class="brand-text">
                    <span class="brand-title">
                        DnD Bookstore
                        <span class="brand-badge">Katalog Sastra</span>
                    </span>
                    <span class="brand-subtitle">Inventaris Novel & Karya Sastra Best Seller Indonesia</span>
                </div>
            </a>
            <a href="create.php" class="btn btn-primary" id="btn-add-product">
                <svg fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                <span>Tambah Novel</span>
            </a>
        </div>
    </header>

    <main class="container">
        <!-- Notifikasi Status -->
        <?php if (!empty($statusMsg)): ?>
            <div class="alert alert-<?= $statusType ?>">
                <svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <?php if ($statusType === "success"): ?>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    <?php else: ?>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                    <?php endif; ?>
                </svg>
                <span><?= htmlspecialchars($statusMsg, ENT_QUOTES, "UTF-8") ?></span>
            </div>
        <?php endif; ?>

        <!-- Dashboard Statistics Overview -->
        <section class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon stat-icon-amber">
                    <svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                    </svg>
                </div>
                <div>
                    <div class="stat-label">Total Judul Novel</div>
                    <div class="stat-value"><?= (int)$totalProducts ?> Buku</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon stat-icon-emerald">
                    <svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <div class="stat-label">Nilai Valuasi Inventaris</div>
                    <div class="stat-value">Rp <?= number_format($totalValuation, 0, ",", ".") ?></div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon stat-icon-indigo">
                    <svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                    </svg>
                </div>
                <div>
                    <div class="stat-label">Total Eksemplar Tersedia</div>
                    <div class="stat-value"><?= number_format($totalUnits, 0, ",", ".") ?> Buku</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon stat-icon-rose">
                    <svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                    </svg>
                </div>
                <div>
                    <div class="stat-label">Perhatian Stok Menipis</div>
                    <div class="stat-value"><?= (int)$lowStockCount ?> Judul</div>
                </div>
            </div>
        </section>

        <!-- Integrated Filter & Search Area -->
        <section class="filter-wrapper">
            <!-- 1. Category Quick Filter Tabs -->
            <div class="category-tabs">
                <?php 
                    $categories = ["Semua", "Petualangan & Alam", "Fiksi Sejarah & Sastra", "Drama & Inspirasi", "Fantasi & Petualangan", "Romance & Drama", "Sci-Fi & Romance", "Aksi & Thriller"];
                    foreach ($categories as $c):
                        $isActive = ($cat === $c || ($cat === "" && $c === "Semua"));
                        $linkParams = [];
                        if ($c !== "Semua") $linkParams["category"] = $c;
                        if ($q !== "") $linkParams["q"] = $q;
                        $catUrl = "index.php" . (!empty($linkParams) ? "?" . http_build_query($linkParams) : "");
                ?>
                    <a href="<?= $catUrl ?>" class="tab-btn <?= $isActive ? 'active' : '' ?>">
                        <?= $c ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- 2. Brand/Author Filter Pills -->
            <div class="brand-pills-container">
                <span class="brand-label">Pilih Penulis:</span>
                <?php 
                    $allBrandParams = [];
                    if ($cat !== "" && $cat !== "Semua") $allBrandParams["category"] = $cat;
                    if ($q !== "") $allBrandParams["q"] = $q;
                    $allBrandUrl = "index.php" . (!empty($allBrandParams) ? "?" . http_build_query($allBrandParams) : "");
                    $isAllBrandActive = ($selectedBrand === "" || $selectedBrand === "Semua");
                ?>
                <a href="<?= $allBrandUrl ?>" class="brand-pill <?= $isAllBrandActive ? 'active' : '' ?>">
                    Semua Penulis
                </a>

                <?php foreach ($availableBrands as $b): 
                    $brandParams = [];
                    if ($cat !== "" && $cat !== "Semua") $brandParams["category"] = $cat;
                    $brandParams["brand"] = $b;
                    if ($q !== "") $brandParams["q"] = $q;
                    $brandUrl = "index.php?" . http_build_query($brandParams);
                    $isBrandActive = ($selectedBrand === $b);
                ?>
                    <a href="<?= $brandUrl ?>" class="brand-pill <?= $isBrandActive ? 'active' : '' ?>">
                        <?= htmlspecialchars($b, ENT_QUOTES, "UTF-8") ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- 3. Search Bar Form -->
            <div class="controls-bar">
                <form method="GET" action="index.php" class="search-form">
                    <?php if ($cat !== "" && $cat !== "Semua"): ?>
                        <input type="hidden" name="category" value="<?= htmlspecialchars($cat, ENT_QUOTES, "UTF-8") ?>">
                    <?php endif; ?>
                    <?php if ($selectedBrand !== "" && $selectedBrand !== "Semua"): ?>
                        <input type="hidden" name="brand" value="<?= htmlspecialchars($selectedBrand, ENT_QUOTES, "UTF-8") ?>">
                    <?php endif; ?>
                    <div class="search-input-wrapper">
                        <svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>
                        <input 
                            type="text" 
                            name="q" 
                            class="search-input" 
                            placeholder="Cari judul novel (3726 mdpl, Laut Bercerita, Bumi), nama penulis, atau genre..." 
                            value="<?= htmlspecialchars($q, ENT_QUOTES, "UTF-8") ?>"
                        >
                    </div>
                    <button type="submit" class="btn btn-primary">Cari</button>
                    <?php if ($q !== "" || ($cat !== "" && $cat !== "Semua") || ($selectedBrand !== "" && $selectedBrand !== "Semua")): ?>
                        <a href="index.php" class="btn btn-secondary">Reset Filter</a>
                    <?php endif; ?>
                </form>
                <div class="stats-count">
                    Ditemukan: <strong><?= count($products) ?></strong> novel
                </div>
            </div>
        </section>

        <!-- Product Cards Flexbox Grid -->
        <?php if (empty($products)): ?>
            <div class="empty-state">
                <svg class="empty-icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                </svg>
                <h3 class="empty-title">Tidak Ada Novel Sesuai Filter</h3>
                <p class="empty-subtitle">
                    <?= $q !== "" ? "Tidak ditemukan novel dengan kata kunci '" . htmlspecialchars($q, ENT_QUOTES, "UTF-8") . "'." : "Katalog novel pada kategori / penulis ini masih kosong." ?>
                </p>
                <a href="create.php" class="btn btn-primary">Tambah Novel Baru</a>
            </div>
        <?php else: ?>
            <div class="products">
                <?php foreach ($products as $p): ?>
                    <article class="card">
                        <!-- Card Media Thumbnail -->
                        <div class="card-media">
                            <img 
                                src="<?= getProductImage($p["image_url"] ?? "", $p["category"]) ?>" 
                                alt="<?= htmlspecialchars($p["name"], ENT_QUOTES, "UTF-8") ?>" 
                                class="card-img"
                                loading="lazy"
                            >
                            <div class="card-badges">
                                <span class="badge-brand"><?= htmlspecialchars($p["brand"] ?? "Penulis", ENT_QUOTES, "UTF-8") ?></span>
                                <span class="badge-category"><?= htmlspecialchars($p["category"], ENT_QUOTES, "UTF-8") ?></span>
                            </div>
                        </div>

                        <!-- Card Body Details -->
                        <div class="card-body">
                            <div>
                                <h3 class="card-title" title="<?= htmlspecialchars($p["name"], ENT_QUOTES, "UTF-8") ?>">
                                    <?= htmlspecialchars($p["name"], ENT_QUOTES, "UTF-8") ?>
                                </h3>
                                <div class="product-price">
                                    Rp <?= number_format($p["price"], 0, ",", ".") ?>
                                </div>
                                <div class="product-stock">
                                    <?php 
                                        $stockClass = "";
                                        $stockText = "Tersedia (" . (int)$p["stock"] . " eks)";
                                        if ($p["stock"] == 0) {
                                            $stockClass = "stock-empty";
                                            $stockText = "Stok Habis";
                                        } elseif ($p["stock"] <= 15) {
                                            $stockClass = "stock-low";
                                            $stockText = "Stok Menipis (" . (int)$p["stock"] . " eks)";
                                        }
                                    ?>
                                    <span class="stock-indicator <?= $stockClass ?>"></span>
                                    <span><?= $stockText ?></span>
                                </div>
                            </div>

                            <div class="card-footer">
                                <span class="card-date"><?= date("d M Y", strtotime($p["created_at"])) ?></span>
                                <div class="actions">
                                    <a href="edit.php?id=<?= (int)$p["id"] ?>" class="btn btn-edit">
                                        Edit
                                    </a>
                                    <form method="POST" action="delete.php" onsubmit="return confirm('Hapus novel \'<?= htmlspecialchars(addslashes($p['name']), ENT_QUOTES, 'UTF-8') ?>\' dari katalog?');" style="display:inline;">
                                        <input type="hidden" name="id" value="<?= (int)$p["id"] ?>">
                                        <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION["csrf"], ENT_QUOTES, "UTF-8") ?>">
                                        <button type="submit" class="btn btn-delete">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

    <footer class="footer">
        <p>Praktikum Pemrograman Web &bull; Pertemuan 3 &bull; DnD Novel Store — Manajemen Katalog Sastra</p>
    </footer>

</body>
</html>
